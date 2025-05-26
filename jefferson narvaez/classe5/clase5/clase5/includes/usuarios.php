<?php
session_start();
require 'db.php'; // debe definir $conn como mysqli conectado a gestion_tareas

// Solo Administrador (rol_id = 1)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Cargar roles
$roles = [];
$res = $conn->query("SELECT rol_id, rol_nombre FROM roles");
while ($r = $res->fetch_assoc()) {
    $roles[$r['rol_id']] = $r['rol_nombre'];
}

// Inicializar para editar
$editando = false;
$u = ['id'=>'','nombre'=>'','email'=>'','rol_id'=>''];

// Agregar usuario
if (isset($_POST['add_user'])) {
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare(
      "INSERT INTO usuarios (nombre,email,contraseña,rol_id) VALUES (?,?,?,?)"
    );
    $stmt->bind_param("sssi",
      $_POST['nombre'], $_POST['email'], $hash, $_POST['rol_id']
    );
    $stmt->execute();
    $stmt->close();
    header("Location: usuarios.php");
    exit();
}

// Preparar edición
if (isset($_GET['edit'])) {
    $editando = true;
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare(
      "SELECT id,nombre,email,rol_id FROM usuarios WHERE id=?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Actualizar usuario
if (isset($_POST['update_user'])) {
    if (!empty($_POST['password'])) {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
          "UPDATE usuarios SET nombre=?,email=?,contraseña=?,rol_id=? WHERE id=?"
        );
        $stmt->bind_param("sssii",
          $_POST['nombre'], $_POST['email'], $hash, $_POST['rol_id'], $_POST['id']
        );
    } else {
        $stmt = $conn->prepare(
          "UPDATE usuarios SET nombre=?,email=?,rol_id=? WHERE id=?"
        );
        $stmt->bind_param("ssii",
          $_POST['nombre'], $_POST['email'], $_POST['rol_id'], $_POST['id']
        );
    }
    $stmt->execute();
    $stmt->close();
    header("Location: usuarios.php");
    exit();
}

// Eliminar usuario
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: usuarios.php");
    exit();
}

// Listar todos los usuarios con rol
$sql = "SELECT u.id,u.nombre,u.email,r.rol_nombre
        FROM usuarios u
        JOIN roles r ON u.rol_id = r.rol_id";
$usuarios = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

  <h2>👥 Gestión de Usuarios</h2>
  <div class="mb-3">
    <a href="dashboard.php" class="btn btn-secondary">← Volver al Dashboard</a>
    <a href="tareas.php"    class="btn btn-info">Ver Tareas</a>
  </div>

  <!-- Formulario Agregar / Editar -->
  <div class="card mb-4">
    <div class="card-header">
      <?= $editando ? '✏️ Editar Usuario' : '➕ Agregar Usuario' ?>
    </div>
    <div class="card-body">
      <form method="POST" action="usuarios.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($u['id']) ?>">
        <div class="row g-2">
          <div class="col-md-3">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre" required
                   value="<?= htmlspecialchars($u['nombre']) ?>">
          </div>
          <div class="col-md-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required
                   value="<?= htmlspecialchars($u['email']) ?>">
          </div>
          <div class="col-md-2">
            <select name="rol_id" class="form-select" required>
              <option value="">Rol...</option>
              <?php foreach ($roles as $rid => $rname): ?>
                <option value="<?= $rid ?>" <?= $u['rol_id']==$rid?'selected':'' ?>>
                  <?= htmlspecialchars($rname) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <input type="password" name="password" class="form-control"
                   placeholder="<?= $editando ? 'Nueva contraseña…' : 'Contraseña' ?>"
                   <?= $editando ? '' : 'required' ?>>
          </div>
          <div class="col-auto text-end">
            <button type="submit"
                    name="<?= $editando ? 'update_user' : 'add_user' ?>"
                    class="btn btn-<?= $editando ? 'warning' : 'primary' ?>">
              <?= $editando ? 'Actualizar' : 'Agregar' ?>
            </button>
            <?php if ($editando): ?>
              <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
            <?php endif; ?>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabla de Usuarios -->
  <table class="table table-striped table-hover">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $usuarios->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['nombre']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['rol_nombre']) ?></td>
        <td>
          <a href="usuarios.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
          <a href="usuarios.php?delete=<?= $row['id'] ?>"
             class="btn btn-sm btn-danger"
             onclick="return confirm('¿Eliminar usuario #<?= $row['id'] ?>?')">
            Eliminar
          </a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</body>
</html>
