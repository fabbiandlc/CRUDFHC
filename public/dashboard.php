<?php
session_start();

// Redirigir si el usuario no ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username']; // Nombre de usuario desde la sesión

date_default_timezone_set('America/Mexico_City');

$fechaHora = strftime('%A, %d de %B de %Y, %I:%M %p');

// Traducir los días y meses manualmente
$dias = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
$diasEspañol = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');

$meses = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
$mesesEspañol = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

// Reemplazar días y meses en inglés por español
$fechaHora = str_replace($dias, $diasEspañol, $fechaHora);
$fechaHora = str_replace($meses, $mesesEspañol, $fechaHora);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="navbar">
        <h1>Dashboard</h1>
        <div class="username-container" onclick="toggleMenu()">
            <span class="username"><?php echo htmlspecialchars($username); ?></span>
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="#">Ver Perfil</a>
                <a href="#">Configuración</a>
                <a href="#">Ayuda</a>
                <a href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </div>
    <div class="sidebar">
        <ul>
            <li><a href="#">Inicio</a></li>
            <li><a href="#">Administración</a></li>
            <li><a href="#">Historial de cambios</a></li>
        </ul>
    </div>
    <div class="content">
        <div class="card">
            <p><strong><?php echo $fechaHora; ?></strong></p>
        </div>

        <div class="card">
            <h3>Overview</h3>
            <p>Welcome to the dashboard, <strong><?php echo htmlspecialchars($username); ?></strong>! Here you can manage your data and access various tools.</p>
        </div>
        <div class="card">
            <h3>Sample Chart</h3>
            <canvas id="sampleChart" width="400" height="200"></canvas>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="scripts.js"></script>
</body>
</html>
