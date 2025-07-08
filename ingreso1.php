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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
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
            width: 535px; /* INPUT DE BÚSQUEDA MÁS LARGO */
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
            margin-top: 100px;
            display: flex;
            align-items: center; /* Alinea verticalmente */
            gap: 20px; /* Espacio entre la tabla y los inputs */
        }

        table {
            width: 50%;
            border-collapse: separate; /* Permite bordes redondeados */
            border-spacing: 0;
            border-radius: 15px; /* Redondea las esquinas de la tabla */
            overflow: hidden; /* Asegura que las esquinas sean visibles */
            text-align: left;
            
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

        th:first-child {
            border-top-left-radius: 15px; /* Redondea la esquina superior izquierda */
        }

        th:last-child {
            border-top-right-radius: 15px; /* Redondea la esquina superior derecha */
        }

        tr:last-child td:first-child {
            border-bottom-left-radius: 15px; /* Redondea la esquina inferior izquierda */
        }

        tr:last-child td:last-child {
            border-bottom-right-radius: 15px; /* Redondea la esquina inferior derecha */
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
        .input-group {
            display: flex;
            flex-direction: column;
            align-items: right;
            margin-bottom: 10px;
        }

        .input-group label {
            color: red; /* Hace los labels invisibles */
            font-size: 24; /* Alternativa para ocultar sin afectar diseño */
        }

        .input-group input {
            width: 200px; /* Ajusta el ancho del input */
            height: 50px; /* Hace el input más alto */
            font-size: 23px;
            text-align: left;
        }

        #totalCambio {
            color: red;
            font-weight: bold;
        }

        /* Estilo para fechas seleccionadas */
        .flatpickr-day.selected {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }

        /* Estilo para el input de fecha */
        .fecha-seleccion {
            font-size: 18px;
            padding: 5px;
            width: 150px;
            border: 2px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<nav class="navbar">
        <div class="menu">
            <div class="menu-item">
                <a href="#">Ingresos totales</a>
                <div class="dropdown">
                    <div class="dropdown-content">
                        <a href="https://www.winston93.edu.mx/tareas93/ingreso1.php">Desayunos</a>
                        <a href="#">Envíos</a>
                    </div>
                </div>
            </div>
            <div class="menu-item">
            <a href="#">Catálogos</a>
                <div class="dropdown">
                    <div class="dropdown-content">
                    <a href="https://www.winston93.edu.mx/tareas93/nombres.html">Maestros</a>
                    <a href="https://www.winston93.edu.mx/tareas93/nombres.html">Conceptos</a>
                    </div>
                </div>
            </div>
            <div class="menu-item">
                <a href="#">Soporte</a>
                <div class="dropdown">
                    <div class="dropdown-content">
                        <a href="#">Centro de ayuda</a>
                        <a href="#">Contactar</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
<div class="container">
    <div class="input-container">
        <div class="input-group">
            <label for="searchInput">Búsqueda</label>
            <div class="autocomplete-container">
                <input type="text" id="searchInput" placeholder="Escribe un nombre..." autocomplete="off">
                <div class="autocomplete-results"></div>
                <br><br>
                <input type="text" id="alumno" name="alumno" placeholder="">
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

        <!-- Botón de acción -->
        <div class="input-group">
            <!-- Botón verde redondo con el signo de más -->
            <button class="add-button" onclick="actualizarTotal();">+</button>
        </div>
    </div>
    <br><br>  <br><br>  
    <!-- Contenedor de la tabla -->
    <div class="table-container" id="tableContainer">
        <table>
            <thead>
                <tr>
                    <th style="color: red;">Cantidad</th>
                    <th style="color: red;">Descripción</th>
                    <th style="color: red;">Costo</th>
                    <th style="color: red;">Fecha</th>
                    <th style="color: red;">Eliminar</th>
                </tr>
            </thead>
            <tbody id="tablaConceptos">
                <!-- Las filas se agregarán aquí -->
            </tbody>
        </table>
     
        <div class="input-group">
            <label for="efectivo">Efectivo</label>
            <input type="number" id="efectivo" placeholder="" onclick="this.value='';" onkeypress="if(event.key === 'Enter') calcularCambio();">
        </div>

        <!-- Campo de total calculado -->
        <div class="input-group">
            <label for="totalCambio">Total</label>
            <input type="text" id="totalCambio" readonly onclick="this.value='';">
        </div>
    </div>
</div>

<script>
// Variables globales
var total = 0;
var fechasSeleccionadas = [];
var flatpickrInstances = [];

// Función para inicializar Flatpickr con selección múltiple
function inicializarDatePicker(selector) {
    var instance = flatpickr(selector, {
        mode: "multiple",
        dateFormat: "Y-m-d",
        locale: "es",
        defaultDate: new Date().toISOString().split('T')[0],
        onChange: function(selectedDates, dateStr, instance) {
            // Actualizar el array de fechas seleccionadas
            fechasSeleccionadas = selectedDates.map(date => date.toISOString().split('T')[0]);
            
            // Si hay más de una fecha seleccionada, agregar nuevas filas
            if (selectedDates.length > 1) {
                var ultimaFecha = selectedDates[selectedDates.length - 1];
                var fechaStr = ultimaFecha.toISOString().split('T')[0];
                
                // Verificar si ya existe una fila con esta fecha
                var existeFecha = false;
                $('.fecha-seleccion').each(function() {
                    if ($(this).val() === fechaStr) {
                        existeFecha = true;
                        return false; // Salir del bucle each
                    }
                });
                
                if (!existeFecha) {
                    // Clonar la última fila y actualizar la fecha
                    var ultimaFila = $('#tablaConceptos tr:last').clone();
                    ultimaFila.find('.fecha-seleccion').val(fechaStr);
                    $('#tablaConceptos').append(ultimaFila);
                    
                    // Inicializar Flatpickr en el nuevo input
                    inicializarDatePicker(ultimaFila.find('.fecha-seleccion'));
                    
                    // Recalcular totales
                   // recalcularTotal();
                }
            }
        },
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            // Resaltar días seleccionados
            var date = dayElem.dateObj.toISOString().split('T')[0];
            if (fechasSeleccionadas.includes(date)) {
                dayElem.classList.add("selected");
            }
        }
    });
    
    flatpickrInstances.push(instance);
    return instance;
}

// Función para agregar una nueva fila
function agregarFila() {
    var concepto = $('#alumnoRef').val();

    $.ajax({
        url: 'obtener_servicio.php',
        method: 'GET',
        data: { concepto: concepto },
        success: function(response) {
            var datos = JSON.parse(response);
            var descripcion = datos.detalle;
            var costo = parseFloat(datos.importe);
            var cantidad = 1;

            // Crear input de fecha con Flatpickr
            var fechaInput = "<input type='text' class='fecha-seleccion' style='font-size: 18px; padding: 5px; width: 150px;'>";

            var checkbox = "<input type='checkbox' class='eliminar-fila'>";

            var fila = "<tr style='background-color: white; color: black; font-weight: bold; font-size: 22px;'>"
                + "<td class='cantidad'>" + cantidad + "</td>"
                + "<td>" + descripcion + "</td>"
                + "<td class='costo'>" + parseFloat(costo).toFixed(2) + "</td>"
                + "<td>" + fechaInput + "</td>"
                + "<td style='text-align: center;'>" + checkbox + "</td>"
                + "</tr>";

            $('#tablaConceptos').append(fila);
            
            // Inicializar Flatpickr en el nuevo input de fecha
            inicializarDatePicker($('#tablaConceptos tr:last .fecha-seleccion'));
            
            // Agregar evento para eliminar fila
            $('.eliminar-fila').off('change').on('change', function() {
                if ($(this).is(':checked')) {
                    var costoFila = parseFloat($(this).closest('tr').find('.costo').text());
                    total -= costoFila;
                    $(this).closest('tr').remove();
                   // recalcularTotal();
                }                
            });
            
            // Sumar el costo al total
            total += costo;
            $('#tableContainer').show();
            $('#alumnoRef').focus();
        }
    });
}

// Función para recalcular el total sumando los valores de todas las filas
function recalcularTotal() {
    total = 0; // Reiniciar el total
    $('#tablaConceptos tr').each(function() {
        // Solo sumar los valores de las filas que no son de total
        var costoFila = parseFloat($(this).find('.costo').text());
        if (!isNaN(costoFila)) {
            total += costoFila;
        }
    });
    actualizarTotal(); // Actualizar el total después de recalcular
}

// Función para actualizar el total
function actualizarTotal() {
    // Comprobar si ya existe la fila con el total
    if ($('#totalFila').length == 0) {
        // Agregar la fila con el total solo si no existe
        var filaTotal = "<tr id='totalFila' style='background-color: white; color: black; font-weight: bold; font-size: 30px;'><td colspan='2'>Total</td><td>" + total.toFixed(2) + "</td></tr>";
        $('#tablaConceptos').append(filaTotal);  // Lo agregamos al final
    } else {
        // Si ya existe la fila de total, actualizar su valor
        $('#totalFila td:nth-child(3)').text(total.toFixed(2));
    }
}

function calcularCambio() {
    let efectivo = parseFloat(document.getElementById("efectivo").value) || 0;
    let totalCambio = total - efectivo;
    document.getElementById("totalCambio").value = totalCambio.toFixed(2);
}

$(document).ready(function() {
    let selectedIndex = -1;

    // Al abrir la página, posiciona el foco en el input de búsqueda
    $('#searchInput').focus();
    
    // Al hacer clic en el input, lo pone en blanco
    $('#searchInput').on('click', function() {
        $(this).val('');
        $('.autocomplete-results').fadeOut();
    });

    $('#searchInput').on('focus', function() {
        $(this).val('');
        $('#alumnoRef').val('');
        $('.autocomplete-results').fadeOut();
    });

    $('#searchInput').on('input', function() {
        $(this).val($(this).val().toUpperCase());
        let query = $(this).val();
        if (query.length > 1) {
            $.ajax({
                url: 'buscar.php',
                type: 'POST',
                data: { query: query },
                success: function(data) {
                    $('.autocomplete-results').html(data).fadeIn();
                }
            });
        } else {
            $('.autocomplete-results').fadeOut();
        }
    });

    $(document).on('click', '.autocomplete-item', function() {
        let nombre = $(this).text();
        let referencia = $(this).data('ref');
        $('#searchInput').val(nombre);
        $('#alumno').val(referencia);
        $('.autocomplete-results').fadeOut();
        // Al seleccionar un registro del autocompletado, mueve el foco al select
        $('#alumnoRef').focus();
    });

    $(document).on('mouseover', '.autocomplete-item', function() {
        $('.autocomplete-item').removeClass('active');
        $(this).addClass('active');
    });

    $('#searchInput').keydown(function(e) {
        let items = $('.autocomplete-item');
        if (items.length === 1) { // Si solo hay un resultado
            if (e.key === 'Enter') {
                items.first().click(); // Selecciona el único registro al presionar Enter
                e.preventDefault();
            }
        } else {
            if (e.key === 'ArrowDown') {
                selectedIndex = (selectedIndex + 1) % items.length;
                items.removeClass('active').eq(selectedIndex).addClass('active');
            } else if (e.key === 'ArrowUp') {
                selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                items.removeClass('active').eq(selectedIndex).addClass('active');
            } else if (e.key === 'Enter' && selectedIndex >= 0) {
                items.eq(selectedIndex).click();
                e.preventDefault();
            }
        }
    });

    $(document).click(function(e) {
        if (!$(e.target).closest('.autocomplete-container').length) {
            $('.autocomplete-results').fadeOut();
        }
    });

    /* Mover con TAB del input al select y abrirlo */
    $('#searchInput').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $('#alumnoRef').focus();
        }
    });

    /* Volver al tamaño original al perder foco */
    $('#alumnoRef').on('blur', function() {
        $(this).prop('size', 1); // Restaurar el tamaño del select
    });

    $('#alumnoRef').on('keydown', function(e) {
        if (e.key === 'Enter') {
            $('#tableContainer').fadeIn(); // Mostrar la tabla
            $('#alumnoRef').prop('size', 1); // Cerrar el select
            $('#alumnoRef').blur(); // Quitar el foco para cerrarlo completamente
            agregarFila();
        }
    });
    
    $('#alumnoRef').on('keydown', function(e) {
        if (event.ctrlKey) {
            $('#tableContainer').fadeIn(); // Mostrar la tabla
            $('#alumnoRef').prop('size', 1); // Cerrar el select
            $('#alumnoRef').blur(); // Quitar el foco para cerrarlo completamente
            actualizarTotal();
            // Al seleccionar un registro del autocompletado, mueve el foco al select
            $('#efectivo').focus();
        }
    });

    /* Estilo cuando el select recibe foco */
    $('#alumnoRef').on('focus', function() {
        $(this).css({
            'background-color': 'yellow',
            'color': 'red',
            'font-size': '24px',
            'font-weight': 'normal',
            'padding': '18px'
        });
    });

    /* Volver al estilo original al perder foco */
    $('#alumnoRef').on('blur', function() {
        $(this).css({
            'background-color': '#e0e0e0',
            'color': 'black',
            'font-size': '22px',
            'font-weight': 'normal',
            'padding': '15px'
        });
    });
});

