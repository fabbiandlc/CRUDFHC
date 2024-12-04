<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'login_db');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Crear un nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_usuario'], $_POST['nueva_contrasena'])) {
    $nuevoUsuario = trim($_POST['nuevo_usuario']);
    $nuevaContrasena = trim($_POST['nueva_contrasena']);

    if (!empty($nuevoUsuario) && !empty($nuevaContrasena)) {
        $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $nuevoUsuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "El nombre de usuario '$nuevoUsuario' ya está registrado. Por favor, elige otro.";
        } else {
            $hashedPassword = password_hash($nuevaContrasena, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $nuevoUsuario, $hashedPassword);

            if ($stmt->execute()) {
                echo "Nuevo usuario '$nuevoUsuario' creado exitosamente.";
            } else {
                echo "Error al crear el usuario.";
            }
        }
        $stmt->close();
    } else {
        echo "Por favor, completa todos los campos.";
    }
}

// Eliminar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $deleteUser = trim($_POST['delete_user']);

    if (!empty($deleteUser)) {
        $stmt = $conn->prepare("DELETE FROM users WHERE username = ?");
        $stmt->bind_param("s", $deleteUser);
        if ($stmt->execute()) {
            echo "Usuario '$deleteUser' eliminado correctamente.";
        } else {
            echo "Error al eliminar el usuario.";
        }
        $stmt->close();
    } else {
        echo "Por favor, indica un usuario para eliminar.";
    }
}

// Editar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'], $_POST['new_password'])) {
    $editUser = trim($_POST['edit_user']);
    $newPassword = trim($_POST['new_password']);

    if (!empty($editUser) && !empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hashedPassword, $editUser);

        if ($stmt->execute()) {
            echo "Contraseña de '$editUser' actualizada correctamente.";
        } else {
            echo "Error al actualizar la contraseña.";
        }
        $stmt->close();
    } else {
        echo "Por favor, completa todos los campos para actualizar.";
    }
}

// Cerrar la conexión
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Barra lateral */
.sidebar {
    width: 250px;
    background: #343a40;
    color: white;
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    padding: 15px;
    box-sizing: border-box;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    margin: 20px 0;
}

.sidebar ul li a {
    color: white;
    text-decoration: none;
}

.sidebar ul li a:hover {
    text-decoration: underline;
}

        .admin-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 600px;
            max-width: 100%;
        }

        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        h3 {
            margin-bottom: 10px;
        }

        input[type="text"], input[type="password"] {
            width: 95%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        form {
            margin-bottom: 20px;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
<div class="sidebar">
        <ul>
            <li><a href="#">Overview</a></li>
            <li><a href="#">Reports</a></li>
            <li><a href="logout_admin.php">Cerrar Sesión</a></li>
        </ul>
    </div>
    <div class="admin-container">
        <h1>Admin Panel</h1>

        <!-- Formulario para crear un nuevo usuario -->
        <h3>Crear Nuevo Usuario</h3>
        <form action="admin.php" method="POST">
            <input type="text" name="nuevo_usuario" placeholder="Nombre de usuario" required>
            <input type="password" name="nueva_contrasena" placeholder="Contraseña" required>
            <button type="submit">Crear Usuario</button>
        </form>

        <h3>Usuarios Registrados</h3>
        <table>
            <tr>
                <th>Nombre de Usuario</th>
                <th>Acciones</th>
            </tr>
            <?php
            // Mostrar los usuarios registrados
            $conn = new mysqli('localhost', 'root', '', 'login_db');
            $result = $conn->query("SELECT username FROM users");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td class='form-actions'>
                            <a href='admin.php?edit_user=" . htmlspecialchars($row['username']) . "'>Editar Contraseña</a> | 
                            <a href='admin.php?delete_user=" . htmlspecialchars($row['username']) . "'>Eliminar</a>
                          </td></tr>";
                }
            } else {
                echo "<tr><td>No hay usuarios registrados</td><td></td></tr>";
            }
            ?>
        </table>

        <!-- Formulario para editar contraseña -->
        <?php
        if (isset($_GET['edit_user'])) {
            $editUser = $_GET['edit_user'];
            echo "
            <h3>Editar Contraseña de '$editUser'</h3>
            <form action='admin.php?edit_user=$editUser' method='GET'>
                <input type='password' name='new_password' placeholder='Nueva Contraseña' required>
                <button type='submit'>Actualizar Contraseña</button>
            </form>";
        }
        ?>
    </div>
</body>
</html>
