# MQTT 协议解析 & 协程客户端 :id=phpmqtt

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

## 捐献及赞助 :id=donate

### 一次性赞助

如果您是个人开发者并且 PHPMQTT 给您解决燃眉之急或带来些许明朗，您可以打赏一杯咖啡或一杯香茗 :)

我们通过以下方式接受赞助：

![alipay](https://cdn.jsdelivr.net/gh/sy-records/staticfile/images/alipay.jpg ':size=362x562')
![wechat](https://cdn.jsdelivr.net/gh/sy-records/staticfile/images/wechatpay.png ':size=autox562')

### 周期性赞助

如果您是企业经营者并且将 PHPMQTT 用在商业产品中，那么赞助 PHPMQTT 有商业上的益处：可以让您的产品所依赖的类库保持健康并得到积极的升级维护，也能获得一些技术支持。

周期性赞助可以获得额外的回报，比如您的名字或组织/公司 Logo 及链接会出现在 PHPMQTT 的 GitHub 仓库中。

> 如您希望为 PHPMQTT 提供周期性的赞助，可邮件至 lufei@simps.io
