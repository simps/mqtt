# Message API

主要方便用于在 Server 中回复对端 ACK。

## 使用示例

```php
use Simps\MQTT\Message\SubAck;
use Simps\MQTT\Protocol\ProtocolInterface;

$codes = [0];
$message_id = 8520;

$ack = new SubAck();
$ack->setCodes($codes)
    ->setMessageId($message_id);

$ack_data = $ack->getContents();
$ack_data = (string) $ack;

// MQTT5
$ack->setProtocolLevel(ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0)
    ->setCodes($codes)
    ->setMessageId($message_id)
    ->setProperties([
        'will_delay_interval' => 60,
        'message_expiry_interval' => 60,
    ]);

$ack_data = $ack->getContents();
$ack_data = (string) $ack;
```
