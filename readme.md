# qz2 部署说明

## Supervisor 进程配置

### Worker 进程（常驻内存服务）

```ini
[program:worker]
command=php think xadmin:worker
directory=/www/wwwroot/qz2.ka57.net/
autorestart=true
startsecs=3
startretries=3
stdout_logfile=/www/server/panel/plugin/supervisor/log/worker.out.log
stderr_logfile=/www/server/panel/plugin/supervisor/log/worker.err.log
stdout_logfile_maxbytes=2MB
stderr_logfile_maxbytes=2MB
user=root
priority=999
numprocs=1
process_name=%(program_name)s_%(process_num)02d
```

### Update 进程（文件变更热重载）

```ini
[program:update]
command=/www/wwwroot/qz2.ka57.net/reload.sh
directory=/www/wwwroot/qz2.ka57.net/
autorestart=true
startsecs=3
startretries=3
stdout_logfile=/www/server/panel/plugin/supervisor/log/update.out.log
stderr_logfile=/www/server/panel/plugin/supervisor/log/update.err.log
stdout_logfile_maxbytes=2MB
stderr_logfile_maxbytes=2MB
user=root
priority=999
numprocs=1
process_name=%(program_name)s_%(process_num)02d
```

## Nginx 反向代理配置

Workerman 作为常驻进程时，Nginx 反向代理必须正确传递 Host 信息，否则 `request()->domain()` 会返回内网IP。

在 `location` 块中加入以下配置：

```nginx
# 将客户端的 Host 和 IP 信息一并转发到对应节点
proxy_set_header X-Host $http_host;
# 将协议转发到对应节点，如果使用非 https 请改为 http
proxy_set_header X-scheme https;
proxy_set_header REMOTE-HOST $remote_addr;
proxy_set_header Upgrade $http_upgrade;
proxy_set_header Connection $connection_upgrade;
add_header X-Cache $upstream_cache_status;
```

> **说明**：`X-Host` 是关键，ThinkPHP 的 Workerman 请求类优先读取此头来确定域名。缺少此配置会导致后端生成的 URL（如 `window.taAdmin`）使用内网IP而非真实域名。

## reload.sh 热重载脚本

监听 `app/` 目录下的文件变更，自动重启 Worker 进程。

```bash
#!/bin/bash

# 监听目录（只监听 app，避免缓存/日志变动触发无限重启）
WATCH_DIR="/www/wwwroot/qz2.ka57.net/app/"

echo "开始监听 $WATCH_DIR 的变动..."

while true; do
    # 阻塞等待文件发生修改、创建、删除事件
    inotifywait -r -e modify,create,delete $WATCH_DIR

    echo "检测到代码更新，正在重启 Worker..."
    supervisorctl restart worker:*

    # 防抖：避免批量上传文件时进程被频繁重启
    sleep 2
done
```

> **依赖**：热重载脚本需要系统已安装 `inotify-tools`。
>
> 安装命令：`apt install inotify-tools` 或 `yum install inotify-tools`
