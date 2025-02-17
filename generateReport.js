document.addEventListener('DOMContentLoaded', function() {
    const { jsPDF } = window.jspdf;

    document.getElementById('generateReport').addEventListener('click', function(event) {
        event.preventDefault(); // Prevents the form from submitting

        const doc = new jsPDF();

        // Fetch values from form
        const itemNumber = document.getElementById('itemnumber').value;
        const description = document.getElementById('description').value;
        const vendor = document.getElementById('vendor').value;
        const poNumber = document.getElementById('ponumber').value;

        // Add details to the PDF
        doc.text(20, 20, `Item Number: ${itemNumber}`);
        doc.text(20, 30, `Description: ${description}`);
        doc.text(20, 40, `Vendor: ${vendor}`);
        doc.text(20, 50, `PO Number: ${poNumber}`);

        // Function to handle adding images to PDF
        function addImageToPDF(inputId, x, y, doc) {
            return new Promise((resolve) => {
                const fileInput = document.getElementById(inputId);
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const imgData = event.target.result;
                        doc.addImage(imgData, 'JPEG', x, y, 180, 160); // Adjust positioning and size as needed
                        resolve();
                    };
                    reader.readAsDataURL(file);
                } else {
                    resolve(); // No image to add, resolve immediately
                }
            });
        }

        // Set positions for images in the PDF
        let yOffset = 60; // Initial y position for the images

        // Process and add each image
        async function processImages() {
            await addImageToPDF('product_front_view', 20, yOffset, doc);
            yOffset += 170;
            await addImageToPDF('product_back_view', 20, yOffset, doc);
            yOffset += 170;
            await addImageToPDF('product_side_view', 20, yOffset, doc);
            yOffset += 170;
            await addImageToPDF('gift_box_front_view', 20, yOffset, doc);
            yOffset += 170;
            await addImageToPDF('product_barcode', 20, yOffset, doc);
            yOffset += 170;
            await addImageToPDF('inner_box_barcode', 20, yOffset, doc);
            yOffset += 170;
            await addImageToPDF('outer_box_barcode', 20, yOffset, doc);
            yOffset += 170;
            await addImageToPDF('inner_box_front_view', 20, yOffset, doc);
            yOffset += 170;
            await addImageToPDF('master_carton_front_view', 20, yOffset, doc);
        }

        // Generate the PDF after processing images
        processImages().then(() => {
            doc.save('project_report.pdf');
        });
    });
});
