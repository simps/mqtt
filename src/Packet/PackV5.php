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

use Simps\MQTT\Hex\ReasonCode;
use Simps\MQTT\Property\PackProperty;
use Simps\MQTT\Protocol\Types;
use Simps\MQTT\Tools\PackTool;

class PackV5
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

        // CONNECT Properties
        $body .= PackProperty::connect($array['properties'] ?? []);

        $body .= PackTool::string($array['client_id']);
        if (!empty($array['will'])) {
            // Will Properties
            $body .= PackProperty::willProperties($array['will']['properties'] ?? []);

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

        // CONNACK Properties
        $body .= PackProperty::connAck($array['properties'] ?? []);

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
        $dup = $array['dup'] ?? 0;
        $retain = $array['retain'] ?? 0;

        // PUBLISH Properties
        $body .= PackProperty::publish($array['properties'] ?? []);

        $body .= $array['message'];
        $head = PackTool::packHeader(Types::PUBLISH, strlen($body), $dup, $qos, $retain);

        return $head . $body;
    }

    public static function subscribe(array $array): string
    {
        $body = PackTool::shortInt($array['message_id']);

        // SUBSCRIBE Properties
        $body .= PackProperty::subscribe($array['properties'] ?? []);

        foreach ($array['topics'] as $topic => $options) {
            $body .= PackTool::string($topic);

            $subscribeOptions = 0;
            if (isset($options['qos'])) {
                $subscribeOptions |= (int) $options['qos'];
            }
            if (isset($options['no_local'])) {
                $subscribeOptions |= (int) $options['no_local'] << 2;
            }
            if (isset($options['retain_as_published'])) {
                $subscribeOptions |= (int) $options['retain_as_published'] << 3;
            }
            if (isset($options['retain_handling'])) {
                $subscribeOptions |= (int) $options['retain_handling'] << 4;
            }
            $body .= chr($subscribeOptions);
        }

        $head = PackTool::packHeader(Types::SUBSCRIBE, strlen($body), 0, 1);

        return $head . $body;
    }

    public static function subAck(array $array): string
    {
        $body = PackTool::shortInt($array['message_id']);

        // SUBACK Properties
        $body .= PackProperty::pubAndSub($array['properties'] ?? []);

        $body .= call_user_func_array(
            'pack',
            array_merge(['C*'], $array['payload'])
        );
        $head = PackTool::packHeader(Types::SUBACK, strlen($body));

        return $head . $body;
    }

    public static function unSubscribe(array $array): string
    {
        $body = PackTool::shortInt($array['message_id']);

        // UNSUBSCRIBE Properties
        $body .= PackProperty::unSubscribe($array['properties'] ?? []);

        foreach ($array['topics'] as $topic) {
            $body .= PackTool::string($topic);
        }
        $head = PackTool::packHeader(Types::UNSUBSCRIBE, strlen($body), 0, 1);

        return $head . $body;
    }

    public static function unSubAck(array $array): string
    {
        $body = PackTool::shortInt($array['message_id']);

        // UNSUBACK Properties
        $body .= PackProperty::pubAndSub($array['properties'] ?? []);

        $code = !empty($array['code']) ? $array['code'] : ReasonCode::SUCCESS;
        $body .= chr($code);
        $head = PackTool::packHeader(Types::UNSUBACK, strlen($body));

        return $head . $body;
    }

    public static function disconnect(array $array): string
    {
        $code = !empty($array['code']) ? $array['code'] : ReasonCode::NORMAL_DISCONNECTION;
        $body = chr($code);

        // DISCONNECT Properties
        $body .= PackProperty::disConnect($array['properties'] ?? []);

        $head = PackTool::packHeader(Types::DISCONNECT, strlen($body));

        return $head . $body;
    }

    public static function genReasonPhrase(array $array): string
    {
        $body = PackTool::shortInt($array['message_id']);
        $code = !empty($array['code']) ? $array['code'] : ReasonCode::SUCCESS;
        $body .= chr($code);

        // pubAck, pubRec, pubRel, pubComp Properties
        $body .= PackProperty::pubAndSub($array['properties'] ?? []);

        if ($array['type'] === Types::PUBREL) {
            $head = PackTool::packHeader($array['type'], strlen($body), 0, 1);
        } else {
            $head = PackTool::packHeader($array['type'], strlen($body));
        }

        return $head . $body;
    }

    public static function auth(array $array): string
    {
        $code = !empty($array['code']) ? $array['code'] : ReasonCode::SUCCESS;
        $body = chr($code);

        // AUTH Properties
        $body .= PackProperty::auth($array['properties'] ?? []);

        $head = PackTool::packHeader(Types::AUTH, strlen($body));

        return $head . $body;
    }
}
