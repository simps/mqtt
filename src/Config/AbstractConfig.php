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
namespace Simps\MQTT\Config;

abstract class AbstractConfig
{
    public function __construct(array $data = [])
    {
        foreach ($data as $k => $v) {
            $methodName = 'set' . ucfirst($k);
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($v);
            } else {
                $this->{$k} = $v;
            }
        }
    }
}
