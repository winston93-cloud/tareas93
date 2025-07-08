<?php
$host = 'localhost';
$user = 'winston_richard'; // Cambia si tienes otro usuario en MySQL
$pass = '101605'; // Cambia si tienes una contraseña en MySQL
$db = 'winston_general'; // Nombre de tu base de datos

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
if (isset($_POST['query'])) {
    $query = $conn->real_escape_string($_POST['query']);
    $sql = "SELECT alumno_app, alumno_apm, alumno_nombre, alumno_ref FROM alumno_todos 
    WHERE CONVERT(CONCAT(alumno_app, ' ', alumno_apm, ' ', alumno_nombre) USING utf8) 
      LIKE CONVERT('%$query%' USING utf8) 
      AND alumno_ciclo_escolar = 21 
      AND alumno_status > 0
      LIMIT 10";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $nombre_completo = "{$row['alumno_app']} {$row['alumno_apm']} {$row['alumno_nombre']}";
            echo "<div class='autocomplete-item' data-ref='{$row['alumno_ref']}'>$nombre_completo</div>";
        }
    } else {
        echo "<div class='autocomplete-item'>No hay resultados</div>";
    }
}

$conn->close();
?>
