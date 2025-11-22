-- Base de datos para Sistema de Registro de Atenciones de Consejería y Tutoría
-- Fecha: 21 de noviembre de 2025

CREATE DATABASE IF NOT EXISTS consejeria_tutoria;
USE consejeria_tutoria;

-- Tabla de Semestres
CREATE TABLE IF NOT EXISTS semestres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Docentes
CREATE TABLE IF NOT EXISTS docentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(20),
    especialidad VARCHAR(100),
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Estudiantes
CREATE TABLE IF NOT EXISTS estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(20),
    carrera VARCHAR(100),
    password VARCHAR(255) NOT NULL DEFAULT 'password123',
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Temas de Consejería
CREATE TABLE IF NOT EXISTS temas_consejeria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    categoria ENUM(
        'plan_estudios',
        'desarrollo_profesional',
        'insercion_laboral',
        'plan_tesis',
        'otros'
    ) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Atenciones
CREATE TABLE IF NOT EXISTS atenciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estudiante_id INT NOT NULL,
    docente_id INT NOT NULL,
    semestre_id INT NOT NULL,
    tema_id INT NOT NULL,
    fecha_atencion DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME,
    consulta_estudiante TEXT NOT NULL,
    descripcion_atencion TEXT,
    evidencia VARCHAR(255),
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id) ON DELETE RESTRICT,
    FOREIGN KEY (docente_id) REFERENCES docentes(id) ON DELETE RESTRICT,
    FOREIGN KEY (semestre_id) REFERENCES semestres(id) ON DELETE RESTRICT,
    FOREIGN KEY (tema_id) REFERENCES temas_consejeria(id) ON DELETE RESTRICT,
    INDEX idx_fecha (fecha_atencion),
    INDEX idx_estudiante (estudiante_id),
    INDEX idx_docente (docente_id),
    INDEX idx_semestre (semestre_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Usuarios del Sistema (para autenticación)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(200) NOT NULL,
    rol ENUM('admin', 'docente', 'coordinador') NOT NULL DEFAULT 'docente',
    docente_id INT NULL,
    activo BOOLEAN DEFAULT TRUE,
    ultimo_acceso TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (docente_id) REFERENCES docentes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuarios de ejemplo (contraseñas sin encriptar)
INSERT INTO usuarios (username, password, nombre_completo, rol) VALUES
('admin', 'admin123', 'Administrador del Sistema', 'admin'),
('docente1', 'docente123', 'María González Pérez', 'docente'),
('coordinador', 'coord123', 'Carlos Rodríguez López', 'coordinador');

-- Insertar datos de ejemplo para Temas de Consejería
INSERT INTO temas_consejeria (nombre, categoria, descripcion) VALUES
('Selección de cursos y plan de estudios', 'plan_estudios', 'Asesoría sobre elección de cursos y planificación académica'),
('Cambio de especialidad o carrera', 'plan_estudios', 'Orientación para cambios en el plan académico'),
('Desarrollo de habilidades profesionales', 'desarrollo_profesional', 'Fortalecimiento de competencias profesionales'),
('Preparación para el mercado laboral', 'desarrollo_profesional', 'Consejos para inserción laboral exitosa'),
('Búsqueda de prácticas profesionales', 'insercion_laboral', 'Apoyo en la búsqueda de oportunidades de prácticas'),
('Elaboración de CV y perfil profesional', 'insercion_laboral', 'Asesoría en preparación de documentos laborales'),
('Definición de tema de tesis', 'plan_tesis', 'Orientación en la selección del tema de investigación'),
('Metodología de investigación', 'plan_tesis', 'Apoyo en aspectos metodológicos de la tesis'),
('Avances y seguimiento de tesis', 'plan_tesis', 'Seguimiento del progreso de la investigación'),
('Orientación personal y motivacional', 'otros', 'Apoyo en aspectos personales que afectan el rendimiento'),
('Gestión del tiempo y organización', 'otros', 'Técnicas de administración del tiempo'),
('Trámites administrativos', 'otros', 'Información sobre procedimientos académicos');

-- Insertar semestre de ejemplo
INSERT INTO semestres (nombre, fecha_inicio, fecha_fin, activo) VALUES
('2025-2', '2025-08-01', '2025-12-20', TRUE),
('2026-1', '2026-03-01', '2026-07-15', FALSE);

-- Insertar docentes de ejemplo
INSERT INTO docentes (codigo, nombres, apellidos, email, especialidad) VALUES
('DOC001', 'María', 'González Pérez', 'maria.gonzalez@universidad.edu', 'Ingeniería de Software'),
('DOC002', 'Carlos', 'Rodríguez López', 'carlos.rodriguez@universidad.edu', 'Bases de Datos'),
('DOC003', 'Ana', 'Martínez Silva', 'ana.martinez@universidad.edu', 'Desarrollo Profesional');

-- Insertar estudiantes de ejemplo (con contraseñas en texto plano)
INSERT INTO estudiantes (codigo, nombres, apellidos, email, carrera, password) VALUES
('EST001', 'Juan', 'Pérez García', 'juan.perez@estudiante.edu', 'Ingeniería de Sistemas', 'est123'),
('EST002', 'Laura', 'Torres Ruiz', 'laura.torres@estudiante.edu', 'Ingeniería de Software', 'est123'),
('EST003', 'Pedro', 'Sánchez Mora', 'pedro.sanchez@estudiante.edu', 'Ciencias de la Computación', 'est123');

-- Vista para reportes: Atenciones por semestre y docente
CREATE VIEW vista_atenciones_resumen AS
SELECT 
    s.nombre AS semestre,
    CONCAT(d.nombres, ' ', d.apellidos) AS docente,
    tc.categoria AS categoria_tema,
    tc.nombre AS tema,
    COUNT(a.id) AS total_atenciones,
    COUNT(DISTINCT a.estudiante_id) AS estudiantes_unicos
FROM atenciones a
INNER JOIN semestres s ON a.semestre_id = s.id
INNER JOIN docentes d ON a.docente_id = d.id
INNER JOIN temas_consejeria tc ON a.tema_id = tc.id
GROUP BY s.id, d.id, tc.id
ORDER BY s.nombre DESC, total_atenciones DESC;

-- Vista para consulta de atenciones completas
CREATE VIEW vista_atenciones_detalle AS
SELECT 
    a.id,
    a.fecha_atencion,
    a.hora_inicio,
    a.hora_fin,
    CONCAT(e.nombres, ' ', e.apellidos) AS estudiante,
    e.codigo AS codigo_estudiante,
    e.carrera,
    CONCAT(d.nombres, ' ', d.apellidos) AS docente,
    s.nombre AS semestre,
    tc.nombre AS tema,
    tc.categoria AS categoria_tema,
    a.consulta_estudiante,
    a.descripcion_atencion,
    a.observaciones,
    a.evidencia
FROM atenciones a
INNER JOIN estudiantes e ON a.estudiante_id = e.id
INNER JOIN docentes d ON a.docente_id = d.id
INNER JOIN semestres s ON a.semestre_id = s.id
INNER JOIN temas_consejeria tc ON a.tema_id = tc.id
ORDER BY a.fecha_atencion DESC, a.hora_inicio DESC;
