<div class="modal fade" id="scannerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">สแกน QR Code ตู้ Kiosk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="stopBrowserScanner()"></button>
                </div>
                <div class="modal-body">
                    <div id="reader" style="width: 100%; border-radius: 10px; overflow: hidden;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal"
                        onclick="stopBrowserScanner()">ยกเลิก</button>
                </div>
            </div>
        </div>
    </div>
