# Debug Tools

提供了 5 种方法来调试二进制数据，实际上就是一个二进制数据查看工具。

主要是将二进制数据转为ASCII、十六进制的格式进行查看，可以用来调试 TCP、WebSocket、UDP 等。

```php
public function hexDump(): string // 以16进制显示
public function hexDumpAscii(): string // 以16进制和相应的ASCII字符显示
public function printableText(): string // 可打印字符
public function hexStream(): string // 16进制流
public function ascii(): string // 以ASCII字符显示
```

可以通过实例化`Simps\MQTT\Tools\Debug`或者`Simps\MQTT\Tools\Common`/`Simps\MQTT\Tools\UnPackTool`静态调用：

- 实例化

```php
use Simps\MQTT\Tools\Debug;

$debug = new Debug('0:simps-mqtt/user001/update{
  "msg": "hello, mqtt"
}');

//$debug = (new Debug())->setEncode('0:simps-mqtt/user001/update{
//  "msg": "hello, mqtt"
//}');

echo $debug->hexDump(), PHP_EOL;
echo $debug->hexDumpAscii(), PHP_EOL;
echo $debug->printableText(), PHP_EOL;
echo $debug->hexStream(), PHP_EOL;
echo $debug->ascii();
```

- 静态调用

```php
use Simps\MQTT\Tools\UnPackTool;

echo UnPackTool::hexDumpAscii('0:simps-mqtt/user001/update{
  "msg": "hello, mqtt"
}');
```

```text
00000000    30 3a 73 69 6d 70 73 2d 6d 71 74 74 2f 75 73 65    0:simps-mqtt/use
00000010    72 30 30 31 2f 75 70 64 61 74 65 7b 0a 20 20 22    r001/update{.  "
00000020    6d 73 67 22 3a 20 22 68 65 6c 6c 6f 2c 20 6d 71    msg": "hello, mq
00000030    74 74 22 0a 7d                                     tt".}
```
