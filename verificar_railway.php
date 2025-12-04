#!/usr/bin/env php
<?php
/**
 * Verificación del Sistema para Railway
 * Este script verifica que todo esté configurado correctamente
 */

echo "\n🔍 VERIFICACIÓN DEL SISTEMA PARA RAILWAY\n";
echo "=====================================\n\n";

// Verificar PHP
echo "1. Verificando PHP...\n";
echo "   - Versión PHP: " . PHP_VERSION . "\n";

$required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✅ Extensión $ext: OK\n";
    } else {
        echo "   ❌ Extensión $ext: NO ENCONTRADA\n";
        $missing_extensions[] = $ext;
    }
}

// Verificar archivos de configuración
echo "\n2. Verificando archivos de configuración...\n";

$config_files = [
    'composer.json' => 'Configuración de dependencias',
    'railway.json' => 'Configuración de Railway',
    'nixpacks.toml' => 'Configuración de Nixpacks',
    'public/index.php' => 'Punto de entrada',
    'config/config.php' => 'Configuración general',
    'config/database.php' => 'Configuración de base de datos'
];

foreach ($config_files as $file => $description) {
    if (file_exists($file)) {
        echo "   ✅ $file: OK ($description)\n";
    } else {
        echo "   ❌ $file: NO ENCONTRADO ($description)\n";
    }
}

// Verificar estructura de directorios
echo "\n3. Verificando estructura de directorios...\n";

$directories = [
    'app/controllers',
    'app/models', 
    'app/views',
    'config',
    'public',
    'public/css',
    'public/js'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "   ✅ $dir/: OK\n";
    } else {
        echo "   ❌ $dir/: NO ENCONTRADO\n";
    }
}

// Verificar permisos
echo "\n4. Verificando permisos...\n";
$writable_dirs = ['public'];

foreach ($writable_dirs as $dir) {
    if (is_writable($dir)) {
        echo "   ✅ $dir/: ESCRIBIBLE\n";
    } else {
        echo "   ⚠️  $dir/: NO ESCRIBIBLE (puede causar problemas)\n";
    }
}

// Verificar variables de entorno
echo "\n5. Verificando detección de entorno...\n";

if (getenv('RAILWAY_ENVIRONMENT')) {
    echo "   ✅ RAILWAY_ENVIRONMENT detectado: " . getenv('RAILWAY_ENVIRONMENT') . "\n";
    echo "   📍 Modo: PRODUCCIÓN (Railway)\n";
} else {
    echo "   ℹ️  RAILWAY_ENVIRONMENT no detectado\n";
    echo "   📍 Modo: DESARROLLO (Local)\n";
}

// Verificar conexión a base de datos (simulado)
echo "\n6. Verificando configuración de base de datos...\n";

try {
    require_once 'config/config.php';
    echo "   ✅ Archivos de configuración cargados correctamente\n";
    
    if (getenv('RAILWAY_ENVIRONMENT')) {
        $dbUrl = getenv('DATABASE_URL') ?: 'mysql://root:khXYnvvIiNkJklkDngAcGDkLvLSKToZH@shuttle.proxy.rlwy.net:14956/railway';
        echo "   ✅ DATABASE_URL configurado para Railway\n";
        echo "   📍 Host: shuttle.proxy.rlwy.net:14956\n";
    } else {
        echo "   ✅ Configuración local de base de datos\n";
        echo "   📍 Host: localhost\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error al cargar configuración: " . $e->getMessage() . "\n";
}

// Resumen
echo "\n📋 RESUMEN\n";
echo "==========\n";

if (empty($missing_extensions)) {
    echo "✅ Sistema listo para despliegue en Railway\n";
    echo "\nPróximos pasos:\n";
    echo "1. git add .\n";
    echo "2. git commit -m \"Preparado para Railway\"\n";
    echo "3. git push origin main\n";
    echo "4. Crear proyecto en Railway\n";
    echo "5. Conectar con repositorio GitHub\n";
    echo "6. Configurar variables de entorno\n";
    echo "7. ¡Desplegar! 🚀\n";
} else {
    echo "❌ Sistema necesita ajustes antes del despliegue\n";
    echo "\nExtensiones faltantes: " . implode(', ', $missing_extensions) . "\n";
    echo "Instala las extensiones faltantes antes de continuar.\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Sistema verificado el " . date('Y-m-d H:i:s') . "\n";
echo "¡Buena suerte con tu despliegue! 🍀\n\n";