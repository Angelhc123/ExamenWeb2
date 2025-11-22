<?php
/**
 * Modelo Atención
 */

class Atencion {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todas las atenciones con detalles
     */
    public function getAll($limit = null) {
        $sql = "SELECT * FROM vista_atenciones_detalle";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Buscar atención por ID con detalles
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM vista_atenciones_detalle WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtener atenciones por estudiante
     */
    public function getByEstudiante($estudianteId, $semestreId = null) {
        if ($semestreId) {
            $stmt = $this->db->prepare("
                SELECT * FROM vista_atenciones_detalle 
                WHERE codigo_estudiante = (SELECT codigo FROM estudiantes WHERE id = ?)
                AND semestre = (SELECT nombre FROM semestres WHERE id = ?)
                ORDER BY fecha_atencion DESC
            ");
            $stmt->execute([$estudianteId, $semestreId]);
        } else {
            $stmt = $this->db->prepare("
                SELECT * FROM vista_atenciones_detalle 
                WHERE codigo_estudiante = (SELECT codigo FROM estudiantes WHERE id = ?)
                ORDER BY fecha_atencion DESC
            ");
            $stmt->execute([$estudianteId]);
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener atenciones por docente
     */
    public function getByDocente($docenteId, $semestreId = null) {
        if ($semestreId) {
            $stmt = $this->db->prepare("
                SELECT a.*, 
                       CONCAT(e.nombres, ' ', e.apellidos) as estudiante,
                       e.codigo as codigo_estudiante,
                       tc.nombre as tema
                FROM atenciones a
                INNER JOIN estudiantes e ON a.estudiante_id = e.id
                INNER JOIN temas_consejeria tc ON a.tema_id = tc.id
                WHERE a.docente_id = ? AND a.semestre_id = ?
                ORDER BY a.fecha_atencion DESC, a.hora_inicio DESC
            ");
            $stmt->execute([$docenteId, $semestreId]);
        } else {
            $stmt = $this->db->prepare("
                SELECT a.*, 
                       CONCAT(e.nombres, ' ', e.apellidos) as estudiante,
                       e.codigo as codigo_estudiante,
                       tc.nombre as tema
                FROM atenciones a
                INNER JOIN estudiantes e ON a.estudiante_id = e.id
                INNER JOIN temas_consejeria tc ON a.tema_id = tc.id
                WHERE a.docente_id = ?
                ORDER BY a.fecha_atencion DESC, a.hora_inicio DESC
            ");
            $stmt->execute([$docenteId]);
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener atenciones por semestre
     */
    public function getBySemestre($semestreId) {
        $stmt = $this->db->prepare("
            SELECT * FROM vista_atenciones_detalle 
            WHERE semestre = (SELECT nombre FROM semestres WHERE id = ?)
            ORDER BY fecha_atencion DESC
        ");
        $stmt->execute([$semestreId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Crear nueva atención
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO atenciones (
                estudiante_id, docente_id, semestre_id, tema_id,
                fecha_atencion, hora_inicio, hora_fin,
                consulta_estudiante, descripcion_atencion, evidencia, observaciones
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['estudiante_id'],
            $data['docente_id'],
            $data['semestre_id'],
            $data['tema_id'],
            $data['fecha_atencion'],
            $data['hora_inicio'],
            $data['hora_fin'] ?? null,
            $data['consulta_estudiante'],
            $data['descripcion_atencion'] ?? null,
            $data['evidencia'] ?? null,
            $data['observaciones'] ?? null
        ]);
    }
    
    /**
     * Actualizar atención
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE atenciones 
            SET estudiante_id = ?, docente_id = ?, semestre_id = ?, tema_id = ?,
                fecha_atencion = ?, hora_inicio = ?, hora_fin = ?,
                consulta_estudiante = ?, descripcion_atencion = ?, evidencia = ?, observaciones = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['estudiante_id'],
            $data['docente_id'],
            $data['semestre_id'],
            $data['tema_id'],
            $data['fecha_atencion'],
            $data['hora_inicio'],
            $data['hora_fin'] ?? null,
            $data['consulta_estudiante'],
            $data['descripcion_atencion'] ?? null,
            $data['evidencia'] ?? null,
            $data['observaciones'] ?? null,
            $id
        ]);
    }
    
    /**
     * Eliminar atención
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM atenciones WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Obtener resumen de atenciones (para reportes)
     */
    public function getResumen($semestreId = null) {
        if ($semestreId) {
            $stmt = $this->db->prepare("
                SELECT * FROM vista_atenciones_resumen 
                WHERE semestre = (SELECT nombre FROM semestres WHERE id = ?)
            ");
            $stmt->execute([$semestreId]);
        } else {
            $stmt = $this->db->query("SELECT * FROM vista_atenciones_resumen");
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Contar atenciones por tema
     */
    public function contarPorTema($semestreId = null) {
        if ($semestreId) {
            $stmt = $this->db->prepare("
                SELECT tc.nombre, tc.categoria, COUNT(*) as total
                FROM atenciones a
                INNER JOIN temas_consejeria tc ON a.tema_id = tc.id
                WHERE a.semestre_id = ?
                GROUP BY tc.id
                ORDER BY total DESC
            ");
            $stmt->execute([$semestreId]);
        } else {
            $stmt = $this->db->query("
                SELECT tc.nombre, tc.categoria, COUNT(*) as total
                FROM atenciones a
                INNER JOIN temas_consejeria tc ON a.tema_id = tc.id
                GROUP BY tc.id
                ORDER BY total DESC
            ");
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Validar que no existan atenciones duplicadas en la misma fecha/hora
     */
    public function validarDisponibilidad($docenteId, $fecha, $hora) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM atenciones 
            WHERE docente_id = ? 
            AND fecha_atencion = ? 
            AND hora_inicio = ?
        ");
        $stmt->execute([$docenteId, $fecha, $hora]);
        $result = $stmt->fetch();
        return $result['total'] == 0;
    }
}
