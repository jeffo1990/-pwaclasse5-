<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido - Sistema de Gestión de Tareas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #5C9DBF, #DCEEFF);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        .btn-custom {
            border-radius: 2rem;
            padding: 0.5rem 2rem;
        }
        .logo {
            width: 60px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <div class="card p-5">
            <img src="https://cdn-icons-png.flaticon.com/512/1077/1077063.png" class="logo" alt="Logo">
            <h1 class="mb-3">Bienvenido al Sistema de Gestión de Tareas</h1>
            <p class="mb-4">Administra proyectos, asigna tareas y colabora con tu equipo de forma eficiente.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="includes/login.php" class="btn btn-primary btn-custom">Iniciar sesión</a>
                <a href="includes/register.php" class="btn btn-outline-primary btn-custom">Registrarse</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
