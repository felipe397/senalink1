<?php
require_once '../Config/conexion.php'; // Asegúrate de que esta ruta sea correcta
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    try {
        // Intentar encontrar al usuario en la tabla de empresas
        $sql = 'SELECT * FROM empresas WHERE correo = :correo';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['correo' => $correo]);
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($empresa && password_verify($contrasena, $empresa['contrasena'])) {
            $_SESSION['user_id'] = $empresa['id'];
            $_SESSION['correo'] = $empresa['correo'];
            $_SESSION['role_id'] = 3; // Role de Empresa
            
            // Redirigir a la página de la empresa
            header('Location: ../html/Empresa/Home.html');
            exit();
        }

        // Si no se encontró en empresas, intentar en la tabla de usuarios (funcionarios o administradores)
        $sql = 'SELECT * FROM usuarios WHERE correo = :correo';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['correo' => $correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['correo'] = $usuario['correo'];
            $_SESSION['role_id'] = $usuario['rol']; // Role_id dinámico según el usuario

            // Redirigir según el rol
            if ($_SESSION['role_id'] == 'SuperAdmin') {
                header('Location: ../../Administrador/home.html');
            } elseif ($_SESSION['role_id'] == 'AdminSENA') {
                header('Location: ../../Administrador/home.html');
            } else {
                echo 'Acceso denegado';
            }
            exit();
        } else {
            echo 'Usuario o contraseña incorrectos';
            exit();
        }
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Método no permitido.";
}
