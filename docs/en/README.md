# MQTT Coroutine Client

MQTT Protocol Analysis and Coroutine Client for PHP.

[![Latest Stable Version](https://poser.pugx.org/simps/mqtt/v)](//packagist.org/packages/simps/mqtt)
[![Total Downloads](https://poser.pugx.org/simps/mqtt/downloads)](//packagist.org/packages/simps/mqtt)
[![Latest Unstable Version](https://poser.pugx.org/simps/mqtt/v/unstable)](//packagist.org/packages/simps/mqtt)
[![License](https://poser.pugx.org/simps/mqtt/license)](https://github.com/simps/mqtt/blob/master/LICENSE)

## Install

```bash
composer require simps/mqtt
```

## Examples

see [examples](https://github.com/simps/mqtt/tree/master/examples)

## Client API

### __construct()

Create a MQTT client instance

```php
Simps\MQTT\Client::__construct(array $config, array $swConfig = [], int $type = SWOOLE_SOCK_TCP)
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
    'protocol_level' => 4, // or 3
];
```

* `array $swConfig`

To set the configuration of `Swoole\Coroutine\Client`, please see Swoole document: [set()](https://www.swoole.co.uk/docs/modules/swoole-coroutine-client-set)

### connect()

Connect Broker

```php
Simps\MQTT\Client->connect(bool $clean = true, array $will = [])
```

* `bool $clean`

Clean session. default is `true`. see [Clean Session](http://docs.oasis-open.org/mqtt/mqtt/v3.1.1/errata01/os/mqtt-v3.1.1-errata01-os-complete.html#_Toc442180843)

* `array $will`

When a client is disconnected, Broker will automatically send a will message to other clients

```php
$will = [
    'topic' => '',
    'qos' => 1,
    'retain' => 0,
    'content' => '',
];
```

### publish()

push a message to a topic

```php
Simps\MQTT\Client->publish($topic, $content, $qos = 0, $dup = 0, $retain = 0)
```

### subscribe()

Subscribe to one topic or multiple topics

```php
Simps\MQTT\Client->subscribe(array $topics)
```

* `array $topics`

```php
$topics = [
    // topic => Qos
    'topic1' => 0, 
    'topic2' => 1,
];
```

### unSubscribe()

Unsubscribe from a topic or multiple topics

```php
Simps\MQTT\Client->unSubscribe(array $topics)
```

* `array $topics`

```php
$topics = ['topic1', 'topic2'];
```

### close()

Disconnect from Broker connect. The `DISCONNECT(14)` message is send to Broker

```php
Simps\MQTT\Client->close()
```

### recv()

Receive messages

```php
Simps\MQTT\Client->recv(): bool|arary|string
```

### send()

Send messages

```php
Simps\MQTT\Client->send(array $data, $response = true)
```

* `array $data`

`$data` is the data to be sent and must contain information such as `type`

* `bool $response`

Are acknowledgements required. If `true`, `recv()` is called once

### ping()

Send a heartbeat

```php
Simps\MQTT\Client->ping()
```

### buildMessageId()

Generate MessageId

```php
Simps\MQTT\Client->buildMessageId()
```

### genClientId()

Generate ClientId

```php
Simps\MQTT\Client->genClientID()
```
