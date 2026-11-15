@echo off
echo Creating storage folders if missing...
if not exist "storage\framework\views" mkdir storage\framework\views
if not exist "storage\framework\cache\data" mkdir storage\framework\cache\data
if not exist "storage\framework\sessions" mkdir storage\framework\sessions
if not exist "storage\logs" mkdir storage\logs
if not exist "bootstrap\cache" mkdir bootstrap\cache
echo.
echo Running composer install...
composer install
echo.
if not exist ".env" (
  copy .env.example .env
  php artisan key:generate
)
echo.
echo Run: php artisan migrate
echo Then: php artisan serve
pause
