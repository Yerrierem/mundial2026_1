<?php
$servername = "localhost";
$username = "root";
$password = "rJ2XXP7U";
$dbname = "bdm_redsocial";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    error_log("Error de conexión: " . $conn->connect_error);
    die(json_encode([
        'success' => false,
        'message' => 'Error al conectar con la base de datos'
    ]));
}

// Establecer el conjunto de caracteres
$conn->set_charset("utf8mb4");
?>