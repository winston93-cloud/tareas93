<?php
$host = 'localhost';
$user = 'winston_richard'; // Cambia si tienes otro usuario en MySQL
$password = '101605'; // Cambia si tienes una contraseña en MySQL
$dbname = 'winston_general'; // Nombre de tu base de datos


// Conexión a la base de datos
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Vaciar la tabla alumno_todos
$sql_truncate = "TRUNCATE TABLE alumno_todos";
if (!$conn->query($sql_truncate)) {
    die("Error al vaciar la tabla alumno_todos: " . $conn->error);
}

// Insertar datos de la tabla alumno en alumno_todos
$sql_insert_alumno = "INSERT INTO alumno_todos SELECT * FROM alumno";
if (!$conn->query($sql_insert_alumno)) {
    die("Error al insertar datos desde alumno: " . $conn->error);
}

// Insertar datos de la tabla alumno_maestro en alumno_todos
$sql_insert_alumno_maestro = "INSERT INTO alumno_todos SELECT * FROM alumno_maestros";
if (!$conn->query($sql_insert_alumno_maestro)) {
    die("Error al insertar datos desde alumno_maestro: " . $conn->error);
}

echo "<😊>";

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda Autocompletada</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Fondo con marco azul ocupando toda la pantalla */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
            height: 100vh;
        }

        /* Marco azul en toda la pantalla */
        .container {
            width: 100vw;
            height: 100vh;
            border: 10px solid blue;
            border-radius: 20px;
            padding: 20px;
            box-sizing: border-box;
            position: relative;
        }

        /* Contenedor de los inputs en la esquina superior izquierda */
        .input-container {
            position: absolute;
            top: 20px;
            left: 20px;
            display: flex;
            align-items: flex-start;
            gap: 40px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Estilos para los inputs */
        input, select {
            padding: 15px;
            font-size: 22px;
            border-radius: 10px;
            border: 2px solid blue;
            text-transform: uppercase;
            outline: none;
        }

        #searchInput {
            width: 500px; /* INPUT DE BÚSQUEDA MÁS LARGO */
        }

        #alumnoRef {
            width: 300px;
            background-color: #e0e0e0;
            transition: all 0.3s ease;
        }

        /* 🎨 Estilo al recibir foco */
        #alumnoRef:focus {
            background-color: yellow !important;
            color: red !important;
            font-size: 24px !important;
            font-weight: normal;
            padding: 18px;
        }

        /* Autocompletado */
        .autocomplete-container {
            position: relative;
            width: 500px;
        }

        .autocomplete-results {
            position: absolute;
            width: 100%;
            background: white;
            border: 2px solid blue;
            border-radius: 10px;
            max-height: 250px;
            overflow-y: auto;
            display: none;
            z-index: 1000;
        }

        .autocomplete-results div {
            padding: 15px;
            cursor: pointer;
            border-bottom: 1px solid #ddd;
            font-size: 20px;
        }

        .autocomplete-results div:hover, .autocomplete-results .active {
            background: blue;
            color: white;
            font-size: 22px;
        }

        /* 🎨 ESTILO DEL SELECT */
        #alumnoRef option {
            background-color: white;
            color: black;
            font-size: 16px;
            padding: 10px;
            font-weight: normal; /* Eliminar negrita */
        }

        /* 🎨 OPCIONES ENFOCADAS */
        #alumnoRef option:focus, #alumnoRef option:hover {
            background-color: #ff1493; /* Fresa */
            color: yellow;
            font-size: 24px;
            font-weight: bold;
        }

        /* 🎨 OPCIÓN SELECCIONADA MÁS GRANDE */
        #alumnoRef option:checked {
            font-size: 26px;
            background-color: #ff1493;
            color: yellow;
            font-weight: bold;
        }

        /* Tabla */
        .table-container {
            margin-top: 120px;
            display: none;
        }

        table {
            width: 50%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 3px solid blue;
        }

        th {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        /* Estilos del botón */
        .add-button {
            background-color: #28a745; /* Color verde */
            color: white; /* Color del texto */
            font-size: 24px; /* Tamaño de la fuente */
            width: 50px; /* Ancho */
            height: 50px; /* Alto */
            border-radius: 50%; /* Hace el botón redondo */
            border: none; /* Sin borde */
            display: flex;
            justify-content: center; /* Centra el contenido horizontalmente */
            align-items: center; /* Centra el contenido verticalmente */
            cursor: pointer; /* Cambia el cursor a mano */
            transition: background-color 0.3s; /* Efecto al pasar el mouse */
        }

        .add-button:hover {
            background-color: #218838; /* Color verde más oscuro cuando se pasa el mouse */
        }

        .add-button:focus {
            outline: none; /* Elimina el contorno del botón al hacer clic */
        }
    </style>
</head>
<body>
<div class="container">
    <div class="input-container">
        <div class="input-group">
            <label for="searchInput">Búsqueda</label>
            <div class="autocomplete-container">
                <input type="text" id="searchInput" placeholder="Escribe un nombre..." autocomplete="off">
                <div class="autocomplete-results"></div>
            </div>
        </div>
        <div class="input-group">
            <label for="alumnoRef">Concepto</label>
            <select id="alumnoRef">
                <option value="DCH">DCH</option>
                <option value="DG">DG</option>
                <option value="COMIDA">COMIDA</option>
                <option value="MEDIA">MEDIA</option>
                <option value="ESTANCIA 5">ESTANCIA 5</option>
                <option value="ESTANCIA 7">ESTANCIA 7</option>
                <option value="TAREA 5">TAREA 5</option>
                <option value="TAREA 7">TAREA 7</option>
                <option value="EST. MES 5">EST. MES 5</option>
                <option value="EST. MES 7">EST. MES 7</option>
            </select>
        </div>
        <div class="input-group">
            <button class="add-button" onclick="agregarFila();">+</button>
        </div>
    </div>
    
    <div class="table-container" id="tableContainer">
        <table>
            <thead>
                <tr>
                    <th>Cantidad</th>
                    <th>Descripción</th>
                    <th>Costo</th>
                </tr>
            </thead>
            <tbody id="tablaConceptos">
                <!-- Las filas se agregarán aquí -->
            </tbody>
        </table>
    </div>
</div>

<script>
    function agregarFila() {
        var concepto = $('#alumnoRef').val();

        // Realizamos la consulta para obtener la descripción y el costo
        $.ajax({
            url: 'obtener_servicio.php', // Cambia esto con la URL de tu script PHP que consulta la base de datos
            method: 'GET',
            data: { concepto: concepto },
            success: function (response) {
                var datos = JSON.parse(response);
                var descripcion = datos.descripcion;
                var costo = datos.costo;
                var cantidad = 1;

                // Crear una nueva fila y agregarla a la tabla
                var fila = "<tr><td>" + cantidad + "</td><td>" + descripcion + "</td><td>" + costo + "</td></tr>";
                $('#tablaConceptos').append(fila);

                // Mostrar la tabla si aún no está visible
                $('#tableContainer').show();
            }
        });
    }

    // Autocompletar
    $(document).ready(function () {
        $('#searchInput').on('input', function () {
            var query = $(this).val();
            if (query.length > 2) {
                $.ajax({
                    url: 'buscar_alumno.php', // Cambia esto con la URL de tu script PHP para la búsqueda
                    method: 'GET',
                    data: { query: query },
                    success: function (response) {
                        var resultados = JSON.parse(response);
                        var lista = '';
                        resultados.forEach(function (alumno) {
                            lista += '<div class="autocomplete-item">' + alumno.nombre + '</div>';
                        });
                        $('.autocomplete-results').html(lista).show();

                        $('.autocomplete-item').on('click', function () {
                            $('#searchInput').val($(this).text());
                            $('.autocomplete-results').hide();
                        });
                    }
                });
            } else {
                $('.autocomplete-results').hide();
            }
        });

        $('#alumnoRef').on('change', function () {
            agregarFila();
        });
    });
</script>

</body>
</html>
