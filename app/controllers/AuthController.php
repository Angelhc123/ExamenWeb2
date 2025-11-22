<?php
/**
 * Controlador de Autenticación
 */

class AuthController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Ruta por defecto - redirige al login
     */
    public function index() {
        $this->login();
    }
    
    /**
     * Mostrar formulario de login
     */
    public function login() {
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['usuario_logueado'])) {
            header('Location: ' . BASE_URL . 'atenciones');
            exit;
        }
        
        require_once APP_PATH . 'views/auth/login.php';
    }
    
    /**
     * Procesar login
     */
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL);
            exit;
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Usuario y contraseña son obligatorios';
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Buscar usuario en la base de datos
        $stmt = $this->db->prepare("
            SELECT u.*, d.nombres as docente_nombres, d.apellidos as docente_apellidos 
            FROM usuarios u 
            LEFT JOIN docentes d ON u.docente_id = d.id 
            WHERE u.username = ? AND u.activo = 1
        ");
        $stmt->execute([$username]);
        $usuario = $stmt->fetch();
        
        if ($usuario && $password === $usuario['password']) {
            // Login exitoso
            $_SESSION['usuario_logueado'] = true;
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['username'] = $usuario['username'];
            $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['docente_id'] = $usuario['docente_id'];
            
            // Actualizar último acceso
            $stmt = $this->db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
            $stmt->execute([$usuario['id']]);
            
            $_SESSION['success'] = 'Bienvenido, ' . $usuario['nombre_completo'];
            header('Location: ' . BASE_URL . 'atenciones');
        } else {
            $_SESSION['error'] = 'Usuario o contraseña incorrectos';
            header('Location: ' . BASE_URL);
        }
        exit;
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        header('Location: ' . BASE_URL);
        exit;
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public static function verificarAutenticacion() {
        if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
            header('Location: ' . BASE_URL);
            exit;
        }
    }
    
    /**
     * Verificar rol de usuario
     */
    public static function verificarRol($rolesPermitidos = []) {
        self::verificarAutenticacion();
        
        if (!empty($rolesPermitidos) && !in_array($_SESSION['rol'], $rolesPermitidos)) {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta sección';
            header('Location: ' . BASE_URL . 'atenciones');
            exit;
        }
    }
}
?>