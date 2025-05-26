<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$rol = $_SESSION['user']['rol_id'];
$nombre = $_SESSION['user']['nombre'];

// Roles: 1=Administrador, 2=Gerente, 3=Miembro

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Dashboard - Sistema de Tareas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container mt-5">

<h2>Bienvenido, <?= htmlspecialchars($nombre) ?></h2>
<a href="logout.php" class="btn btn-danger float-end">Cerrar sesión</a>

<hr />

<?php if ($rol == 1): ?>
  <h3>Administrador</h3>
  <a href="usuarios.php" class="btn btn-primary mb-3">Gestionar Usuarios</a>
  <a href="tareas.php" class="btn btn-secondary mb-3">Ver todas las tareas</a>
<?php elseif ($rol == 2): ?>
  <h3>Gerente de Proyecto</h3>
  <a href="tareas.php" class="btn btn-primary mb-3">Gestionar Tareas de mi proyecto</a>
<?php else: ?>
  <h3>Miembro del equipo</h3>
  <a href="tareas.php" class="btn btn-primary mb-3">Mis tareas asignadas</a>
<?php endif; ?>

</body>
</html>
