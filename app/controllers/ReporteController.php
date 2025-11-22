<?php
/**
 * Controlador de Reportes
 */

class ReporteController {
    private $atencionModel;
    private $semestreModel;
    private $docenteModel;
    
    public function __construct() {
        $this->atencionModel = new Atencion();
        $this->semestreModel = new Semestre();
        $this->docenteModel = new Docente();
    }
    
    /**
     * Panel principal de reportes
     */
    public function index() {
        $semestres = $this->semestreModel->getAll();
        $semestreActivo = $this->semestreModel->getActivo();
        
        require_once APP_PATH . 'views/reportes/index.php';
    }
    
    /**
     * Reporte por semestre
     */
    public function porSemestre() {
        $semestreId = $_GET['semestre_id'] ?? null;
        
        if (!$semestreId) {
            $semestreActivo = $this->semestreModel->getActivo();
            $semestreId = $semestreActivo['id'];
        }
        
        $semestre = $this->semestreModel->findById($semestreId);
        $semestres = $this->semestreModel->getAll();
        $resumen = $this->atencionModel->getResumen($semestreId);
        $porTema = $this->atencionModel->contarPorTema($semestreId);
        
        require_once APP_PATH . 'views/reportes/por_semestre.php';
    }
    
    /**
     * Reporte por docente
     */
    public function porDocente() {
        $docenteId = $_GET['docente_id'] ?? null;
        $semestreId = $_GET['semestre_id'] ?? null;
        
        $docentes = $this->docenteModel->getAll();
        $semestres = $this->semestreModel->getAll();
        
        $atenciones = [];
        $estadisticas = null;
        $docente = null;
        $semestre = null;
        
        if ($docenteId) {
            $docente = $this->docenteModel->findById($docenteId);
            $atenciones = $this->atencionModel->getByDocente($docenteId, $semestreId);
            $estadisticas = $this->docenteModel->getEstadisticas($docenteId, $semestreId);
            
            if ($semestreId) {
                $semestre = $this->semestreModel->findById($semestreId);
            }
        }
        
        require_once APP_PATH . 'views/reportes/por_docente.php';
    }
    
    /**
     * Reporte por temas
     */
    public function porTemas() {
        $semestreId = $_GET['semestre_id'] ?? null;
        
        if (!$semestreId) {
            $semestreActivo = $this->semestreModel->getActivo();
            $semestreId = $semestreActivo['id'];
        }
        
        $semestre = $this->semestreModel->findById($semestreId);
        $semestres = $this->semestreModel->getAll();
        $porTema = $this->atencionModel->contarPorTema($semestreId);
        
        // Agrupar por categoría
        $porCategoria = [];
        foreach ($porTema as $tema) {
            $categoria = $tema['categoria'];
            if (!isset($porCategoria[$categoria])) {
                $porCategoria[$categoria] = [
                    'nombre' => $this->getNombreCategoria($categoria),
                    'total' => 0,
                    'temas' => []
                ];
            }
            $porCategoria[$categoria]['total'] += $tema['total'];
            $porCategoria[$categoria]['temas'][] = $tema;
        }
        
        require_once APP_PATH . 'views/reportes/por_temas.php';
    }
    
    /**
     * Exportar reporte a CSV
     */
    public function exportarCSV() {
        $semestreId = $_GET['semestre_id'] ?? null;
        $tipo = $_GET['tipo'] ?? 'general';
        
        $atenciones = $semestreId 
            ? $this->atencionModel->getBySemestre($semestreId)
            : $this->atencionModel->getAll();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=reporte_atenciones_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // Encabezados
        fputcsv($output, [
            'ID', 'Fecha', 'Hora', 'Estudiante', 'Código', 'Carrera',
            'Docente', 'Semestre', 'Tema', 'Categoría', 'Consulta'
        ]);
        
        // Datos
        foreach ($atenciones as $atencion) {
            fputcsv($output, [
                $atencion['id'],
                $atencion['fecha_atencion'],
                $atencion['hora_inicio'],
                $atencion['estudiante'],
                $atencion['codigo_estudiante'],
                $atencion['carrera'],
                $atencion['docente'],
                $atencion['semestre'],
                $atencion['tema'],
                $atencion['categoria_tema'],
                $atencion['consulta_estudiante']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Obtener nombre legible de categoría
     */
    private function getNombreCategoria($categoria) {
        $categorias = [
            'plan_estudios' => 'Plan de Estudios',
            'desarrollo_profesional' => 'Desarrollo Profesional',
            'insercion_laboral' => 'Inserción Laboral',
            'plan_tesis' => 'Plan de Tesis',
            'otros' => 'Otros'
        ];
        return $categorias[$categoria] ?? $categoria;
    }
}
