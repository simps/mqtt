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

use Simps\MQTT\Hex\ReasonCode;
use Simps\MQTT\Protocol\Types;
use Simps\MQTT\Protocol\V5;

class Auth extends AbstractMessage
{
    protected $code = ReasonCode::SUCCESS;

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    // AUTH type is only available in MQTT5
    public function getContents(bool $getArray = false)
    {
        $buffer = [
            'type' => Types::AUTH,
            'code' => $this->getCode(),
            'properties' => $this->getProperties(),
        ];

        if ($getArray) {
            return $buffer;
        }

        return V5::pack($buffer);
    }
}
