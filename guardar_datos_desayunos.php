<?php
$host = 'localhost';
$user = 'winston_richard'; 
$password = '101605'; 
$dbname = 'winston_general';

// Conectar a la base de datos
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Recibir los datos desde AJAX
$datos = json_decode($_POST['datos'], true);

foreach ($datos as $fila) {
    $alumno = $conn->real_escape_string($fila['alumno']);
    $concepto = $conn->real_escape_string($fila['concepto']);
    $costo = $conn->real_escape_string($fila['costo']);
    $fecha = $conn->real_escape_string($fila['fecha']);

    $sql = "INSERT INTO datos_desayunos (referencia, concepto, costo, fecha) 
            VALUES ('$alumno', '$concepto', '$costo', '$fecha')";

    if (!$conn->query($sql)) {
        echo "Error: " . $conn->error;
    }
}

echo "Éxito";
$conn->close();
?>
