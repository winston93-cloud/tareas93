/**
 * Dependencias necesarias:
 * 
 * jQuery 3.6.0:
 * <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 * 
 * Flatpickr (para el selector de fechas):
 * <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
 * <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
 * <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
 * 
 * Estilos CSS:
 * <link rel="stylesheet" href="styles.css">
 */

// Cargar dependencias dinámicamente
function loadDependencies() {
    // Cargar jQuery
    if (typeof jQuery === 'undefined') {
        const jqueryScript = document.createElement('script');
        jqueryScript.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        document.head.appendChild(jqueryScript);
    }

    // Cargar Flatpickr
    if (typeof flatpickr === 'undefined') {
        const flatpickrCss = document.createElement('link');
        flatpickrCss.rel = 'stylesheet';
        flatpickrCss.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css';
        document.head.appendChild(flatpickrCss);

        const flatpickrScript = document.createElement('script');
        flatpickrScript.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
        document.head.appendChild(flatpickrScript);

        const flatpickrEsScript = document.createElement('script');
        flatpickrEsScript.src = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js';
        document.head.appendChild(flatpickrEsScript);
    }

    // Cargar estilos CSS personalizados
    const customCss = document.createElement('link');
    customCss.rel = 'stylesheet';
    customCss.href = 'styles.css';
    document.head.appendChild(customCss);
}

// Cargar dependencias cuando el documento esté listo
document.addEventListener('DOMContentLoaded', loadDependencies);

// Variables globales
let total = 0;
let fechasSeleccionadas = [];
let flatpickrInstances = [];

// Mapa de servicios
const serviciosMap = {
    DCH: { detalle: 'Desayuno', importe: 50.00 },
    DG: { detalle: 'Desayuno Grande', importe: 70.00 },
    COMIDA: { detalle: 'Comida', importe: 80.00 },
    MEDIA: { detalle: 'Media', importe: 40.00 },
    'ESTANCIA 5': { detalle: 'Estancia 5', importe: 100.00 },
    'ESTANCIA 7': { detalle: 'Estancia 7', importe: 120.00 },
    'TAREA 5': { detalle: 'Tarea 5', importe: 60.00 },
    'TAREA 7': { detalle: 'Tarea 7', importe: 80.00 },
    'EST. MES 5': { detalle: 'Estancia Mensual 5', importe: 500.00 },
    'EST. MES 7': { detalle: 'Estancia Mensual 7', importe: 700.00 }
};

