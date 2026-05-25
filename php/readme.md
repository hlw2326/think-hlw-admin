# HlwAdmin

基于 ThinkPHP 8.2 + ThinkLibrary 6.1 的后台管理系统，内置后台管理、权限控制、微信开发和本地插件扩展能力。

## 环境要求

- PHP >= 8.2
- Composer
- MySQL 5.7+ 或 SQLite
- Nginx / Apache，站点运行目录指向 `public/`

## 快速开始

```bash
composer install
cp .env.example .env
php think migrate:run
php think xadmin:publish --migrate
php think run --host 192.168.5.12 --port 8000
```

如果安装依赖时遇到 `runtime/cache` 写入权限问题，可先执行：

```bash
composer install --no-scripts
```

生产或宝塔环境请确认 `runtime/`、`public/upload/` 可写，必要时调整目录权限和 PHP-FPM 运行用户。

## 常用目录

```text
app/admin/       后台管理模块
app/wechat/      微信开发模块
app/index/       入口页面模块
config/          应用配置
database/        数据迁移与本地数据库
public/          Web 根目录
package/         本地插件包
runtime/         运行时目录
vendor/          Composer 依赖
```

## 插件

本地插件放在 `package/` 目录，当前常用插件包括：

- `think-plugin-backup`
- `think-plugin-collect`
- `think-plugin-mp-base`

## 注意事项

- 不要提交 `.env`、`runtime/`、`vendor/`、`public/upload/` 等本地或运行时文件。
- 使用 SQLite 时，建议定期备份 `database/sqlite.db`。
- 后台入口默认为 `/admin`。

## 许可证

MIT
