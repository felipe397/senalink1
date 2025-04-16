<?php
// Incluir la conexión usando ruta relativa segura
include(__DIR__ . '/../Config/conexion.php');

class EmpresaController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function registrarEmpresa($nit, $nombre, $telefono, $correo, $direccion, $actividad_economica, $contrasena) {
        try {
            // Validación básica
            if (empty($nit) || empty($nombre) || empty($telefono) || empty($correo) || empty($direccion) || empty($actividad_economica) || empty($contrasena)) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            // Verificar si ya existe una empresa con ese NIT o correo
            $stmt = $this->conn->prepare("SELECT id FROM empresas WHERE nit = ? OR correo = ?");
            $stmt->execute([$nit, $correo]);

            if ($stmt->rowCount() > 0) {
                return "Error: La empresa ya está registrada.";
            }

            // Encriptar contraseña
            $contrasena_hashed = password_hash($contrasena, PASSWORD_DEFAULT);
            $fecha_creacion = date("Y-m-d H:i:s");

            // Insertar nueva empresa
            $sql = "INSERT INTO empresas 
                        (nit, nombre, telefono, correo, direccion, actividad_economica, contrasena, estado, fecha_creacion) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'Activo', ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $nit, $nombre, $telefono, $correo, $direccion, $actividad_economica, $contrasena_hashed, $fecha_creacion
            ]);

            // Redirigir si todo fue bien
            header('Location: ../html/Empresa/index.html');
            exit;
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}

// Manejo del POST (cuando se envía el formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empresaController = new EmpresaController($conn);

    $nit = $_POST['nit'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $actividad_economica = $_POST['actividad_economica'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    $resultado = $empresaController->registrarEmpresa($nit, $nombre, $telefono, $correo, $direccion, $actividad_economica, $contrasena);

    if ($resultado) {
        echo $resultado;
    }
} else {
    echo "Método no permitido.";
}
?>
