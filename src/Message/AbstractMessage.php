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
namespace Simps\MQTT\Message;

use Simps\MQTT\Protocol\ProtocolInterface;

abstract class AbstractMessage
{
    protected $protocolLevel = ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1_1;

    protected $properties = [];

    public function __construct(array $data = [])
    {
        foreach ($data as $k => $v) {
            $methodName = 'set' . ucfirst($k);
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($v);
            } elseif (isset($this->{$k})) {
                $this->{$k} = $v;
            }
        }
    }

    public function getProtocolLevel(): int
    {
        if (!empty($this->getProperties()) && $this->protocolLevel !== ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0) {
            return ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0;
        }

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

    public function isMQTT5(): bool
    {
        return $this->getProtocolLevel() === ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0;
    }

    abstract public function getContents(bool $getArray = false);

    public function toArray(): array
    {
        return $this->getContents(true);
    }

    public function __toString()
    {
        return $this->getContents();
    }
}
