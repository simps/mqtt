# Client API

## __construct()

Create an instance of the MQTT over WebSocket client

```php
Simps\MQTT\WebSocketClient::__construct(string $host, int $port, ClientConfig $config, string $path = '/mqtt', bool $ssl = false)
```

- `string $host`

Broker's host

- `int $port`

Broker's port

- `ClientConfig $config`

ClientConfig object.

- `string $path`

WebSocket path, default is `/mqtt`

- `bool $ssl`

Whether to use SSL, default is `false`

Example.

```php
$config = [
    'userName' => '',
    'password' => '',
    'clientId' => '',
    'keepAlive' => 10,
    'protocolName' => 'MQTT', // or MQIsdp
    'protocolLevel' => 4, // or 3, 5
    'properties' => [], // optional in MQTT5
    'delay' => 3000, // 3s
    'maxAttempts' => 5,
    'swooleConfig' => []
];
$configObj = new Simps\MQTT\Config\ClientConfig($config);
$client = new Simps\MQTT\WebSocketClient('broker.emqx.io', 8083, $configObj, '/mqtt');
```

## connect()

Connect Broker

```php
Simps\MQTT\WebSocketClient->connect(bool $clean = true, array $will = [])
```

- `bool $clean`

Clean session. default is `true`.

For a detailed description, please see the corresponding protocol document: `Clean Session`.

- `array $will`

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
Simps\MQTT\WebSocketClient->publish($topic, $message, $qos = 0, $dup = 0, $retain = 0, array $properties = [])
```

## subscribe()

Subscribe to one topic or multiple topics

```php
Simps\MQTT\WebSocketClient->subscribe(array $topics, array $properties = [])
```

- `array $topics`

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

- `array $properties`

Optional in MQTT5

## unSubscribe()

Unsubscribe from a topic or multiple topics

```php
Simps\MQTT\WebSocketClient->unSubscribe(array $topics, array $properties = [])
```

- `array $topics`

```php
$topics = ['topic1', 'topic2'];
```

- `array $properties`

Optional in MQTT5

## close()

Disconnect from Broker connect. The `DISCONNECT(14)` message is send to Broker

```php
Simps\MQTT\WebSocketClient->close(int $code = ReasonCode::NORMAL_DISCONNECTION, array $properties = [])
```

## auth()

New AUTH type added in MQTT5. Authentication exchange.

```php
Simps\MQTT\WebSocketClient->auth(int $code = ReasonCode::SUCCESS, array $properties = [])
```

## send()

Send messages

```php
Simps\MQTT\WebSocketClient->send(array $data, $response = true)
```

- `array $data`

`$data` is the data to be sent and must contain information such as `type`

- `bool $response`

Are acknowledgements required. If `true`, `recv()` is called once

## recv()

Receive messages

```php
Simps\MQTT\WebSocketClient->recv(): bool|arary|string
```

## ping()

Send a heartbeat

```php
Simps\MQTT\WebSocketClient->ping()
```

## buildMessageId()

Generate MessageId

```php
Simps\MQTT\WebSocketClient->buildMessageId()
```

## genClientId()

Generate ClientId

```php
Simps\MQTT\WebSocketClient::genClientID(string $prefix = 'Simps_')
```

## getClient()

Get an instance of `Swoole\Coroutine\Http\Client`

```php
Simps\MQTT\WebSocketClient->getClient()
```
