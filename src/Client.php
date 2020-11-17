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
use Swoole\Coroutine;

class Client
{
    /** @var \Swoole\Coroutine\Client */
    private $client;

    private $config = [
        'host' => '127.0.0.1',
        'port' => 1883,
        'time_out' => 0.5,
        'user_name' => '',
        'password' => '',
        'client_id' => '',
        'keep_alive' => 0,
        'protocol_name' => 'MQTT',
        'protocol_level' => 4,
    ];

    private $messageId = 0;

    public function __construct(array $config, array $swConfig = [], int $type = SWOOLE_SOCK_TCP)
    {
        $this->config = array_replace_recursive($this->config, $config);
        $this->client = new Coroutine\Client($type);
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
            'protocol_level' => $this->config['protocol_level'],
            'clean_session' => $clean ? 0 : 1,
            'client_id' => $this->config['client_id'],
            'keep_alive' => $this->config['keep_alive'],
            'user_name' => $this->config['user_name'],
            'password' => $this->config['password'],
        ];
        if (!empty($will)) {
            $data['will'] = $will;
        }

        return $this->send($data);
    }

    public function subscribe(array $topics)
    {
        $data = [
            'type' => Types::SUBSCRIBE,
            'message_id' => $this->buildMessageId(),
            'topics' => $topics,
        ];

        return $this->send($data);
    }

    public function unSubscribe(array $topics)
    {
        $data = [
            'type' => Types::UNSUBSCRIBE,
            'message_id' => $this->buildMessageId(),
            'topics' => $topics,
        ];

        return $this->send($data);
    }

    public function publish($topic, $content, $qos = 0, $dup = 0, $retain = 0)
    {
        $response = ($qos > 0) ? true : false;

        return $this->send(
            [
                'type' => Types::PUBLISH,
                'message_id' => $this->buildMessageId(),
                'topic' => $topic,
                'message' => $content,
                'qos' => $qos,
                'dup' => $dup,
                'retain' => $retain,
            ],
            $response
        );
    }

    public function ping()
    {
        return $this->send(['type' => Types::PINGREQ]);
    }

    public function close()
    {
        $this->send(['type' => Types::DISCONNECT], false);

        return $this->client->close();
    }

    private function reConnect()
    {
        $reConnectTime = 1;
        $result = false;
        while (!$result) {
            Coroutine::sleep(3);
            $this->client->close();
            $result = $this->client->connect($this->config['host'], $this->config['port'], $this->config['time_out']);
            ++$reConnectTime;
        }
        $this->connect();
    }

    public function send(array $data, $response = true)
    {
        $package = Protocol::pack($data);
        $this->client->send($package);
        if ($response) {
            return $this->recv();
        }

        return true;
    }

    public function recv()
    {
        $response = $this->client->recv();

        if ($response === '') {
            $this->reConnect();
        } elseif ($response === false) {
            if ($this->client->errCode !== SOCKET_ETIMEDOUT) {
                throw new RuntimeException($this->client->errMsg, $this->client->errCode);
            }
        } elseif (strlen($response) > 0) {
            return Protocol::unpack($response);
        }

        return true;
    }

    public function buildMessageId()
    {
        return ++$this->messageId;
    }
}
