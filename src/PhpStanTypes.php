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
namespace Simps\MQTT;

/**
 * @phpstan-type ArrayMap array<string, mixed>
 * @phpstan-type StringMap array<string, string>
 * @phpstan-type IntList list<int>
 * @phpstan-type IntStringMap array<int, string>
 * @phpstan-type MqttProperties ArrayMap
 * @phpstan-type ConnectData ArrayMap
 * @phpstan-type MessageData ArrayMap
 * @phpstan-type PacketData ArrayMap
 * @phpstan-type WillData array{
 *     topic: string,
 *     message?: string,
 *     qos?: int,
 *     retain?: int,
 *     properties?: MqttProperties
 * }
 * @phpstan-type SubscribeOptions array{
 *     qos?: int,
 *     no_local?: bool,
 *     retain_as_published?: bool,
 *     retain_handling?: int
 * }
 * @phpstan-type SubscribeTopics array<string, int|SubscribeOptions>
 * @phpstan-type UnsubscribeTopics list<string>
 */
final class PhpStanTypes
{
    private function __construct()
    {
    }
}
