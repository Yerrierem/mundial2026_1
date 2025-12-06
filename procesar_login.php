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

// Obtener datos
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

try {
    // Buscar usuario en la base de datos
    $stmt = $conn->prepare("SELECT id, nombre_usuario, correo, contraseña FROM usuarios WHERE correo = ?");
    if (!$stmt) {
        throw new Exception("Error en preparación: " . $conn->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Correo electrónico o contraseña incorrectos']);
        exit;
    }

    $user = $result->fetch_assoc();

    // Verificar contraseña
    if (password_verify($password, $user['contraseña'])) {
        // Inicio de sesión exitoso
        echo json_encode([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'userId' => $user['id'],
            'username' => $user['nombre_usuario'],
            'email' => $user['correo']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Correo electrónico o contraseña incorrectos']);
    }

    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>