<?php
/**
 * This file is part of Simps
 *
 * @link     https://github.com/simps/mqtt
 * @contact  Lu Fei <lufei@simps.io>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code
 */

declare(strict_types=1);

namespace Simps\MQTT;

use Simps\MQTT\Config\ClientConfig;
use Simps\MQTT\Exception\ConnectException;
use Simps\MQTT\Exception\ProtocolException;
use Simps\MQTT\Hex\ReasonCode;
use Swoole\Coroutine;

class Client
{
    /** @var Coroutine\Client|\Swoole\Client */
    private $client;

    private $messageId = 0;

    private $connectData = [];

    private $host;

    private $port;

    private $config;

    private $clientType;

    public const COROUTINE_CLIENT_TYPE = 1;

    public const SYNC_CLIENT_TYPE = 2;

    public function __construct(
        string $host,
        int $port,
        ?ClientConfig $config = null,
        int $clientType = self::COROUTINE_CLIENT_TYPE
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->config = $config;
        $this->clientType = $clientType;

        if ($this->isCoroutineClientType()) {
            $this->client = new Coroutine\Client($config->getSockType());
        } else {
            $this->client = new \Swoole\Client($config->getSockType());
        }
        $this->client->set($config->getSwooleConfig());
        if (!$this->client->connect($host, $port)) {
            $this->reConnect();
        }
    }

    public function connect(bool $clean = true, array $will = [])
    {
        $data = [
            'type' => Protocol\Types::CONNECT,
            'protocol_name' => $this->getConfig()->getProtocolName(),
            'protocol_level' => $this->getConfig()->getProtocolLevel(),
            'clean_session' => $clean,
            'client_id' => $this->getConfig()->getClientId(),
            'keep_alive' => $this->getConfig()->getKeepAlive(),
            'properties' => $this->getConfig()->getProperties(),
            'user_name' => $this->getConfig()->getUserName(),
            'password' => $this->getConfig()->getPassword(),
        ];
        if (!empty($will)) {
            $data['will'] = $will;
        }

        $this->connectData = $data;

        return $this->send($data);
    }

    public function subscribe(array $topics, array $properties = [])
    {
        $data = [
            'type' => Protocol\Types::SUBSCRIBE,
            'message_id' => $this->buildMessageId(),
            'properties' => $properties,
            'topics' => $topics,
        ];

        return $this->send($data);
    }

    public function unSubscribe(array $topics, array $properties = [])
    {
        $data = [
            'type' => Protocol\Types::UNSUBSCRIBE,
            'message_id' => $this->buildMessageId(),
            'properties' => $properties,
            'topics' => $topics,
        ];

        return $this->send($data);
    }

    public function publish(
        string $topic,
        string $message,
        int $qos = 0,
        int $dup = 0,
        int $retain = 0,
        array $properties = []
    ) {
        if (empty($topic)) {
            switch ($this->getConfig()->getProtocolLevel()) {
                case Protocol\ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0:
                    if (!isset($properties['topic_alias'])) {
                        throw new ProtocolException(
                            'Protocol Error, Topic cannot be empty or need to set topic_alias'
                        );
                    }
                    break;
                default:
                    throw new ProtocolException('Protocol Error, Topic cannot be empty');
            }
        }

        $response = $qos > 0;

        return $this->send(
            [
                'type' => Protocol\Types::PUBLISH,
                'qos' => $qos,
                'dup' => $dup,
                'retain' => $retain,
                'topic' => $topic,
                'message_id' => $this->buildMessageId(),
                'properties' => $properties,
                'message' => $message,
            ],
            $response
        );
    }

    public function ping()
    {
        return $this->send(['type' => Protocol\Types::PINGREQ]);
    }

    public function close(int $code = ReasonCode::NORMAL_DISCONNECTION, array $properties = []): bool
    {
        $this->send(['type' => Protocol\Types::DISCONNECT, 'code' => $code, 'properties' => $properties], false);

        return $this->client->close();
    }

    public function auth(int $code = ReasonCode::SUCCESS, array $properties = [])
    {
        return $this->send(['type' => Protocol\Types::AUTH, 'code' => $code, 'properties' => $properties]);
    }

    private function reConnect()
    {
        $result = false;
        $maxAttempts = $this->getConfig()->getMaxAttempts();
        $delay = $this->getConfig()->getDelay();
        while (!$result) {
            if ($maxAttempts === 0) {
                if ($this->isCoroutineClientType()) {
                    $errMsg = $this->client->errMsg;
                } else {
                    $errMsg = socket_strerror($this->client->errCode);
                }
                throw new ConnectException($errMsg, $this->client->errCode);
            }
            $this->sleep($delay);
            $this->client->close();
            $result = $this->client->connect($this->getHost(), $this->getPort());
            if ($maxAttempts > 0) {
                $maxAttempts--;
            }
        }
    }

    public function send(array $data, bool $response = true)
    {
        if ($this->getConfig()->getProtocolLevel() === Protocol\ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0) {
            $package = Protocol\V5::pack($data);
        } else {
            $package = Protocol\V3::pack($data);
        }

        $this->client->send($package);

        if ($response) {
            return $this->recv();
        }

        return true;
    }

    public function recv()
    {
        $response = $this->getResponse();
        if ($response === '' || !$this->client->isConnected()) {
            $this->reConnect();
            $this->connect($this->getConnectData('clean_session') ?? true, $this->getConnectData('will') ?? []);
        } elseif ($response === false) {
            if ($this->client->errCode === SOCKET_ECONNRESET) {
                $this->client->close();
            } elseif ($this->client->errCode !== SOCKET_ETIMEDOUT) {
                if ($this->isCoroutineClientType()) {
                    $errMsg = $this->client->errMsg;
                } else {
                    $errMsg = socket_strerror($this->client->errCode);
                }
                throw new ConnectException($errMsg, $this->client->errCode);
            }
        } elseif (is_string($response) && strlen($response) > 0) {
            if ($this->getConfig()->getProtocolLevel() === Protocol\ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0) {
                return Protocol\V5::unpack($response);
            }

            return Protocol\V3::unpack($response);
        }

        return true;
    }

    protected function getResponse()
    {
        if ($this->isCoroutineClientType()) {
            $response = $this->client->recv();
        } else {
            $write = $error = [];
            $read = [$this->client];
            $n = swoole_client_select($read, $write, $error);
            if ($n > 0) {
                $response = $this->client->recv();
            } else {
                $response = true;
            }
        }

        return $response;
    }

    protected function isCoroutineClientType(): bool
    {
        if ($this->clientType === self::COROUTINE_CLIENT_TYPE) {
            return true;
        }

        return false;
    }

    public function buildMessageId(): int
    {
        if ($this->messageId === 65535) {
            $this->messageId = 0;
        }

        return ++$this->messageId;
    }

    public static function genClientID(string $prefix = 'Simps_'): string
    {
        return uniqid($prefix);
    }

    public function sleep(int $ms): void
    {
        if ($this->isCoroutineClientType()) {
            Coroutine::sleep($ms / 1000);
        } else {
            usleep($ms * 1000);
        }
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getConfig(): ClientConfig
    {
        return $this->config;
    }

    public function getConnectData(?string $key = null)
    {
        if ($key) {
            if (isset($this->connectData[$key])) {
                return $this->connectData[$key];
            }

            return null;
        }

        return $this->connectData;
    }
}
