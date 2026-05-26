# 去水印解析

`hlw2326/think-plugin-qsy` 是 ThinkAdmin v6 小程序插件，提供用户登录、广告配置、小程序跳转列表、解析提交与解析记录管理。

- 命名空间：`plugin\qsy`
- 表名前缀：`qsy_`
- API 前缀：`/plugin-qsy/api.v1.{controller}/{action}`
- 配置前缀：`qsy.*`

第一版解析服务采用可替换适配层，默认 `DemoParserAdapter` 返回规范化演示数据。后续接入真实去水印服务时，只需替换 `ParserAdapterInterface` 的实现。
