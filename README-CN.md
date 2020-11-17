[English](./README.md) | 中文

# MQTT 协程客户端

适用于 PHP 的 MQTT 协议解析和协程客户端。

[![Latest Stable Version](https://poser.pugx.org/simps/mqtt/v)](//packagist.org/packages/simps/mqtt)
[![Total Downloads](https://poser.pugx.org/simps/mqtt/downloads)](//packagist.org/packages/simps/mqtt)
[![Latest Unstable Version](https://poser.pugx.org/simps/mqtt/v/unstable)](//packagist.org/packages/simps/mqtt)
[![License](https://poser.pugx.org/simps/mqtt/license)](LICENSE)

## 安装

```bash
composer require simps/mqtt
```

## 示例

参考 [examples](./examples) 目录

## 方法

### __construct()

创建一个MQTT客户端实例

```php
Simps\MQTT\Client::__construct(array $config, array $swConfig = [], int $type = SWOOLE_SOCK_TCP)
```

* 参数`array $config`

客户端选项数组，可以设置以下选项：

```php
$config = [
    'host' => '127.0.0.1', // MQTT服务端IP
    'port' => 1883, // MQTT服务端端口
    'time_out' => 5, // 连接MQTT服务端超时时间，默认0.5秒
    'user_name' => '', // 用户名
    'password' => '', // 密码
    'client_id' => '', // 客户端id
    'keep_alive' => 10, // 默认0秒，设置成0代表禁用
    'protocol_name' => 'MQTT', // 协议名，默认为MQTT(3.1.1版本)，也可为MQIsdp(3.1版本)
    'protocol_level' => 4, // 协议等级，MQTT为4，MQIsdp为3
];
```

* 参数`array $swConfig`

用于设置`Swoole\Coroutine\Client`的配置，请参考Swoole文档：[set()](https://wiki.swoole.com/#/coroutine_client/client?id=set)

### connect()

连接Broker

```php
Simps\MQTT\Client->connect(bool $clean = true, array $will = [])
```

* 参数`bool $clean`

清理会话，默认为`true`

具体描述请参考[清理会话 Clean Session](https://mcxiaoke.gitbook.io/mqtt/03-controlpackets/0301-connect#qing-li-hui-hua-clean-session)

* 参数`array $will`

遗嘱消息，当客户端断线后Broker会自动发送遗嘱消息给其它客户端

需要设置的内容如下

```php
$will = [
    'topic' => '', // 主题
    'qos' => 1, // QoS等级
    'retain' => 0, // retain标记
    'content' => '', // content
];
```

### publish()

向某个主题发布一条消息

```php
Simps\MQTT\Client->publish($topic, $content, $qos = 0, $dup = 0, $retain = 0)
```

* 参数`$topic` 主题
* 参数`$content` 内容
* 参数`$qos` QoS等级，默认0
* 参数`$dup` 重发标志，默认0
* 参数`$retain` retain标记，默认0

### subscribe()

订阅一个主题或者多个主题

```php
Simps\MQTT\Client->subscribe(array $topics)
```

* 参数`array $topics`

`$topics`的`key`是主题，值为`QoS`的数组，例如

```php
$topics = [
    // 主题 => Qos
    'topic1' => 0, 
    'topic2' => 1,
];
```

### unSubscribe()

取消订阅一个主题或者多个主题

```php
Simps\MQTT\Client->unSubscribe(array $topics)
```

* 参数`array $topics`

```php
$topics = ['topic1', 'topic2'];
```

### close()

正常断开与Broker的连接，`DISCONNECT(14)`报文会被发送到Broker

```php
Simps\MQTT\Client->close()
```

### recv()

接收消息

```php
Simps\MQTT\Client->recv(): bool|arary|string
```

### send()

发送消息

```php
Simps\MQTT\Client->send(array $data, $response = true)
```

* 参数`array $data`

`$data`是需要发送的数据，必须包含`type`等信息

* 参数`bool $response`

是否需要回执。如果为`true`，会调用一次`recv()`

### ping()

发送心跳包

```php
Simps\MQTT\Client->ping()
```

### buildMessageId()

生成MessageId

```php
Simps\MQTT\Client->buildMessageId()
```
