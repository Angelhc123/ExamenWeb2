<?php
// ESTE LOGIN ESTÁ DEPRECATED - USAR public/index.php
// Redirigir al sistema correcto
header('Location: public/index.php');
exit;

/* 
// SISTEMA SIMPLE - SIN COMPLICACIONES (DEPRECATED)
session_start();

// Configuración básica
define('BASE_URL', 'http://localhost/consejeria-tutoria/');
define('DB_HOST', 'localhost');
define('DB_NAME', 'consejeria_tutoria');
define('DB_USER', 'root');
define('DB_PASS', '');

// Conexión a BD
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Error BD: " . $e->getMessage());
}

// PROCESAR LOGIN
if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $tipo = $_POST['tipo'] ?? 'admin';
    
    if ($username && $password) {
        if ($tipo == 'estudiante') {
            // Login de estudiante
            $stmt = $pdo->prepare("SELECT * FROM estudiantes WHERE codigo = ? AND password = ?");
            $stmt->execute([$username, $password]);
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION['logueado'] = true;
                $_SESSION['tipo'] = 'estudiante';
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario'] = $user['nombres'] . ' ' . $user['apellidos'];
                $_SESSION['codigo'] = $user['codigo'];
                header('Location: estudiante_panel.php');
                exit;
            }
        } else {
            // Login de admin/docente
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ? AND password = ?");
            $stmt->execute([$username, $password]);
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION['logueado'] = true;
                $_SESSION['tipo'] = 'admin';
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario'] = $user['nombre_completo'];
                $_SESSION['rol'] = $user['rol'];
                header('Location: admin_panel_v2.php');
                exit;
            }
        }
        $error = "Usuario o contraseña incorrectos";
    }
}

// MOSTRAR LOGIN
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Consejería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-primary">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-dark text-white text-center">
                        <h3>🎓 CONSEJERÍA TUTORÍA</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label>Tipo de Usuario:</label>
                                <select name="tipo" class="form-select" onchange="cambiarTipo()">
                                    <option value="admin">Administrador/Docente</option>
                                    <option value="estudiante">Estudiante</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label id="lbl-user">Usuario:</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Contraseña:</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">ENTRAR</button>
                        </form>
                        
                        <script>
                        function cambiarTipo() {
                            const tipo = document.querySelector('[name="tipo"]').value;
                            const lbl = document.getElementById('lbl-user');
                            if (tipo == 'estudiante') {
                                lbl.textContent = 'Código de Estudiante:';
                            } else {
                                lbl.textContent = 'Usuario:';
                            }
                        }
                        </script>
                        
                        <hr>
                        <small class="text-muted">
                            <strong>Administradores:</strong><br>
                            admin / admin123<br>
                            docente1 / docente123<br>
                            <strong>Estudiantes:</strong><br>
                            EST001 / est123<br>
                            EST002 / est123<br>
                            EST003 / est123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
*/