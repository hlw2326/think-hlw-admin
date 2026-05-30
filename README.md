# HlwAdmin 通用型全栈管理系统与微信小程序框架

本项目是一个基于 ThinkPHP + Vue 3 (uni-app) 的通用型全栈开发框架。

## 使用方法

### 1. 后端部署与运行

```bash
cd php
composer install
cp .env.example .env     # 配置您的数据库连接与微信小程序 AppID/AppSecret 参数
php think migrate:run
php think xadmin:publish --migrate
php think run --host 127.0.0.1 --port 8000
```

### 2. 前端部署与运行

```bash
cd uni
pnpm install
pnpm dev       # 启动开发环境，进行微信小程序编译
pnpm build     # 构建生产环境
```
