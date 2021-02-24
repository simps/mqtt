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

namespace Simps\MQTT\Message;

use Simps\MQTT\Protocol\Types;
use Simps\MQTT\Protocol\V3;
use Simps\MQTT\Protocol\V5;

class ConnAck extends AbstractMessage
{
    protected $code = 0;

    protected $sessionPresent = 0;

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getSessionPresent(): int
    {
        return $this->sessionPresent;
    }

    public function setSessionPresent(int $sessionPresent): self
    {
        $this->sessionPresent = $sessionPresent;

        return $this;
    }

    public function getContents()
    {
        $buffer = [
            'type' => Types::CONNACK,
            'code' => $this->getCode(),
            'session_present' => $this->getSessionPresent(),
        ];

        if ($this->isMQTT5()) {
            $buffer['properties'] = $this->getProperties();

            return V5::pack($buffer);
        }

        return V3::pack($buffer);
    }
}
