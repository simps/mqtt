# MQTT 协议解析 & 协程客户端 :id=phpmqtt

适用于 PHP 的 MQTT 协议解析和协程客户端。

支持 MQTT 协议 `3.1`、`3.1.1` 和 `5.0` 版本，支持`QoS 0`、`QoS 1`、`QoS 2`。

支持 MQTT over WebSocket。

## 依赖要求

* PHP >= `7.1`
* mbstring PHP 扩展
* [Swoole 扩展](https://github.com/swoole/swoole-src) >= `v4.4.20` 或 `v4.5.3` (使用 [Client](/zh-cn/client) 时需要)

## 安装

```bash
composer require simps/mqtt
```

## 示例

参考 [examples](https://github.com/simps/mqtt/tree/master/examples) 目录

## 关注

![](https://cdn.jsdelivr.net/gh/sy-records/staticfile/images/202012/wechat_white.png)

## 赞赏 :id=donate

如果给您解决燃眉之急或带来些许明朗，您可以打赏一杯咖啡或一杯香茗 :)

[donate](https://donate.qq52o.me/ ':include :type=iframe')

