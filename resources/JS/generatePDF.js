function generatePDF(name) {
  const container = document.getElementById("container");

  // Use html2canvas with a higher scale for better quality
  html2canvas(container, {
    scale: 3, // Increase scale for higher resolution (2x or 3x recommended)
  }).then((canvas) => {
    const imgData = canvas.toDataURL("image/png");
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF("p", "mm", "a4");

    // Calculate image dimensions to maintain aspect ratio
    const imgWidth = 210; // A4 width in mms
    const pageHeight = 297; // A4 height in mm
    const imgHeight = (canvas.height * imgWidth) / canvas.width;

    let heightLeft = imgHeight;
    let position = 0;

    // Add the image to the PDF and handle multi-page if content is too long
    pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
    heightLeft -= pageHeight;

    while (heightLeft >= 0) {
      position = heightLeft - imgHeight;
      pdf.addPage();
      pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
      heightLeft -= pageHeight;
    }

    pdf.save(name + ".pdf");
  });
}
