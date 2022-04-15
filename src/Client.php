<?php

declare(strict_types=1);
/**
 * This file is part of Simps.
 *
 * @link     https://github.com/simps/mqtt
 * @contact  Lu Fei <lufei@simps.io>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Simps\MQTT;

use Simps\MQTT\Config\ClientConfig;
use Simps\MQTT\Exception\ConnectException;
use Simps\MQTT\Exception\ProtocolException;
use Simps\MQTT\Hex\ReasonCode;
use Simps\MQTT\Tools\Common;
use Swoole\Coroutine;

class Client
{
    /** @var Coroutine\Client|\Swoole\Client */
    private $client;

    /** @var int */
    private $messageId = 0;

    /** @var array */
    private $connectData = [];

    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var ClientConfig */
    private $config;

    /** @var int */
    private $clientType;

    public const COROUTINE_CLIENT_TYPE = 1;

    public const SYNC_CLIENT_TYPE = 2;

    public function __construct(
        string $host,
        int $port,
        ClientConfig $config,
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
            if (!isset($will['topic']) || empty($will['topic'])) {
                throw new ProtocolException('Topic cannot be empty');
            }
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
            if ($this->getConfig()->isMQTT5()) {
                if (!isset($properties['topic_alias']) || empty($properties['topic_alias'])) {
                    throw new ProtocolException('Topic cannot be empty or need to set topic_alias');
                }
            } else {
                throw new ProtocolException('Topic cannot be empty');
            }
        }

        $response = $qos > 0;

        // A PUBLISH packet MUST NOT contain a Packet Identifier if its QoS value is set to 0
        $message_id = 0;
        if ($qos) {
            $message_id = $this->buildMessageId();
        }

        return $this->send(
            [
                'type' => Protocol\Types::PUBLISH,
                'qos' => $qos,
                'dup' => $dup,
                'retain' => $retain,
                'topic' => $topic,
                'message_id' => $message_id,
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
                $this->handleException();
            }
            $this->sleep($delay);
            $this->client->close();
            $result = $this->client->connect($this->getHost(), $this->getPort());
            if ($maxAttempts > 0) {
                $maxAttempts--;
            }
        }
    }

    private function handleException()
    {
        if ($this->isCoroutineClientType()) {
            $errMsg = $this->client->errMsg;
        } else {
            $errMsg = socket_strerror($this->client->errCode);
        }
        $this->client->close();
        throw new ConnectException($errMsg, $this->client->errCode);
    }

    public function send(array $data, bool $response = true)
    {
        if ($this->getConfig()->isMQTT5()) {
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
        } elseif ($response === false && $this->client->errCode !== SOCKET_ETIMEDOUT) {
            $this->handleException();
        } elseif (is_string($response) && strlen($response) !== 0) {
            $this->handleVerbose($response);

            if ($this->getConfig()->isMQTT5()) {
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
        return $this->clientType === self::COROUTINE_CLIENT_TYPE;
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

    public function getClient()
    {
        return $this->client;
    }

    protected function handleVerbose(string $data)
    {
        switch ($this->getConfig()->getVerbose()) {
            case MQTT_VERBOSE_HEXDUMP:
                echo Common::hexDump($data), PHP_EOL;
                break;
            case MQTT_VERBOSE_HEXDUMP_ASCII:
                echo Common::hexDumpAscii($data), PHP_EOL;
                break;
            case MQTT_VERBOSE_ASCII:
                echo Common::ascii($data), PHP_EOL;
                break;
            case MQTT_VERBOSE_TEXT:
                echo Common::printableText($data), PHP_EOL;
                break;
            case MQTT_VERBOSE_HEX_STREAM:
                echo Common::hexStream($data), PHP_EOL;
                break;
            case MQTT_VERBOSE_NONE:
            default:
                break;
        }
    }
}
