# Debug Tools

The tool provides 5 methods for debugging binary data, essentially functioning as a binary data viewer.

It primarily converts binary data into ASCII or hexadecimal formats for viewing, useful for debugging TCP, WebSocket, UDP, and other protocols.

```php
public function hexDump(): string // Display in hexadecimal
public function hexDumpAscii(): string // Display in both hexadecimal and corresponding ASCII characters
public function printableText(): string // Printable characters
public function hexStream(): string // Hexadecimal stream
public function ascii(): string // Display in ASCII characters
```

You can call these methods statically or instantiate `Simps\MQTT\Tools\Debug` or `Simps\MQTT\Tools\Common`/`Simps\MQTT\Tools\UnPackTool`:

- Instantiation

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

- Static call

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
