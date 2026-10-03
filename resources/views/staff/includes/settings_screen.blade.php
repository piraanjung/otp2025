   <div id="settingsScreen" class="is-hidden">
        <div class="sub-header">
            <button onclick="backToMenuFromSettings()" class="btn-back">⬅️ กลับเมนูหลัก</button>
            <h3 style="margin: 0;">ตั้งค่าระบบเครื่องพิมพ์</h3>
        </div>

        <div class="container" style="max-width: 500px; margin: 20px auto;">
            <div class="card" style="text-align: center; padding: 25px 20px;">
                <div style="font-size: 50px; margin-bottom: 10px;">🖨️</div>
                <h4 style="margin: 0 0 10px 0; font-weight: bold;">Bluetooth Thermal Printer</h4>
                <p style="color: #6c757d; font-size: 14px; margin-bottom: 20px;">
                    กรุณาเปิดเครื่องพิมพ์ใบเสร็จและจับคู่บลูทูธบนระบบสมาร์ทโฟนก่อนทำการกดเชื่อมต่อ
                </p>

                <div
                    style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e3e6f0;">
                    <span id="status" style="font-weight: bold; font-size: 15px; color: #858796;">
                        🔴 ยังไม่ได้เชื่อมต่ออุปกรณ์
                    </span>
                </div>

                <div class="bottom-action-bar">
                    <button id="connectButton" class="btn btn-info col-4">
                        <span>🔄 ค้นหา & เชื่อมต่ออุปกรณ์</span>
                    </button>
                    <button id="printImageButton" onclick="printReceipt()" class="btn btn-primary col-8 shadow-sm">
                        <span class="material-icons-round">print</span>
                        <span id="printBtnText">พิมพ์ใบเสร็จ</span>
                    </button>
                </div>
                <div class="row justify-content-center mb-4">
                    <div class="col-12 col-md-6">
                        <div id="status-card" class="status-badge bg-light text-secondary border">
                            <span class="material-icons-round text-primary">info</span>
                            <div>
                                <small class="d-block text-uppercase fw-bold"
                                    style="font-size: 0.7rem;">สถานะการเชื่อมต่อ</small>
                                <span id="status-text">ยังไม่ได้เชื่อมต่อ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <canvas id="canvas" style="display: none;"></canvas>
            </div>
        </div>
    </div>