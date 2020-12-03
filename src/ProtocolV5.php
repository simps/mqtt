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

namespace Simps\MQTT;

use Simps\MQTT\Exception\InvalidArgumentException;
use Simps\MQTT\Exception\LengthException;
use Simps\MQTT\Exception\RuntimeException;
use Simps\MQTT\Packet\PackV5;
use Simps\MQTT\Packet\UnPackV5;
use Throwable;
use TypeError;

class ProtocolV5
{
    public static function pack(array $array)
    {
        try {
            $type = $array['type'];
            switch ($type) {
                case Types::CONNECT:
                    $package = PackV5::connect($array);
                    break;
                case Types::CONNACK:
                    $package = PackV5::connAck($array);
                    break;
                case Types::PUBLISH:
                    $package = PackV5::publish($array);
                    break;
                case Types::PUBACK:
                case Types::PUBREC:
                case Types::PUBREL:
                case Types::PUBCOMP:
                case Types::UNSUBACK:
                    $body = pack('n', $array['message_id']);
                    if ($type === Types::PUBREL) {
                        $head = PackV5::packHeader($type, strlen($body), 0, 1);
                    } else {
                        $head = PackV5::packHeader($type, strlen($body));
                    }
                    $package = $head . $body;
                    break;
                case Types::SUBSCRIBE:
                    $package = PackV5::subscribe($array);
                    break;
                case Types::SUBACK:
                    $package = PackV5::subAck($array);
                    break;
                case Types::UNSUBSCRIBE:
                    $package = PackV5::unSubscribe($array);
                    break;
                case Types::PINGREQ:
                case Types::PINGRESP:
                case Types::DISCONNECT:
                    $package = PackV5::packHeader($type, 0);
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
                    $package = UnPackV5::connect($remaining);
                    break;
                case Types::CONNACK:
                    $package = UnPackV5::connAck($remaining);
                    break;
                case Types::PUBLISH:
                    $dup = ord($data[0]) >> 3 & 0x1;
                    $qos = ord($data[0]) >> 1 & 0x3;
                    $retain = ord($data[0]) & 0x1;
                    $package = UnPackV5::publish($dup, $qos, $retain, $remaining);
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
                    $package = UnPackV5::subscribe($remaining);
                    break;
                case Types::SUBACK:
                    $package = UnPackV5::subAck($remaining);
                    break;
                case Types::UNSUBSCRIBE:
                    $package = UnPackV5::unSubscribe($remaining);
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

    public static function printf(string $data)
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
}
