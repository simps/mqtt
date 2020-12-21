# Protocol API

Use `pack` and `unpack` to package and parse according to the MQTT protocol.

## MQTT 3.1.x

```php
Simps\MQTT\Protocol::pack(array $array)

Simps\MQTT\Protocol::unpack(string $data)
```

## MQTT 5.0

```php
Simps\MQTT\ProtocolV5::pack(array $array)

Simps\MQTT\ProtocolV5::unpack(string $data)
```

## Constants

Constants for MQTT protocol levels and names

```php
Simps\MQTT\ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1; // 3.1
Simps\MQTT\ProtocolInterface::MQTT_PROTOCOL_LEVEL_3_1_1; // 3.1.1
Simps\MQTT\ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0; // 5.0

Simps\MQTT\ProtocolInterface::MQISDP_PROTOCOL_NAME; // MQIsdp
Simps\MQTT\ProtocolInterface::MQTT_PROTOCOL_NAME; // MQTT
```