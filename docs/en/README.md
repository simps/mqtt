# MQTT Protocol Analysis & Coroutine Client

MQTT Protocol Analysis and Coroutine Client for PHP.

Support for MQTT protocol versions `3.1`, `3.1.1` and `5.0`.

Support for `QoS 0`, `QoS 1`, `QoS 2`.

Support for MQTT over WebSocket.

## Requirements

* PHP >= `7.1`
* ext-mbstring
* [ext-swoole](https://github.com/swoole/swoole-src) >= `4.4.20` (The ext-swoole >= `v4.4.20` or `v4.5.3` needs to be loaded when using [the MQTT Client](/en/client))

## Install

```bash
composer require simps/mqtt
```

## Examples

see [examples](https://github.com/simps/mqtt/tree/master/examples)
