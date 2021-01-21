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

use Simps\MQTT\Exception\ProtocolException;
use Simps\MQTT\Protocol\ProtocolInterface;
use Simps\MQTT\Protocol\Types;
use Simps\MQTT\Tools\PackTool;

class Pack
{
    public static function connect(array $array): string
    {
        $body = PackTool::string($array['protocol_name']) . chr($array['protocol_level']);
        $connectFlags = 0;
        if (!empty($array['clean_session'])) {
            $connectFlags |= 1 << 1;
        }
        if (!empty($array['will'])) {
            $connectFlags |= 1 << 2;
            if (isset($array['will']['qos'])) {
                if ($array['will']['qos'] > ProtocolInterface::MQTT_QOS_2) {
                    throw new ProtocolException("QoS {$array['will']['qos']} not supported");
                }
                $connectFlags |= $array['will']['qos'] << 3;
            }
            if (!empty($array['will']['retain'])) {
                $connectFlags |= 1 << 5;
            }
        }
        if (!empty($array['password'])) {
            $connectFlags |= 1 << 6;
        }
        if (!empty($array['user_name'])) {
            $connectFlags |= 1 << 7;
        }
        $body .= chr($connectFlags);

        $keepAlive = !empty($array['keep_alive']) && (int) $array['keep_alive'] >= 0 ? (int) $array['keep_alive'] : 0;
        $body .= PackTool::shortInt($keepAlive);

        $body .= PackTool::string($array['client_id']);
        if (!empty($array['will'])) {
            $body .= PackTool::string($array['will']['topic']);
            $body .= PackTool::string($array['will']['message']);
        }
        if (!empty($array['user_name'])) {
            $body .= PackTool::string($array['user_name']);
        }
        if (!empty($array['password'])) {
            $body .= PackTool::string($array['password']);
        }
        $head = PackTool::packHeader(Types::CONNECT, strlen($body));

        return $head . $body;
    }

    public static function connAck(array $array): string
    {
        $body = !empty($array['session_present']) ? chr(1) : chr(0);
        $code = !empty($array['code']) ? $array['code'] : 0;
        $body .= chr($code);
        $head = PackTool::packHeader(Types::CONNACK, strlen($body));

        return $head . $body;
    }

    public static function publish(array $array): string
    {
        $body = PackTool::string($array['topic']);
        $qos = $array['qos'] ?? 0;
        if ($qos) {
            $body .= PackTool::shortInt($array['message_id']);
        }
        $body .= $array['message'];
        $dup = $array['dup'] ?? 0;
        $retain = $array['retain'] ?? 0;
        $head = PackTool::packHeader(Types::PUBLISH, strlen($body), $dup, $qos, $retain);

        return $head . $body;
    }

    public static function subscribe(array $array): string
    {
        $id = $array['message_id'];
        $body = PackTool::shortInt($id);
        foreach ($array['topics'] as $topic => $qos) {
            $body .= PackTool::string($topic);
            $body .= chr($qos);
        }
        $head = PackTool::packHeader(Types::SUBSCRIBE, strlen($body), 0, 1);

        return $head . $body;
    }

    public static function subAck(array $array): string
    {
        $payload = $array['payload'];
        $body = PackTool::shortInt($array['message_id']) . call_user_func_array(
            'pack',
            array_merge(['C*'], $payload)
        );
        $head = PackTool::packHeader(Types::SUBACK, strlen($body));

        return $head . $body;
    }

    public static function unSubscribe(array $array): string
    {
        $body = PackTool::shortInt($array['message_id']);
        foreach ($array['topics'] as $topic) {
            $body .= PackTool::string($topic);
        }
        $head = PackTool::packHeader(Types::UNSUBSCRIBE, strlen($body), 0, 1);

        return $head . $body;
    }
}
