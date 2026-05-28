@echo off
title ThinkPHP Service Starter

:MENU
cls
echo ===================================================
echo.
echo           ThinkPHP 8.2 Service Starter
echo.
echo ===================================================
echo.
echo   [1] Start default dev server (http://127.0.0.1:8000)
echo   [2] Start LAN dev server     (http://192.168.5.12:8000)
echo   [3] Start with custom IP and Port
echo   [4] Exit
echo.
echo ===================================================
echo.

:: Check if php command exists in PATH
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] PHP command not found. Please ensure PHP is installed and added to PATH.
    echo.
    pause
    exit /b 1
)

set "choice="
set /p "choice=Please enter your choice [1-4] (default is 1): "
if "%choice%"=="" set choice=1

if "%choice%"=="1" goto DEFAULT_RUN
if "%choice%"=="2" goto LAN_RUN
if "%choice%"=="3" goto CUSTOM_RUN
if "%choice%"=="4" goto EXIT
goto MENU

:DEFAULT_RUN
echo.
echo [INFO] Starting default server...
echo [INFO] Please open in your browser: http://127.0.0.1:8000
echo [INFO] Press Ctrl + C and enter Y to stop the server.
echo ---------------------------------------------------
php think run
goto PAUSE_END

:LAN_RUN
echo.
echo [INFO] Starting LAN server...
echo [INFO] Please open in your browser: http://192.168.5.12:8000
echo [INFO] Press Ctrl + C and enter Y to stop the server.
echo ---------------------------------------------------
php think run --host 192.168.5.12 --port 8000
goto PAUSE_END

:CUSTOM_RUN
echo.
set "HOST=127.0.0.1"
set "PORT=8000"
set /p "INPUT_HOST=Enter IP address (default: 127.0.0.1): "
if not "%INPUT_HOST%"=="" set "HOST=%INPUT_HOST%"
set /p "INPUT_PORT=Enter Port number (default: 8000): "
if not "%INPUT_PORT%"=="" set "PORT=%INPUT_PORT%"
echo.
echo [INFO] Starting custom server...
echo [INFO] Please open in your browser: http://%HOST%:%PORT%
echo [INFO] Press Ctrl + C and enter Y to stop the server.
echo ---------------------------------------------------
php think run --host %HOST% --port %PORT%
goto PAUSE_END

:PAUSE_END
echo.
echo Service stopped.
pause
goto MENU

:EXIT
exit
