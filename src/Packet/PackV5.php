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

namespace Simps\MQTT\Packet;

use Simps\MQTT\Types;

class PackV5
{
    public static function connAck(array $array): string
    {
        $body = !empty($array['session_present']) ? chr(1) : chr(0);
        $code = !empty($array['code']) ? $array['code'] : 0;
        $body .= chr($code);
        if (!empty($array['maximum_packet_size'])) {
            $body .= pack('N', $array['maximum_packet_size']);
        }
        $retain_available = 0;
        if (!isset($array['retain_available']) || !empty($array['retain_available'])) {
            $retain_available = 1;
        }
        $body .= chr($retain_available);
        $shared_subscription_available = 0;
        if (!isset($array['shared_subscription_available']) || !empty($array['shared_subscription_available'])) {
            $shared_subscription_available = 1;
        }
        $body .= chr($shared_subscription_available);
        $topic_alias_maximum = 0;
        if (isset($array['topic_alias_maximum'])) {
            $topic_alias_maximum = $array['topic_alias_maximum'];
        }
        $body .= pack('n', $topic_alias_maximum);
        $subscription_identifier_available = 0;
        if (!isset($array['subscription_identifier_available']) || !empty($array['subscription_identifier_available'])) {
            $subscription_identifier_available = 1;
        }
        $body .= chr($subscription_identifier_available);
        $wildcard_subscription_available = 0;
        if (!isset($array['wildcard_subscription_available']) || !empty($array['wildcard_subscription_available'])) {
            $wildcard_subscription_available = 1;
        }
        $body .= chr($wildcard_subscription_available);
        $head = static::packHeader(Types::CONNACK, strlen($body));

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
