<?php

$conn = new mysqli("localhost", "root", "", "gestion_tareas");


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $contraseña = password_hash($_POST["contraseña"], PASSWORD_DEFAULT);
    $rol_id = (int)$_POST["rol_id"];

    $verificar = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $verificar->bind_param("s", $email);
    $verificar->execute();
    $verificar->store_result();

    if ($verificar->num_rows > 0) {
        $mensaje = "El correo ya está registrado.";
    } else {

        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, contraseña, rol_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nombre, $email, $contraseña, $rol_id);
        if ($stmt->execute()) {
            header("Location: login.php?registro=exito");
            exit();
        } else {
            $mensaje = "Error al registrar: " . $conn->error;
        }
        $stmt->close();
    }
    $verificar->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrarse - Sistema de Tareas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #DCEEFF, #5C9DBF);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            border-radius: 1rem;
            padding: 2rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<div class="card">
    <h3 class="text-center mb-4">Registro de Usuario</h3>

    <?php if ($mensaje): ?>
        <div class="alert alert-warning"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre completo</label>
            <input type="text" class="form-control" name="nombre" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <div class="mb-3">
            <label for="contraseña" class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="contraseña" required>
        </div>

        <div class="mb-3">
            <label for="rol_id" class="form-label">Rol</label>
            <select name="rol_id" class="form-select" required>
                <option value="">Seleccione un rol</option>
                <option value="1">Administrador</option>
                <option value="2">Gerente de proyecto</option>
                <option value="3">Miembro del equipo</option>
            </select>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Registrarse</button>
        </div>
    </form>

    <div class="text-center mt-3">
        ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
    </div>
</div>

</body>
</html>
