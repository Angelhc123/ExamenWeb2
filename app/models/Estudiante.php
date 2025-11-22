<?php
/**
 * Modelo Estudiante
 */

class Estudiante {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los estudiantes activos
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM estudiantes WHERE activo = 1 ORDER BY apellidos, nombres");
        return $stmt->fetchAll();
    }
    
    /**
     * Buscar estudiante por código
     */
    public function findByCodigo($codigo) {
        $stmt = $this->db->prepare("SELECT * FROM estudiantes WHERE codigo = ? AND activo = 1");
        $stmt->execute([$codigo]);
        return $stmt->fetch();
    }
    
    /**
     * Buscar estudiante por ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM estudiantes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Buscar estudiantes por nombre o apellido
     */
    public function search($termino) {
        $termino = "%$termino%";
        $stmt = $this->db->prepare("
            SELECT * FROM estudiantes 
            WHERE (nombres LIKE ? OR apellidos LIKE ? OR codigo LIKE ?) 
            AND activo = 1 
            ORDER BY apellidos, nombres
        ");
        $stmt->execute([$termino, $termino, $termino]);
        return $stmt->fetchAll();
    }
    
    /**
     * Crear nuevo estudiante
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO estudiantes (codigo, nombres, apellidos, email, telefono, carrera) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['codigo'],
            $data['nombres'],
            $data['apellidos'],
            $data['email'],
            $data['telefono'],
            $data['carrera']
        ]);
    }
    
    /**
     * Actualizar estudiante
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE estudiantes 
            SET nombres = ?, apellidos = ?, email = ?, telefono = ?, carrera = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['nombres'],
            $data['apellidos'],
            $data['email'],
            $data['telefono'],
            $data['carrera'],
            $id
        ]);
    }
    
    /**
     * Obtener número de atenciones de un estudiante
     */
    public function getNumeroAtenciones($estudianteId, $semestreId = null) {
        if ($semestreId) {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM atenciones 
                WHERE estudiante_id = ? AND semestre_id = ?
            ");
            $stmt->execute([$estudianteId, $semestreId]);
        } else {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM atenciones 
                WHERE estudiante_id = ?
            ");
            $stmt->execute([$estudianteId]);
        }
        $result = $stmt->fetch();
        return $result['total'];
    }
}
