function updateGlobalPrinterStatus() {
            const isConnected = localStorage.getItem('is_printer_connected') === 'true';
            const printerName = localStorage.getItem('connected_printer_name') || 'เครื่องพิมพ์';

            const lblName = document.getElementById('lblGlobalPrinterName');
            const lblIndicator = document.getElementById('lblGlobalPrinterIndicator');
            const statusBox = document.getElementById('globalPrinterStatus');

            if (!statusBox || !lblName || !lblIndicator) return;

            if (isConnected || printCharacteristic) {
                lblName.innerText = `🟢 เชื่อมต่ออยู่กับ (${printerName})`;
                lblName.style.color = "#28a745";
                lblIndicator.style.background = "#28a745";
                statusBox.style.borderLeft = "5px solid #28a745";
            } else {
                lblName.innerText = "🔴 ยังไม่ได้เชื่อมต่ออุปกรณ์";
                lblName.style.color = "#dc3545";
                lblIndicator.style.background = "#dc3545";
                statusBox.style.borderLeft = "5px solid #dc3545";
            }
        }
