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

/**
 * @method static string hexDump(string $encode)
 * @method static string hexDumpAscii(string $encode)
 * @method static string printableText(string $encode)
 * @method static string hexStream(string $encode)
 * @method static string ascii(string $encode)
 */
abstract class Common
{
    public static function printf(string $data): void
    {
        echo "\033[36m";
        for ($i = 0; $i < strlen($data); $i++) {
            $ascii = ord($data[$i]);
            if ($ascii > 31) {
                $chr = $data[$i];
            } else {
                $chr = ' ';
            }
            printf("%4d: %08b : 0x%02x : %d : %s\n", $i, $ascii, $ascii, $ascii, $chr);
        }
        echo "\033[0m";
    }

    public static function __callStatic($method, $arguments)
    {
        return (new Debug())->setEncode(...$arguments)->{$method}();
    }
}
