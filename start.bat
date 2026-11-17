@echo off
title Hall-Ease Application Launcher
echo ===================================================
echo               Starting Hall-Ease
echo ===================================================

echo [1/2] Checking and starting database on port 3307...
netstat -ano | findstr :3307 >nul
if %errorlevel% neq 0 (
    start /B "" "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqld.exe" --defaults-file="C:\laragon\bin\mysql\mysql-8.0.30-winx64\my.ini" --port=3307
    timeout /t 2 /nobreak >nul
) else (
    echo Database is already running on port 3307.
)

echo [2/2] Opening application in your default browser...
start http://127.0.0.1:8000

echo ===================================================
echo  Hall-Ease server is running at http://127.0.0.1:8000
echo  Press Ctrl+C to stop the server when done.
echo ===================================================
php artisan serve --port=8000
