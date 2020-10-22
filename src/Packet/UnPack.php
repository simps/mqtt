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

use Simps\MQTT\Exception\MQTTLengthException;
use Simps\MQTT\Types;

class UnPack
{
    public static function connect(string $remaining): array
    {
        $protocolName = static::string($remaining);
        $protocolLevel = ord($remaining[0]);
        $cleanSession = ord($remaining[1]) >> 1 & 0x1;
        $willFlag = ord($remaining[1]) >> 2 & 0x1;
        $willQos = ord($remaining[1]) >> 3 & 0x3;
        $willRetain = ord($remaining[1]) >> 5 & 0x1;
        $passwordFlag = ord($remaining[1]) >> 6 & 0x1;
        $userNameFlag = ord($remaining[1]) >> 7 & 0x1;
        $remaining = substr($remaining, 2);
        $keepAlive = unpack('n', $remaining)[1];
        $remaining = substr($remaining, 2);
        $clientId = static::string($remaining);
        if ($willFlag) {
            $willTopic = static::string($remaining);
            $willMessage = static::string($remaining);
        }
        $userName = $password = '';
        if ($userNameFlag) {
            $userName = static::string($remaining);
        }
        if ($passwordFlag) {
            $password = static::string($remaining);
        }
        $package = [
            'type' => Types::CONNECT,
            'protocol_name' => $protocolName,
            'protocol_level' => $protocolLevel,
            'clean_session' => $cleanSession,
            'will' => [],
            'user_name' => $userName,
            'password' => $password,
            'keep_alive' => $keepAlive,
            'client_id' => $clientId,
        ];
        if ($willFlag) {
            $package['will'] = [
                'qos' => $willQos,
                'retain' => $willRetain,
                'topic' => $willTopic,
                'message' => $willMessage,
            ];
        } else {
            unset($package['will']);
        }

        return $package;
    }

    public static function connAck(string $remaining): array
    {
        return ['type' => Types::CONNACK, 'session_present' => ord($remaining[0]) & 0x01, 'code' => ord($remaining[1])];
    }

    public static function publish(int $dup, int $qos, int $retain, string $remaining): array
    {
        $topic = static::string($remaining);
        if ($qos) {
            $messageId = static::shortInt($remaining);
        }
        $package = [
            'type' => Types::PUBLISH,
            'topic' => $topic,
            'message' => $remaining,
            'dup' => $dup,
            'qos' => $qos,
            'retain' => $retain,
        ];
        if ($qos) {
            $package['message_id'] = $messageId;
        }

        return $package;
    }

    public static function subscribe(string $remaining): array
    {
        $messageId = static::shortInt($remaining);
        $topics = [];
        while ($remaining) {
            $topic = static::string($remaining);
            $qos = ord($remaining[0]);
            $topics[$topic] = $qos;
            $remaining = substr($remaining, 1);
        }

        return ['type' => Types::SUBSCRIBE, 'message_id' => $messageId, 'topics' => $topics];
    }

    public static function subAck(string $remaining): array
    {
        $messageId = static::shortInt($remaining);
        $tmp = unpack('C*', $remaining);

        return ['type' => Types::SUBACK, 'message_id' => $messageId, 'codes' => array_values($tmp)];
    }

    public static function unSubscribe(string $remaining): array
    {
        $messageId = static::shortInt($remaining);
        $topics = [];
        while ($remaining) {
            $topic = static::string($remaining);
            $topics[] = $topic;
        }

        return ['type' => Types::UNSUBSCRIBE, 'message_id' => $messageId, 'topics' => $topics];
    }

    private static function string(&$remaining)
    {
        $length = unpack('n', $remaining)[1];
        if ($length + 2 > strlen($remaining)) {
            throw new MQTTLengthException("length:{$length} not enough for unpack string");
        }
        $string = substr($remaining, 2, $length);
        $remaining = substr($remaining, $length + 2);

        return $string;
    }

    private static function shortInt(&$remaining)
    {
        $tmp = unpack('n', $remaining);
        $remaining = substr($remaining, 2);

        return $tmp[1];
    }
}
