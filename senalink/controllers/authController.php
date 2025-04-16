<?php
include('../../Config/conexion.php'); // o conexion.php si ahí está la conexión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!$conn) {
        die("Error: No se pudo establecer la conexión.");
    }

    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);
    $rol = trim($_POST['rol']);

    if (empty($correo) || empty($contrasena) || empty($rol)) {
        die("Faltan campos.");
    }

    // Buscar el usuario
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ? AND rol = ?");
    $stmt->execute([$correo, $rol]);

    if ($stmt->rowCount() == 1) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($contrasena, $usuario['contrasena'])) {
            $_SESSION['usuario'] = $usuario;
            header("Location: ../html/index.html"); // redirige
            exit;
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
    }
}
} else {
    echo "Método no permitido.";
}  

$conn = null; // Cierra la conexión