// Función para inicializar Flatpickr
function inicializarDatePicker(selector) {
    const instance = flatpickr(selector, {
        mode: "multiple",
        dateFormat: "Y-m-d",
        locale: "es",
        defaultDate: new Date().toISOString().split('T')[0],
        onChange: function(selectedDates, dateStr, instance) {
            fechasSeleccionadas = selectedDates.map(date => date.toISOString().split('T')[0]);
            
            if (selectedDates.length > 1) {
                const ultimaFecha = selectedDates[selectedDates.length - 1];
                const fechaStr = ultimaFecha.toISOString().split('T')[0];
                
                let existeFecha = false;
                document.querySelectorAll('.fecha-seleccion').forEach(input => {
                    if (input.value === fechaStr) {
                        existeFecha = true;
                    }
                });
                
                if (!existeFecha) {
                    const ultimaFila = document.querySelector('#tablaConceptos tr:last-child').cloneNode(true);
                    ultimaFila.querySelector('.fecha-seleccion').value = fechaStr;
                    document.getElementById('tablaConceptos').appendChild(ultimaFila);
                    
                    inicializarDatePicker(ultimaFila.querySelector('.fecha-seleccion'));
                }
            }
        },
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            const date = dayElem.dateObj.toISOString().split('T')[0];
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
    const concepto = document.getElementById('alumnoRef').value;
    const servicio = serviciosMap[concepto];
    
    if (servicio) {
        const descripcion = servicio.detalle;
        const costo = parseFloat(servicio.importe);
        const cantidad = 1;

        const fechaInput = "<input type='text' class='fecha-seleccion' style='font-size: 18px; padding: 5px; width: 150px;'>";
        const checkbox = "<input type='checkbox' class='eliminar-fila'>";

        const fila = document.createElement('tr');
        fila.style.backgroundColor = 'white';
        fila.style.color = 'black';
        fila.style.fontWeight = 'bold';
        fila.style.fontSize = '22px';
        fila.innerHTML = `
            <td class='cantidad'>${cantidad}</td>
            <td>${descripcion}</td>
            <td class='costo'>${costo.toFixed(2)}</td>
            <td>${fechaInput}</td>
            <td style='text-align: center;'>${checkbox}</td>
        `;

        document.getElementById('tablaConceptos').appendChild(fila);
        
        inicializarDatePicker(fila.querySelector('.fecha-seleccion'));
        
        fila.querySelector('.eliminar-fila').addEventListener('change', function() {
            if (this.checked) {
                const costoFila = parseFloat(this.closest('tr').querySelector('.costo').textContent);
                total -= costoFila;
                this.closest('tr').remove();
                actualizarTotal();
            }
        });
        
        total += costo;
        document.getElementById('tableContainer').style.display = 'block';
        document.getElementById('alumnoRef').focus();
    }
}

// Función para recalcular el total
function recalcularTotal() {
    total = 0;
    document.querySelectorAll('#tablaConceptos tr').forEach(tr => {
        const costoFila = parseFloat(tr.querySelector('.costo')?.textContent);
        if (!isNaN(costoFila)) {
            total += costoFila;
        }
    });
    actualizarTotal();
}

// Función para actualizar el total
function actualizarTotal() {
    const totalFila = document.getElementById('totalFila');
    if (!totalFila) {
        const filaTotal = document.createElement('tr');
        filaTotal.id = 'totalFila';
        filaTotal.style.backgroundColor = 'white';
        filaTotal.style.color = 'black';
        filaTotal.style.fontWeight = 'bold';
        filaTotal.style.fontSize = '30px';
        filaTotal.innerHTML = `<td colspan='2'>Total</td><td>${total.toFixed(2)}</td>`;
        document.getElementById('tablaConceptos').appendChild(filaTotal);
    } else {
        totalFila.querySelector('td:nth-child(3)').textContent = total.toFixed(2);
    }
}

// Función para calcular el cambio
function calcularCambio() {
    const efectivo = parseFloat(document.getElementById('efectivo').value) || 0;
    const totalCambio = total - efectivo;
    document.getElementById('totalCambio').value = totalCambio.toFixed(2);
}

// Event Listeners cuando el documento está listo
document.addEventListener('DOMContentLoaded', function() {
    let selectedIndex = -1;

    // Enfocar el input de búsqueda al cargar
    document.getElementById('searchInput').focus();
    
    // Eventos para el input de búsqueda
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('click', function() {
        this.value = '';
        document.querySelector('.autocomplete-results').style.display = 'none';
    });

    searchInput.addEventListener('focus', function() {
        this.value = '';
        document.getElementById('alumnoRef').value = '';
        document.querySelector('.autocomplete-results').style.display = 'none';
    });

    searchInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
        const query = this.value;
        if (query.length > 1) {
            // Aquí iría la llamada AJAX a tu backend PHP
            // Por ahora usamos un array de ejemplo
            const resultados = [
                { nombre: 'Ejemplo 1', id: '1' },
                { nombre: 'Ejemplo 2', id: '2' }
            ];
            
            const resultadosHTML = resultados.map(r => 
                `<div class="autocomplete-item" data-ref="${r.id}">${r.nombre}</div>`
            ).join('');
            
            document.querySelector('.autocomplete-results').innerHTML = resultadosHTML;
            document.querySelector('.autocomplete-results').style.display = 'block';
        } else {
            document.querySelector('.autocomplete-results').style.display = 'none';
        }
    });

    // Eventos para el select de concepto
    const alumnoRef = document.getElementById('alumnoRef');
    alumnoRef.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('tableContainer').style.display = 'block';
            this.size = 1;
            this.blur();
            agregarFila();
        }
    });

    alumnoRef.addEventListener('focus', function() {
        this.style.backgroundColor = 'yellow';
        this.style.color = 'red';
        this.style.fontSize = '24px';
        this.style.fontWeight = 'normal';
        this.style.padding = '18px';
    });

    alumnoRef.addEventListener('blur', function() {
        this.style.backgroundColor = '#e0e0e0';
        this.style.color = 'black';
        this.style.fontSize = '22px';
        this.style.fontWeight = 'normal';
        this.style.padding = '15px';
    });

    // Evento para guardar datos (ESC)
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const alumnoRef = document.getElementById('alumno').value;
            const datos = [];
            
            document.querySelectorAll('#tablaConceptos tr').forEach(tr => {
                const descripcion = tr.querySelector('td:nth-child(2)').textContent;
                const costo = tr.querySelector('td:nth-child(3)').textContent;
                const fecha = tr.querySelector('.fecha-seleccion').value;

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
                // Aquí iría la llamada AJAX a tu backend PHP para guardar los datos
                console.log('Datos a guardar:', datos);
                // location.reload();
            } else {
                alert('No hay datos para guardar.');
            }
        }
    });
});

