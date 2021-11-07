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

class PingResp extends AbstractMessage
{
    public function getContents(bool $getArray = false)
    {
        $buffer = [
            'type' => Types::PINGRESP,
        ];

        if ($getArray) {
            return $buffer;
        }

        if ($this->isMQTT5()) {
            return V5::pack($buffer);
        }

        return V3::pack($buffer);
    }
}
