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

namespace Simps\MQTT;

use Simps\MQTT\Exception\InvalidArgumentException;
use Simps\MQTT\Exception\LengthException;
use Simps\MQTT\Exception\RuntimeException;
use Simps\MQTT\Packet\Pack;
use Simps\MQTT\Packet\UnPack;
use Throwable;
use TypeError;

class Protocol
{
    public static function pack(array $array)
    {
        try {
            $type = $array['type'];
            switch ($type) {
                case Types::CONNECT:
                    $package = Pack::connect($array);
                    break;
                case Types::CONNACK:
                    $package = Pack::connAck($array);
                    break;
                case Types::PUBLISH:
                    $package = Pack::publish($array);
                    break;
                case Types::PUBACK:
                case Types::PUBREC:
                case Types::PUBREL:
                case Types::PUBCOMP:
                case Types::UNSUBACK:
                    $body = pack('n', $array['message_id']);
                    if ($type === Types::PUBREL) {
                        $head = Pack::packHeader($type, strlen($body), 0, 1);
                    } else {
                        $head = Pack::packHeader($type, strlen($body));
                    }
                    $package = $head . $body;
                    break;
                case Types::SUBSCRIBE:
                    $package = Pack::subscribe($array);
                    break;
                case Types::SUBACK:
                    $package = Pack::subAck($array);
                    break;
                case Types::UNSUBSCRIBE:
                    $package = Pack::unSubscribe($array);
                    break;
                case Types::PINGREQ:
                case Types::PINGRESP:
                case Types::DISCONNECT:
                    $package = Pack::packHeader($type, 0);
                    break;
                default:
                    throw new InvalidArgumentException('MQTT Type not exist');
            }
        } catch (TypeError $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode());
        } catch (Throwable $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode());
        }

        return $package;
    }

    public static function unpack(string $data)
    {
        try {
            $type = static::getType($data);
            $remaining = static::getRemaining($data);
            switch ($type) {
                case Types::CONNECT:
                    $package = UnPack::connect($remaining);
                    break;
                case Types::CONNACK:
                    $package = UnPack::connAck($remaining);
                    break;
                case Types::PUBLISH:
                    $dup = ord($data[0]) >> 3 & 0x1;
                    $qos = ord($data[0]) >> 1 & 0x3;
                    $retain = ord($data[0]) & 0x1;
                    $package = UnPack::publish($dup, $qos, $retain, $remaining);
                    break;
                case Types::PUBACK:
                case Types::PUBREC:
                case Types::PUBREL:
                case Types::PUBCOMP:
                case Types::UNSUBACK:
                    $package = ['type' => $type, 'message_id' => unpack('n', $remaining)[1]];
                    break;
                case Types::PINGREQ:
                case Types::PINGRESP:
                case Types::DISCONNECT:
                    $package = ['type' => $type];
                    break;
                case Types::SUBSCRIBE:
                    $package = UnPack::subscribe($remaining);
                    break;
                case Types::SUBACK:
                    $package = UnPack::subAck($remaining);
                    break;
                case Types::UNSUBSCRIBE:
                    $package = UnPack::unSubscribe($remaining);
                    break;
                default:
                    $package = [];
            }
        } catch (TypeError $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode());
        } catch (Throwable $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode());
        }

        return $package;
    }

    public static function getType(string $data)
    {
        return ord($data[0]) >> 4;
    }

    public static function getRemainingLength(string $data, &$headBytes)
    {
        $headBytes = $multiplier = 1;
        $value = 0;
        do {
            if (!isset($data[$headBytes])) {
                throw new LengthException('Malformed Remaining Length');
            }
            $digit = ord($data[$headBytes]);
            $value += ($digit & 127) * $multiplier;
            $multiplier *= 128;
            ++$headBytes;
        } while (($digit & 128) != 0);

        return $value;
    }

    public static function getRemaining(string $data)
    {
        $remainingLength = static::getRemainingLength($data, $headBytes);

        return substr($data, $headBytes, $remainingLength);
    }
}
