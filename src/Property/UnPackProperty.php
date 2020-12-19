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

use Simps\MQTT\Exception\InvalidArgumentException;
use Simps\MQTT\Hex\Property;
use Simps\MQTT\Tools\UnPackTool;

class UnPackProperty
{
    public static function connect(int $length, &$remaining)
    {
        $properties = [];
        do {
            $property = ord($remaining[0]);
            if (isset(PacketMap::$connect[$property])) {
                $key = PacketMap::$connect[$property];
                $remaining = substr($remaining, 1);
                switch ($property) {
                    case Property::SESSION_EXPIRY_INTERVAL:
                        $properties[$key] = UnPackTool::longInt($remaining);
                        $length -= 5;
                        break;
                    case Property::AUTHENTICATION_METHOD:
                        $properties[$key] = UnPackTool::string($remaining);
                        $length -= 1;
                        $length -= strlen($properties[$key]);
                        break;
                    case Property::REQUEST_PROBLEM_INFORMATION:
                    case Property::REQUEST_RESPONSE_INFORMATION:
                        $properties[$key] = UnPackTool::byte($remaining);
                        $length -= 2;
                        break;
                    case Property::RECEIVE_MAXIMUM:
                    case Property::TOPIC_ALIAS_MAXIMUM:
                    case Property::MAXIMUM_PACKET_SIZE:
                        $properties[$key] = UnPackTool::shortInt($remaining);
                        $length -= 3;
                        break;
                    // TODO
                    case Property::AUTHENTICATION_DATA:
                    case Property::USER_PROPERTY:
                        trigger_error("{$properties[$key]} is not yet supported, please go to https://github.com/simps/mqtt/issues to submit an issue", E_USER_WARNING);
                        $properties[$key] = '';
                        break;
                }
            } else {
                $errType = dechex($property);
                throw new InvalidArgumentException("Property [0x{$errType}] not exist");
            }
        } while ($length > 0);

        return $properties;
    }

    public static function willProperties(int $length, &$remaining)
    {
        $properties = [];
        do {
            $property = ord($remaining[0]);
            if (isset(PacketMap::$willProperties[$property])) {
                $key = PacketMap::$willProperties[$property];
                $remaining = substr($remaining, 1);
                switch ($property) {
                    case Property::MESSAGE_EXPIRY_INTERVAL:
                    case Property::WILL_DELAY_INTERVAL:
                        $properties[$key] = UnPackTool::longInt($remaining);
                        $length -= 5;
                        break;
                    case Property::CONTENT_TYPE:
                    case Property::RESPONSE_TOPIC:
                        $properties[$key] = UnPackTool::string($remaining);
                        $length -= 1;
                        $length -= strlen($properties[$key]);
                        break;
                    case Property::PAYLOAD_FORMAT_INDICATOR:
                        $properties[$key] = UnPackTool::byte($remaining);
                        $length -= 2;
                        break;
                    // TODO
                    case Property::CORRELATION_DATA:
                    case Property::USER_PROPERTY:
                        trigger_error("{$properties[$key]} is not yet supported, please go to https://github.com/simps/mqtt/issues to submit an issue", E_USER_WARNING);
                        $properties[$key] = '';
                        break;
                }
            } else {
                $errType = dechex($property);
                throw new InvalidArgumentException("Property [0x{$errType}] not exist");
            }
        } while ($length > 0);

        return $properties;
    }

    public static function connAck(int $length, &$remaining)
    {
        $properties = [];
        do {
            $property = ord($remaining[0]);
            if (isset(PacketMap::$connAck[$property])) {
                $key = PacketMap::$connAck[$property];
                $remaining = substr($remaining, 1);
                switch ($property) {
                    case Property::SESSION_EXPIRY_INTERVAL:
                    case Property::MAXIMUM_PACKET_SIZE:
                        $properties[$key] = UnPackTool::longInt($remaining);
                        $length -= 5;
                        break;
                    case Property::SERVER_KEEP_ALIVE:
                    case Property::RECEIVE_MAXIMUM:
                    case Property::TOPIC_ALIAS_MAXIMUM:
                        $properties[$key] = UnPackTool::shortInt($remaining);
                        $length -= 3;
                        break;
                    case Property::ASSIGNED_CLIENT_IDENTIFIER:
                    case Property::AUTHENTICATION_METHOD:
                    case Property::RESPONSE_INFORMATION:
                    case Property::SERVER_REFERENCE:
                    case Property::REASON_STRING:
                        $properties[$key] = UnPackTool::string($remaining);
                        $length -= 1;
                        $length -= strlen($properties[$key]);
                        break;
                    case Property::MAXIMUM_QOS:
                    case Property::RETAIN_AVAILABLE:
                    case Property::WILDCARD_SUBSCRIPTION_AVAILABLE:
                    case Property::SUBSCRIPTION_IDENTIFIER_AVAILABLE:
                    case Property::SHARED_SUBSCRIPTION_AVAILABLE:
                        $properties[$key] = UnPackTool::byte($remaining);
                        $length -= 2;
                        break;
                    // TODO
                    case Property::AUTHENTICATION_DATA:
                    case Property::USER_PROPERTY:
                        trigger_error("{$properties[$key]} is not yet supported, please go to https://github.com/simps/mqtt/issues to submit an issue", E_USER_WARNING);
                        $properties[$key] = '';
                        break;
                }
            } else {
                $errType = dechex($property);
                throw new InvalidArgumentException("Property [0x{$errType}] not exist");
            }
        } while ($length > 0);

        return $properties;
    }

