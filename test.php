<?php
/**
 * Página de prueba del sistema
 */
echo "<h1>🎯 Sistema de Consejería y Tutoría</h1>";
echo "<h2>✅ Estado del Sistema:</h2>";

// Verificar configuración
echo "<p><strong>BASE_URL:</strong> " . (defined('BASE_URL') ? BASE_URL : 'No definido') . "</p>";
echo "<p><strong>APP_PATH:</strong> " . (defined('APP_PATH') ? APP_PATH : 'No definido') . "</p>";

// Verificar autoload
echo "<p><strong>Clases cargadas:</strong></p><ul>";
$classes = ['Database', 'AuthController', 'AtencionController', 'HomeController'];
foreach ($classes as $class) {
    echo "<li>{$class}: " . (class_exists($class) ? '✅' : '❌') . "</li>";
}
echo "</ul>";

// Verificar sesión
echo "<p><strong>Sesión:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? '✅ Activa' : '❌ Inactiva') . "</p>";

// Verificar base de datos
try {
    $db = Database::getInstance()->getConnection();
    echo "<p><strong>Base de datos:</strong> ✅ Conectada</p>";
} catch (Exception $e) {
    echo "<p><strong>Base de datos:</strong> ❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>🔗 Enlaces de prueba:</h2>";
echo "<ul>";
echo "<li><a href='" . BASE_URL . "'>Inicio (Home)</a></li>";
echo "<li><a href='" . BASE_URL . "auth/login'>Login</a></li>";
echo "<li><a href='" . BASE_URL . "atenciones'>Atenciones</a></li>";
echo "<li><a href='" . BASE_URL . "reportes'>Reportes</a></li>";
echo "</ul>";
?>