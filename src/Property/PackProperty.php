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

namespace Simps\MQTT\Property;

use Simps\MQTT\Hex\Property;
use Simps\MQTT\Tools\PackTool;

class PackProperty
{
    public static function connect(array $data): string
    {
        $length = 0;
        $tmpBody = '';
        $connect = array_flip(PacketMap::$connect);
        foreach ($data as $key => $item) {
            if (isset($connect[$key])) {
                $property = $connect[$key];
                $tmpBody .= chr($property);
                switch ($property) {
                    case Property::SESSION_EXPIRY_INTERVAL:
                        $length += 5;
                        $tmpBody .= PackTool::longInt($item);
                        break;
                    case Property::AUTHENTICATION_METHOD:
                    case Property::AUTHENTICATION_DATA:
                        $length += 3;
                        $length += strlen($item);
                        $tmpBody .= PackTool::string($item);
                        break;
                    case Property::REQUEST_PROBLEM_INFORMATION:
                    case Property::REQUEST_RESPONSE_INFORMATION:
                        $length += 2;
                        $tmpBody .= chr((int) $item);
                        break;
                    case Property::RECEIVE_MAXIMUM:
                    case Property::TOPIC_ALIAS_MAXIMUM:
                    case Property::MAXIMUM_PACKET_SIZE:
                        $length += 3;
                        $tmpBody .= PackTool::shortInt($item);
                        break;
                }
            } else {
                // Property::USER_PROPERTY
                $length += 5;
                $length += strlen((string) $key);
                $length += strlen((string) $item);
                $tmpBody .= chr($connect['user_property']);
                $tmpBody .= PackTool::stringPair((string) $key, (string) $item);
            }
        }

        return chr($length) . $tmpBody;
    }

    public static function willProperties(array $data): string
    {
        $length = 0;
        $tmpBody = '';
        $willProperties = array_flip(PacketMap::$willProperties);
        foreach ($data as $key => $item) {
            if (isset($willProperties[$key])) {
                $property = $willProperties[$key];
                $tmpBody .= chr($property);
                switch ($property) {
                    case Property::MESSAGE_EXPIRY_INTERVAL:
                    case Property::WILL_DELAY_INTERVAL:
                        $length += 5;
                        $tmpBody .= PackTool::longInt($item);
                        break;
                    case Property::CONTENT_TYPE:
                    case Property::RESPONSE_TOPIC:
                    case Property::CORRELATION_DATA:
                        $length += 3;
                        $length += strlen($item);
                        $tmpBody .= PackTool::string($item);
                        break;
                    case Property::PAYLOAD_FORMAT_INDICATOR:
                        $length += 2;
                        $tmpBody .= chr((int) $item);
                        break;
                }
            } else {
                // Property::USER_PROPERTY
                $length += 5;
                $length += strlen((string) $key);
                $length += strlen((string) $item);
                $tmpBody .= chr($willProperties['user_property']);
                $tmpBody .= PackTool::stringPair((string) $key, (string) $item);
            }
        }

        return chr($length) . $tmpBody;
    }

    public static function connAck(array $data): string
    {
        $length = 0;
        $tmpBody = '';
        $connAck = array_flip(PacketMap::$connAck);
        foreach ($data as $key => $item) {
            if (isset($connAck[$key])) {
                $property = $connAck[$key];
                $tmpBody .= chr($property);
                switch ($property) {
                    case Property::SESSION_EXPIRY_INTERVAL:
                    case Property::MAXIMUM_PACKET_SIZE:
                        $length += 5;
                        $tmpBody .= PackTool::longInt($item);
                        break;
                    case Property::SERVER_KEEP_ALIVE:
                    case Property::RECEIVE_MAXIMUM:
                    case Property::TOPIC_ALIAS_MAXIMUM:
                        $length += 3;
                        $tmpBody .= PackTool::shortInt($item);
                        break;
                    case Property::ASSIGNED_CLIENT_IDENTIFIER:
                    case Property::AUTHENTICATION_METHOD:
                    case Property::AUTHENTICATION_DATA:
                    case Property::RESPONSE_INFORMATION:
                    case Property::SERVER_REFERENCE:
                    case Property::REASON_STRING:
                        $length += 3;
                        $length += strlen($item);
                        $tmpBody .= PackTool::string($item);
                        break;
                    case Property::MAXIMUM_QOS:
                    case Property::RETAIN_AVAILABLE:
                    case Property::WILDCARD_SUBSCRIPTION_AVAILABLE:
                    case Property::SUBSCRIPTION_IDENTIFIER_AVAILABLE:
                    case Property::SHARED_SUBSCRIPTION_AVAILABLE:
                        $length += 2;
                        $tmpBody .= chr((int) $item);
                        break;
                }
            } else {
                // Property::USER_PROPERTY
                $length += 5;
                $length += strlen((string) $key);
                $length += strlen((string) $item);
                $tmpBody .= chr($connAck['user_property']);
                $tmpBody .= PackTool::stringPair((string) $key, (string) $item);
            }
        }

        return chr($length) . $tmpBody;
    }

