# HlwAdmin 全栈管理系统与小程序工程

本项目是一个前后端分离的全栈系统工程，包含基于 ThinkPHP 的高性能后台管理系统与基于 uni-app 的微信小程序前端。

---

## 📂 项目结构概览

```text
hlw-admin/ (项目根目录)
├── php/             # 后端项目 (基于 ThinkPHP 8.2 + ThinkLibrary 6.1)
└── uni/             # 前端项目 (基于 Vue 3 + uni-app + Vite)
```

---

## 🚀 快速开始

### 1. 后端服务 (PHP)

后端基于 **ThinkPHP 8.2**，站点运行目录指向 `public/`。

**环境要求：**

- PHP >= 8.2
- Composer
- MySQL 5.7+ 或 SQLite
- Nginx / Apache

**部署步骤：**

```bash
cd php
composer install
cp .env.example .env     # 配置您的数据库及微信参数
php think migrate:run
php think xadmin:publish --migrate
php think run --host 192.168.5.12 --port 8000
```

> 💡 _提示：如果是本地或宝塔环境部署，请确保 `runtime/` 和 `public/upload/` 目录有写入权限。_

👉 详细配置与进阶说明请阅读：[后端说明文档](file:///f:/mini/admin/php/readme.md)

---

### 2. 小程序前端 (Uni-app)

前端基于 **Vue 3** + **uni-app** 构建，主要用于短视频助手、用户中心、广告奖励和帮助内容展示。

**环境要求：**

- Node.js
- pnpm
- 微信开发者工具

**部署与运行：**

```bash
cd uni
pnpm install
pnpm dev       # 开发模式构建，读取 .env.dev
pnpm build     # 生产模式构建，读取 .env.prod
```

> ⚠️ _注意：上传体验版或线上版本前，请确保运行 `pnpm build` 构建生产版，然后使用微信开发者工具打开 `dist/build/mp-weixin` 进行上传，切勿直接上传 dev 版本以免请求到开发接口。_

👉 详细前端约定与目录说明请阅读：[前端说明文档](file:///f:/mini/admin/uni/README.md)

---

## 🛠️ 核心功能与技术栈

| 模块                    | 技术栈                                   | 核心功能                                                        |
| :---------------------- | :--------------------------------------- | :-------------------------------------------------------------- |
| **PHP 后端** (`/php`)   | ThinkPHP 8.2, ThinkLibrary 6.1, Composer | 后台管理、系统权限控制、微信开放接口、本地插件扩展 (`package/`) |
| **小程序前端** (`/uni`) | Vue 3, uni-app, Vite, TypeScript, Pinia  | 短视频助手、用户中心、广告奖励、多端适配与展示                  |

---

## 📌 开发与协作规范

1. **环境配置文件**
   - 不要将本地的 `.env` 提交到 Git。
   - 不要提交 `/php/runtime/`、`/php/vendor/` 以及 `/uni/node_modules/` 等运行时或依赖目录。
2. **前后端接口约定**
   - 接口字段命名统一采用**下划线（snake_case）**风格（例如：`video_url`、`source_url`）。
3. **前端代码质量**
   - 修改前端代码后请在提交前运行 `pnpm type-check` 进行 TypeScript 与 Vue 的类型检查。
   - 提示弹窗推荐统一使用 `hlw.$msg.toast()`。

---

## 📄 开源许可证

本项目遵循 **MIT** 开源协议。
