@echo off
chcp 65001 > nul
echo ============================================
echo   IMPORTAR BASE DE DATOS A RAILWAY
echo ============================================
echo.
echo ⚠️  IMPORTANTE: Tienes que importar railway_db.sql
echo.
echo 📁 Archivo: railway_db.sql
echo 🗄️  Destino: Base de datos Railway
echo.
echo ============================================
echo   OPCIÓN 1: HEIDI SQL (RECOMENDADO)
echo ============================================
echo.
echo 1. Abre HeidiSQL
echo 2. Nueva sesión ^> MySQL (TCP/IP)
echo 3. Configuración:
echo    - Hostname: shuttle.proxy.rlwy.net
echo    - Port: 14956
echo    - User: root
echo    - Password: khXYnvvIiNkJklkDngAcGDkLvLSKToZH
echo 4. Click "Open"
echo 5. Selecciona database "railway" (izquierda)
echo 6. File ^> Load SQL file ^> railway_db.sql
echo 7. Click "Execute" (F9)
echo.
echo ============================================
echo   OPCIÓN 2: MYSQL COMMAND LINE
echo ============================================
echo.
echo mysql -h shuttle.proxy.rlwy.net -P 14956 -u root -pkhXYnvvIiNkJklkDngAcGDkLvLSKToZH railway ^< railway_db.sql
echo.
echo ============================================
echo.
pause
