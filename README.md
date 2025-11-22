# Sistema de Consejería y Tutoría

Sistema web para el registro y seguimiento de atenciones de consejería y tutoría a estudiantes.

## Características

- **Registro de Atenciones**: Permite registrar cada atención considerando semestre, fecha, hora, estudiante, docente y tema tratado.
- **Gestión de Temas**: Los temas están organizados en categorías:
  - Plan de estudios
  - Desarrollo profesional
  - Inserción laboral
  - Plan de tesis
  - Otros
- **Reportes y Estadísticas**:
  - Reporte por semestre
  - Reporte por docente
  - Reporte por temas
  - Exportación a CSV
- **Validaciones**: El sistema valida la disponibilidad de docentes y la integridad de los datos.

## Tecnologías Utilizadas

- PHP 7.4+
- MySQL / MariaDB
- Bootstrap 5.3
- Arquitectura MVC (Modelo-Vista-Controlador)

## Instalación

### Opción 1: Despliegue en Railway (Producción) 🚀

**Ver guía completa en:** `PASOS_RAPIDOS.md`

1. Importar `consejeria_db.sql` a Railway MySQL
2. Subir código a GitHub
3. Conectar Railway con el repositorio
4. Configurar variables de entorno
5. ¡Listo! Railway desplegará automáticamente

**Conexión Railway MySQL:**
```
Host: shuttle.proxy.rlwy.net:14956
User: root
Database: railway
```

### Opción 2: Instalación Local (Desarrollo) 💻

#### 1. Requisitos Previos

- XAMPP, WAMP o servidor similar con PHP y MySQL
- HeidiSQL o phpMyAdmin para gestión de base de datos
- Navegador web moderno

#### 2. Configurar Base de Datos Local

1. Abre HeidiSQL y conéctate a tu servidor MySQL local
2. Ejecuta el archivo `consejeria_db.sql` para crear la base de datos y tablas
3. Verifica que la base de datos `consejeria_tutoria` se haya creado correctamente

#### 3. Configurar la Aplicación Local

1. Copia la carpeta `consejeria-tutoria` a tu directorio web (ej: `C:\xampp\htdocs\`)
2. La aplicación ya está configurada para detectar automáticamente el entorno (Railway o Local)
3. Para desarrollo local, no necesitas cambiar nada en `config/database.php`

#### 4. Iniciar el Sistema Local

1. Asegúrate de que Apache y MySQL estén corriendo
2. Abre tu navegador y visita: `http://localhost/consejeria-tutoria/`
3. El sistema mostrará la página principal con el listado de atenciones

## Estructura del Proyecto

```
consejeria-tutoria/
├── app/
│   ├── controllers/
│   │   ├── AtencionController.php
│   │   └── ReporteController.php
│   ├── models/
│   │   ├── Atencion.php
│   │   ├── Docente.php
│   │   ├── Estudiante.php
│   │   ├── Semestre.php
│   │   └── TemaConsejeria.php
│   └── views/
│       ├── atenciones/
│       │   ├── index.php
│       │   ├── crear.php
│       │   └── ver.php
│       ├── reportes/
│       │   ├── index.php
│       │   └── por_semestre.php
│       └── layouts/
│           └── main.php
├── config/
│   ├── config.php
│   └── database.php
├── public/
│   ├── css/
│   │   └── estilos.css
│   ├── js/
│   │   └── main.js
│   └── index.php
└── consejeria_db.sql
```

## Uso del Sistema

### Registrar una Nueva Atención

1. Click en "Nueva Atención" en el menú
2. Selecciona o busca el estudiante por código
3. Selecciona el docente responsable
4. Elige el semestre (por defecto viene el activo)
5. Indica fecha y hora de la atención
6. Selecciona la categoría y tema específico
7. Describe la consulta del estudiante
8. Opcionalmente añade descripción de la atención, evidencia y observaciones
9. Click en "Guardar Atención"

### Ver Reportes

1. Accede a la sección "Reportes"
2. Selecciona el tipo de reporte:
   - **Por Semestre**: Muestra estadísticas generales del semestre seleccionado
   - **Por Docente**: Muestra las atenciones de un docente específico
   - **Por Temas**: Analiza los temas más consultados
3. Exporta los datos a CSV para análisis externo

## Datos de Ejemplo

El sistema incluye datos de ejemplo:
- 3 docentes
- 3 estudiantes
- 2 semestres
- 12 temas de consejería predefinidos

## Personalización

- **Añadir más temas**: Inserta registros en la tabla `temas_consejeria`
- **Gestionar semestres**: Actualiza la tabla `semestres` y activa el semestre actual
- **Añadir usuarios**: Modifica la tabla `usuarios` (las contraseñas se almacenan en texto plano para simplicidad educativa)

## Seguridad

- El sistema utiliza PDO con prepared statements para prevenir SQL injection
- Las sesiones están configuradas con httponly y use_only_cookies
- Los errores se ocultan en producción (cambiar `display_errors` a 0 en `config.php`)

## Soporte

Para reportar problemas o sugerencias, documenta el error y revisa los logs de PHP.

## Licencia

Sistema desarrollado para uso académico y educativo.

---
**Versión**: 1.0.0  
**Fecha**: Noviembre 2025
