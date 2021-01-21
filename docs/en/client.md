# Client API

## __construct()

Create a MQTT client instance

```php
Simps\MQTT\Client::__construct(string $host, int $port, ?ClientConfig $config = null, int $clientType = Client::COROUTINE_CLIENT_TYPE)
```

* `string $host`

Broker's host

* `int $port`

Broker's port

* `ClientConfig $config`

ClientConfig object.

Example.

```php
$config = [
    'userName' => '', // 用户名
    'password' => '', // 密码
    'clientId' => '', // 客户端id
    'keepAlive' => 10, // 默认0秒，设置成0代表禁用
    'protocolName' => 'MQTT', // or MQIsdp
    'protocolLevel' => 4, // or 3, 5
    'properties' => [], // optional in MQTT5
    'reconnectDelay' => 3,
    'swooleConfig' => []
];
$configObj = new Simps\MQTT\Config\ClientConfig($config);
$client = new Simps\MQTT\Client('127.0.0.1', 1883, $configObj);
```

!> The Client will use the corresponding protocol resolution according to the `protocol_level` set.

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
