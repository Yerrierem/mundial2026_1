<?php
$servername = "localhost";
$username = "root";
$password = "rJ2XXP7U";
$dbname = "futbol_ar";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "Conexión exitosa a la base de datos!<br>";
    
    // Verificar si la tabla usuarios existe
    $result = $conn->query("SHOW TABLES LIKE 'usuarios'");
    if ($result->num_rows > 0) {
        echo "La tabla 'usuarios' existe.<br>";
        
        // Mostrar estructura de la tabla
        $table_info = $conn->query("DESCRIBE usuarios");
        echo "Estructura de la tabla:<br>";
        while ($row = $table_info->fetch_assoc()) {
            echo "Campo: " . $row['Field'] . " - Tipo: " . $row['Type'] . "<br>";
        }
    } else {
        echo "La tabla 'usuarios' NO existe.<br>";
    }
}

$conn->close();
?>