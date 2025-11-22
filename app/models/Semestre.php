<?php
/**
 * Modelo Semestre
 */

class Semestre {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los semestres
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM semestres ORDER BY fecha_inicio DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener el semestre activo
     */
    public function getActivo() {
        $stmt = $this->db->query("SELECT * FROM semestres WHERE activo = 1 LIMIT 1");
        return $stmt->fetch();
    }
    
    /**
     * Buscar semestre por ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM semestres WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Crear nuevo semestre
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO semestres (nombre, fecha_inicio, fecha_fin, activo) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['nombre'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $data['activo'] ?? 0
        ]);
    }
    
    /**
     * Activar un semestre (y desactivar los demás)
     */
    public function activar($id) {
        try {
            $this->db->beginTransaction();
            
            // Desactivar todos
            $this->db->exec("UPDATE semestres SET activo = 0");
            
            // Activar el seleccionado
            $stmt = $this->db->prepare("UPDATE semestres SET activo = 1 WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
