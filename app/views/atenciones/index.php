<?php
$titulo = 'Listado de Atenciones';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-list-check"></i> Registro de Atenciones</h2>
    <a href="<?php echo BASE_URL; ?>atenciones/crear" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Atención
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Semestre</label>
                <select name="semestre_id" class="form-select">
                    <option value="">Todos los semestres</option>
                    <?php foreach ($semestres as $sem): ?>
                        <option value="<?php echo $sem['id']; ?>" <?php echo ($semestreActivo && $sem['id'] == $semestreActivo['id']) ? 'selected' : ''; ?>>
                            <?php echo $sem['nombre']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Buscar</label>
                <input type="text" name="buscar" class="form-control" placeholder="Estudiante, docente, tema...">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-search"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de atenciones -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Últimas 50 atenciones registradas</h5>
    </div>
    <div class="card-body">
        <?php if (empty($atenciones)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No hay atenciones registradas aún.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estudiante</th>
                            <th>Docente</th>
                            <th>Tema</th>
                            <th>Semestre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($atenciones as $atencion): ?>
                            <tr>
                                <td><?php echo $atencion['id']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($atencion['fecha_atencion'])); ?></td>
                                <td><?php echo date('H:i', strtotime($atencion['hora_inicio'])); ?></td>
                                <td>
                                    <strong><?php echo $atencion['estudiante']; ?></strong><br>
                                    <small class="text-muted"><?php echo $atencion['codigo_estudiante']; ?></small>
                                </td>
                                <td><?php echo $atencion['docente']; ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo $atencion['categoria_tema']; ?></span><br>
                                    <small><?php echo $atencion['tema']; ?></small>
                                </td>
                                <td><?php echo $atencion['semestre']; ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo BASE_URL; ?>atenciones/ver/<?php echo $atencion['id']; ?>" 
                                           class="btn btn-info" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>atenciones/editar/<?php echo $atencion['id']; ?>" 
                                           class="btn btn-warning" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$contenido = ob_get_clean();
require_once APP_PATH . 'views/layouts/main.php';
?>
