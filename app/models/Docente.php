<?php
/**
 * Modelo Docente
 */

class Docente {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los docentes activos
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM docentes WHERE activo = 1 ORDER BY apellidos, nombres");
        return $stmt->fetchAll();
    }
    
    /**
     * Buscar docente por ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM docentes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Buscar docente por código
     */
    public function findByCodigo($codigo) {
        $stmt = $this->db->prepare("SELECT * FROM docentes WHERE codigo = ? AND activo = 1");
        $stmt->execute([$codigo]);
        return $stmt->fetch();
    }
    
    /**
     * Crear nuevo docente
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO docentes (codigo, nombres, apellidos, email, telefono, especialidad) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['codigo'],
            $data['nombres'],
            $data['apellidos'],
            $data['email'],
            $data['telefono'],
            $data['especialidad']
        ]);
    }
    
    /**
     * Actualizar docente
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE docentes 
            SET nombres = ?, apellidos = ?, email = ?, telefono = ?, especialidad = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['nombres'],
            $data['apellidos'],
            $data['email'],
            $data['telefono'],
            $data['especialidad'],
            $id
        ]);
    }
    
    /**
     * Obtener estadísticas de atenciones por docente
     */
    public function getEstadisticas($docenteId, $semestreId = null) {
        if ($semestreId) {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_atenciones,
                    COUNT(DISTINCT estudiante_id) as estudiantes_atendidos,
                    COUNT(DISTINCT tema_id) as temas_tratados
                FROM atenciones 
                WHERE docente_id = ? AND semestre_id = ?
            ");
            $stmt->execute([$docenteId, $semestreId]);
        } else {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_atenciones,
                    COUNT(DISTINCT estudiante_id) as estudiantes_atendidos,
                    COUNT(DISTINCT tema_id) as temas_tratados
                FROM atenciones 
                WHERE docente_id = ?
            ");
            $stmt->execute([$docenteId]);
        }
        return $stmt->fetch();
    }
}
