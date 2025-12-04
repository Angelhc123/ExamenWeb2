<?php
/**
 * Configuración de Base de Datos
 * Sistema de Registro de Atenciones de Consejería y Tutoría
 */

// Configuración para Railway (producción) o local (desarrollo)
if (getenv('RAILWAY_ENVIRONMENT')) {
    // Configuración de Railway
    $dbUrl = getenv('DATABASE_URL') ?: 'mysql://root:khXYnvvIiNkJklkDngAcGDkLvLSKToZH@shuttle.proxy.rlwy.net:14956/railway';
    $dbParts = parse_url($dbUrl);
    
    define('DB_HOST', $dbParts['host']);
    define('DB_PORT', $dbParts['port']);
    define('DB_NAME', ltrim($dbParts['path'], '/'));
    define('DB_USER', $dbParts['user']);
    define('DB_PASS', $dbParts['pass']);
} else {
    // Configuración local
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_NAME', 'consejeria_tutoria');
    define('DB_USER', 'root');
    define('DB_PASS', '');
}
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Evitar clonación
    private function __clone() {}
    
    // Evitar deserialización
    public function __wakeup() {
        throw new Exception("No se puede deserializar un singleton.");
    }
}
