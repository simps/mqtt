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

use Simps\MQTT\Protocol\Types;
use Simps\MQTT\Protocol\V3;
use Simps\MQTT\Protocol\V5;

class SubAck extends AbstractMessage
{
    protected $messageId = 0;

    protected $codes = [];

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function setMessageId(int $messageId): self
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function getCodes(): array
    {
        return $this->codes;
    }

    public function setCodes(array $codes): self
    {
        $this->codes = $codes;

        return $this;
    }

    public function getContents(bool $getArray = false)
    {
        $buffer = [
            'type' => Types::SUBACK,
            'message_id' => $this->getMessageId(),
            'codes' => $this->getCodes(),
        ];

        if ($this->isMQTT5()) {
            $buffer['properties'] = $this->getProperties();
        }

        if ($getArray) {
            return $buffer;
        }

        if ($this->isMQTT5()) {
            return V5::pack($buffer);
        }

        return V3::pack($buffer);
    }
}