    public static function publish(array $data): string
    {
        $length = 0;
        $tmpBody = '';
        $publish = array_flip(PacketMap::$publish);
        foreach ($data as $key => $item) {
            if (isset($publish[$key])) {
                $property = $publish[$key];
                $tmpBody .= chr($property);
                switch ($property) {
                    case Property::MESSAGE_EXPIRY_INTERVAL:
                        $length += 5;
                        $tmpBody .= PackTool::longInt($item);
                        break;
                    case Property::TOPIC_ALIAS:
                        $length += 3;
                        $tmpBody .= PackTool::shortInt($item);
                        break;
                    case Property::CONTENT_TYPE:
                    case Property::RESPONSE_TOPIC:
                    case Property::CORRELATION_DATA:
                        $length += 3;
                        $length += strlen($item);
                        $tmpBody .= PackTool::string($item);
                        break;
                    case Property::PAYLOAD_FORMAT_INDICATOR:
                        $length += 2;
                        $tmpBody .= chr((int) $item);
                        break;
                    case Property::SUBSCRIPTION_IDENTIFIER:
                        $length += 1;
                        $value = PackTool::varInt((int) $item);
                        $length += strlen($value);
                        $tmpBody .= $value;
                        break;
                }
            } else {
                // Property::USER_PROPERTY
                $length += 5;
                $length += strlen((string) $key);
                $length += strlen((string) $item);
                $tmpBody .= chr($publish['user_property']);
                $tmpBody .= PackTool::stringPair((string) $key, (string) $item);
            }
        }

        return chr($length) . $tmpBody;
    }

    public static function pubAndSub(array $data): string
    {
        $length = 0;
        $tmpBody = '';
        $pubAndSub = array_flip(PacketMap::$pubAndSub);
        foreach ($data as $key => $item) {
            if (isset($pubAndSub[$key])) {
                $property = $pubAndSub[$key];
                $tmpBody .= chr($property);
                switch ($property) {
                    case Property::REASON_STRING:
                        $length += 3;
                        $length += strlen($item);
                        $tmpBody .= PackTool::string($item);
                        break;
                }
            } else {
                // Property::USER_PROPERTY
                $length += 5;
                $length += strlen((string) $key);
                $length += strlen((string) $item);
                $tmpBody .= chr($pubAndSub['user_property']);
                $tmpBody .= PackTool::stringPair((string) $key, (string) $item);
            }
        }

        return chr($length) . $tmpBody;
    }

    public static function subscribe(array $data): string
    {
        $length = 0;
        $tmpBody = '';
        $subscribe = array_flip(PacketMap::$subscribe);
        foreach ($data as $key => $item) {
            if (isset($subscribe[$key])) {
                $property = $subscribe[$key];
                $tmpBody .= chr($property);
                switch ($property) {
                    case Property::SUBSCRIPTION_IDENTIFIER:
                        $length += 1;
                        $value = PackTool::varInt((int) $item);
                        $length += strlen($value);
                        $tmpBody .= $value;
                        break;
                }
            } else {
                // Property::USER_PROPERTY
                $length += 5;
                $length += strlen((string) $key);
                $length += strlen((string) $item);
                $tmpBody .= chr($subscribe['user_property']);
                $tmpBody .= PackTool::stringPair((string) $key, (string) $item);
            }
        }

        return chr($length) . $tmpBody;
    }

    public static function unSubscribe(array $data): string
    {
        $length = 0;
        $tmpBody = '';
        $unSubscribe = array_flip(PacketMap::$unSubscribe);
        foreach ($data as $key => $item) {
            // Property::USER_PROPERTY
            $length += 5;
            $length += strlen((string) $key);
            $length += strlen((string) $item);
            $tmpBody .= chr($unSubscribe['user_property']);
            $tmpBody .= PackTool::stringPair((string) $key, (string) $item);
        }

        return chr($length) . $tmpBody;
    }

    public static function disConnect(array $data): string
    {
        $length = 0;
        $tmpBody = '';
        $disConnect = array_flip(PacketMap::$disConnect);
        foreach ($data as $key => $item) {
            if (isset($disConnect[$key])) {
                $property = $disConnect[$key];
                $tmpBody .= chr($property);
                switch ($property) {
                    case Property::SESSION_EXPIRY_INTERVAL:
                        $length += 5;
                        $tmpBody .= PackTool::longInt($item);
                        break;
                    case Property::SERVER_REFERENCE:
                    case Property::REASON_STRING:
                        $length += 3;
                        $length += strlen($item);
                        $tmpBody .= PackTool::string($item);
                        break;
                }
            } else {
                // Property::USER_PROPERTY
                $length += 5;
                $length += strlen((string) $key);
                $length += strlen((string) $item);
                $tmpBody .= chr($disConnect['user_property']);
                $tmpBody .= PackTool::stringPair((string) $key, (string) $item);
            }
        }

        return chr($length) . $tmpBody;
    }

    public static function auth(array $data): string
    {
        $length = 0;
        $tmpBody = '';
        $auth = array_flip(PacketMap::$auth);
        foreach ($data as $key => $item) {
            if (isset($auth[$key])) {
                $property = $auth[$key];
                $tmpBody .= chr($property);
                switch ($property) {
                    case Property::AUTHENTICATION_METHOD:
                    case Property::AUTHENTICATION_DATA:
                    case Property::REASON_STRING:
                        $length += 3;
                        $length += strlen($item);
                        $tmpBody .= PackTool::string($item);
                        break;
                }
            } else {
                // Property::USER_PROPERTY
                $length += 5;
                $length += strlen((string) $key);
                $length += strlen((string) $item);
                $tmpBody .= chr($auth['user_property']);
                $tmpBody .= PackTool::stringPair((string) $key, (string) $item);
            }
        }

        return chr($length) . $tmpBody;
    }
}
