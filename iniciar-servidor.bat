@echo off
REM Inicia el panel Lusa Gestion en el puerto 5080, accesible por Tailscale y red local.
REM Los puertos 8000 y 8001 quedan libres para los otros proyectos.
cd /d "%~dp0"
php artisan optimize:clear
echo.
echo Panel disponible en:
echo   http://localhost:5080/admin
echo   http://100.97.235.20:5080/admin  (Tailscale)
echo.
php artisan serve --host=0.0.0.0 --port=5080
pause
