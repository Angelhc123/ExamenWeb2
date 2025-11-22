<?php
$titulo = 'Reportes y Estadísticas';
ob_start();
?>

<div class="mb-4">
    <h2><i class="bi bi-bar-chart"></i> Reportes y Estadísticas</h2>
</div>

<div class="row">
    <!-- Reporte por Semestre -->
    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-calendar3 text-primary" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Reporte por Semestre</h5>
                <p class="card-text">
                    Visualiza las atenciones registradas por semestre académico, 
                    con estadísticas de docentes, estudiantes y temas tratados.
                </p>
                <a href="<?php echo BASE_URL; ?>reportes/porSemestre" class="btn btn-primary">
                    <i class="bi bi-graph-up"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Reporte por Docente -->
    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-person-badge text-success" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Reporte por Docente</h5>
                <p class="card-text">
                    Consulta el número de atenciones realizadas por cada docente,
                    estudiantes atendidos y temas abordados.
                </p>
                <a href="<?php echo BASE_URL; ?>reportes/porDocente" class="btn btn-success">
                    <i class="bi bi-people"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Reporte por Temas -->
    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-tags text-info" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Reporte por Temas</h5>
                <p class="card-text">
                    Analiza los temas más consultados por categoría y
                    su distribución en los diferentes semestres.
                </p>
                <a href="<?php echo BASE_URL; ?>reportes/porTemas" class="btn btn-info">
                    <i class="bi bi-pie-chart"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Exportar Datos -->
    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-file-earmark-spreadsheet text-warning" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Exportar a CSV</h5>
                <p class="card-text">
                    Descarga los datos de las atenciones en formato CSV
                    para análisis en Excel u otras herramientas.
                </p>
                <form method="GET" action="<?php echo BASE_URL; ?>reportes/exportarCSV" class="mt-3">
                    <select name="semestre_id" class="form-select mb-2">
                        <option value="">Todos los semestres</option>
                        <?php foreach ($semestres as $sem): ?>
                            <option value="<?php echo $sem['id']; ?>" 
                                    <?php echo ($semestreActivo && $sem['id'] == $semestreActivo['id']) ? 'selected' : ''; ?>>
                                <?php echo $sem['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-download"></i> Exportar CSV
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Resumen General -->
<div class="card shadow-sm mt-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-clipboard-data"></i> Resumen General del Sistema</h5>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="p-3">
                    <h3 class="text-primary">
                        <i class="bi bi-journal-check"></i>
                        <div><?php echo count($semestres); ?></div>
                    </h3>
                    <p class="text-muted mb-0">Semestres Registrados</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3">
                    <h3 class="text-success">
                        <i class="bi bi-person-workspace"></i>
                        <div>Varios</div>
                    </h3>
                    <p class="text-muted mb-0">Docentes Activos</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3">
                    <h3 class="text-info">
                        <i class="bi bi-mortarboard"></i>
                        <div>Varios</div>
                    </h3>
                    <p class="text-muted mb-0">Estudiantes Atendidos</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3">
                    <h3 class="text-warning">
                        <i class="bi bi-clipboard-check"></i>
                        <div>Total</div>
                    </h3>
                    <p class="text-muted mb-0">Atenciones Registradas</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$contenido = ob_get_clean();
require_once APP_PATH . 'views/layouts/main.php';
?>
