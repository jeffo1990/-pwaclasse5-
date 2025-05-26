<?php
session_start();
require 'db.php'; // Debe definir $conn como mysqli conectado a gestion_tareas

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass  = $_POST['password'];

    // Buscamos al usuario
    $stmt = $conn->prepare("SELECT id, nombre, contraseña, rol_id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($pass, $user['contraseña'])) {
        // Guardar en sesión
        $_SESSION['usuario']    = $user['nombre'];
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['rol_id']     = $user['rol_id'];

        // Redirigir según rol
        switch ($user['rol_id']) {
            case 1:
                header("Location: usuarios.php");
                break;
            case 2:
            case 3:
                header("Location: tareas.php");
                break;
            default:
                // En caso de rol inesperado, al dashboard
                header("Location: dashboard.php");
        }
        exit;
    } else {
        $error = $user ? "Contraseña incorrecta" : "Usuario no encontrado";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login - Sistema de Tareas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h2>Iniciar sesión</h2>
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST" action="login.php">
    <input type="email"    name="email"    class="form-control mb-3" placeholder="Email"      required>
    <input type="password" name="password" class="form-control mb-3" placeholder="Contraseña" required>
    <button type="submit" class="btn btn-primary">Entrar</button>
  </form>
  <p class="mt-3">¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
</body>
</html>
