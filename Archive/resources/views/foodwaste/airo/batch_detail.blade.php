<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดล็อต {{ $batch->batch_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
       <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

   <style>
        body {
            background: #f8fafc;
        }

        .gallery-img {
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: cover;
            border-radius: 12px;
        }

        .log-card {
            border: none;
            border-radius: 15px;
        }
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
</head>
<body>
<div class="menu-backdrop" id="menuBackdrop"></div>

    <button class="menu-trigger-btn" id="openMenuBtn">
        <i class="bi bi-list"></i>
    </button>
        @include('foodwaste.airo._sidebar', ['userWastePref' => $waste_preference])
    <div class="container py-4" style="max-width: 500px;">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('foodwaste.airo.batch_history') }}" class="btn btn-light rounded-circle me-3"><i
                    class="bi bi-chevron-left"></i></a>
            <div>
                <h4 class="fw-bold mb-0">รายละเอียดล็อต</h4>
                <small class="text-muted">{{ $batch->batch_code }}</small>
            </div>
        </div>

        <h6 class="fw-bold mb-3"><i class="bi bi-images text-success"></i> คลังรูปเศษอาหารในล็อตนี้</h6>
        <div class="row g-2 mb-4">
            @foreach($batch->wasteLogs as $log)
                <div class="col-4">
                    <img src="{{ asset('storage/' . $log->photo_path) }}" class="gallery-img shadow-sm"
                        onclick="showImage(this.src)" data-bs-toggle="modal" data-bs-target="#imgModal">
                </div>
            @endforeach
        </div>

        <h6 class="fw-bold mb-3"><i class="bi bi-list-ul text-primary"></i> บันทึกการทิ้งรายวัน</h6>
        @foreach($batch->wasteLogs as $log)
            <div class="card log-card shadow-sm mb-3">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 text-center" style="min-width: 50px;">
                        <div class="fw-bold text-success">{{ $log->weight_kg }}</div>
                        <small class="text-muted">kg</small>
                    </div>
                    <div class="flex-grow-1 border-start ps-3">
                        <div class="fw-bold small">{{ $log->created_at->format('d M Y | H:i') }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-droplet-fill text-info"></i> ความชื้น: {{ $log->moisture }} |
                            <i class="bi bi-thermometer-half text-danger"></i> ความร้อน:
                            @if($log->temperature_feel == 'warm') อุ่น (ดี)
                            @elseif($log->temperature_feel == 'hot') ร้อน (ทำงานเร็ว)
                            @else เย็น (ช้า) @endif
                            | ผสมแล้ว: {{ $log->is_mixed ? '✅' : '❌' }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="modal fade" id="imgModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0 text-center">
                    <img id="modal_img" src="" class="img-fluid rounded-3">
                </div>
            </div>
        </div>
    </div>

    <script>
        function showImage(src) {
            document.getElementById('modal_img').src = src;
        }
    </script>
</body>

</html>
