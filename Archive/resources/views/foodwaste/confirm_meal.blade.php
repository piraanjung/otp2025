<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันเมนูอาหาร | AiroBact Bin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Kanit', sans-serif; }
        .card-food { border: 2px solid #ffffff; transition: all 0.2s; border-radius: 20px; }
        .card-food:hover { border-color: #0d6efd; }
        .img-preview { width: 100%; max-height: 250px; object-fit: cover; border-radius: 15px; }
        .border-dashed { border-style: dashed !important; border-width: 2px; }
        .new-menu-section { display: none; }
        .pending-box {
            display: none;
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            border-radius: 10px;
            padding: 10px;
            font-size: 0.9rem;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body class="py-4">

<div class="container" style="max-width: 500px;">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <h5 class="text-center text-primary fw-bold mb-3">🍽️ ตรวจสอบเมนูอาหาร</h5>

            <div class="text-center mb-4">
                <img src="{{ asset($path) }}" class="img-preview shadow-sm">
            </div>

            <form action="{{ route('foodwaste.save_meal') }}" method="POST" id="mealForm">
                @csrf
                <input type="hidden" name="photo_path" value="{{ $path }}">

                <h6 class="text-primary fw-bold mb-3">
                    <i class="bi bi-robot"></i> รายการที่ AI วิเคราะห์พบ:
                </h6>

                <div id="food-items-container">
                    @foreach($aiDataArray as $index => $item)
                    <div class="card card-food shadow-sm mb-3 food-item-block" data-ai-cal="{{ $item['calories'] }}">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary rounded-pill px-3">รายการที่ {{ $index + 1 }}</span>
                                <button type="button" class="btn btn-sm text-danger border-0" onclick="this.closest('.food-item-block').remove()">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-muted mb-1">เลือกเมนูอาหาร</label>
                                <select name="selected_foods[]" class="form-select border-primary shadow-none" onchange="toggleNewMenu(this)" required>
                                    <option value="">-- ค้นหาเมนู --</option>
                                    @foreach($localFoods as $category => $foods)
                                        <optgroup label="🍲 {{ $category }}">
                                            @foreach($foods as $food)
                                                <option value="{{ $food->menu_name }}" data-cal="{{ $food->calories }}"
                                                    {{ $item['menu_name'] == $food->menu_name ? 'selected' : '' }}>
                                                    {{ $food->menu_name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                    <option value="NEW_MENU" class="text-success fw-bold"
                                        {{ !isset($localFoods) || $item['menu_name'] != '' ? 'selected' : '' }}>
                                        ➕ เพิ่มเมนูใหม่เอง
                                    </option>
                                </select>
                            </div>

                            <div class="new-menu-section p-2 bg-light border rounded mb-3">
                                <div class="row g-2">
                                    <div class="col-5">
                                        <select name="new_categories[]" class="form-select form-select-sm">
                                            <option value="ทั่วไป">ทั่วไป</option>
                                            <option value="ต้ม/แกง">ต้ม/แกง</option>
                                            <option value="ผัด/ทอด">ผัด/ทอด</option>
                                        </select>
                                    </div>
                                    <div class="col-7">
                                        <input type="text" name="new_menu_names[]" class="form-control form-control-sm"
                                               placeholder="ระบุชื่อเมนู" value="{{ $item['menu_name'] }}">
                                    </div>
                                </div>
                            </div>

                            <div class="cal-management mt-2">
                                <label class="small fw-bold text-muted mb-1">แคลอรี่ (kcal)</label>

                                <input type="number" name="calories[]"
                                       class="form-control cal-input bg-light fw-bold"
                                       value="{{ $item['calories'] }}"
                                       style="display: {{ $item['calories'] > 0 ? 'block' : 'none' }};" readonly>

                                <div class="pending-box" style="display: {{ $item['calories'] > 0 ? 'none' : 'block' }};">
                                    <i class="bi bi-hourglass-split"></i> รอเจ้าหน้าที่วิเคราะห์แคลอรี่
                                </div>
                            </div>

                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-outline-primary w-100 mb-4 border-dashed fw-bold py-2" onclick="addManualItem()">
                    <i class="bi bi-plus-circle-dotted"></i> เพิ่มรายการอาหารเอง (AI มองไม่เห็น)
                </button>

                <button type="submit" class="btn btn-success w-100 fs-5 rounded-pill shadow-sm py-2 fw-bold">
                    ✅ บันทึกมื้ออาหารนี้
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// 1. ฟังก์ชันจัดการเปิด/ปิดกล่องเมนูใหม่ และสลับ Input แคลอรี่
function toggleNewMenu(selectElement) {
    const block = selectElement.closest('.food-item-block');
    const newSection = block.querySelector('.new-menu-section');
    const calInput = block.querySelector('.cal-input');
    const pendingBox = block.querySelector('.pending-box');

    // ดึงค่าแคลอรี่ที่ AI วิเคราะห์มา
    const aiCalorie = parseInt(block.getAttribute('data-ai-cal')) || 0;
    
    // ตรวจสอบว่าเป็นรายการใหม่ที่เพิ่งกดเพิ่มหรือไม่ (ดูจากการมี attribute data-is-manual)
    const isManualItem = block.getAttribute('data-is-manual') === 'true';

    if (selectElement.value === 'NEW_MENU') {
        newSection.style.display = 'block';

        // 🌟 ถ้าไม่ใช่รายการที่กดเพิ่มเอง และ AI ให้แคลอรี่มา ให้โชว์แคลอรี่
        if (!isManualItem && aiCalorie > 0) {
            calInput.style.display = 'block';
            calInput.value = aiCalorie;
            pendingBox.style.display = 'none';
        } else {
            // ถ้าเป็นรายการที่กดเพิ่มเอง หรือ AI ให้แคลอรี่มาเป็น 0 ให้รอเจ้าหน้าที่
            calInput.style.display = 'none';
            calInput.value = 0;
            pendingBox.style.display = 'block';
        }
    } else if (selectElement.value !== '') {
        newSection.style.display = 'none';
        const opt = selectElement.options[selectElement.selectedIndex];
        calInput.value = opt.getAttribute('data-cal');
        calInput.style.display = 'block';   
        pendingBox.style.display = 'none';  
    }
}

// 2. ฟังก์ชันเพิ่มรายการอาหารใหม่ด้วยตัวเอง
function addManualItem() {
    const container = document.getElementById('food-items-container');
    const blocks = container.querySelectorAll('.food-item-block');
    if (blocks.length === 0) return;

    // โคลนรายการล่าสุดมา
    const newBlock = blocks[0].cloneNode(true);
    const count = container.querySelectorAll('.food-item-block').length + 1;

    // รีเซ็ตและตั้งค่าว่าเป็น Manual Item
    newBlock.setAttribute('data-is-manual', 'true');
    newBlock.setAttribute('data-ai-cal', '0'); 
    
    newBlock.querySelector('.badge').innerText = 'รายการที่ ' + count;
    const select = newBlock.querySelector('select');
    select.value = 'NEW_MENU';

    newBlock.querySelector('input[name="new_menu_names[]"]').value = '';

    const calInp = newBlock.querySelector('.cal-input');
    const pendBx = newBlock.querySelector('.pending-box');
    calInp.value = 0;
    calInp.style.display = 'none';
    pendBx.style.display = 'block';

    newBlock.querySelector('.new-menu-section').style.display = 'block';
    container.appendChild(newBlock);
}

// 3. รันเช็คสถานะตอนโหลดหน้าครั้งแรก
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('select[name="selected_foods[]"]').forEach(s => {
        if(s.value === 'NEW_MENU') toggleNewMenu(s);
    });
});

// 4. Spinner ตอนกด Submit
document.getElementById('mealForm').addEventListener('submit', function() {
    Swal.fire({
        title: 'กำลังบันทึก...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
});
</script>

</body>
</html>