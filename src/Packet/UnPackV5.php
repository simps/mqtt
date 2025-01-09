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
namespace Simps\MQTT\Packet;

use Simps\MQTT\Hex\ReasonCode;
use Simps\MQTT\Property\UnPackProperty;
use Simps\MQTT\Protocol\Types;
use Simps\MQTT\Tools\UnPackTool;

class UnPackV5
{
    public static function connect(string $remaining): array
    {
        $protocolName = UnPackTool::string($remaining);
        $protocolLevel = ord($remaining[0]);
        $cleanSession = ord($remaining[1]) >> 1 & 0x1;
        $willFlag = ord($remaining[1]) >> 2 & 0x1;
        $willQos = ord($remaining[1]) >> 3 & 0x3;
        $willRetain = ord($remaining[1]) >> 5 & 0x1;
        $passwordFlag = ord($remaining[1]) >> 6 & 0x1;
        $userNameFlag = ord($remaining[1]) >> 7 & 0x1;
        $remaining = substr($remaining, 2);
        $keepAlive = UnPackTool::shortInt($remaining);
        $propertiesTotalLength = UnPackTool::byte($remaining);
        if ($propertiesTotalLength) {
            $properties = UnPackProperty::connect($propertiesTotalLength, $remaining);
        }
        $clientId = UnPackTool::string($remaining);
        $willProperties = [];
        if ($willFlag) {
            $willPropertiesTotalLength = UnPackTool::byte($remaining);
            if ($willPropertiesTotalLength) {
                $willProperties = UnPackProperty::willProperties($willPropertiesTotalLength, $remaining);
            }
            $willTopic = UnPackTool::string($remaining);
            $willMessage = UnPackTool::string($remaining);
        }
        $userName = $password = '';
        if ($userNameFlag) {
            $userName = UnPackTool::string($remaining);
        }
        if ($passwordFlag) {
            $password = UnPackTool::string($remaining);
        }
        $package = [
            'type' => Types::CONNECT,
            'protocol_name' => $protocolName,
            'protocol_level' => $protocolLevel,
            'clean_session' => $cleanSession,
            'properties' => [],
            'will' => [],
            'user_name' => $userName,
            'password' => $password,
            'keep_alive' => $keepAlive,
            'client_id' => $clientId,
        ];

        if ($propertiesTotalLength) {
            $package['properties'] = $properties;
        } else {
            unset($package['properties']);
        }

        if ($willFlag) {
            if ($willPropertiesTotalLength) {
                $package['will']['properties'] = $willProperties;
            }
            $package['will'] += [
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
        $sessionPresent = ord($remaining[0]) & 0x01;
        $code = ord($remaining[1]);
        $remaining = substr($remaining, 2);

        $package = [
            'type' => Types::CONNACK,
            'session_present' => $sessionPresent,
            'code' => $code,
        ];

        $propertiesTotalLength = UnPackTool::byte($remaining);
        if ($propertiesTotalLength) {
            $package['properties'] = UnPackProperty::connAck($propertiesTotalLength, $remaining);
        }

        return $package;
    }

    public static function publish(int $dup, int $qos, int $retain, string $remaining): array
    {
        $topic = UnPackTool::string($remaining);

        $package = [
            'type' => Types::PUBLISH,
            'dup' => $dup,
            'qos' => $qos,
            'retain' => $retain,
            'topic' => $topic,
        ];

        if ($qos) {
            $package['message_id'] = UnPackTool::shortInt($remaining);
        }

        $propertiesTotalLength = UnPackTool::byte($remaining);
        if ($propertiesTotalLength) {
            $package['properties'] = UnPackProperty::publish($propertiesTotalLength, $remaining);
        }

        $package['message'] = $remaining;

        return $package;
    }

    public static function subscribe(string $remaining): array
    {
        $messageId = UnPackTool::shortInt($remaining);

        $package = [
            'type' => Types::SUBSCRIBE,
            'message_id' => $messageId,
        ];

        $propertiesTotalLength = UnPackTool::byte($remaining);
        if ($propertiesTotalLength) {
            $package['properties'] = UnPackProperty::subscribe($propertiesTotalLength, $remaining);
        }

        $topics = [];
        while ($remaining) {
            $topic = UnPackTool::string($remaining);
            $topics[$topic] = [
                'qos' => ord($remaining[0]) & 0x3,
                'no_local' => (bool) (ord($remaining[0]) >> 2 & 0x1),
                'retain_as_published' => (bool) (ord($remaining[0]) >> 3 & 0x1),
                'retain_handling' => ord($remaining[0]) >> 4,
            ];
            $remaining = substr($remaining, 1);
        }

        $package['topics'] = $topics;

        return $package;
    }

    public static function subAck(string $remaining): array
    {
        $messageId = UnPackTool::shortInt($remaining);

        $package = [
            'type' => Types::SUBACK,
            'message_id' => $messageId,
        ];

        $propertiesTotalLength = UnPackTool::byte($remaining);
        if ($propertiesTotalLength) {
            $package['properties'] = UnPackProperty::pubAndSub($propertiesTotalLength, $remaining);
        }

        $codes = unpack('C*', $remaining);
        $package['codes'] = array_values($codes);

        return $package;
    }

    public static function unSubscribe(string $remaining): array
    {
        $messageId = UnPackTool::shortInt($remaining);

        $package = [
            'type' => Types::UNSUBSCRIBE,
            'message_id' => $messageId,
        ];

        $propertiesTotalLength = UnPackTool::byte($remaining);
        if ($propertiesTotalLength) {
            $package['properties'] = UnPackProperty::unSubscribe($propertiesTotalLength, $remaining);
        }
        $topics = [];
        while ($remaining) {
            $topic = UnPackTool::string($remaining);
            $topics[] = $topic;
        }

        $package['topics'] = $topics;

        return $package;
    }

    public static function unSubAck(string $remaining): array
    {
        $messageId = UnPackTool::shortInt($remaining);

        $package = [
            'type' => Types::UNSUBACK,
            'message_id' => $messageId,
        ];

        $propertiesTotalLength = UnPackTool::byte($remaining);
        if ($propertiesTotalLength) {
            $package['properties'] = UnPackProperty::pubAndSub($propertiesTotalLength, $remaining);
        }

        $codes = unpack('C*', $remaining);
        $package['codes'] = array_values($codes);

        return $package;
    }

    public static function disconnect(string $remaining): array
    {
        if (isset($remaining[0])) {
            $code = UnPackTool::byte($remaining);
        } else {
            $code = ReasonCode::NORMAL_DISCONNECTION;
        }
        $package = [
            'type' => Types::DISCONNECT,
            'code' => $code,
        ];

        $propertiesTotalLength = 0;
        if (isset($remaining[0])) {
            $propertiesTotalLength = UnPackTool::byte($remaining);
        }

        if ($propertiesTotalLength) {
            $package['properties'] = UnPackProperty::disconnect($propertiesTotalLength, $remaining);
        }

        return $package;
    }

    public static function getReasonCode(int $type, string $remaining): array
    {
        $messageId = UnPackTool::shortInt($remaining);

        if (isset($remaining[0])) {
            $code = UnPackTool::byte($remaining);
        } else {
            $code = ReasonCode::SUCCESS;
        }

        $package = [
            'type' => $type,
            'message_id' => $messageId,
            'code' => $code,
        ];

        $propertiesTotalLength = 0;
        if (isset($remaining[0])) {
            $propertiesTotalLength = UnPackTool::byte($remaining);
        }

        if ($propertiesTotalLength) {
            $package['properties'] = UnPackProperty::pubAndSub($propertiesTotalLength, $remaining);
        }

        return $package;
    }

    public static function auth(string $remaining): array
    {
        if (isset($remaining[0])) {
            $code = UnPackTool::byte($remaining);
        } else {
            $code = ReasonCode::SUCCESS;
        }
        $package = [
            'type' => Types::AUTH,
            'code' => $code,
        ];

        $propertiesTotalLength = 0;
        if (isset($remaining[0])) {
            $propertiesTotalLength = UnPackTool::byte($remaining);
        }

        if ($propertiesTotalLength) {
            $package['properties'] = UnPackProperty::auth($propertiesTotalLength, $remaining);
        }

        return $package;
    }
}
