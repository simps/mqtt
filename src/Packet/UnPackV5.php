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

use Simps\MQTT\Exception\LengthException;
use Simps\MQTT\Types;

class UnPackV5
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
        $keepAlive = static::shortInt($remaining);
        $propertiesTotalLength = ord($remaining[0]);
        $remaining = substr($remaining, 1);
        if ($propertiesTotalLength) {
            $sessionExpiryIntervalFlag = ord($remaining[0]) & ~0x11;
            if ($sessionExpiryIntervalFlag === 0) {
                $remaining = substr($remaining, 1);
                $sessionExpiryInterval = static::longInt($remaining);
            }
            $receiveMaximumFlag = ord($remaining[0]) & ~0x21;
            if ($receiveMaximumFlag === 0) {
                $remaining = substr($remaining, 1);
                $receiveMaximum = static::shortInt($remaining);
            }
            $receiveMaximumFlag = ord($remaining[0]) & ~0x22;
            if ($receiveMaximumFlag === 0) {
                $remaining = substr($remaining, 1);
                $topicAliasMaximum = static::shortInt($remaining);
            }
        }
        $clientId = static::string($remaining);
        if ($willFlag) {
            $willPropertiesTotalLength = ord($remaining[0]);
            $remaining = substr($remaining, 1);
            if ($willPropertiesTotalLength) {
                $willDelayIntervalFlag = ord($remaining[0]) & ~0x18;
                if ($willDelayIntervalFlag === 0) {
                    $remaining = substr($remaining, 1);
                    $willDelayInterval = static::longInt($remaining);
                }
                $messageExpiryIntervalFlag = ord($remaining[0]) & ~0x02;
                if ($messageExpiryIntervalFlag === 0) {
                    $remaining = substr($remaining, 1);
                    $messageExpiryInterval = static::longInt($remaining);
                }
                $contentTypeFlag = ord($remaining[0]) & ~0x03;
                if ($contentTypeFlag === 0) {
                    $remaining = substr($remaining, 1);
                    $contentType = static::string($remaining);
                }
                $payloadFormatIndicatorFlag = ord($remaining[0]) & ~0x01;
                if ($payloadFormatIndicatorFlag === 0) {
                    $payloadFormatIndicator = ord($remaining[1]);
                    $remaining = substr($remaining, 2);
                }
            }
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
        ];

        if ($propertiesTotalLength) {
            if ($sessionExpiryIntervalFlag === 0) {
                $package['session_expiry_interval'] = $sessionExpiryInterval;
            }
            if ($receiveMaximumFlag === 0) {
                $package['receive_maximum'] = $receiveMaximum;
            }
            if ($receiveMaximumFlag === 0) {
                $package['topic_alias_aximum'] = $topicAliasMaximum;
            }
        }

        $package['client_id'] = $clientId;

        if ($willFlag) {
            if ($willPropertiesTotalLength) {
                if ($willDelayIntervalFlag === 0) {
                    $package['will']['will_delay_interval'] = $willDelayInterval;
                }
                if ($messageExpiryIntervalFlag === 0) {
                    $package['will']['message_expiry_interval'] = $messageExpiryInterval;
                }
                if ($contentTypeFlag === 0) {
                    $package['will']['content_type'] = $contentType;
                }
                if ($payloadFormatIndicatorFlag === 0) {
                    $package['will']['payload_format_indicator'] = $payloadFormatIndicator;
                }
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

    private static function string(&$remaining)
    {
        $length = unpack('n', $remaining)[1];
        if ($length + 2 > strlen($remaining)) {
            throw new LengthException("unpack remaining length error, get {$length}");
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

    private static function longInt(&$remaining)
    {
        $tmp = unpack('N', $remaining);
        $remaining = substr($remaining, 4);

        return $tmp[1];
    }
}
