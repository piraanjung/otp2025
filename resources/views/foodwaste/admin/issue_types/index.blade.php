@extends('layouts.foodwaste')

<!-- สมมติว่าใน layouts.foodwaste ของคุณใช้ @yield('content') นะครับ ถ้าใช้ชื่ออื่นอย่าลืมแก้ให้ตรงกัน -->
@section('content')
<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary">
            <i class="bi bi-tags-fill me-2"></i>จัดการหมวดหมู่ปัญหา
        </h4>

        <!-- ปุ่มเปิด Modal เพิ่มหมวดหมู่ -->
        <button class="btn btn-primary rounded-pill fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-lg me-1"></i> เพิ่มหมวดหมู่ใหม่
        </button>
    </div>

    <!-- Alert แจ้งเตือน -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- ตารางข้อมูล -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted">ID</th>
                            <th class="py-3">ชื่อหมวดหมู่ปัญหา</th>
                            <th class="py-3 text-center">สถานะการใช้งาน</th>
                            <th class="pe-4 py-3 text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($issueTypes as $type)
                        <tr>
                            <td class="ps-4 text-muted">#{{ $type->id }}</td>
                            <td class="fw-bold text-dark">{{ $type->name }}</td>
                            <td class="text-center">
                                @if($type->is_active)
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill border border-success-subtle">
                                        <i class="bi bi-check-circle-fill me-1"></i> เปิดใช้งาน
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill border border-danger-subtle">
                                        <i class="bi bi-x-circle-fill me-1"></i> ปิดใช้งาน (ซ่อน)
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <!-- ปุ่มแก้ไข (เปิด Modal) -->
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $type->id }}">
                                    <i class="bi bi-pencil-square"></i> แก้ไข
                                </button>

                                <!-- ฟอร์มสลับสถานะ (เปิด/ปิด) -->
                                <form action="{{ route('foodwaste.admin.issue_types.toggle', $type->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-{{ $type->is_active ? 'danger' : 'success' }} rounded-pill px-3" onclick="return confirm('ยืนยันการเปลี่ยนสถานะหมวดหมู่นี้?');">
                                        <i class="bi bi-{{ $type->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        {{ $type->is_active ? 'ปิดใช้งาน' : 'เปิดใช้งาน' }}
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal แก้ไข (วนลูปสร้างตามจำนวนไอเทม) -->
                        <div class="modal fade" id="editModal{{ $type->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow">
                                    <form action="{{ route('foodwaste.admin.issue_types.update', $type->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header bg-light border-bottom-0 rounded-top-4">
                                            <h6 class="modal-title fw-bold text-primary">แก้ไขหมวดหมู่ #{{ $type->id }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-muted">ชื่อหมวดหมู่ปัญหา</label>
                                                <input type="text" name="name" class="form-control form-control-lg bg-light border-0" value="{{ $type->name }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-white border-top-0 rounded-bottom-4">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4">บันทึกการแก้ไข</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inboxes text-secondary opacity-50 mb-3 d-block" style="font-size: 3rem;"></i>
                                    <h5 class="fw-bold">ยังไม่มีหมวดหมู่ปัญหา</h5>
                                    <p class="small">คลิกที่ปุ่ม "เพิ่มหมวดหมู่ใหม่" ด้านบนเพื่อเริ่มต้นใช้งาน</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal เพิ่มหมวดหมู่ใหม่ (อยู่นอกลูป) -->
<!-- Modal เพิ่มหมวดหมู่ใหม่ (แบบเพิ่มได้หลายรายการพร้อมกัน) -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('foodwaste.admin.issue_types.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light border-bottom-0 rounded-top-4">
                    <h6 class="modal-title fw-bold text-primary"><i class="bi bi-plus-circle-fill me-2"></i>สร้างหมวดหมู่ปัญหาใหม่</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <label class="form-label small fw-bold text-muted">ชื่อหมวดหมู่ปัญหา <span class="text-danger">*</span></label>

                    <!-- กล่องสำหรับใส่ Input ที่จะงอกเพิ่มขึ้นมา -->
                    <div id="dynamic-inputs-container">
                        <div class="input-group mb-2 dynamic-row">
                            <!-- สังเกตตรง name="names[]" มีวงเล็บก้ามปู เพื่อส่งค่าเป็น Array -->
                            <input type="text" name="names[]" class="form-control form-control-lg bg-light border-0" placeholder="หมวดหมู่ที่ 1 (เช่น ถังมีกลิ่นเหม็น)" required autofocus>
                        </div>
                    </div>

                    <!-- ปุ่มสำหรับกดเพิ่มช่องกรอกข้อมูล -->
                    <button type="button" id="add-more-btn" class="btn btn-sm btn-outline-secondary rounded-pill mt-2">
                        <i class="bi bi-plus-lg"></i> เพิ่มช่องกรอกข้อมูล
                    </button>

                    <div class="form-text mt-3">คุณสามารถกดเพิ่มช่องเพื่อสร้างหลายหมวดหมู่พร้อมกันได้</div>
                </div>

                <div class="modal-footer bg-white border-top-0 rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">สร้างหมวดหมู่ทั้งหมด</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@section('script')
<!-- เพิ่ม Script เล็กๆ สำหรับควบคุมการกดปุ่ม "เพิ่มช่อง" -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('dynamic-inputs-container');
    const addBtn = document.getElementById('add-more-btn');
    let rowCount = 1;

    // เมื่อกดปุ่ม "เพิ่มช่อง"
    addBtn.addEventListener('click', function() {
        rowCount++;

        // สร้าง div ใหม่
        const newRow = document.createElement('div');
        newRow.className = 'input-group mb-2 dynamic-row';

        // ใส่ HTML ช่องกรอกข้อมูล + ปุ่มลบ (ถังขยะ)
        newRow.innerHTML = `
            <input type="text" name="names[]" class="form-control form-control-lg bg-light border-0" placeholder="หมวดหมู่ที่ ${rowCount}" required>
            <button class="btn btn-outline-danger remove-btn" type="button" title="ลบช่องนี้">
                <i class="fa fa-trash"></i>
            </button>
        `;

        // เอาไปต่อท้ายในกล่อง
        container.appendChild(newRow);
    });

    // เมื่อกดปุ่ม "ถังขยะ" (ลบช่องที่ไม่อยากพิมพ์แล้ว)
    container.addEventListener('click', function(e) {
        // เช็คว่ากดโดนปุ่ม class remove-btn หรือไอคอนถังขยะข้างในไหม
        if (e.target.closest('.remove-btn')) {
            const row = e.target.closest('.dynamic-row');
            row.remove(); // ลบแถวนั้นทิ้ง
        }
    });
});
</script>
@endsection
