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

class Will extends AbstractMessage
{
    protected $topic = '';

    protected $qos = ProtocolInterface::MQTT_QOS_0;

    protected $retain = ProtocolInterface::MQTT_RETAIN_0;

    protected $message = '';

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getQos(): int
    {
        return $this->qos;
    }

    public function setQos(int $qos): self
    {
        $this->qos = $qos;

        return $this;
    }

    public function getRetain(): int
    {
        return $this->retain;
    }

    public function setRetain(int $retain): self
    {
        $this->retain = $retain;

        return $this;
    }

    public function getContents(bool $getArray = false)
    {
        $buffer = [
            'topic' => $this->getTopic(),
            'qos' => $this->getQos(),
            'retain' => $this->getRetain(),
            'message' => $this->getMessage(),
        ];

        if ($this->isMQTT5()) {
            $buffer['properties'] = $this->getProperties();
        }

        if ($getArray) {
            return $buffer;
        }

        // The will message can only be an array
        return '';
    }
}
