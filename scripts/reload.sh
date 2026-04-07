#!/bin/bash
# 监听多个目录（避免缓存/日志目录，防止无限重启）
WATCH_DIRS=(
    "/www/wwwroot/qz2.ka57.net/app/"
    "/www/wwwroot/qz2.ka57.net/config/"
)

echo "开始监听以下目录的变动: ${WATCH_DIRS[*]}"

while true; do
    # 阻塞等待目录下的文件发生 修改、创建、删除 事件
    CHANGED=$(inotifywait -r -e modify,create,delete --format '%w%f' "${WATCH_DIRS[@]}")

    echo "[$(date '+%Y-%m-%d %H:%M:%S')] 检测到文件变动: $CHANGED，正在重启 Worker..."
    # 重启 Supervisor 中的进程 (根据你的配置，进程组叫 worker)
    cd /www/wwwroot/qz2.ka57.net && php think xadmin:worker reload && echo "平滑重启成功" || echo "平滑重启失败，退出码: $?"

    # 停顿 2 秒，防止 SFTP 一次性上传多个文件导致进程被疯狂重启 (防抖)
    sleep 2
done