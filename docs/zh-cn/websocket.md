# WebSocket Client API

## __construct()

创建一个 MQTT over WebSocket 客户端实例

```php
Simps\MQTT\WebSocketClient::__construct(string $host, int $port, ClientConfig $config, string $path = '/mqtt', bool $ssl = false)
```

- 参数`string $host`

Broker 的 IP 地址

- 参数`int $port`

Broker 的端口

- 参数`ClientConfig $config`

客户端配置对象

- 参数`string $path`

WebSocket 的路径，默认为`/mqtt`

- 参数`bool $ssl`

是否使用 SSL，默认为`false`

示例：

```php
$config = [
    'userName' => '', // 用户名
    'password' => '', // 密码
    'clientId' => '', // 客户端id
    'keepAlive' => 10, // 默认0秒，设置成0代表禁用
    'protocolName' => 'MQTT', // 协议名，默认为MQTT(3.1.1版本)，也可为MQIsdp(3.1版本)
    'protocolLevel' => 4, // 协议等级，MQTT3.1.1版本为4，5.0版本为5，MQIsdp为3
    'properties' => [], // MQTT5 中所需要的属性
    'delay' => 3000, // 重连时的延迟时间 (毫秒)
    'maxAttempts' => 5, // 最大重连次数。默认-1，表示不限制
    'swooleConfig' => []
];
$configObj = new Simps\MQTT\Config\ClientConfig($config);
$client = new Simps\MQTT\WebSocketClient('broker.emqx.io', 8083, $configObj, '/mqtt');
```

## connect()

连接 Broker

```php
Simps\MQTT\WebSocketClient->connect(bool $clean = true, array $will = [])
```

- 参数`bool $clean`

清理会话，默认为`true`

具体描述请查看对应协议文档：`清理会话 Clean Session`

- 参数`array $will`

遗嘱消息，当客户端断线后 Broker 会自动发送遗嘱消息给其它客户端

需要设置的内容如下

```php
$will = [
    'topic' => '', // 主题
    'qos' => 1, // QoS等级
    'retain' => 0, // retain标记
    'message' => '', // 遗嘱消息内容
    'properties' => [], // MQTT5 中需要，可选
];
```

## publish()

向某个主题发布一条消息

```php
Simps\MQTT\WebSocketClient->publish($topic, $message, $qos = 0, $dup = 0, $retain = 0, array $properties = [])
```

- 参数`$topic` 主题
- 参数`$message` 内容
- 参数`$qos` QoS 等级，默认 0
- 参数`$dup` 重发标志，默认 0
- 参数`$retain` retain 标记，默认 0
- 参数`$properties` 属性，MQTT5 中需要，可选

## subscribe()

订阅一个主题或者多个主题

```php
Simps\MQTT\WebSocketClient->subscribe(array $topics, array $properties = [])
```

- 参数`array $topics`

`$topics`的`key`是主题，值为`QoS`的数组，例如

```php
// MQTT 3.x
$topics = [
    // 主题 => Qos
    'topic1' => 0,
    'topic2' => 1,
];

// MQTT 5.0
$topics = [
    // 主题 => 选项
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

- 参数`array $properties`

属性，MQTT5 中需要，可选

## unSubscribe()

取消订阅一个主题或者多个主题

```php
Simps\MQTT\WebSocketClient->unSubscribe(array $topics, array $properties = [])
```

- 参数`array $topics`

```php
$topics = ['topic1', 'topic2'];
```

- 参数`array $properties`

属性，MQTT5 中需要，可选

## close()

正常断开与 Broker 的连接，`DISCONNECT(14)`报文会被发送到 Broker

```php
Simps\MQTT\WebSocketClient->close(int $code = ReasonCode::NORMAL_DISCONNECTION, array $properties = [])
```

- 参数`int $code`

响应码，MQTT5 中需要，MQTT3 直接调用即可

- 参数`array $properties`

属性，MQTT5 中需要

## auth()

MQTT5 中新增的认证交换机制。

```php
Simps\MQTT\WebSocketClient->auth(int $code = ReasonCode::SUCCESS, array $properties = [])
```

## send()

发送消息

```php
Simps\MQTT\WebSocketClient->send(array $data, $response = true)
```

- 参数`array $data`

`$data`是需要发送的数据，必须包含`type`等信息

- 参数`bool $response`

是否需要回执。如果为`true`，会调用一次`recv()`

## recv()

接收消息

```php
Simps\MQTT\WebSocketClient->recv(): bool|arary|string
```

## ping()

发送心跳包

```php
Simps\MQTT\WebSocketClient->ping()
```

## buildMessageId()

生成 MessageId

```php
Simps\MQTT\WebSocketClient->buildMessageId()
```

## genClientId()

生成 ClientId

```php
Simps\MQTT\WebSocketClient::genClientID(string $prefix = 'Simps_')
```

## getClient()

获取 `Swoole\Coroutine\Http\Client` 的实例

```php
Simps\MQTT\WebSocketClient->getClient()
```
