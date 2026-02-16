import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

/**
 * Report Export Utility
 * Handles PDF and Word document generation for reports
 * 
 * DESIGN PRINCIPLES:
 * 1. Consistent Header/Footer on every page.
 * 2. Clear professional typography.
 * 3. Safe content margins to avoid text cut-off.
 */

const COMPANY_NAME = "iTech Media Logic";

export const exportToPDF = async ({ element, report, userName, userRole, companyName }) => {
    if (!element) return;

    // Show element briefly for capture
    const originalDisplay = element.style.display;
    element.style.display = 'block';

    try {
        const canvas = await html2canvas(element, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#FFFFFF',
            width: element.offsetWidth,
            height: element.scrollHeight,
            windowWidth: element.offsetWidth,
            windowHeight: element.scrollHeight
        });

        const pdf = new jsPDF('p', 'mm', 'a4');
        const pdfWidth = 210;
        const pdfHeight = 297;

        // LAYOUT CONSTANTS (mm)
        const margin = 20;
        const headerTop = 10;
        const headerBottom = 40; // Where content starts
        const footerStart = 275; // Where footer line starts
        const footerText = 282; // Where footer text is

        const contentWidth = pdfWidth - (margin * 2);
        const contentHeightPerPage = footerStart - headerBottom - 5; // Safe zone

        const scaleFactor = contentWidth / canvas.width;
        const pageHeightInCanvasPx = contentHeightPerPage / scaleFactor;

        let currentSourceY = 0;
        let pageNum = 1;
        const totalPages = Math.ceil(canvas.height / pageHeightInCanvasPx);

        while (currentSourceY < canvas.height) {
            if (pageNum > 1) {
                pdf.addPage();
            }

            // --- HEADER (Every Page) ---
            drawHeader(pdf, {
                report,
                userName,
                userRole,
                companyName: companyName || COMPANY_NAME,
                margin,
                pdfWidth,
                top: headerTop
            });

            // --- CONTENT SLICE ---
            const remainingCanvasHeight = canvas.height - currentSourceY;
            const sliceHeightPx = Math.min(remainingCanvasHeight, pageHeightInCanvasPx);

            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = canvas.width;
            tempCanvas.height = sliceHeightPx;
            const ctx = tempCanvas.getContext('2d');

            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
            ctx.drawImage(
                canvas,
                0, currentSourceY, canvas.width, sliceHeightPx,
                0, 0, canvas.width, sliceHeightPx
            );

            const sliceImgData = tempCanvas.toDataURL('image/png');
            const sliceHeightPdf = sliceHeightPx * scaleFactor;

            // Add slice below header
            pdf.addImage(sliceImgData, 'PNG', margin, headerBottom, contentWidth, sliceHeightPdf);

            // --- FOOTER (Every Page) ---
            drawFooter(pdf, {
                report,
                pageNum,
                totalPages,
                margin,
                pdfWidth,
                pdfHeight,
                lineY: footerStart,
                textY: footerText
            });

            currentSourceY += sliceHeightPx;
            pageNum++;
        }

        const fileName = report.period?.start ? report.period.start.replace(/ /g, '-') : 'Report';
        pdf.save(`Weekly-Report-${fileName}.pdf`);
    } catch (error) {
        console.error('PDF Export failed:', error);
        throw error;
    } finally {
        element.style.display = originalDisplay;
    }
};

const drawHeader = (pdf, { report, userName, userRole, companyName, margin, pdfWidth, top }) => {
    // 1. Right-Aligned Metadata (Gray)
    pdf.setFontSize(8);
    pdf.setTextColor(150);
    pdf.setFont('helvetica', 'normal');
    const periodText = (report.period?.start || 'N/A') + ' — ' + (report.period?.end || 'N/A');
    pdf.text(periodText, pdfWidth - margin, top + 2, { align: 'right' });
    pdf.text('Internship Program', pdfWidth - margin, top + 6, { align: 'right' });

    // 2. Main Title (Bold)
    pdf.setTextColor(0, 0, 0);
    pdf.setFontSize(22);
    pdf.setFont('helvetica', 'bold');
    pdf.text((report.report_title || 'WEEKLY PROGRESS REPORT').toUpperCase(), margin, top + 8);

    // 3. Company & Role Line
    pdf.setFontSize(10);
    pdf.setFont('helvetica', 'bold');
    pdf.setTextColor(80);
    pdf.text(companyName, margin, top + 14);

    // 4. Personal Info Sub-bar
    pdf.setFontSize(9);
    pdf.setTextColor(0);
    pdf.setFont('helvetica', 'bold');
    pdf.text(`Intern: ${userName}`, margin, top + 22);
    pdf.text(`Role: ${userRole}`, 130, top + 22);

    // 5. Heavy Professional Divider
    pdf.setDrawColor(0);
    pdf.setLineWidth(0.8);
    pdf.line(margin, top + 25, pdfWidth - margin, top + 25);
};

const drawFooter = (pdf, { report, pageNum, totalPages, margin, pdfWidth, lineY, textY }) => {
    // Divider
    pdf.setDrawColor(200);
    pdf.setLineWidth(0.2);
    pdf.line(margin, lineY, pdfWidth - margin, lineY);

    // Info
    pdf.setFontSize(8);
    pdf.setTextColor(150);
    pdf.setFont('helvetica', 'normal');
    pdf.text(report.footer_text || 'Generated via Internal Journal System', margin, textY);
    pdf.text(`Page ${pageNum} of ${totalPages}`, pdfWidth - margin, textY, { align: 'right' });
};

export const exportToDocs = ({ report, userName, companyName, marked }) => {
    const docContent = `
        <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
        <head><title>Weekly Report</title></head>
        <body style="font-family: Arial, sans-serif; padding: 40px;">
            <div style="border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 20px;">
                <h1 style="text-transform: uppercase; margin: 0; font-size: 24pt;">Weekly Progress Report</h1>
                <p style="margin: 10px 0 0 0; color: #666; font-size: 14pt;"><strong>${companyName}</strong></p>
                <div style="margin-top: 20px; font-size: 11pt;">
                    <p><strong>Intern:</strong> ${userName}</p>
                    <p><strong>Period:</strong> ${report.period?.start || 'N/A'} - ${report.period?.end || 'N/A'}</p>
                </div>
            </div>
            <div style="line-height: 1.6;">
                ${marked.parse(report.report)}
            </div>
        </body>
        </html>
    `;

    const blob = new Blob(['\ufeff', docContent], { type: 'application/msword' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    const docFileName = report.period?.start ? report.period.start.replace(/ /g, '-') : 'Report';
    link.download = `Weekly-Report-${docFileName}.doc`;
    link.click();
    URL.revokeObjectURL(url);
};
