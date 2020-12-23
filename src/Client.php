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

use Simps\MQTT\Exception\RuntimeException;
use Simps\MQTT\Hex\ReasonCode;
use Swoole\Coroutine;

class Client
{
    /** @var \Swoole\Coroutine\Client */
    private $client;

    private $config = [
        'host' => '127.0.0.1',
        'port' => 1883,
        'time_out' => 0.5,
        'select_time_out' => 0.5,
        'user_name' => '',
        'password' => '',
        'client_id' => '',
        'keep_alive' => 0,
        'protocol_name' => ProtocolInterface::MQTT_PROTOCOL_NAME,
        'protocol_level' => ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1_1,
        'properties' => [],
    ];

    private $messageId = 0;

    private $connectData = [];

    private $clientType;

    const COROUTINE_CLIENT_TYPE = 1;

    const SYNC_CLIENT_TYPE = 2;

    public function __construct(array $config, array $swConfig = [], int $type = SWOOLE_SOCK_TCP, int $clientType = self::COROUTINE_CLIENT_TYPE)
    {
        $this->config = array_replace_recursive($this->config, $config);
        $this->clientType = $clientType;
        if ($this->isCoroutineClientType()) {
            $this->client = new Coroutine\Client($type);
        } else {
            $this->client = new \Swoole\Client($type);
        }
        if (!empty($swConfig)) {
            $this->client->set($swConfig);
        }
        if (!$this->client->connect($this->config['host'], $this->config['port'], $this->config['time_out'])) {
            $this->reConnect();
        }
    }

    public function connect(bool $clean = true, array $will = [])
    {
        $data = [
            'type' => Types::CONNECT,
            'protocol_name' => $this->config['protocol_name'],
            'protocol_level' => (int) $this->config['protocol_level'],
            'clean_session' => $clean ? 0 : 1,
            'client_id' => $this->config['client_id'],
            'keep_alive' => $this->config['keep_alive'],
            'properties' => $this->config['properties'],
            'user_name' => $this->config['user_name'],
            'password' => $this->config['password'],
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
            'type' => Types::SUBSCRIBE,
            'message_id' => $this->buildMessageId(),
            'properties' => $properties,
            'topics' => $topics,
        ];

        return $this->send($data);
    }

    public function unSubscribe(array $topics, array $properties = [])
    {
        $data = [
            'type' => Types::UNSUBSCRIBE,
            'message_id' => $this->buildMessageId(),
            'properties' => $properties,
            'topics' => $topics,
        ];

        return $this->send($data);
    }

    public function publish($topic, $message, $qos = 0, $dup = 0, $retain = 0, array $properties = [])
    {
        $response = ($qos > 0) ? true : false;

        return $this->send(
            [
                'type' => Types::PUBLISH,
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
        return $this->send(['type' => Types::PINGREQ]);
    }

    public function close(int $code = ReasonCode::NORMAL_DISCONNECTION, array $properties = [])
    {
        $this->send(['type' => Types::DISCONNECT, 'code' => $code, 'properties' => $properties], false);

        return $this->client->close();
    }

    public function auth(int $code = ReasonCode::SUCCESS, array $properties = [])
    {
        return $this->send(['type' => Types::AUTH, 'code' => $code, 'properties' => $properties]);
    }

    private function reConnect()
    {
        $reConnectTime = 1;
        $result = false;
        while (!$result) {
            if ($this->isCoroutineClientType()) {
                Coroutine::sleep(3);
            } else {
                sleep(3);
            }
            $this->client->close();
            $result = $this->client->connect($this->config['host'], $this->config['port'], $this->config['time_out']);
            ++$reConnectTime;
        }
        $this->connect((bool) $this->connectData['clean_session'] ?? true, $this->connectData['will'] ?? []);
    }

    public function send(array $data, $response = true)
    {
        if ($this->config['protocol_level'] === 5) {
            $package = ProtocolV5::pack($data);
        } else {
            $package = Protocol::pack($data);
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
        } elseif ($response === false) {
            if ($this->client->errCode === SOCKET_ECONNRESET) {
                $this->client->close();
            } elseif ($this->client->errCode !== SOCKET_ETIMEDOUT) {
                if ($this->isCoroutineClientType()) {
                    $errMsg = $this->client->errMsg;
                } else {
                    $errMsg = socket_strerror($this->client->errCode);
                }
                throw new RuntimeException($errMsg, $this->client->errCode);
            }
        } elseif (is_string($response) && strlen($response) > 0) {
            if ($this->config['protocol_level'] === 5) {
                return ProtocolV5::unpack($response);
            }

            return Protocol::unpack($response);
        }

        return true;
    }

    protected function getResponse()
    {
        if ($this->isCoroutineClientType()) {
            $response = $this->client->recv();
        } else {
            while (true) {
                $write = $error = [];
                $read = [$this->client];
                $n = swoole_client_select($read, $write, $error, $this->config['select_time_out']);
                if ($n > 0) {
                    $response = $this->client->recv();
                } else {
                    $response = true;
                }
                break;
            }
        }

        return $response;
    }

    protected function isCoroutineClientType()
    {
        if ($this->clientType === self::COROUTINE_CLIENT_TYPE) {
            return true;
        }

        return false;
    }

    public function buildMessageId()
    {
        return ++$this->messageId;
    }

    public static function genClientID(string $prefix = 'Simps_'): string
    {
        return uniqid($prefix);
    }
}
