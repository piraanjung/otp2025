<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการหมัก | Envsogo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

    <style>
        body { background: #f8fafc; font-family: 'Nunito', sans-serif; }
        .batch-card { border-radius: 18px; border: none; transition: 0.2s; }
        .status-filling { border-left: 6px solid #fbbf24; } /* สีเหลือง - กำลังเท */
        .status-composting { border-left: 6px solid #3b82f6; } /* สีฟ้า - กำลังหมัก */
        .status-ready { border-left: 6px solid #22c55e; } /* สีเขียว - พร้อมใช้ */
        .app-container { max-width: 500px; margin: auto; }

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

<div class="container py-4 app-container">

    <div class="d-flex align-items-center mb-4">
        <a href="{{ url()->previous() }}" class="btn btn-light rounded-circle me-3"><i class="bi bi-chevron-left"></i></a>
        <h4 class="fw-bold mb-0">📜 ประวัติล็อตปุ๋ยหมัก</h4>
    </div>

    @foreach($batches as $batch)
    <div class="card batch-card shadow-sm mb-3
        {{ $batch->status == 'filling' ? 'status-filling' : ($batch->status == 'ready' ? 'status-ready' : 'status-composting') }}">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="fw-bold text-dark d-block">ล็อต: {{ $batch->batch_code }}</span>
                    <small class="text-muted"><i class="bi bi-calendar-event"></i> เริ่มเมื่อ: {{ $batch->start_date->format('d M Y') }}</small>
                </div>
                <span class="badge {{ $batch->status == 'filling' ? 'bg-warning text-dark' : ($batch->status == 'ready' ? 'bg-success' : 'bg-primary') }} rounded-pill">
                    {{ $batch->status == 'filling' ? 'กำลังเติม' : ($batch->status == 'ready' ? 'พร้อมใช้' : 'กำลังหมัก') }}
                </span>
            </div>

            <hr class="my-2 opacity-25">

            <div class="row text-center small">
                <div class="col-4 border-end">
                    <div class="text-muted">น้ำหนักเปียก</div>
                    <div class="fw-bold">{{ number_format($batch->wasteLogs->sum('weight_kg'), 1) }} กก.</div>
                </div>
                <div class="col-4 border-end text-success">
                    <div class="text-muted">คาร์บอน</div>
                    <div class="fw-bold">{{ number_format($batch->wasteLogs->sum('carbon_saved_kg'), 2) }}</div>
                </div>
                <div class="col-4">
                    <div class="text-muted">อายุล็อต</div>
                    <div class="fw-bold">{{ abs(round(now()->diffInDays($batch->start_date))) }} วัน</div>
                </div>
            </div>

            <div class="mt-3 d-grid">
                <a href="{{ route('foodwaste.airo.batch_detail', ['id' => $batch->id]) }}"  class="btn btn-outline-secondary btn-sm rounded-pill">ดูรายละเอียดการเทขยะ</a>
            </div>
        </div>
    </div>
    @endforeach

    @if($batches->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2">ยังไม่มีประวัติการหมัก</p>
        </div>
    @endif
</div>
</body>
</html>
