# Message API

It is mainly convenient for replying to the ACK on the other side in the Server/Client.

## Usage examples

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

### Server

```php
$server->send($fd, $ack->getContents());
$server->send($fd, (string) $ack);
```

### Client

```php
// Add param to get an array
$client->send($ack->getContents(true), false);
```
