<?php
/**
 * Modelo Tema de Consejería
 */

class TemaConsejeria {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los temas activos
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM temas_consejeria WHERE activo = 1 ORDER BY categoria, nombre");
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener temas por categoría
     */
    public function getByCategoria($categoria) {
        $stmt = $this->db->prepare("SELECT * FROM temas_consejeria WHERE categoria = ? AND activo = 1 ORDER BY nombre");
        $stmt->execute([$categoria]);
        return $stmt->fetchAll();
    }
    
    /**
     * Buscar tema por ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM temas_consejeria WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtener todas las categorías
     */
    public function getCategorias() {
        return [
            'plan_estudios' => 'Asuntos relacionados con el plan de estudios',
            'desarrollo_profesional' => 'Asuntos relacionados con el desarrollo profesional',
            'insercion_laboral' => 'Asuntos relacionados con la inserción laboral',
            'plan_tesis' => 'Asuntos Académicos del Proceso de Plan de Tesis o Tesis',
            'otros' => 'Otros'
        ];
    }
    
    /**
     * Crear nuevo tema
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO temas_consejeria (nombre, categoria, descripcion) 
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([
            $data['nombre'],
            $data['categoria'],
            $data['descripcion']
        ]);
    }
}
