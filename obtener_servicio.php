<?php
$host = 'localhost';
$user = 'winston_richard';
$password = '101605';
$dbname = 'winston_general';

// Conexión a la base de datos
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el concepto seleccionado
$concepto = $_GET['concepto'];

// Consulta para obtener el servicio_detalle y servicio_importe
$sql = "SELECT servicio_detalle, servicio_importe FROM concepto_servicios WHERE TRIM(servicio_ab) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $concepto);
$stmt->execute();
$stmt->bind_result($servicio_detalle, $servicio_importe);

$response = array();

if ($stmt->fetch()) {
    $response = array(
        'success' => true,
        'detalle' => $servicio_detalle,
        'importe' => $servicio_importe
    );
} else {
    $response = array('success' => false);
}

// Cerrar la conexión
$stmt->close();
$conn->close();

// Devolver la respuesta en formato JSON
echo json_encode($response);
?>
