# 自适应协议等级

用于在一个端口中支持多种 MQTT 协议等级类型。

具体介绍可以查看博客：[MQTT 怎么在单独一个端口上分别使用 v3.x 和 v5.0 协议解析？](https://qq52o.me/2796.html)

```php
use Simps\MQTT\Protocol\Types;
use Simps\MQTT\Protocol\V3;
use Simps\MQTT\Protocol\V5;
use Simps\MQTT\Tools\UnPackTool;
use Simps\MQTT\Protocol\ProtocolInterface;

$type = UnPackTool::getType($data);
if ($type === Types::CONNECT) {
    $level = UnPackTool::getLevel($data);
    $class = $level === ProtocolInterface::MQTT_PROTOCOL_LEVEL_5_0 ? V5::class : V3::class;
}
```
