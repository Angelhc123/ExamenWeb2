# Script PowerShell para importar RAILWAY_DB.SQL a Railway

Write-Host "========================================" -ForegroundColor Cyan
Write-Host " IMPORTAR BASE DE DATOS A RAILWAY" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$MYSQL_HOST = "shuttle.proxy.rlwy.net"
$MYSQL_PORT = "14956"
$MYSQL_USER = "root"
$MYSQL_PASS = "khXYnvvIiNkJklkDngAcGDkLvLSKToZH"
$MYSQL_DB = "railway"

Write-Host "IMPORTANTE: Este script usa railway_db.sql" -ForegroundColor Yellow
Write-Host "Archivo optimizado para Railway (sin CREATE DATABASE)" -ForegroundColor Yellow
Write-Host ""
Write-Host "Conectando a Railway MySQL..." -ForegroundColor Green
Write-Host "Host: $MYSQL_HOST`:$MYSQL_PORT"
Write-Host "Database: $MYSQL_DB"
Write-Host ""

# Verificar si el archivo SQL existe
if (-not (Test-Path "railway_db.sql")) {
    Write-Host "ERROR: No se encontró railway_db.sql" -ForegroundColor Red
    pause
    exit 1
}

# Verificar si mysql está disponible
$mysqlPath = Get-Command mysql -ErrorAction SilentlyContinue

if (-not $mysqlPath) {
    Write-Host "ERROR: MySQL client no encontrado" -ForegroundColor Red
    Write-Host ""
    Write-Host "=== USA HEIDISQL MANUALMENTE ===" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "1. Abre HeidiSQL" -ForegroundColor Cyan
    Write-Host "2. Nueva sesión > MySQL (TCP/IP)" -ForegroundColor Cyan
    Write-Host "3. Configuración:" -ForegroundColor Cyan
    Write-Host "   - Hostname: $MYSQL_HOST" -ForegroundColor White
    Write-Host "   - Port: $MYSQL_PORT" -ForegroundColor White
    Write-Host "   - User: $MYSQL_USER" -ForegroundColor White
    Write-Host "   - Password: $MYSQL_PASS" -ForegroundColor White
    Write-Host "4. Click 'Open'" -ForegroundColor Cyan
    Write-Host "5. Selecciona database 'railway'" -ForegroundColor Cyan
    Write-Host "6. File > Load SQL file > railway_db.sql" -ForegroundColor Cyan
    Write-Host "7. Click 'Execute' (F9)" -ForegroundColor Cyan
    Write-Host ""
    pause
    exit 1
}

Write-Host "Importando railway_db.sql..." -ForegroundColor Yellow

try {
    Get-Content "railway_db.sql" -Raw | mysql -h $MYSQL_HOST -P $MYSQL_PORT -u $MYSQL_USER -p$MYSQL_PASS $MYSQL_DB 2>&1
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "========================================" -ForegroundColor Green
        Write-Host " ✓ IMPORTACION EXITOSA!" -ForegroundColor Green
        Write-Host "========================================" -ForegroundColor Green
        Write-Host ""
        Write-Host "Usuarios creados:" -ForegroundColor Cyan
        Write-Host "  - admin / admin123" -ForegroundColor White
        Write-Host "  - docente1 / docente123" -ForegroundColor White
        Write-Host "  - coordinador / coord123" -ForegroundColor White
        Write-Host ""
        Write-Host "Ahora puedes acceder a tu app en Railway!" -ForegroundColor Green
    } else {
        Write-Host ""
        Write-Host "ERROR: La importación falló" -ForegroundColor Red
    }
} catch {
    Write-Host ""
    Write-Host "ERROR: $_" -ForegroundColor Red
}

Write-Host ""
pause
