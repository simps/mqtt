<?php
/**
 * This file is part of Simps
 *
 * @link     https://github.com/simps/mqtt
 * @contact  lufei <lufei@simps.io>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code
 */

declare(strict_types=1);

namespace Simps\MQTT\Packet;

use Simps\MQTT\Types;

class Pack
{
    public static function connect(array $array): string
    {
        $body = static::string($array['protocol_name']) . chr($array['protocol_level']);
        $connect_flags = 0;
        if (!empty($array['clean_session'])) {
            $connect_flags |= 1 << 1;
        }
        if (!empty($array['will'])) {
            $connect_flags |= 1 << 2;
            $connect_flags |= $array['will']['qos'] << 3;
            if ($array['will']['retain']) {
                $connect_flags |= 1 << 5;
            }
        }
        if (!empty($array['password'])) {
            $connect_flags |= 1 << 6;
        }
        if (!empty($array['user_name'])) {
            $connect_flags |= 1 << 7;
        }
        $body .= chr($connect_flags);

        $keepAlive = !empty($array['keep_alive']) && (int) $array['keep_alive'] >= 0 ? (int) $array['keep_alive'] : 0;
        $body .= pack('n', $keepAlive);

        $body .= static::string($array['client_id']);
        if (!empty($array['will'])) {
            $body .= static::string($array['will']['topic']);
            $body .= static::string($array['will']['message']);
        }
        if (isset($array['user_name'])) {
            $body .= static::string($array['user_name']);
        }
        if (isset($array['password'])) {
            $body .= static::string($array['password']);
        }
        $head = static::packHeader(Types::CONNECT, strlen($body));

        return $head . $body;
    }

    public static function connAck(array $array): string
    {
        $body = !empty($array['session_present']) ? chr(1) : chr(0);
        $code = !empty($array['code']) ? $array['code'] : 0;
        $body .= chr($code);
        $head = static::packHeader(Types::CONNACK, strlen($body));

        return $head . $body;
    }

    public static function publish(array $array): string
    {
        $body = static::string($array['topic']);
        $qos = $array['qos'] ?? 0;
        if ($qos) {
            $body .= pack('n', $array['message_id']);
        }
        $body .= $array['message'];
        $dup = $array['dup'] ?? 0;
        $retain = $array['retain'] ?? 0;
        $head = static::packHeader(Types::PUBLISH, strlen($body), $dup, $qos, $retain);

        return $head . $body;
    }

    public static function subscribe(array $array): string
    {
        $id = $array['message_id'];
        $body = pack('n', $id);
        foreach ($array['topics'] as $topic => $qos) {
            $body .= Pack::string($topic);
            $body .= chr($qos);
        }
        $head = Pack::packHeader(Types::SUBSCRIBE, strlen($body), 0, 1);

        return $head . $body;
    }

    public static function subAck(array $array): string
    {
        $payload = $array['payload'];
        $body = pack('n', $array['message_id']) . call_user_func_array(
            'pack',
            array_merge(['C*'], $payload)
        );
        $head = static::packHeader(Types::SUBACK, strlen($body));

        return $head . $body;
    }

    public static function unSubscribe(array $array): string
    {
        $body = pack('n', $array['message_id']);
        foreach ($array['topics'] as $topic) {
            $body .= static::string($topic);
        }
        $head = static::packHeader(Types::UNSUBSCRIBE, strlen($body), 0, 1);

        return $head . $body;
    }

    private static function string(string $str)
    {
        $len = strlen($str);

        return pack('n', $len) . $str;
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

    private static function packRemainingLength(int $bodyLength)
    {
        $length=$bodyLength;
        $string = '';
        do {
            $digit = $length % 128;
            $length = $length >> 7;
            if ($length > 0) {
                $digit = ($digit | 0x80);
            }
            $string .= chr($digit);
        } while ($length > 0);

        return $string;
    }
}