    public static function publish(int $length, &$remaining)
    {
        $properties = [];
        do {
            $property = ord($remaining[0]);
            if (isset(PacketMap::$publish[$property])) {
                $key = PacketMap::$publish[$property];
                $remaining = substr($remaining, 1);
                switch ($property) {
                    case Property::MESSAGE_EXPIRY_INTERVAL:
                        $properties[$key] = UnPackTool::longInt($remaining);
                        $length -= 5;
                        break;
                    case Property::TOPIC_ALIAS:
                        $properties[$key] = UnPackTool::shortInt($remaining);
                        $length -= 3;
                        break;
                    case Property::CONTENT_TYPE:
                    case Property::RESPONSE_TOPIC:
                        $properties[$key] = UnPackTool::string($remaining);
                        $length -= 1;
                        $length -= strlen($properties[$key]);
                        break;
                    case Property::PAYLOAD_FORMAT_INDICATOR:
                        $properties[$key] = UnPackTool::byte($remaining);
                        $length -= 2;
                        break;
                    // TODO
                    case Property::SUBSCRIPTION_IDENTIFIER:
                    case Property::CORRELATION_DATA:
                    case Property::USER_PROPERTY:
                        trigger_error("{$properties[$key]} is not yet supported, please go to https://github.com/simps/mqtt/issues to submit an issue", E_USER_WARNING);
                        $properties[$key] = '';
                        break;
                }
            } else {
                $errType = dechex($property);
                throw new InvalidArgumentException("Property [0x{$errType}] not exist");
            }
        } while ($length > 0);

        return $properties;
    }

    public static function pubAndSub(int $length, &$remaining)
    {
        $properties = [];
        do {
            $property = ord($remaining[0]);
            if (isset(PacketMap::$pubAndSub[$property])) {
                $key = PacketMap::$pubAndSub[$property];
                $remaining = substr($remaining, 1);
                switch ($property) {
                    case Property::REASON_STRING:
                        $properties[$key] = UnPackTool::string($remaining);
                        $length -= 1;
                        $length -= strlen($properties[$key]);
                        break;
                    // TODO
                    case Property::USER_PROPERTY:
                        trigger_error("{$properties[$key]} is not yet supported, please go to https://github.com/simps/mqtt/issues to submit an issue", E_USER_WARNING);
                        $properties[$key] = '';
                        break;
                }
            } else {
                $errType = dechex($property);
                throw new InvalidArgumentException("Property [0x{$errType}] not exist");
            }
        } while ($length > 0);

        return $properties;
    }

    public static function subscribe(int $length, &$remaining)
    {
        $properties = [];
        do {
            $property = ord($remaining[0]);
            if (isset(PacketMap::$subscribe[$property])) {
                $key = PacketMap::$subscribe[$property];
                $remaining = substr($remaining, 1);
                switch ($property) {
                    // TODO
                    case Property::SUBSCRIPTION_IDENTIFIER:
                    case Property::USER_PROPERTY:
                        trigger_error("{$properties[$key]} is not yet supported, please go to https://github.com/simps/mqtt/issues to submit an issue", E_USER_WARNING);
                        $properties[$key] = '';
                        break;
                }
            } else {
                $errType = dechex($property);
                throw new InvalidArgumentException("Property [0x{$errType}] not exist");
            }
        } while ($length > 0);

        return $properties;
    }

    public static function unSubscribe(int $length, &$remaining)
    {
        $properties = [];
        do {
            $property = ord($remaining[0]);
            if (isset(PacketMap::$unSubscribe[$property])) {
                $key = PacketMap::$unSubscribe[$property];
                $remaining = substr($remaining, 1);
                switch ($property) {
                    // TODO
                    case Property::USER_PROPERTY:
                        trigger_error("{$properties[$key]} is not yet supported, please go to https://github.com/simps/mqtt/issues to submit an issue", E_USER_WARNING);
                        $properties[$key] = '';
                        break;
                }
            } else {
                $errType = dechex($property);
                throw new InvalidArgumentException("Property [0x{$errType}] not exist");
            }
        } while ($length > 0);

        return $properties;
    }

    public static function disConnect(int $length, &$remaining)
    {
        $properties = [];
        do {
            $property = ord($remaining[0]);
            if (isset(PacketMap::$disConnect[$property])) {
                $key = PacketMap::$disConnect[$property];
                $remaining = substr($remaining, 1);
                switch ($property) {
                    case Property::SESSION_EXPIRY_INTERVAL:
                        $properties[$key] = UnPackTool::longInt($remaining);
                        $length -= 5;
                        break;
                    case Property::SERVER_REFERENCE:
                    case Property::REASON_STRING:
                        $properties[$key] = UnPackTool::string($remaining);
                        $length -= 1;
                        $length -= strlen($properties[$key]);
                        break;
                    // TODO
                    case Property::USER_PROPERTY:
                        trigger_error("{$properties[$key]} is not yet supported, please go to https://github.com/simps/mqtt/issues to submit an issue", E_USER_WARNING);
                        $properties[$key] = '';
                        break;
                }
            } else {
                $errType = dechex($property);
                throw new InvalidArgumentException("Property [0x{$errType}] not exist");
            }
        } while ($length > 0);

        return $properties;
    }
}
