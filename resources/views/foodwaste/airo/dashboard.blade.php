<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AiroBact Bin | บันทึกข้อมูล</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* --- CSS ของเดิมของคุณ --- */
        body { background-color: #f4f7f6; font-family: 'Nunito', sans-serif; overflow-x: hidden; }
        .app-container { max-width: 500px; margin: auto; padding-bottom: 30px; }
        .card { border-radius: 20px; border: none; overflow: hidden; }
        .upload-area { border: 2px dashed #cbd5e1; border-radius: 15px; background-color: #f8fafc; cursor: pointer; }
        .preview-img { max-height: 250px; object-fit: cover; border-radius: 15px; display: none; }
        .form-label-custom { font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 8px; display: block; }

        /* --- 🌟 1. เพิ่ม CSS สำหรับ Sidebar และปุ่มเปิด (เพื่อให้ซ่อนและเลื่อนได้) --- */
        .menu-trigger-btn {
            position: fixed; top: 15px; left: 15px; z-index: 1030;
            width: 45px; height: 45px; border-radius: 50%;
            background: #fff; border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            color: #11998e; display: flex; align-items: center; justify-content: center; font-size: 24px;
        }

        .menu-backdrop {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 1040;
            opacity: 0; visibility: hidden; transition: 0.3s; backdrop-filter: blur(2px);
        }
        .menu-backdrop.active { opacity: 1; visibility: visible; }

        .modern-sidebar {
            position: fixed; top: 0; left: -280px; width: 280px; height: 100%;
            background: #fff; z-index: 1045; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 15px rgba(0,0,0,0.1); display: flex; flex-direction: column;
        }
        .modern-sidebar.active { left: 0; }

        /* ตกแต่งลิงก์ Sidebar เบื้องต้น */
        .sidebar-link { display: flex; align-items: center; padding: 12px 20px; color: #444; text-decoration: none; }
        .sidebar-divider { padding: 15px 20px 5px; font-size: 0.8rem; color: #888; font-weight: bold; text-transform: uppercase; }
        .sidebar-header { padding: 30px 20px; background: linear-gradient(135deg, #11998e, #38ef7d); color: white; }
    </style>
        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

</head>
<body>
    <div class="menu-backdrop" id="menuBackdrop"></div>

    <button class="menu-trigger-btn" id="openMenuBtn">
        <i class="bi bi-list"></i>
    </button>

    <div class="container py-3 app-container">
@include('foodwaste.airo._sidebar', ['userWastePref' => $waste_preference])
    <div class="d-flex align-items-center justify-content-between mb-4">
        <a href="{{ url('line/dashboard/'.$waste_preference->wastePreference->id.'/'.Auth::user()->org_id_fk) }}" class="btn btn-light rounded-circle shadow-sm"><i class="bi bi-chevron-left"></i></a>
        <h4 class="text-success fw-bold mb-0">🌱 AiroBact Bin</h4>
        <div style="width: 40px;"></div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="section-title text-warning"><i class="bi bi-camera-fill"></i> 1. ถ่ายรูปมื้ออาหาร</div>
            <form id="mealForm" action="{{ route('foodwaste.store_meal') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="meal_photo" class="upload-area w-100 text-center py-4 mb-3 d-block">
                    <div id="meal_placeholder">
                        <i class="bi bi-plus-circle-dotted text-muted" style="font-size: 2.5rem;"></i>
                        <div class="text-muted mt-2 small">แตะเพื่อถ่ายรูปอาหารก่อนกิน</div>
                    </div>
                    <img id="meal_preview" class="preview-img img-fluid w-100 mt-2">
                </label>
                <input type="file" id="meal_photo" name="meal_photo" class="d-none" accept="image/*" capture="camera" required onchange="previewImage(this, 'meal_preview', 'meal_placeholder')">
                <button type="submit" class="btn btn-warning w-100 rounded-pill py-2 shadow-sm fw-bold">วิเคราะห์เมนูด้วย AI ✨</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4 border-top border-success border-4">
        <div class="card-body p-4">
            <div class="section-title text-success"><i class="bi bi-trash3-fill"></i> 2. บันทึกขยะลงถัง</div>

            @if($latestMeals->isNotEmpty())
                <div class="mb-4 p-3 rounded-4 bg-light border-0" style="background-color: #f1f5f9 !important;">
                    <p class="text-muted small fw-bold mb-2">🍽️ มื้อที่เพิ่งบันทึกไป:</p>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset( $latestEntry->photo_path) }}" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover; margin-right: 12px;">
                        <div class="flex-grow-1 small">
                            @foreach($latestMeals as $meal)
                                <span class="badge bg-white text-dark border fw-normal">{{ $meal->menu_name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <form id="store_waste_form" action="{{ route('foodwaste.store_waste') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="waste_photo" class="upload-area w-100 text-center py-4 mb-4 d-block">
                    <div id="waste_placeholder">
                        <i class="bi bi-recycle text-muted" style="font-size: 2.5rem;"></i>
                        <div class="text-muted mt-2 small">ถ่ายรูปเศษอาหารที่เหลือทิ้ง</div>
                    </div>
                    <img id="waste_preview" class="preview-img img-fluid w-100 mt-2">
                </label>
                <input type="file" id="waste_photo" name="waste_photo" class="d-none" accept="image/*" capture="camera" required onchange="previewImage(this, 'waste_preview', 'waste_placeholder')">

                <div class="mb-4">
                    <label class="form-label-custom">น้ำหนักเศษอาหาร (กก.)</label>
                    <input type="number" step="0.01" name="weight_kg" class="form-control form-control-lg border-0 bg-light rounded-pill ps-4" placeholder="0.00" required>
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <label class="form-label-custom">ความชื้น</label>
                        <select name="moisture" class="form-select border-0 bg-light rounded-pill">
                            <option value="good">ร่วนพอดี</option>
                            <option value="dry">แห้งเกิน</option>
                            <option value="wet">แฉะเกิน</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label-custom">ความร้อนถัง</label>
                        <select name="temperature_feel" class="form-select border-0 bg-light rounded-pill">
                            <option value="warm">อุ่น (ดีมาก)</option>
                            <option value="hot">ร้อน (ทำงานเร็ว)</option>
                            <option value="cool">เย็น (นิ่ง/ช้า)</option>
                        </select>
                    </div>
                </div>

                <div class="form-check mb-4 bg-light p-3 rounded-4">
                    <input class="form-check-input ms-1" type="checkbox" name="is_mixed" id="mixed" style="transform: scale(1.2);">
                    <label class="form-check-label ms-3 small fw-bold text-success" for="mixed">คลุกเคล้าขยะเรียบร้อยแล้ว</label>
                </div>

                <button type="submit" class="btn btn-success w-100 rounded-pill py-3 shadow-sm fw-bold fs-5">บันทึกลงถังหมัก 🌿</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function previewImage(input, previewId, placeholderId) {
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

   $(document).ready(function() {
    // ใช้คำสั่ง .on('submit') กับ ID ที่ถูกต้อง
    $(document).on('submit', '#mealForm', function(e) {
        // เช็คก่อนว่าฟอร์มนี้มีอยู่จริงไหม (กัน Error)
        if (this) {
            Swal.fire({
                title: 'กำลังวิเคราะห์อาหาร...',
                text: 'กรุณารอสักครู่ AI กำลังประมวลผลให้คุณ',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        return true; // ปล่อยให้ฟอร์มส่งค่าไปที่ Server
    });
});
</script>

<script>
    // ดักจับเหตุการณ์ตอนกดปุ่ม Submit ฟอร์ม
    document.getElementById('store_waste_form').addEventListener('submit', function() {
        // แสดงหน้าจอโหลดและบล็อคการคลิกทั้งหมด
        Swal.fire({
            title: 'กำลังบันทึกลงถังหมัก...',
            html: 'กรุณารอสักครู่ ระบบกำลังจัดเก็บข้อมูลของคุณ ♻️',
            allowOutsideClick: false, // ห้ามคลิกพื้นที่ว่างเพื่อปิด
            allowEscapeKey: false,    // ห้ามกดปุ่ม ESC เพื่อปิด
            showConfirmButton: false, // ซ่อนปุ่ม "ตกลง"
            didOpen: () => {
                Swal.showLoading();   // แสดงสปินเนอร์หมุนๆ
            }
        });
        // ปล่อยให้ฟอร์มทำงานต่อส่งค่าไปที่ Controller
    });
</script>
</body>
</html>
