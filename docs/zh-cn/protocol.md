# Protocol API

使用`pack`和`unpack`按照 MQTT 协议来进行打包和解析。

## MQTT 3.1.x

```php
Simps\MQTT\Protocol\V3::pack(array $array)

Simps\MQTT\Protocol\V3::unpack(string $data)
```

## MQTT 5.0

```php
Simps\MQTT\Protocol\V5::pack(array $array)

Simps\MQTT\Protocol\V5::unpack(string $data)
```

## Constants

MQTT 协议等级和名称的常量

```php
Simps\MQTT\Protocol\ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1; // 3.1
Simps\MQTT\Protocol\ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1_1; // 3.1.1
Simps\MQTT\Protocol\ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0; // 5.0

Simps\MQTT\Protocol\ProtocolInterface::MQISDP_PROTOCOL_NAME; // MQIsdp
Simps\MQTT\Protocol\ProtocolInterface::MQTT_PROTOCOL_NAME; // MQTT
```

## ReasonCode

原因码小于`0x80`表示操作成功完成，成功的常规原因码为`0`，原因码值为`0x80`或更大表示失败。

可以使用`ReasonCode::getReasonPhrase()`方法将原因码转为人类可读的名称。

```php
Simps\MQTT\Hex\ReasonCode::getReasonPhrase(0x86);

Simps\MQTT\Hex\ReasonCode::getReasonPhrase(0x02, true);
```
