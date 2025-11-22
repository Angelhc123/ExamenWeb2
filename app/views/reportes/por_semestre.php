<?php
$titulo = 'Reporte por Semestre';
ob_start();
?>

<div class="mb-4">
    <h2><i class="bi bi-calendar3"></i> Reporte por Semestre</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>reportes">Reportes</a></li>
            <li class="breadcrumb-item active">Por Semestre</li>
        </ol>
    </nav>
</div>

<!-- Selector de Semestre -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-10">
                <label class="form-label">Seleccionar Semestre</label>
                <select name="semestre_id" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($semestres as $sem): ?>
                        <option value="<?php echo $sem['id']; ?>" 
                                <?php echo ($semestre && $sem['id'] == $semestre['id']) ? 'selected' : ''; ?>>
                            <?php echo $sem['nombre']; ?> 
                            <?php echo $sem['activo'] ? '(Activo)' : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <a href="<?php echo BASE_URL; ?>reportes/exportarCSV?semestre_id=<?php echo $semestre['id']; ?>" 
                   class="btn btn-warning w-100">
                    <i class="bi bi-download"></i> CSV
                </a>
            </div>
        </form>
    </div>
</div>

<?php if ($semestre): ?>
    <!-- Información del Semestre -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-info-circle"></i> Semestre: <?php echo $semestre['nombre']; ?>
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Fecha de Inicio:</strong><br>
                        <?php echo date('d/m/Y', strtotime($semestre['fecha_inicio'])); ?>
                    </p>
                </div>
                <div class="col-md-4">
                    <p><strong>Fecha de Fin:</strong><br>
                        <?php echo date('d/m/Y', strtotime($semestre['fecha_fin'])); ?>
                    </p>
                </div>
                <div class="col-md-4">
                    <p><strong>Estado:</strong><br>
                        <span class="badge <?php echo $semestre['activo'] ? 'bg-success' : 'bg-secondary'; ?>">
                            <?php echo $semestre['activo'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen por Docente y Tema -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-table"></i> Resumen de Atenciones por Docente y Tema</h5>
        </div>
        <div class="card-body">
            <?php if (empty($resumen)): ?>
                <div class="alert alert-info">
                    No hay atenciones registradas para este semestre.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Docente</th>
                                <th>Categoría</th>
                                <th>Tema</th>
                                <th class="text-center">Total Atenciones</th>
                                <th class="text-center">Estudiantes Únicos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resumen as $item): ?>
                                <tr>
                                    <td><?php echo $item['docente']; ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo ucfirst(str_replace('_', ' ', $item['categoria_tema'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $item['tema']; ?></td>
                                    <td class="text-center">
                                        <strong><?php echo $item['total_atenciones']; ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $item['estudiantes_unicos']; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Distribución por Temas -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Distribución por Temas</h5>
        </div>
        <div class="card-body">
            <?php if (empty($porTema)): ?>
                <div class="alert alert-info">
                    No hay datos para mostrar.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th>Tema</th>
                                <th class="text-center">Cantidad</th>
                                <th>Distribución</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total = array_sum(array_column($porTema, 'total'));
                            foreach ($porTema as $tema): 
                                $porcentaje = ($tema['total'] / $total) * 100;
                            ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo ucfirst(str_replace('_', ' ', $tema['categoria'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $tema['nombre']; ?></td>
                                    <td class="text-center"><strong><?php echo $tema['total']; ?></strong></td>
                                    <td>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: <?php echo $porcentaje; ?>%">
                                                <?php echo number_format($porcentaje, 1); ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td class="text-center"><strong><?php echo $total; ?></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php
$contenido = ob_get_clean();
require_once APP_PATH . 'views/layouts/main.php';
?>
