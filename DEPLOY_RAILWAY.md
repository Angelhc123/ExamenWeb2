# 🚀 Guía de Despliegue en Railway

## Configuración Completada

✅ La aplicación ya está configurada para detectar automáticamente si está en Railway o en local.

## Pasos para Desplegar en Railway

### 1️⃣ Preparar la Base de Datos

Ya tienes MySQL desplegado en Railway con esta conexión:
```
mysql://root:khXYnvvIiNkJklkDngAcGDkLvLSKToZH@shuttle.proxy.rlwy.net:14956/railway
```

**Importar la base de datos:**

1. Conéctate a tu base de datos Railway usando HeidiSQL o MySQL Workbench:
   - **Host**: `shuttle.proxy.rlwy.net`
   - **Port**: `14956`
   - **Usuario**: `root`
   - **Contraseña**: `khXYnvvIiNkJklkDngAcGDkLvLSKToZH`
   - **Base de datos**: `railway`

2. Ejecuta el script SQL `consejeria_db.sql` completo

### 2️⃣ Subir el Proyecto a GitHub

```powershell
# Inicializar git (si no está inicializado)
git init

# Añadir todos los archivos
git add .

# Hacer commit
git commit -m "Initial commit - Sistema de Consejería y Tutoría"

# Conectar con tu repositorio (ya tienes ExamenWeb2)
git remote add origin https://github.com/Angelhc123/ExamenWeb2.git

# O si ya existe el remote:
git remote set-url origin https://github.com/Angelhc123/ExamenWeb2.git

# Subir a GitHub
git push -u origin main
```

### 3️⃣ Crear Proyecto en Railway

1. Ve a [railway.app](https://railway.app)
2. Click en **"New Project"**
3. Selecciona **"Deploy from GitHub repo"**
4. Autoriza Railway a acceder a tu GitHub
5. Selecciona el repositorio **ExamenWeb2**

### 4️⃣ Configurar Variables de Entorno en Railway

En el dashboard de Railway, ve a **Variables** y añade:

```
RAILWAY_ENVIRONMENT=production
DATABASE_URL=mysql://root:khXYnvvIiNkJklkDngAcGDkLvLSKToZH@shuttle.proxy.rlwy.net:14956/railway
```

### 5️⃣ Conectar con MySQL Service

En Railway:
1. Click en **"New"** → **"Database"** → **"Add MySQL"**
2. Railway creará automáticamente las variables de entorno
3. O usa tu base de datos existente configurando la URL manualmente

### 6️⃣ Verificar el Despliegue

Railway automáticamente:
- ✅ Detectará que es un proyecto PHP
- ✅ Instalará las dependencias con Composer
- ✅ Ejecutará el servidor PHP
- ✅ Te dará una URL pública (ej: `https://tu-proyecto.up.railway.app`)

### 7️⃣ Acceder a tu Aplicación

Una vez desplegado:
1. Railway te mostrará la URL pública
2. Accede a esa URL en tu navegador
3. El sistema debería funcionar exactamente igual que en local

## 🔧 Comandos Útiles

### Verificar logs en Railway:
```
En el dashboard → Click en "View Logs"
```

### Actualizar el despliegue:
```powershell
git add .
git commit -m "Actualización del sistema"
git push origin main
# Railway se desplegará automáticamente
```

### Verificar conexión local a Railway DB:
```powershell
# Prueba de conexión usando PowerShell
php -r "try { \$pdo = new PDO('mysql:host=shuttle.proxy.rlwy.net:14956;dbname=railway', 'root', 'khXYnvvIiNkJklkDngAcGDkLvLSKToZH'); echo 'Conexión exitosa'; } catch(PDOException \$e) { echo 'Error: ' . \$e->getMessage(); }"
```

## 📝 Notas Importantes

1. **Base de Datos**: El script SQL ya está configurado con datos de ejemplo (docentes, estudiantes, temas, semestres)

2. **URL Base**: La aplicación detecta automáticamente la URL en Railway, no necesitas cambiar nada

3. **Errores**: En producción (Railway), los errores no se muestran en pantalla pero se registran en logs

4. **Archivos estáticos**: Bootstrap y otros recursos CDN funcionarán sin problemas

5. **Seguridad**: 
   - El archivo `.gitignore` ya está configurado
   - No subas archivos `.env` con credenciales sensibles
   - Las credenciales de Railway están en variables de entorno

## ⚠️ Troubleshooting

### Error de conexión a base de datos:
- Verifica que las variables de entorno estén correctas
- Asegúrate de que la base de datos Railway esté activa
- Revisa los logs en Railway

### Error 500:
- Revisa los logs de Railway
- Verifica que todas las tablas estén creadas
- Comprueba permisos de archivos

### Página en blanco:
- Verifica que el archivo `public/index.php` exista
- Revisa la configuración de `railway.json`
- Comprueba los logs de PHP

## 🎯 Checklist de Despliegue

- [ ] Base de datos MySQL activa en Railway
- [ ] Script SQL ejecutado en Railway DB
- [ ] Código subido a GitHub (ExamenWeb2)
- [ ] Proyecto creado en Railway
- [ ] Variables de entorno configuradas
- [ ] Despliegue exitoso
- [ ] Aplicación accesible desde URL pública
- [ ] Funcionalidades probadas (crear atención, ver reportes, etc.)

## 🌐 URLs del Proyecto

- **GitHub**: https://github.com/Angelhc123/ExamenWeb2
- **Railway**: [Tu URL aquí después del despliegue]

---
¡Tu aplicación está lista para desplegarse en Railway! 🚀
