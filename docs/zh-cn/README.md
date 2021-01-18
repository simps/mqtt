# MQTT 协议解析 & 协程客户端

适用于 PHP 的 MQTT 协议解析和协程客户端。

支持 MQTT 协议 `3.1`、`3.1.1` 和 `5.0` 版本，支持`QoS 0`、`QoS 1`、`QoS 2`。

## 依赖要求

* PHP >= `7.1`
* mbstring PHP 扩展
* [Swoole 扩展](https://github.com/swoole/swoole-src) >= `4.4.19` (使用 [Client](/zh-cn/client) 时需要)

## 安装

```bash
composer require simps/mqtt
```

## 示例

参考 [examples](https://github.com/simps/mqtt/tree/master/examples) 目录