// Configuración de la base de datos
const dbConfig = {
    host: 'localhost',
    user: 'winston_richard',
    password: '101605',
    database: 'winston_general'
};

// Función para conectar a la base de datos
async function connectDB() {
    try {
        const conn = await mysql.createConnection(dbConfig);
        return conn;
    } catch (error) {
        console.error('Error de conexión:', error);
        throw error;
    }
}

// Función para vaciar y poblar la tabla alumno_todos
async function resetAlumnoTodos() {
    const conn = await connectDB();
    try {
        // Vaciar la tabla
        await conn.query('TRUNCATE TABLE alumno_todos');
        
        // Insertar datos de alumno
        await conn.query('INSERT INTO alumno_todos SELECT * FROM alumno');
        
        // Insertar datos de alumno_maestros
        await conn.query('INSERT INTO alumno_todos SELECT * FROM alumno_maestros');
        
        console.log('Tabla alumno_todos actualizada correctamente');
    } catch (error) {
        console.error('Error al actualizar alumno_todos:', error);
        throw error;
    } finally {
        await conn.end();
    }
}

// Función para buscar alumnos
async function buscarAlumnos(query) {
    const conn = await connectDB();
    try {
        const [rows] = await conn.query(
            'SELECT nombre, id FROM alumno_todos WHERE nombre LIKE ? LIMIT 10',
            [`%${query}%`]
        );
        return rows;
    } catch (error) {
        console.error('Error en la búsqueda:', error);
        throw error;
    } finally {
        await conn.end();
    }
}

// Función para guardar datos de desayunos
async function guardarDesayunos(datos) {
    const conn = await connectDB();
    try {
        for (const dato of datos) {
            await conn.query(
                'INSERT INTO desayunos (alumno, concepto, costo, fecha) VALUES (?, ?, ?, ?)',
                [dato.alumno, dato.concepto, dato.costo, dato.fecha]
            );
        }
        console.log('Datos guardados correctamente');
    } catch (error) {
        console.error('Error al guardar los datos:', error);
        throw error;
    } finally {
        await conn.end();
    }
}

// Función para obtener detalles del servicio
function obtenerServicio(concepto) {
    const serviciosMap = {
        DCH: { detalle: 'Desayuno', importe: 50.00 },
        DG: { detalle: 'Desayuno Grande', importe: 70.00 },
        COMIDA: { detalle: 'Comida', importe: 80.00 },
        MEDIA: { detalle: 'Media', importe: 40.00 },
        'ESTANCIA 5': { detalle: 'Estancia 5', importe: 100.00 },
        'ESTANCIA 7': { detalle: 'Estancia 7', importe: 120.00 },
        'TAREA 5': { detalle: 'Tarea 5', importe: 60.00 },
        'TAREA 7': { detalle: 'Tarea 7', importe: 80.00 },
        'EST. MES 5': { detalle: 'Estancia Mensual 5', importe: 500.00 },
        'EST. MES 7': { detalle: 'Estancia Mensual 7', importe: 700.00 }
    };
    
    return serviciosMap[concepto] || null;
}

// Exportar las funciones para su uso en el frontend
window.backend = {
    resetAlumnoTodos,
    buscarAlumnos,
    guardarDesayunos,
    obtenerServicio
};