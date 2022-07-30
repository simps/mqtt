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
namespace Simps\MQTT\Tools;

class PackTool extends Common
{
    public static function string(string $str): string
    {
        $len = strlen($str);

        return pack('n', $len) . $str;
    }

    public static function stringPair(string $key, string $value): string
    {
        return static::string($key) . static::string($value);
    }

    public static function longInt(int $int): string
    {
        return pack('N', $int);
    }

    public static function shortInt(int $int): string
    {
        return pack('n', $int);
    }

    public static function varInt(int $int): string
    {
        return static::packRemainingLength($int);
    }

    public static function packHeader(int $type, int $bodyLength, int $dup = 0, int $qos = 0, int $retain = 0): string
    {
        $type = $type << 4;
        if ($dup) {
            $type |= 1 << 3;
        }
        if ($qos) {
            $type |= $qos << 1;
        }
        if ($retain) {
            $type |= 1;
        }

        return chr($type) . static::packRemainingLength($bodyLength);
    }

    public static function genProperties(string $body): string
    {
        return static::packRemainingLength(strlen($body)) . $body;
    }

    protected static function packRemainingLength(int $bodyLength): string
    {
        $string = '';
        do {
            $digit = $bodyLength % 128;
            $bodyLength = $bodyLength >> 7;
            if ($bodyLength > 0) {
                $digit = ($digit | 0x80);
            }
            $string .= chr($digit);
        } while ($bodyLength > 0);

        return $string;
    }
}
