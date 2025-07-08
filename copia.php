import { jsPDF } from "jspdf";

// Crear un nuevo PDF
const doc = new jsPDF();

// Agregar contenido
doc.text("Hola mundo!", 10, 10);
doc.text("Este es un PDF generado en el navegador", 10, 20);

// Agregar una tabla (opcional)
const data = [
  ["Nombre", "Edad", "País"],
  ["Juan", "30", "México"],
  ["Ana", "25", "España"]
];
doc.autoTable({
  head: [data[0]],
  body: data.slice(1),
  startY: 30
});

// Guardar el PDF
doc.save("documento.pdf");