<?php
/**
 * Archivo de entrada principal - DIRECTO AL LOGIN
 */

require_once __DIR__ . '/../config/config.php';

// Verificar si ya está logueado
if (isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado'] === true) {
    // Ya está logueado, mostrar sistema principal
    $url = trim($_SERVER['REQUEST_URI'], '/');
    
    if (strpos($url, 'atenciones') !== false) {
        // Ir a atenciones
        require_once APP_PATH . 'controllers/AtencionController.php';
        $controller = new AtencionController();
        
        if (strpos($url, 'crear') !== false) {
            $controller->crear();
        } elseif (strpos($url, 'guardar') !== false) {
            $controller->guardar();
        } else {
            $controller->index();
        }
        
    } elseif (strpos($url, 'reportes') !== false) {
        // Ir a reportes
        require_once APP_PATH . 'controllers/ReporteController.php';
        $controller = new ReporteController();
        $controller->index();
        
    } elseif (strpos($url, 'logout') !== false) {
        // Logout DIRECTO
        $_SESSION = array();
        session_destroy();
        header('Location: ' . BASE_URL);
        exit;
        
    } else {
        // Por defecto, ir a atenciones
        require_once APP_PATH . 'controllers/AtencionController.php';
        $controller = new AtencionController();
        $controller->index();
    }
    
} else {
    // No está logueado
    
    if (strpos($_SERVER['REQUEST_URI'], 'authenticate') !== false && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Procesar login DIRECTO
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Usuario y contraseña son obligatorios';
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Conectar a BD y verificar usuario
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE username = ? AND activo = 1");
            $stmt->execute([$username]);
            $usuario = $stmt->fetch();
            
            if ($usuario && $password === $usuario['password']) {
                // Login exitoso
                $_SESSION['usuario_logueado'] = true;
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['username'] = $usuario['username'];
                $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
                $_SESSION['rol'] = $usuario['rol'];
                $_SESSION['success'] = 'Bienvenido, ' . $usuario['nombre_completo'];
                
                // Actualizar último acceso
                $stmt = $db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
                $stmt->execute([$usuario['id']]);
                
                header('Location: ' . BASE_URL . 'atenciones');
                exit;
            } else {
                $_SESSION['error'] = 'Usuario o contraseña incorrectos';
                header('Location: ' . BASE_URL);
                exit;
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error del sistema: ' . $e->getMessage();
            header('Location: ' . BASE_URL);
            exit;
        }
    } else {
        // Mostrar vista de login directamente
        require_once APP_PATH . 'views/auth/login.php';
    }
}
?>
