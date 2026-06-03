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
php think run --host 127.0.0.1 --port 8000
```

如果安装依赖时遇到 `runtime/cache` 写入权限问题，可先执行：

```bash
composer install --no-scripts
```

生产或宝塔环境请确认 `runtime/`、`public/upload/` 可写，必要时调整目录权限和 PHP-FPM 运行用户。

## 启动 Workerman 服务

通过 Workerman 方式，启动默认 Http 服务：

```bash
php think xadmin:worker
```

> [!TIP]
> 生产或宝塔环境下，建议在宝塔 **应用商店** 安装 **进程守护管理器3.0.6** 来添加并管理该后台守护进程（启动命令为 `php think xadmin:worker`），以确保服务稳定运行及异常自动重启。

### Nginx 反向代理配置

由于 Workerman 默认运行在本地端口（默认监听 `127.0.0.1:2346`，可在 [config/worker.php](file:///f:/mini/admin/php/config/worker.php#L7) 中修改 `port` 参数），在生产环境下需要配置 Nginx 反向代理以对外提供服务（支持 80/443 端口及 SSL）。

**宝塔反向代理配置步骤：**

1. 打开宝塔面板对应的站点设置。
2. 选择 **反向代理** -> **添加反向代理**。
3. **代理名称** 填入如 `workerman`。
4. **目标URL** 填入 `http://127.0.0.1:2346`（若在 [worker.php](file:///f:/mini/admin/php/config/worker.php) 中修改了端口，此处请同步修改）。
5. 开启 **启用反向代理**。

**Nginx 配置文件规则参考：**

```nginx
location / {
    proxy_pass http://127.0.0.1:2346;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded-for;
    proxy_set_header Host $host;
}
```

## 常用目录

```text
app/admin/       后台管理模块
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
