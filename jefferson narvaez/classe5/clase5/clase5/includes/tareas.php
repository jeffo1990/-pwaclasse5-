<?php
session_start();
require 'db.php'; // debe definir $conn como mysqli conectado a gestion_tareas

// 1) Proteger acceso: cualquiera con sesión válida
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$rol_id     = $_SESSION['rol_id'];

// 2) Cargar posibles destinatarios (rol 3) para asignar tareas
$miembros = [];
if (in_array($rol_id, [1,2])) {
    $res = $conn->query("SELECT id,nombre FROM usuarios WHERE rol_id = 3");
    while ($r = $res->fetch_assoc()) {
        $miembros[$r['id']] = $r['nombre'];
    }
}

// 3) Procesar acciones
// 3a) Agregar tarea (rol 1,2)
if (isset($_POST['add_task']) && in_array($rol_id, [1,2])) {
    $t = $conn->real_escape_string($_POST['titulo']);
    $d = $conn->real_escape_string($_POST['descripcion']);
    $u = (int)$_POST['usuario_id'];
    $conn->query("INSERT INTO tareas (titulo,descripcion,estado,usuario_id)
                  VALUES ('$t','$d','Pendiente',$u)");
    header("Location: tareas.php");
    exit();
}

// 3b) Preparar edición (rol 1,2)
$editando    = false;
$tarea_edit  = ['id'=>'','titulo'=>'','descripcion'=>'','usuario_id'=>'','estado'=>'Pendiente'];
if (isset($_GET['edit']) && in_array($rol_id, [1,2])) {
    $eid = (int)$_GET['edit'];
    $qr  = $conn->query("SELECT * FROM tareas WHERE id=$eid");
    if ($qr->num_rows) {
        $editando   = true;
        $tarea_edit = $qr->fetch_assoc();
    }
}

// 3c) Actualizar tarea (rol 1,2)
if (isset($_POST['update_task']) && in_array($rol_id, [1,2])) {
    $id = (int)$_POST['id'];
    $t  = $conn->real_escape_string($_POST['titulo']);
    $d  = $conn->real_escape_string($_POST['descripcion']);
    $u  = (int)$_POST['usuario_id'];
    $e  = $conn->real_escape_string($_POST['estado']);
    $conn->query("UPDATE tareas 
                  SET titulo='$t',descripcion='$d',estado='$e',usuario_id=$u 
                  WHERE id=$id");
    header("Location: tareas.php");
    exit();
}

// 3d) Eliminar tarea (rol 1,2)
if (isset($_GET['delete']) && in_array($rol_id, [1,2])) {
    $del = (int)$_GET['delete'];
    $conn->query("DELETE FROM tareas WHERE id=$del");
    header("Location: tareas.php");
    exit();
}

// 3e) Miembro cambia estado (rol 3)
if (isset($_GET['change']) && $rol_id == 3) {
    $cid  = (int)$_GET['change'];
    $new  = ($_GET['estado'] === 'Completado') ? 'Completado' : 'En proceso';
    $conn->query("UPDATE tareas 
                  SET estado='$new' 
                  WHERE id=$cid AND usuario_id=$usuario_id");
    header("Location: tareas.php");
    exit();
}

// 4) Obtener listado según rol
if (in_array($rol_id, [1,2])) {
    $sql = "SELECT t.*,u.nombre AS asignado 
            FROM tareas t 
            JOIN usuarios u ON t.usuario_id=u.id";
} else {
    $sql = "SELECT t.*,u.nombre AS asignado 
            FROM tareas t 
            JOIN usuarios u ON t.usuario_id=u.id 
            WHERE t.usuario_id=$usuario_id";
}
$ts = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Tareas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

  <h2>📋 Tareas</h2>
  <p>
    Bienvenido, <strong><?= htmlspecialchars($_SESSION['usuario']) ?></strong>
    (Rol: <?= $rol_id==1?'Administrador':($rol_id==2?'Gerente':'Miembro') ?>)
    <a href="logout.php" class="btn btn-sm btn-danger float-end">Cerrar sesión</a>
  </p>
  <hr>

  <?php if (in_array($rol_id, [1,2])): ?>
  <!-- Formulario Agregar / Editar -->
  <div class="card mb-4">
    <div class="card-header">
      <?= $editando 
          ? "✏️ Editar Tarea #{$tarea_edit['id']}" 
          : "➕ Nueva Tarea" 
      ?>
    </div>
    <div class="card-body">
      <form method="POST" class="row g-2">
        <input type="hidden" name="id" value="<?= $tarea_edit['id'] ?>">
        <div class="col-md-4">
          <input type="text" name="titulo" class="form-control" placeholder="Título" required
            value="<?= htmlspecialchars($tarea_edit['titulo']) ?>">
        </div>
        <div class="col-md-4">
          <input type="text" name="descripcion" class="form-control" placeholder="Descripción"
            value="<?= htmlspecialchars($tarea_edit['descripcion']) ?>">
        </div>
        <div class="col-md-2">
          <select name="usuario_id" class="form-select" required>
            <option value="">Asignar a...</option>
            <?php foreach ($miembros as $id => $nom): ?>
              <option value="<?= $id ?>"
                <?= $tarea_edit['usuario_id']==$id ? 'selected' : '' ?>>
                <?= htmlspecialchars($nom) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php if ($editando): ?>
        <div class="col-md-2">
          <select name="estado" class="form-select">
            <?php foreach (['Pendiente','En proceso','Completado'] as $st): ?>
              <option value="<?= $st ?>"
                <?= $tarea_edit['estado']==$st ? 'selected' : '' ?>>
                <?= $st ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>
        <div class="col-auto">
          <button type="submit" name="<?= $editando ? 'update_task' : 'add_task' ?>"
                  class="btn btn-<?= $editando ? 'warning' : 'primary' ?>">
            <?= $editando ? 'Actualizar' : 'Crear' ?>
          </button>
          <?php if ($editando): ?>
            <a href="tareas.php" class="btn btn-secondary">Cancelar</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <!-- Tabla de tareas -->
  <table class="table table-striped table-bordered">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Descripción</th>
        <th>Estado</th>
        <th>Asignado a</th>
        <?php if (in_array($rol_id, [1,2])): ?>
          <th>Acciones</th>
        <?php else: ?>
          <th>Cambiar Estado</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php while ($t = $ts->fetch_assoc()): ?>
      <tr>
        <td><?= $t['id'] ?></td>
        <td><?= htmlspecialchars($t['titulo']) ?></td>
        <td><?= htmlspecialchars($t['descripcion']) ?></td>
        <td><?= $t['estado'] ?></td>
        <td><?= htmlspecialchars($t['asignado']) ?></td>
        <?php if (in_array($rol_id, [1,2])): ?>
        <td>
          <a href="?edit=<?= $t['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
          <a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-danger"
             onclick="return confirm('¿Eliminar tarea #<?= $t['id'] ?>?')">
            Eliminar
          </a>
        </td>
        <?php else: /* Miembro */ ?>
        <td>
          <a href="?change=<?= $t['id'] ?>&estado=En proceso" class="btn btn-sm btn-secondary">
            En proceso
          </a>
          <a href="?change=<?= $t['id'] ?>&estado=Completado" class="btn btn-sm btn-success">
            Completado
          </a>
        </td>
        <?php endif; ?>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</body>
</html>
