# HlwAdmin

通用后台管理系统，基于 ThinkPHP 8.2 + ThinkLibrary 构建。

## 简介

HlwAdmin 是一套简洁、高效的后台管理解决方案，集成了用户管理、权限控制、微信开发、小程序支持等常用模块，适用于快速搭建各类后台应用。

## 技术栈

- **框架**：ThinkPHP 8.2+
- **基础库**：ThinkLibrary 6.1
- **PHP 版本**：>= 8.2
- **插件系统**：think-plugs（Admin、Center、Wechat、Worker）
- **插件包**：think-plugin-backup、think-plugin-collect、think-plugin-mp-base

## 目录结构

```
hlw-admin/
├── app/                    # 应用目录
│   ├── admin/              # 后台管理模块
│   │   ├── controller/     # 控制器
│   │   ├── lang/           # 语言包
│   │   ├── route/          # 路由配置
│   │   └── view/           # 视图模板
│   ├── wechat/             # 微信开发模块
│   │   ├── command/        # 命令行
│   │   ├── controller/     # 控制器（含 API）
│   │   ├── model/          # 数据模型
│   │   ├── service/        # 业务服务
│   │   └── lang/           # 语言包
│   ├── mini/               # 小程序模块
│   │   ├── controller/     # 控制器（含 API）
│   │   ├── model/          # 数据模型
│   │   ├── service/        # 业务服务
│   │   └── lang/           # 语言包
│   └── index/              # 入口页面模块
│       └── controller/     # 控制器
├── config/                 # 配置文件目录
│   ├── app.php             # 应用配置
│   ├── cache.php           # 缓存配置
│   ├── database.php        # 数据库配置
│   ├── route.php           # 路由配置
│   ├── session.php         # 会话配置
│   ├── view.php            # 视图配置
│   ├── log.php             # 日志配置
│   ├── cookie.php          # Cookie 配置
│   ├── lang.php            # 语言配置
│   ├── phinx.php           # 数据库迁移配置
│   └── worker.php          # Worker 配置
├── database/               # 数据库相关
│   ├── migrations/         # 数据迁移文件
│   └── sqlite.db           # SQLite 数据库文件（已忽略）
├── public/                 # Web 根目录
│   ├── static/             # 静态资源
│   ├── upload/             # 上传文件目录（已忽略）
│   └── index.php           # 入口文件
├── package/                # 本地插件包（已忽略）
├── runtime/                # 运行时目录（已忽略）
├── vendor/                 # Composer 依赖（已忽略）
├── extend/                 # 扩展类库
├── .env                    # 环境配置（已忽略）
├── .gitignore              # Git 忽略配置
├── composer.json           # Composer 配置
└── README.md               # 项目说明文档
```

## 模块说明

### admin - 后台管理模块

提供后台管理的核心功能：

- **用户管理**（`User.php`）：系统用户 CRUD 操作
- **权限认证**（`Auth.php`）：权限验证与访问控制
- **菜单管理**（`Menu.php`）：后台菜单配置
- **系统配置**（`Config.php`）：系统参数设置
- **文件管理**（`File.php`）：文件浏览与管理
- **操作日志**（`Oplog.php`）：记录用户操作行为
- **队列管理**（`Queue.php`）：后台任务队列管理
- **登录入口**（`Login.php`）：管理员登录认证
- **首页入口**（`Index.php`）：后台首页

### wechat - 微信开发模块

支持微信公众号/小程序开发：

- **粉丝管理**（`Fans.php`）：粉丝信息与标签管理
- **自动回复**（`Auto.php`）：关键词/关注自动回复
- **自定义菜单**（`Menu.php`）：菜单配置
- **素材管理**（`News.php`）：图文素材管理
- **消息推送**（`Push.php`）：模板消息推送
- **微信支付**（`payment/`）：支付与退款功能
- **JS-SDK**（`Js.php`）：网页调用配置
- **微信登录**（`Login.php`）：网页端微信登录

### mini - 小程序模块

提供小程序相关 API：

- **用户体系**（`user/`）：用户登录、积分、会员卡
- **广告管理**（`Ad.php`）：小程序广告配置
- **公告通知**（`Notice.php`）：消息推送
- **工具类**（`Tools.php`）：常用工具接口
- **帮助中心**（`HelpCate.php`、`Help.php`）：帮助文档

## 环境要求

| 项目 | 要求 |
|------|------|
| PHP 版本 | >= 8.2 |
| 必需扩展 | json |
| 推荐 Web 服务器 | Nginx / Apache |
| 数据库 | MySQL 5.7+ / SQLite |

## 安装部署

### 1. 安装依赖

```bash
composer install
```

### 2. 配置环境变量

复制 `.env.example` 为 `.env`，配置数据库连接等信息。

### 3. 访问后台

- 后台地址：`/admin`
- 默认管理员账号：由系统初始化创建

## 插件开发

项目支持本地插件开发，插件包放在 `package/` 目录下：

- `think-plugin-backup` - 数据备份插件
- `think-plugin-collect` - 采集插件
- `think-plugin-mp-base` - 小程序基础插件

## 注意事项

- 请勿将 `.env`、`.gitignore` 中已忽略的文件提交到版本库
- `package/`、`runtime/`、`vendor/` 目录已加入 Git 忽略
- 生产环境请确保 `public/upload/` 目录有写入权限
- 建议定期备份 `database/sqlite.db` 数据库文件

## 许可证

本项目基于 [MIT](https://opensource.org/licenses/MIT) 协议开源。

## 联系方式

- 作者：hlw2326
- 邮箱：1608626143@qq.com
- 官网：https://www.hlw2326.com
