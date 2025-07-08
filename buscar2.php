<?php
// Conexión a la base de datos (reemplaza con tus propias credenciales)
$servername = "localhost";
$username = "winston_richard";
$password = "101605";
$dbname = "winston_general";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el término de búsqueda del usuario
$query = strtolower($_POST["query"]);

// Realizar la consulta en la base de datos (reemplaza con tu propia consulta)
$sql = "SELECT nombre, identificador FROM ch_nombres WHERE LOWER(identificador) LIKE '%$query%'";
$result = $conn->query($sql);

// Verificar si la consulta fue exitosa
if ($result === false) {
    die("Error en la consulta: " . $conn->error);
}
// Construir la tabla de resultados con enlaces para abrir la ventana modal
$table = "<table class='table'>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Identificador</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $table .= "<tr>
                    <td>{$row["nombre"]}</td>
                    <td>{$row["identificador"]}</td>
                    <td><a href='#' class='open-modal' data-nombre='{$row["nombre"]}' data-apellido='{$row["identificador"]}'>Ver Detalles</a></td>
                    <td><a href='#' class='open-modal text-danger' data-nombre='{$row["nombre"]}'>Eliminar Nombre</a></td>

                </tr>";
    }
} else {
    $table .= "<tr><td colspan='3'>No se encontraron resultados</td></tr>";
}

$table .= "</tbody></table>";

// Devolver la tabla al archivo JavaScript
echo $table;

// Cerrar la conexión
$conn->close();
?>