document.addEventListener("keydown", function(event) {
    if (event.ctrlKey) {
        $('#tableContainer').fadeIn(); // Mostrar la tabla
        $('#alumnoRef').prop('size', 1); // Cerrar el select
        $('#alumnoRef').blur(); // Quitar el foco para cerrarlo completamente
        actualizarTotal();
        // Al seleccionar un registro del autocompletado, mueve el foco al select
        $('#efectivo').focus();
    }
});

document.addEventListener("keydown", function(event) {
    if (event.key === "Escape") {
        let alumnoRef = $("#alumno").val(); // Obtener el número del alumno
        let datos = [];
        
        // Verifica si hay filas en la tabla
        let filas = $("#tablaConceptos tr"); // Selector corregido
        console.log("Número de filas encontradas:", filas.length); // Depuración

        if (filas.length === 0) {
            alert("No hay filas en la tabla.");
            return; // Salir si no hay filas
        }

        // Recorre las filas de la tabla
        filas.each(function() {
            let descripcion = $(this).find("td:eq(1)").text();
            let costo = $(this).find("td:eq(2)").text();
            let fecha = $(this).find("td:eq(3) input").val();

            console.log("Fila encontrada:", { descripcion, costo, fecha }); // Depuración

            if (descripcion && costo && fecha) {
                datos.push({
                    alumno: alumnoRef,
                    concepto: descripcion,
                    costo: costo,
                    fecha: fecha
                });
            }
        });

        if (datos.length > 0) {
            console.log("Datos a enviar:", datos); // Depuración
            $.ajax({
                url: "guardar_datos_desayunos.php",
                type: "POST",
                data: { datos: JSON.stringify(datos) },
                success: function(response) {
                    console.log("Respuesta del servidor:", response); // Depuración
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error("Error en la solicitud AJAX:", error); // Depuración
                    alert("Error al guardar los datos.");
                }
            });
        } else {
            alert("No hay datos para guardar.");
        }
    }
});
</script>
</body>
</html>