<?php
$titulo = 'Registrar Nueva Atención';
ob_start();
?>

<div class="mb-4">
    <h2><i class="bi bi-plus-circle"></i> Registrar Nueva Atención</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>atenciones">Atenciones</a></li>
            <li class="breadcrumb-item active">Nueva</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Formulario de Registro de Atención</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo BASE_URL; ?>atenciones/guardar" id="formAtencion">
                    
                    <!-- Información del Estudiante -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-person"></i> Datos del Estudiante</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Código del Estudiante <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="codigo_estudiante" class="form-control" 
                                               placeholder="Ej: EST001" required>
                                        <button type="button" class="btn btn-outline-secondary" id="btnBuscarEstudiante">
                                            <i class="bi bi-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Estudiante</label>
                                    <select name="estudiante_id" id="estudiante_id" class="form-select" required>
                                        <option value="">Seleccionar estudiante...</option>
                                        <?php foreach ($estudiantes as $est): ?>
                                            <option value="<?php echo $est['id']; ?>" 
                                                    data-codigo="<?php echo $est['codigo']; ?>">
                                                <?php echo $est['apellidos'] . ', ' . $est['nombres']; ?> (<?php echo $est['codigo']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div id="infoEstudiante" class="alert alert-info d-none">
                                <strong>Estudiante seleccionado:</strong><br>
                                <span id="nombreEstudiante"></span><br>
                                <small>Carrera: <span id="carreraEstudiante"></span></small>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la Atención -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-calendar-event"></i> Datos de la Atención</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Docente Responsable <span class="text-danger">*</span></label>
                                    <select name="docente_id" class="form-select" required>
                                        <option value="">Seleccionar docente...</option>
                                        <?php foreach ($docentes as $doc): ?>
                                            <option value="<?php echo $doc['id']; ?>">
                                                <?php echo $doc['apellidos'] . ', ' . $doc['nombres']; ?>
                                                <?php if ($doc['especialidad']): ?>
                                                    - <?php echo $doc['especialidad']; ?>
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Semestre <span class="text-danger">*</span></label>
                                    <select name="semestre_id" class="form-select" required>
                                        <?php foreach ($semestres as $sem): ?>
                                            <option value="<?php echo $sem['id']; ?>" 
                                                    <?php echo ($semestreActivo && $sem['id'] == $semestreActivo['id']) ? 'selected' : ''; ?>>
                                                <?php echo $sem['nombre']; ?>
                                                <?php echo $sem['activo'] ? ' (Activo)' : ''; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Fecha de Atención <span class="text-danger">*</span></label>
                                    <input type="date" name="fecha_atencion" class="form-control" 
                                           value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Hora de Inicio <span class="text-danger">*</span></label>
                                    <input type="time" name="hora_inicio" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Hora de Fin</label>
                                    <input type="time" name="hora_fin" class="form-control">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Categoría del Tema <span class="text-danger">*</span></label>
                                    <select id="categoria_tema" class="form-select" required>
                                        <option value="">Seleccionar categoría...</option>
                                        <?php foreach ($categorias as $key => $nombre): ?>
                                            <option value="<?php echo $key; ?>"><?php echo $nombre; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Tema Específico <span class="text-danger">*</span></label>
                                    <select name="tema_id" id="tema_id" class="form-select" required>
                                        <option value="">Primero seleccione una categoría...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción de la Atención -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-chat-text"></i> Descripción</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Consulta del Estudiante <span class="text-danger">*</span></label>
                                <textarea name="consulta_estudiante" class="form-control" rows="3" 
                                          placeholder="Describa la consulta o motivo de la atención..." required></textarea>
                                <small class="text-muted">Descripción de lo que el estudiante consultó o necesitó.</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Descripción de la Atención</label>
                                <textarea name="descripcion_atencion" class="form-control" rows="4" 
                                          placeholder="Describa cómo se desarrolló la atención, consejos brindados, etc."></textarea>
                                <small class="text-muted">Detalles de la orientación o consejo brindado.</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Evidencia</label>
                                <input type="text" name="evidencia" class="form-control" 
                                       placeholder="Ej: documento.pdf, captura.jpg, enlace...">
                                <small class="text-muted">Referencia a algún documento o evidencia de la atención.</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Observaciones Adicionales</label>
                                <textarea name="observaciones" class="form-control" rows="2" 
                                          placeholder="Cualquier observación adicional..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo BASE_URL; ?>atenciones" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Atención
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$scripts = '
<script>
// Filtrar temas por categoría
const temas = ' . json_encode($temas) . ';

document.getElementById("categoria_tema").addEventListener("change", function() {
    const categoria = this.value;
    const temaSelect = document.getElementById("tema_id");
    
    temaSelect.innerHTML = "<option value=\"\">Seleccionar tema...</option>";
    
    if (categoria) {
        const temasFiltrados = temas.filter(t => t.categoria === categoria);
        temasFiltrados.forEach(tema => {
            const option = document.createElement("option");
            option.value = tema.id;
            option.textContent = tema.nombre;
            temaSelect.appendChild(option);
        });
    }
});

// Buscar estudiante por código
document.getElementById("btnBuscarEstudiante").addEventListener("click", function() {
    const codigo = document.getElementById("codigo_estudiante").value;
    if (!codigo) {
        alert("Ingrese un código de estudiante");
        return;
    }
    
    const select = document.getElementById("estudiante_id");
    const option = Array.from(select.options).find(opt => opt.dataset.codigo === codigo);
    
    if (option) {
        select.value = option.value;
        select.dispatchEvent(new Event("change"));
    } else {
        alert("Estudiante no encontrado");
    }
});

// Mostrar información del estudiante
document.getElementById("estudiante_id").addEventListener("change", function() {
    const option = this.options[this.selectedIndex];
    if (this.value) {
        document.getElementById("infoEstudiante").classList.remove("d-none");
        document.getElementById("nombreEstudiante").textContent = option.textContent;
    } else {
        document.getElementById("infoEstudiante").classList.add("d-none");
    }
});
</script>
';

$contenido = ob_get_clean();
require_once APP_PATH . 'views/layouts/main.php';
?>
