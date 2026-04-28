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
use Swoole\Coroutine;

class Client extends BaseClient
{
    public function __construct(
        string $host,
        int $port,
        ClientConfig $config,
        int $clientType = self::COROUTINE_CLIENT_TYPE
    ) {
        $this->setHost($host)
            ->setPort($port)
            ->setConfig($config)
            ->setClientType($clientType);

        if ($this->isCoroutineClientType()) {
            $client = new Coroutine\Client($config->getSockType());
        } else {
            $client = new \Swoole\Client($config->getSockType());
        }
        $client->set($config->getSwooleConfig());
        $this->setClient($client);
        if (!$this->getClient()->connect($host, $port)) {
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
            $result = $this->getClient()->connect($this->getHost(), $this->getPort());
            if ($maxAttempts > 0) {
                $maxAttempts--;
            }
        }
    }

    public function send(array $data, bool $response = true)
    {
        $package = $this->getConfig()->isMQTT5() ? Protocol\V5::pack($data) : Protocol\V3::pack($data);

        $this->getClient()->send($package);

        if ($response) {
            return $this->recv();
        }

        return true;
    }

    public function recv()
    {
        $response = $this->getResponse();
        if ($response === '' || !$this->getClient()->isConnected()) {
            $this->reConnect();
            $this->connect($this->getConnectData('clean_session') ?? true, $this->getConnectData('will') ?? []);
        } elseif ($response === false && $this->getClient()->errCode !== SOCKET_ETIMEDOUT) {
            $this->handleException();
        } elseif (is_string($response)) {
            $this->handleVerbose($response);

            return $this->getConfig()->isMQTT5() ? Protocol\V5::unpack($response) : Protocol\V3::unpack($response);
        }

        return true;
    }

    protected function getResponse()
    {
        if ($this->isCoroutineClientType()) {
            $response = $this->getClient()->recv();
        } else {
            $write = $error = [];
            $read = [$this->getClient()];
            $n = swoole_client_select($read, $write, $error);
            $response = $n > 0 ? $this->getClient()->recv() : true;
        }

        return $response;
    }
}
