@echo off
:: Windows Git Push Automation Helper
:: Usage: commit "feat: your commit message"

if "%~1" == "" (
    echo [ERROR] Please provide a git commit message!
    echo Usage: commit "feat: add hlw-status-bar"
    exit /b 1
)

node "%~dp0git-push.js" %*
