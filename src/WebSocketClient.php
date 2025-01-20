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
use Simps\MQTT\Exception\ProtocolException;
use Swoole\Coroutine\Http\Client;
use Swoole\Http\Status;
use Swoole\WebSocket\CloseFrame;
use Swoole\WebSocket\Frame;

class WebSocketClient extends BaseClient
{
    public function __construct(
        string $host,
        int $port,
        ClientConfig $config,
        string $path = '/mqtt',
        bool $ssl = false
    ) {
        $this->setHost($host)
            ->setPort($port)
            ->setConfig($config)
            ->setPath($path)
            ->setSsl($ssl);

        $client = new Client($host, $port, $ssl);
        $client->set($config->getSwooleConfig());
        $client->setHeaders($config->getHeaders());
        $upgrade = $client->upgrade($path);
        $this->setClient($client);
        if (!$upgrade || $client->getStatusCode() !== Status::SWITCHING_PROTOCOLS) {
            $this->handleException();
        }
    }

    protected function reConnect(): void
    {
        $result = false;
        $maxAttempts = $this->getConfig()->getMaxAttempts();
        $delay = $this->getConfig()->getDelay();
        while (!$result) {
            if ($maxAttempts === 0) {
                $this->handleException();
            }
            $this->sleep($delay);
            $this->getClient()->close();
            $upgrade = $this->getClient()->upgrade($this->getPath());
            if ($upgrade && $this->getClient()->getStatusCode() === Status::SWITCHING_PROTOCOLS) {
                $result = true;
            }
            if ($maxAttempts > 0) {
                $maxAttempts--;
            }
        }
    }

    public function send(array $data, bool $response = true)
    {
        $package = $this->getConfig()->isMQTT5() ? Protocol\V5::pack($data) : Protocol\V3::pack($data);

        $this->getClient()->push($package, WEBSOCKET_OPCODE_BINARY);

        if ($response) {
            return $this->recv();
        }

        return true;
    }

    public function recv()
    {
        $response = $this->getResponse();
        if ($response === false && $this->getClient()->errCode === 0) {
            $this->reConnect();
            $this->connect($this->getConnectData('clean_session') ?? true, $this->getConnectData('will') ?? []);
        } elseif ($response === false && $this->getClient()->errCode !== SOCKET_ETIMEDOUT) {
            $this->handleException();
        } elseif (is_string($response) && strlen($response) !== 0) {
            $this->handleVerbose($response);

            return $this->getConfig()->isMQTT5() ? Protocol\V5::unpack($response) : Protocol\V3::unpack($response);
        }

        return true;
    }

    protected function getResponse()
    {
        $response = $this->getClient()->recv();
        if ($response === false || $response instanceof CloseFrame) {
            return false;
        }
        if ($response instanceof Frame) {
            // If any other type of data frame is received the recipient MUST close the Network Connection.
            if ($response->opcode !== WEBSOCKET_OPCODE_BINARY) {
                $this->getClient()->close();
                throw new ProtocolException('MQTT Control Packets MUST be sent in WebSocket binary data frames.');
            }

            return $response->data;
        }

        return true;
    }
}
