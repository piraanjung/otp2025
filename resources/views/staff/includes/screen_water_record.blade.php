
    <div class="sub-header"
        style="background: #007bff; color: white; padding: 15px; display: flex; align-items: center; gap: 15px;">
        <button onclick="navigateTo('water')" class="btn-back">⬅️ ย้อนกลับ</button>
        <h4 class="fw-bold mb-0 text-white" style="margin:0;">งานประปา - เลือกเส้นทางจดมิเตอร์</h4>
    </div>

    <div class="container p-2" style="max-width: 800px; margin: 0 auto;">

        <!-- การ์ดแสดงข้อมูลเจ้าหน้าที่ & รอบบิล -->
        <div class="card mb-3" style="border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-primary px-3 py-2 inv_period" style="font-size: 14px;">รอบบิลที่ -</span>
                    <small class="text-muted">เจ้าหน้าที่จดมิเตอร์</small>
                </div>
                <h5 class="fw-bold text-dark mb-0 twman_name">คุณ...</h5>
            </div>
        </div>

        <div class="alert alert-warning fw-bold mb-3" style="border-radius: 8px;">
            📍 เส้นทางจดมิเตอร์ที่รับผิดชอบ
        </div>

        <!-- รายการ Subzone จะถูก Render ใส่ในนี้ -->
        <div class="row g-3" id="subzone">
            <!-- ข้อมูล Render จาก JavaScript -->
        </div>

    </div>
