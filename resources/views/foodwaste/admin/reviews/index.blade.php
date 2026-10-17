<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | ตรวจสอบแคลอรี่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Kanit', sans-serif;
        }

        .meal-img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-primary"><i class="bi bi-card-checklist"></i> คิวงาน: รอวิเคราะห์แคลอรี่</h3>
            <span class="badge bg-warning text-dark fs-6 py-2 px-3">
                รอตรวจสอบ {{ $pendingItems->count() }} รายการ
            </span>
        </div>

        <!-- Alert แจ้งเตือนเวลาเซฟสำเร็จ -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="ps-4">รูปภาพอ้างอิง</th>
                                <th scope="col">ชื่อเมนู (User ระบุ)</th>
                                <th scope="col">หมวดหมู่</th>
                                <th scope="col">เวลาที่ส่ง</th>
                                <th scope="col" style="width: 250px;" class="pe-4">ระบุแคลอรี่ (kcal)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingItems as $item)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <!-- รูปภาพมื้ออาหารจากตาราง meal_logs -->
                                        <a href="{{ asset($item->mealLog->photo_path) }}" target="_blank">
                                            <img src="{{ asset($item->mealLog->photo_path) }}"
                                                class="meal-img shadow-sm border">
                                        </a>
                                    </td>
                                    <td>
                                        <span class="fw-bold fs-5 text-dark">{{ $item->menu_name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary rounded-pill fw-normal">{{ $item->category }}</span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $item->created_at->format('d/m/Y H:i') }}<br>
                                        ({{ $item->created_at->diffForHumans() }})
                                    </td>
                                    <td class="pe-4">
                                        <!-- ฟอร์มเซฟแคลอรี่แยกรายบรรทัด -->
                                        <form action="{{ route('foodwaste.admin.reviews.update', $item->id) }}"
                                            method="POST">
                                            @csrf
                                            <div class="input-group">
                                                <input type="number" name="calories" class="form-control bg-light"
                                                    placeholder="ระบุตัวเลข" required>
                                                <button class="btn btn-success fw-bold" type="submit">บันทึก</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                                        <h5 class="mt-3 text-muted fw-bold">ยอดเยี่ยม! ไม่มีคิวงานค้าง</h5>
                                        <p class="text-muted small">รายการอาหารทั้งหมดได้รับการตรวจสอบแคลอรี่แล้ว</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
