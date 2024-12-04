<?php
session_start();

// Mostrar errores para depurar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreUsuario = trim($_POST['nombre_usuario'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');

    if (empty($nombreUsuario) || empty($contrasena)) {
        die("Por favor, completa todos los campos.");
    }

    // Conectar a la base de datos
    $conn = new mysqli('localhost', 'root', '', 'login_db');

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Consulta SQL segura
    $sql = "SELECT password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error en la consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $nombreUsuario);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($storedPassword);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();

        // Comparar contraseñas directamente (sin cifrar)
        if (password_verify($contrasena, $storedPassword)) {
            $_SESSION['username'] = $nombreUsuario;
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>
