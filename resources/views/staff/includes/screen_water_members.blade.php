<!-- Header -->
<div class="sub-header bg-primary text-white p-3 d-flex align-items-center gap-2 sticky-top shadow-sm">
    <button onclick="navigateTo('water-tabwater-record')" class="btn btn-sm btn-light text-primary fw-bold">⬅️
        ย้อนกลับ</button>
    <div>
        <h6 class="mb-0 fw-bold" id="selectedSubzoneTitle">เส้นทาง: หมู่ 1 บ้านคำปอม</h6>
        <small class="text-white-50" id="selectedSubzoneSubtitle">รอบบิลปัจจุบัน</small>
    </div>
</div>

<div class="container p-2" style="max-width: 800px; margin: 0 auto;">

    <!-- 1. ค้นหา & สแกน QR Code -->
    <div class="card border-0 shadow-sm rounded-3 mb-3">
        <div class="card-body p-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search">🔍</i></span>
                <input type="text" id="searchWaterMemberInput" class="form-control border-start-0"
                    placeholder="ค้นหาชื่อ, บ้านเลขที่, เลขมิเตอร์..." onkeyup="filterWaterMembers()">
                <button class="btn btn-outline-primary fw-bold d-flex align-items-center gap-1" type="button"
                    onclick="startQRScanForWater()">
                    📷 สแกน QR
                </button>
            </div>
        </div>
    </div>

    <!-- 2. แท็บบาร์กรองสถานะ (Filter Tabs) -->
    <div class="d-flex gap-2 mb-3">
        <button class="btn btn-sm btn-primary flex-fill fw-bold filter-tab active"
            onclick="setWaterFilter('all', this)">
            ทั้งหมด (<span id="countAll">0</span>)
        </button>
        <button class="btn btn-sm btn-outline-warning flex-fill fw-bold filter-tab"
            onclick="setWaterFilter('pending', this)">
            ยังไม่ได้จด (<span id="countPending">0</span>)
        </button>
        <button class="btn btn-sm btn-outline-success flex-fill fw-bold filter-tab"
            onclick="setWaterFilter('completed', this)">
            จดแล้ว (<span id="countCompleted">0</span>)
        </button>
    </div>

    <!-- 3. รายการผู้ใช้น้ำ (Member Cards Container) -->
    <div id="waterMemberListContainer" class="d-flex flex-column gap-2">
        <!-- ตัวอย่างการ์ด UI (Mockup สำหรับดู Layout) -->
    </div>

</div>
</div>
