# 🚀 PASOS RÁPIDOS PARA EL SISTEMA

## 🏠 CONFIGURACIÓN LOCAL (DESARROLLO)

### ✅ 1. PREPARAR SISTEMA LOCAL

1. **Importar Base de Datos Local:**
   - Abre HeidiSQL o phpMyAdmin
   - Conecta a tu MySQL local (localhost)
   - Ejecuta el archivo `consejeria_db.sql`
   - Verifica que se creó la BD `consejeria_tutoria`

2. **Verificar Instalación:**
   ```bash
   # En la carpeta del proyecto
   php verificar_sistema.php
   ```

3. **Acceder al Sistema:**
   - URL: `http://localhost/consejeria-tutoria/`
   - Usuario: `admin` | Clave: `admin123`

### ✅ 2. ESTRUCTURA DE RUTAS
- `/` → Página de inicio (redirige según autenticación)
- `/auth/login` → Formulario de login
- `/atenciones` → Lista de atenciones (requiere login)
- `/atenciones/crear` → Nueva atención (requiere login)
- `/reportes` → Dashboard de reportes (requiere login)

---

## 📋 CHECKLIST DESPLIEGUE EN RAILWAY (PRODUCCIÓN)

### ✅ 1. IMPORTAR BASE DE DATOS A RAILWAY

**Opción A: Usando HeidiSQL (Recomendado)**
1. Abre HeidiSQL
2. Click en "Nueva" sesión
3. Configuración:
   ```
   Host: shuttle.proxy.rlwy.net
   Puerto: 14956
   Usuario: root
   Contraseña: khXYnvvIiNkJklkDngAcGDkLvLSKToZH
   Base de datos: railway
   ```
4. Click "Abrir"
5. Menú: Archivo > Ejecutar archivo SQL
6. Selecciona `consejeria_db.sql`
7. Click "Ejecutar"

**Opción B: Usando el script automatizado**
```powershell
cd consejeria-tutoria
.\importar_railway.ps1
```

### ✅ 2. SUBIR A GITHUB

```powershell
# Navega a la carpeta del proyecto
cd C:\Users\HP\Documents\Downloads\consejeria-tutoria

# Inicializar git (si no está inicializado)
git init

# Agregar todos los archivos
git add .

# Hacer commit
git commit -m "Sistema de Consejeria y Tutoria - Listo para Railway"

# Configurar repositorio remoto
git branch -M main
git remote add origin https://github.com/Angelhc123/ExamenWeb2.git

# Subir a GitHub
git push -u origin main --force
```

### ✅ 3. DESPLEGAR EN RAILWAY

1. Ve a https://railway.app
2. Login con GitHub
3. Click "New Project"
4. Selecciona "Deploy from GitHub repo"
5. Busca y selecciona **ExamenWeb2**
6. Railway iniciará el despliegue automáticamente

### ✅ 4. CONFIGURAR VARIABLES DE ENTORNO

En Railway Dashboard:
1. Click en tu proyecto
2. Ve a la pestaña **Variables**
3. Agrega estas variables:

```
RAILWAY_ENVIRONMENT=production
DATABASE_URL=mysql://root:khXYnvvIiNkJklkDngAcGDkLvLSKToZH@shuttle.proxy.rlwy.net:14956/railway
```

### ✅ 5. GENERAR DOMINIO PÚBLICO

1. En Railway, ve a **Settings**
2. Sección "Domains"
3. Click "Generate Domain"
4. Railway te dará una URL como: `https://tu-app.up.railway.app`

### ✅ 6. VERIFICAR FUNCIONAMIENTO

1. Abre la URL que Railway te dio
2. Deberías ver el sistema funcionando
3. Prueba crear una atención
4. Revisa los reportes

---

## ⚡ COMANDOS RÁPIDOS

### Ver el estado del repositorio:
```powershell
git status
```

### Ver los logs de Railway:
- Ir a Railway Dashboard > Tu Proyecto > Deployments > Ver Logs

### Actualizar el proyecto después de cambios:
```powershell
git add .
git commit -m "Descripción de cambios"
git push origin main
```
Railway se redesplegará automáticamente.

---

## 🔍 VERIFICAR CONEXIÓN A BD RAILWAY

Prueba rápida desde PowerShell:
```powershell
php -r "try { \$pdo = new PDO('mysql:host=shuttle.proxy.rlwy.net;port=14956;dbname=railway', 'root', 'khXYnvvIiNkJklkDngAcGDkLvLSKToZH'); echo 'CONEXION EXITOSA\n'; } catch(PDOException \$e) { echo 'ERROR: ' . \$e->getMessage() . '\n'; }"
```

---

## 📱 ACCESOS RÁPIDOS

- **Railway Dashboard**: https://railway.app/dashboard
- **GitHub Repo**: https://github.com/Angelhc123/ExamenWeb2
- **Tu App (después del deploy)**: [La URL que Railway genere]

---

## ⚠️ SOLUCIÓN DE PROBLEMAS

### Error: "Could not connect to database"
- Verifica que la variable `DATABASE_URL` esté correcta
- Asegúrate de haber importado el SQL
- Revisa los logs de Railway

### Error 500
- Ve a Railway > Logs para ver el error exacto
- Verifica que todas las tablas existan
- Comprueba que `railway.json` y `nixpacks.toml` estén en la raíz

### Página en blanco
- Verifica que `public/index.php` exista
- Revisa los logs de PHP en Railway
- Asegúrate de que el `RewriteBase` sea `/` en `.htaccess`

---

## 📞 DATOS DE CONEXIÓN RAILWAY

**MySQL Railway:**
- Host: `shuttle.proxy.rlwy.net`
- Port: `14956`
- User: `root`
- Password: `khXYnvvIiNkJklkDngAcGDkLvLSKToZH`
- Database: `railway`

**String de conexión completa:**
```
mysql://root:khXYnvvIiNkJklkDngAcGDkLvLSKToZH@shuttle.proxy.rlwy.net:14956/railway
```

---

¡Todo listo para desplegar! 🎉
