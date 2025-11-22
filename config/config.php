<?php
/**
 * Configuración General del Sistema
 */

// Configuración de la aplicación
define('APP_NAME', 'Sistema de Consejería y Tutoría');
define('APP_VERSION', '1.0.0');

// Detectar URL base automáticamente
if (getenv('RAILWAY_ENVIRONMENT')) {
    // En Railway
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('BASE_URL', $protocol . $host . '/');
} else {
    // En desarrollo local
    define('BASE_URL', 'http://localhost/consejeria-tutoria/');
}

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Zona horaria
date_default_timezone_set('America/Lima');

// Mostrar errores solo en desarrollo
if (getenv('RAILWAY_ENVIRONMENT')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Rutas del sistema
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT_PATH . 'config' . DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', ROOT_PATH . 'public' . DIRECTORY_SEPARATOR);

// Autoload de clases
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . 'models' . DIRECTORY_SEPARATOR,
        APP_PATH . 'controllers' . DIRECTORY_SEPARATOR,
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Incluir base de datos
require_once CONFIG_PATH . 'database.php';
