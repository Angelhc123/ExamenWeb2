<?php
/**
 * Verificar conexión a base de datos y crear datos de ejemplo si no existen
 */

require_once __DIR__ . '/config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "✅ Conexión a base de datos exitosa\n";
    
    // Verificar si existen datos en usuarios
    $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    
    if ($result['total'] == 0) {
        echo "⚠️ No hay usuarios en la BD. Creando datos de ejemplo...\n";
        
        // Crear usuarios de ejemplo
        $stmt = $db->prepare("INSERT INTO usuarios (username, password, nombre_completo, rol) VALUES (?, ?, ?, ?)");
        $usuarios = [
            ['admin', 'admin123', 'Administrador del Sistema', 'admin'],
            ['docente1', 'docente123', 'María González Pérez', 'docente'],
            ['coordinador', 'coord123', 'Carlos Rodríguez López', 'coordinador']
        ];
        
        foreach ($usuarios as $usuario) {
            $stmt->execute($usuario);
        }
        echo "✅ Usuarios de ejemplo creados\n";
    } else {
        echo "✅ La BD ya tiene {$result['total']} usuarios\n";
    }
    
    // Verificar semestres
    $stmt = $db->query("SELECT COUNT(*) as total FROM semestres");
    $result = $stmt->fetch();
    
    if ($result['total'] == 0) {
        echo "⚠️ No hay semestres. Creando semestre activo...\n";
        $stmt = $db->prepare("INSERT INTO semestres (nombre, fecha_inicio, fecha_fin, activo) VALUES (?, ?, ?, ?)");
        $stmt->execute(['2025-2', '2025-08-01', '2025-12-20', 1]);
        echo "✅ Semestre activo creado\n";
    }
    
    echo "\n🎉 Sistema listo para usar!\n";
    echo "Accede a: http://localhost/consejeria-tutoria/\n";
    echo "Usuario: admin | Clave: admin123\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>