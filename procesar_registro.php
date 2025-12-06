<?php
// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Incluir conexión
include 'conexion.php';

// Verificar si se recibieron los datos necesarios
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener y validar datos
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

try {
    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ? OR correo = ?");
    if (!$stmt) {
        throw new Exception("Error en preparación: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (isset($row['nombre_usuario']) && $row['nombre_usuario'] === $username) {
            echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya existe']);
        } else {
            echo json_encode(['success' => false, 'message' => 'El correo electrónico ya está registrado']);
        }
        exit;
    }

    // Procesar imagen de avatar
    $imagen = "IMG/icon.jpg"; // Valor por defecto
    
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['avatar']['type'];
        
        if (in_array($fileType, $allowedTypes)) {
            // Leer el contenido del archivo
            $imagenTemp = $_FILES['avatar']['tmp_name'];
            $imagenData = file_get_contents($imagenTemp);
            $imagen = base64_encode($imagenData);
        }
    }

    // Hash de la contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insertar usuario en la base de datos
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre_usuario, correo, contraseña, imagen) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Error en preparación: " . $conn->error);
    }
    
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $imagen);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Usuario registrado correctamente']);
    } else {
        throw new Exception("Error al ejecutar: " . $stmt->error);
    }

    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>