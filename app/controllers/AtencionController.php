<?php
/**
 * Controlador de Atenciones
 */

class AtencionController {
    private $atencionModel;
    private $estudianteModel;
    private $docenteModel;
    private $semestreModel;
    private $temaModel;
    
    public function __construct() {
        $this->atencionModel = new Atencion();
        $this->estudianteModel = new Estudiante();
        $this->docenteModel = new Docente();
        $this->semestreModel = new Semestre();
        $this->temaModel = new TemaConsejeria();
    }
    
    /**
     * Listar todas las atenciones
     */
    public function index() {
        $atenciones = $this->atencionModel->getAll(50);
        $semestres = $this->semestreModel->getAll();
        $semestreActivo = $this->semestreModel->getActivo();
        
        require_once APP_PATH . 'views/atenciones/index.php';
    }
    
    /**
     * Mostrar formulario de nueva atención
     */
    public function crear() {
        $estudiantes = $this->estudianteModel->getAll();
        $docentes = $this->docenteModel->getAll();
        $semestres = $this->semestreModel->getAll();
        $semestreActivo = $this->semestreModel->getActivo();
        $temas = $this->temaModel->getAll();
        $categorias = $this->temaModel->getCategorias();
        
        require_once APP_PATH . 'views/atenciones/crear.php';
    }
    
    /**
     * Guardar nueva atención
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos
            $errores = $this->validarDatos($_POST);
            
            if (empty($errores)) {
                // Validar disponibilidad del docente
                $disponible = $this->atencionModel->validarDisponibilidad(
                    $_POST['docente_id'],
                    $_POST['fecha_atencion'],
                    $_POST['hora_inicio']
                );
                
                if (!$disponible) {
                    $_SESSION['error'] = 'El docente ya tiene una atención registrada en esa fecha y hora.';
                    header('Location: ' . BASE_URL . 'atenciones/crear');
                    exit;
                }
                
                $resultado = $this->atencionModel->create($_POST);
                
                if ($resultado) {
                    $_SESSION['success'] = 'Atención registrada exitosamente.';
                    header('Location: ' . BASE_URL . 'atenciones');
                } else {
                    $_SESSION['error'] = 'Error al registrar la atención.';
                    header('Location: ' . BASE_URL . 'atenciones/crear');
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $_POST;
                header('Location: ' . BASE_URL . 'atenciones/crear');
            }
            exit;
        }
    }
    
    /**
     * Mostrar formulario de edición
     */
    public function editar($id) {
        $atencion = $this->atencionModel->findById($id);
        
        if (!$atencion) {
            $_SESSION['error'] = 'Atención no encontrada.';
            header('Location: ' . BASE_URL . 'atenciones');
            exit;
        }
        
        $estudiantes = $this->estudianteModel->getAll();
        $docentes = $this->docenteModel->getAll();
        $semestres = $this->semestreModel->getAll();
        $temas = $this->temaModel->getAll();
        $categorias = $this->temaModel->getCategorias();
        
        require_once APP_PATH . 'views/atenciones/editar.php';
    }
    
    /**
     * Actualizar atención
     */
    public function actualizar($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errores = $this->validarDatos($_POST);
            
            if (empty($errores)) {
                $resultado = $this->atencionModel->update($id, $_POST);
                
                if ($resultado) {
                    $_SESSION['success'] = 'Atención actualizada exitosamente.';
                } else {
                    $_SESSION['error'] = 'Error al actualizar la atención.';
                }
            } else {
                $_SESSION['errores'] = $errores;
            }
            
            header('Location: ' . BASE_URL . 'atenciones');
            exit;
        }
    }
    
    /**
     * Ver detalle de una atención
     */
    public function ver($id) {
        $atencion = $this->atencionModel->findById($id);
        
        if (!$atencion) {
            $_SESSION['error'] = 'Atención no encontrada.';
            header('Location: ' . BASE_URL . 'atenciones');
            exit;
        }
        
        require_once APP_PATH . 'views/atenciones/ver.php';
    }
    
    /**
     * Eliminar atención
     */
    public function eliminar($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $this->atencionModel->delete($id);
            
            if ($resultado) {
                $_SESSION['success'] = 'Atención eliminada exitosamente.';
            } else {
                $_SESSION['error'] = 'Error al eliminar la atención.';
            }
            
            header('Location: ' . BASE_URL . 'atenciones');
            exit;
        }
    }
    
    /**
     * Buscar estudiante por código (AJAX)
     */
    public function buscarEstudiante() {
        if (isset($_GET['codigo'])) {
            $estudiante = $this->estudianteModel->findByCodigo($_GET['codigo']);
            header('Content-Type: application/json');
            echo json_encode($estudiante ? $estudiante : ['error' => 'No encontrado']);
            exit;
        }
    }
    
    /**
     * Validar datos del formulario
     */
    private function validarDatos($data) {
        $errores = [];
        
        if (empty($data['estudiante_id'])) {
            $errores[] = 'El estudiante es requerido.';
        }
        
        if (empty($data['docente_id'])) {
            $errores[] = 'El docente es requerido.';
        }
        
        if (empty($data['semestre_id'])) {
            $errores[] = 'El semestre es requerido.';
        }
        
        if (empty($data['tema_id'])) {
            $errores[] = 'El tema es requerido.';
        }
        
        if (empty($data['fecha_atencion'])) {
            $errores[] = 'La fecha de atención es requerida.';
        }
        
        if (empty($data['hora_inicio'])) {
            $errores[] = 'La hora de inicio es requerida.';
        }
        
        if (empty($data['consulta_estudiante'])) {
            $errores[] = 'La consulta del estudiante es requerida.';
        }
        
        return $errores;
    }
}
