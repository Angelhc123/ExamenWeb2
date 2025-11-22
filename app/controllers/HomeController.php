<?php
/**
 * Controlador Principal/Inicio
 */

class HomeController {
    
    /**
     * Página de inicio - redirige según autenticación
     */
    public function index() {
        // Si está autenticado, ir al dashboard
        if (isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado'] === true) {
            header('Location: ' . BASE_URL . 'atenciones');
            exit;
        }
        
        // Si no está autenticado, ir al login
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }
}
?>