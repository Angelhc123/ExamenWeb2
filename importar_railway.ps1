# Script PowerShell para importar la base de datos a Railway

Write-Host "========================================" -ForegroundColor Cyan
Write-Host " IMPORTAR BASE DE DATOS A RAILWAY" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$MYSQL_HOST = "shuttle.proxy.rlwy.net"
$MYSQL_PORT = "14956"
$MYSQL_USER = "root"
$MYSQL_PASS = "khXYnvvIiNkJklkDngAcGDkLvLSKToZH"
$MYSQL_DB = "railway"

Write-Host "Conectando a Railway MySQL..." -ForegroundColor Yellow
Write-Host "Host: $MYSQL_HOST`:$MYSQL_PORT"
Write-Host "Database: $MYSQL_DB"
Write-Host ""

# Verificar si el archivo SQL existe
if (-not (Test-Path "..\consejeria_db.sql")) {
    Write-Host "ERROR: No se encontró el archivo consejeria_db.sql" -ForegroundColor Red
    Write-Host "Asegúrate de ejecutar este script desde la carpeta consejeria-tutoria" -ForegroundColor Red
    pause
    exit 1
}

# Verificar si mysql está disponible
$mysqlPath = Get-Command mysql -ErrorAction SilentlyContinue

if (-not $mysqlPath) {
    Write-Host "ERROR: MySQL client no encontrado" -ForegroundColor Red
    Write-Host ""
    Write-Host "Opciones:" -ForegroundColor Yellow
    Write-Host "1. Usa HeidiSQL para importar manualmente el archivo consejeria_db.sql"
    Write-Host "2. Instala MySQL client desde: https://dev.mysql.com/downloads/mysql/"
    Write-Host ""
    Write-Host "Instrucciones para HeidiSQL:" -ForegroundColor Cyan
    Write-Host "- Host: $MYSQL_HOST"
    Write-Host "- Port: $MYSQL_PORT"
    Write-Host "- User: $MYSQL_USER"
    Write-Host "- Password: $MYSQL_PASS"
    Write-Host "- Database: $MYSQL_DB"
    Write-Host "- Luego: File > Load SQL file > Selecciona consejeria_db.sql > Run"
    Write-Host ""
    pause
    exit 1
}

Write-Host "Importando consejeria_db.sql..." -ForegroundColor Yellow

$sqlFile = Get-Content "..\consejeria_db.sql" -Raw
$sqlFile | mysql -h $MYSQL_HOST -P $MYSQL_PORT -u $MYSQL_USER -p$MYSQL_PASS $MYSQL_DB 2>&1

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host " IMPORTACION EXITOSA!" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "La base de datos ha sido importada correctamente." -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "ERROR: La importación falló" -ForegroundColor Red
    Write-Host "Verifica la conexión y las credenciales." -ForegroundColor Red
}

Write-Host ""
pause
