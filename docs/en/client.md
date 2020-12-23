# Client API

## __construct()

Create a MQTT client instance

```php
Simps\MQTT\Client::__construct(array $config, array $swConfig = [], int $type = SWOOLE_SOCK_TCP, int $clientType = Client::COROUTINE_CLIENT_TYPE)
```

* `array $config`

An array of client options, you can set the following options:

```php
$config = [
    'host' => '127.0.0.1',
    'port' => 1883,
    'time_out' => 5,
    'user_name' => '',
    'password' => '',
    'client_id' => '',
    'keep_alive' => 10,
    'protocol_name' => 'MQTT', // or MQIsdp
    'protocol_level' => 4, // or 3, 5
    'properties' => [], // optional in MQTT5
];
```

!> The Client will use the corresponding protocol resolution according to the `protocol_level` set.

* `array $swConfig`

To set the configuration of `Swoole\Coroutine\Client`, please see Swoole document: [set()](https://www.swoole.co.uk/docs/modules/swoole-coroutine-client-set)

* `int $type`

Set `sockType`, such as: `SWOOLE_TCP`, `SWOOLE_TCP | SWOOLE_SSL`

* `int $clientType`

Set the client type, use a Coroutine Client or a Sync Client, the default is Coroutine Client.

Sync Client for Fpm|Apache environments, mainly for `publish` messages, set to `Client::SYNC_CLIENT_TYPE`.

## connect()

Connect Broker

```php
Simps\MQTT\Client->connect(bool $clean = true, array $will = [])
```

* `bool $clean`

Clean session. default is `true`.

For a detailed description, please see the corresponding protocol document: `Clean Session`.

* `array $will`

When a client is disconnected, Broker will automatically send a will message to other clients

```php
$will = [
    'topic' => '',
    'qos' => 1,
    'retain' => 0,
    'message' => '', // message content
    'properties' => [], // optional in MQTT5
];
```

## publish()

push a message to a topic

```php
Simps\MQTT\Client->publish($topic, $message, $qos = 0, $dup = 0, $retain = 0, array $properties = [])
```

## subscribe()

Subscribe to one topic or multiple topics

```php
Simps\MQTT\Client->subscribe(array $topics, array $properties = [])
```

* `array $topics`

```php
// MQTT 3.x
$topics = [
    // topic => Qos
    'topic1' => 0, 
    'topic2' => 1,
];

// MQTT 5.0
$topics = [
    // topic => options
    'topic1' => [
        'qos' => 1,
        'no_local' => true,
        'retain_as_published' => true,
        'retain_handling' => 2,
    ], 
    'topic2' => [
        'qos' => 2,
        'no_local' => false,
        'retain_as_published' => true,
        'retain_handling' => 1,
    ], 
];
```

* `array $properties`

Optional in MQTT5

## unSubscribe()

Unsubscribe from a topic or multiple topics

```php
Simps\MQTT\Client->unSubscribe(array $topics, array $properties = [])
```

* `array $topics`

```php
$topics = ['topic1', 'topic2'];
```

* `array $properties`

Optional in MQTT5

## close()

Disconnect from Broker connect. The `DISCONNECT(14)` message is send to Broker

```php
Simps\MQTT\Client->close(int $code = ReasonCode::NORMAL_DISCONNECTION, array $properties = [])
```

## auth()

New AUTH type added in MQTT5. Authentication exchange.

```php
Simps\MQTT\Client->auth(int $code = ReasonCode::SUCCESS, array $properties = [])
```

## recv()

Receive messages

```php
Simps\MQTT\Client->recv(): bool|arary|string
```

## send()

Send messages

```php
Simps\MQTT\Client->send(array $data, $response = true)
```

* `array $data`

`$data` is the data to be sent and must contain information such as `type`

* `bool $response`

Are acknowledgements required. If `true`, `recv()` is called once

## ping()

Send a heartbeat

```php
Simps\MQTT\Client->ping()
```

## buildMessageId()

Generate MessageId

```php
Simps\MQTT\Client->buildMessageId()
```

## genClientId()

Generate ClientId

```php
Simps\MQTT\Client->genClientID(string $prefix = 'Simps_')
```
