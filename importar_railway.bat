@echo off
echo ========================================
echo  IMPORTAR BASE DE DATOS A RAILWAY
echo ========================================
echo.

set MYSQL_HOST=shuttle.proxy.rlwy.net
set MYSQL_PORT=14956
set MYSQL_USER=root
set MYSQL_PASS=khXYnvvIiNkJklkDngAcGDkLvLSKToZH
set MYSQL_DB=railway

echo Conectando a Railway MySQL...
echo Host: %MYSQL_HOST%:%MYSQL_PORT%
echo Database: %MYSQL_DB%
echo.

REM Verificar si mysql está disponible
where mysql >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: MySQL client no encontrado
    echo.
    echo Opciones:
    echo 1. Usa HeidiSQL para importar manualmente
    echo 2. Instala MySQL client desde: https://dev.mysql.com/downloads/mysql/
    echo.
    pause
    exit /b 1
)

echo Importando consejeria_db.sql...
mysql -h %MYSQL_HOST% -P %MYSQL_PORT% -u %MYSQL_USER% -p%MYSQL_PASS% %MYSQL_DB% < ..\consejeria_db.sql

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo  IMPORTACION EXITOSA!
    echo ========================================
    echo.
    echo La base de datos ha sido importada correctamente.
) else (
    echo.
    echo ERROR: La importacion fallo
    echo Verifica la conexion y las credenciales.
)

echo.
pause
