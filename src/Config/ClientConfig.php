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

namespace Simps\MQTT\Config;

use Simps\MQTT\Protocol\ProtocolInterface;

class ClientConfig extends AbstractConfig
{
    protected $clientId;

    protected $swooleConfig = [];

    protected $userName;

    protected $password;

    protected $keepAlive = 0;

    protected $protocolName = ProtocolInterface::MQTT_PROTOCOL_NAME;

    protected $protocolLevel = ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1_1;

    protected $properties = [];

    protected $delay = 3000;

    protected $maxAttempts = -1;

    protected $sockType = SWOOLE_SOCK_TCP;

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(?string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getSwooleConfig(): array
    {
        return $this->swooleConfig;
    }

    public function setSwooleConfig(array $config): self
    {
        $this->swooleConfig = $config;

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(?string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getKeepAlive(): int
    {
        return $this->keepAlive;
    }

    public function setKeepAlive(int $keepAlive): self
    {
        $this->keepAlive = $keepAlive;

        return $this;
    }

    public function getProtocolName(): string
    {
        return $this->protocolName;
    }

    public function setProtocolName(string $protocolName): self
    {
        $this->protocolName = $protocolName;

        return $this;
    }

    public function getProtocolLevel(): int
    {
        return $this->protocolLevel;
    }

    public function setProtocolLevel(int $protocolLevel): self
    {
        $this->protocolLevel = $protocolLevel;

        return $this;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }

    public function setDelay(int $ms): self
    {
        $this->delay = $ms;

        return $this;
    }

    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    public function setMaxAttempts(int $attempts): self
    {
        $this->maxAttempts = $attempts;

        return $this;
    }

    public function getSockType(): int
    {
        return $this->sockType;
    }

    public function setSockType(int $sockType): self
    {
        $this->sockType = $sockType;

        return $this;
    }
}
