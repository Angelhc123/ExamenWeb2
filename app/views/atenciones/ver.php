<?php
$titulo = 'Detalle de Atención';
ob_start();
?>

<div class="mb-4">
    <h2><i class="bi bi-eye"></i> Detalle de Atención #<?php echo $atencion['id']; ?></h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>atenciones">Atenciones</a></li>
            <li class="breadcrumb-item active">Detalle</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Información de la Atención</h5>
                <div>
                    <a href="<?php echo BASE_URL; ?>atenciones/editar/<?php echo $atencion['id']; ?>" 
                       class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="<?php echo BASE_URL; ?>atenciones" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                
                <!-- Información del Estudiante -->
                <div class="card mb-3 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-person"></i> Estudiante</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nombre:</strong><br><?php echo $atencion['estudiante']; ?></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Código:</strong><br><?php echo $atencion['codigo_estudiante']; ?></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Carrera:</strong><br><?php echo $atencion['carrera']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de la Atención -->
                <div class="card mb-3 border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-calendar-event"></i> Datos de la Atención</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Docente:</strong><br><?php echo $atencion['docente']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Semestre:</strong><br><?php echo $atencion['semestre']; ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Fecha:</strong><br>
                                    <?php echo date('d/m/Y', strtotime($atencion['fecha_atencion'])); ?>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Hora de Inicio:</strong><br>
                                    <?php echo date('H:i', strtotime($atencion['hora_inicio'])); ?>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Hora de Fin:</strong><br>
                                    <?php echo $atencion['hora_fin'] ? date('H:i', strtotime($atencion['hora_fin'])) : 'No especificada'; ?>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p><strong>Tema:</strong><br>
                                    <span class="badge bg-secondary"><?php echo ucfirst(str_replace('_', ' ', $atencion['categoria_tema'])); ?></span>
                                    <?php echo $atencion['tema']; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="card mb-3 border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-chat-text"></i> Descripción de la Atención</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Consulta del Estudiante:</strong>
                            <p class="mt-2 p-3 bg-light rounded"><?php echo nl2br(htmlspecialchars($atencion['consulta_estudiante'])); ?></p>
                        </div>
                        
                        <?php if ($atencion['descripcion_atencion']): ?>
                            <div class="mb-3">
                                <strong>Descripción de la Atención:</strong>
                                <p class="mt-2 p-3 bg-light rounded"><?php echo nl2br(htmlspecialchars($atencion['descripcion_atencion'])); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($atencion['evidencia']): ?>
                            <div class="mb-3">
                                <strong>Evidencia:</strong>
                                <p class="mt-2 p-3 bg-light rounded">
                                    <i class="bi bi-file-earmark"></i> <?php echo htmlspecialchars($atencion['evidencia']); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($atencion['observaciones']): ?>
                            <div class="mb-3">
                                <strong>Observaciones:</strong>
                                <p class="mt-2 p-3 bg-light rounded"><?php echo nl2br(htmlspecialchars($atencion['observaciones'])); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php
$contenido = ob_get_clean();
require_once APP_PATH . 'views/layouts/main.php';
?>
