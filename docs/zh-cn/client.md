# Client API

## __construct()

创建一个MQTT客户端实例

```php
Simps\MQTT\Client::__construct(array $config, array $swConfig = [], int $type = SWOOLE_SOCK_TCP, int $clientType = Client::COROUTINE_CLIENT_TYPE)
```

* 参数`array $config`

客户端选项数组，可以设置以下选项：

```php
$config = [
    'host' => '127.0.0.1', // MQTT服务端IP
    'port' => 1883, // MQTT服务端端口
    'user_name' => '', // 用户名
    'password' => '', // 密码
    'client_id' => '', // 客户端id
    'keep_alive' => 10, // 默认0秒，设置成0代表禁用
    'protocol_name' => 'MQTT', // 协议名，默认为MQTT(3.1.1版本)，也可为MQIsdp(3.1版本)
    'protocol_level' => 4, // 协议等级，MQTT3.1.1版本为4，5.0版本为5，MQIsdp为3
    'properties' => [], // MQTT5 中所需要的属性
];
```

!> Client 会根据设置的`protocol_level`来使用对应的协议解析

* 参数`array $swConfig`

设置`Swoole\Coroutine\Client | Swoole\Client`的配置，请参考Swoole文档：[set()](https://wiki.swoole.com/#/coroutine_client/client?id=set)

* 参数`int $type`

设置`sockType`，如：`SWOOLE_TCP`、`SWOOLE_TCP | SWOOLE_SSL`

* 参数`int $clientType`

设置客户端类型，使用协程 Client 还是同步阻塞 Client。默认为协程 Client。

同步阻塞 Client 适用于 Fpm|Apache 环境，主要用于`publish`消息，设置为`Client::SYNC_CLIENT_TYPE`。

## connect()

连接Broker

```php
Simps\MQTT\Client->connect(bool $clean = true, array $will = [])
```

* 参数`bool $clean`

清理会话，默认为`true`

具体描述请查看对应协议文档：`清理会话 Clean Session`

* 参数`array $will`

遗嘱消息，当客户端断线后Broker会自动发送遗嘱消息给其它客户端

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
Simps\MQTT\Client->publish($topic, $message, $qos = 0, $dup = 0, $retain = 0, array $properties = [])
```

* 参数`$topic` 主题
* 参数`$message` 内容
* 参数`$qos` QoS等级，默认0
* 参数`$dup` 重发标志，默认0
* 参数`$retain` retain标记，默认0
* 参数`$properties` 属性，MQTT5 中需要，可选

## subscribe()

订阅一个主题或者多个主题

```php
Simps\MQTT\Client->subscribe(array $topics, array $properties = [])
```

* 参数`array $topics`

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

* 参数`array $properties`

属性，MQTT5 中需要，可选

## unSubscribe()

取消订阅一个主题或者多个主题

```php
Simps\MQTT\Client->unSubscribe(array $topics, array $properties = [])
```

* 参数`array $topics`

```php
$topics = ['topic1', 'topic2'];
```

* 参数`array $properties`

属性，MQTT5 中需要，可选

## close()

正常断开与Broker的连接，`DISCONNECT(14)`报文会被发送到Broker

```php
Simps\MQTT\Client->close(int $code = ReasonCode::NORMAL_DISCONNECTION, array $properties = [])
```

* 参数`int $code`

响应码，MQTT5 中需要，MQTT3直接调用即可

* 参数`array $properties`

属性，MQTT5中需要

## auth()

MQTT5 中新增的认证交换机制。

```php
Simps\MQTT\Client->auth(int $code = ReasonCode::SUCCESS, array $properties = [])
```

## recv()

接收消息

```php
Simps\MQTT\Client->recv(): bool|arary|string
```

## send()

发送消息

```php
Simps\MQTT\Client->send(array $data, $response = true)
```

* 参数`array $data`

`$data`是需要发送的数据，必须包含`type`等信息

* 参数`bool $response`

是否需要回执。如果为`true`，会调用一次`recv()`

## ping()

发送心跳包

```php
Simps\MQTT\Client->ping()
```

## buildMessageId()

生成MessageId

```php
Simps\MQTT\Client->buildMessageId()
```

## genClientId()

生成ClientId

```php
Simps\MQTT\Client->genClientID(string $prefix = 'Simps_')
```
