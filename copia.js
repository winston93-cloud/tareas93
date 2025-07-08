function generarPDFDesdeTabla() {
  const doc = new jsPDF();
  
  // Título
  doc.setFontSize(20);
  doc.text('Reporte de Pagos', 105, 15, { align: 'center' });
  
  // Obtener datos de la tabla
  const rows = [];
  $('#tablaConceptos tr').each(function() {
    const row = [];
    $(this).find('td').each(function() {
      // Excluir columna de checkbox
      if(!$(this).find('input[type="checkbox"]').length) {
        row.push($(this).text().trim() || $(this).find('input').val());
      }
    });
    if(row.length > 0) rows.push(row);
  });
  
  // Agregar tabla al PDF
  doc.autoTable({
    head: [['Cantidad', 'Descripción', 'Costo', 'Fecha']],
    body: rows,
    startY: 25,
    styles: {
      fontSize: 10,
      cellPadding: 2,
      halign: 'center'
    },
    headStyles: {
      fillColor: [41, 128, 185],
      textColor: 255
    }
  });
  
  // Total
  const total = $('#totalFila td:nth-child(3)').text();
  doc.text(`Total: ${total}`, 14, doc.lastAutoTable.finalY + 10);
  
  // Guardar
  doc.save(`reporte_${new Date().toISOString().slice(0,10)}.pdf`);
}

// Uso: agregar botón que llame a generarPDFDesdeTabla()