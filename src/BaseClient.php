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
use Swoole\Coroutine\Http\Client as WebSocketClient;

abstract class BaseClient
{
    public const COROUTINE_CLIENT_TYPE = 1;

    public const SYNC_CLIENT_TYPE = 2;

    public const WEBSOCKET_CLIENT_TYPE = 3;

    /** @var Coroutine\Client|\Swoole\Client|WebSocketClient */
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

    /** @var string */
    private $path = '/mqtt';

    /** @var bool */
    private $ssl = false;

    /**
     * @return $this
     */
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return $this
     */
    public function setClientType(int $clientType): self
    {
        $this->clientType = $clientType;

        return $this;
    }

    public function getClientType(): int
    {
        return $this->clientType;
    }

    /**
     * @return $this
     */
    public function setConfig(ClientConfig $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function getConfig(): ClientConfig
    {
        return $this->config;
    }

    /**
     * @param Coroutine\Client|\Swoole\Client|WebSocketClient $client
     * @return $this
     */
    public function setClient($client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Coroutine\Client|\Swoole\Client|WebSocketClient
     */
    public function getClient()
    {
        return $this->client;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setSsl(bool $ssl): self
    {
        $this->ssl = $ssl;

        return $this;
    }

    public function getSsl(): bool
    {
        return $this->ssl;
    }

    public function setConnectData(array $connectData): self
    {
        $this->connectData = $connectData;

        return $this;
    }

    /**
     * @return null|array|string
     */
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

    protected function isCoroutineClientType(): bool
    {
        return $this->clientType === self::COROUTINE_CLIENT_TYPE;
    }

    protected function isWebSocketClientType(): bool
    {
        return $this->clientType === self::WEBSOCKET_CLIENT_TYPE;
    }

    public function sleep(int $ms): void
    {
        $this->isCoroutineClientType() ? Coroutine::sleep($ms / 1000) : usleep($ms * 1000);
    }

    public function buildMessageId(): int
    {
        return ++$this->messageId > 65535 ? $this->messageId = 1 : $this->messageId;
    }

    public static function genClientID(string $prefix = 'Simps_'): string
    {
        return uniqid($prefix);
    }

    protected function handleVerbose(string $data): void
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

    protected function handleException(): void
    {
        if ($this->isCoroutineClientType() || $this->isWebSocketClientType()) {
            $errMsg = $this->client->errMsg;
        } else {
            $errMsg = socket_strerror($this->client->errCode);
        }
        $this->client->close();
        throw new ConnectException($errMsg, $this->client->errCode);
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
            if (empty($will['topic'])) {
                throw new ProtocolException('Topic cannot be empty');
            }
            $data['will'] = $will;
        }

        $this->setConnectData($data);

        return $this->send($data);
    }

    public function subscribe(array $topics, array $properties = [])
    {
        return $this->send([
            'type' => Protocol\Types::SUBSCRIBE,
            'message_id' => $this->buildMessageId(),
            'properties' => $properties,
            'topics' => $topics,
        ]);
    }

    public function unSubscribe(array $topics, array $properties = [])
    {
        return $this->send([
            'type' => Protocol\Types::UNSUBSCRIBE,
            'message_id' => $this->buildMessageId(),
            'properties' => $properties,
            'topics' => $topics,
        ]);
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
                if (empty($properties['topic_alias'])) {
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

    abstract protected function reConnect(): void;

    abstract public function send(array $data, bool $response = true);

    abstract public function recv();

    abstract protected function getResponse();
}
