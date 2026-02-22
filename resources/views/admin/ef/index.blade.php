@extends('layouts.super-admin')


@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary">
                        <i class="bi bi-cloud-haze2-fill"></i> จัดการค่า Emission Factor (EF)
                    </h2>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEFModal">
                            <i class="bi bi-plus-circle"></i> เพิ่มข้อมูลรายชิ้น
                        </button>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>สำเร็จ!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-file-earmark-excel"></i> นำเข้าจาก DEFRA (Excel)</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">เลือกไฟล์ Excel (.xlsx) ที่ดาวน์โหลดจาก DEFRA
                                    เพื่ออัปเดตข้อมูลมหาศาลในครั้งเดียว</p>
                                <form action="{{ route('ef.import') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-outline-success">เริ่มการนำเข้าข้อมูล</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">ชื่อวัสดุ / ประเภทขยะ</th>
                                                <th class="text-center">ค่า EF (kgCO2e/kg)</th>
                                                <th class="text-center">แหล่งที่มา</th>
                                                <th class="text-end pe-4">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($factors as $ef)
                                                <tr>
                                                    <td class="ps-4">
                                                        <span class="fw-bold">{{ $ef->material_name }}</span><br>
                                                        <small class="text-muted">หน่วยฐาน: 1 {{ $ef->unit }}</small>
                                                    </td>
                                                    <td class="text-center text-primary fw-bold">
                                                        {{ number_format($ef->ef_value, 6) }}
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info text-dark">{{ $ef->source ?? 'N/A' }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="text-dark">{{ $ef->example ?? 'N/A' }}</span>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        <div class="btn-group">
                                                            {{-- ปุ่ม Edit (สามารถเพิ่ม Modal แก้ไขได้ในอนาคต) --}}

                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-secondary edit-ef-btn"
                                                                data-bs-toggle="modal" data-bs-target="#editEFModal"
                                                                data-id="{{ $ef->id }}" data-name="{{ $ef->material_name }}"
                                                                data-value="{{ $ef->ef_value }}"
                                                                data-example="{{ $ef->example }}"
                                                                data-source="{{ $ef->source }}">
                                                                <i class="fa fa-pencil"></i>
                                                            </button>
                                                            {{-- ปุ่ม Delete --}}
                                                            <form action="{{ route('ef.destroy', $ef->id) }}" method="POST"
                                                                onsubmit="return confirm('ยืนยันการลบข้อมูลนี้?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-5 text-muted">ไม่พบข้อมูล Emission
                                                        Factor ในระบบ</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addEFModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('ef.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">เพิ่มข้อมูล Emission Factor ใหม่</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ชื่อวัสดุ (Material Name)</label>
                            <input type="text" name="material_name" class="form-control"
                                placeholder="เช่น Plastics: PET (incl. forming)" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ค่า EF (ต่อ 1 กิโลกรัม)</label>
                            <input type="number" step="0.000001" name="ef_value" class="form-control" placeholder="0.000000"
                                required>
                            <div class="form-text text-danger">*หากข้อมูลเป็น Tonnes ให้หาร 1,000 ก่อนกรอก</div>
                        </div>
                        <div class="mb-3">
                            <textarea name="example" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">แหล่งข้อมูล (Source)</label>
                            <select name="source" class="form-control">
                                <option value="DEFRA 2025" selected>DEFRA 2025</option>
                                <option value="อบก. 2569">อบก. 2569</option>
                                <option value="อบก. 2569/DEFRA 2025">อบก. 2569 / DEFRA 2025</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editEFModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editEFForm" method="POST">
                @csrf
                @method('PUT') {{-- สำคัญมากสำหรับการ Update ใน Laravel --}}
                <div class="modal-content">
                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title">แก้ไขค่า Emission Factor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">ชื่อวัสดุ</label>
                            <input type="text" name="material_name" id="edit_material_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ค่า EF (ต่อ 1 กิโลกรัม)</label>
                            <input type="number" step="0.000001" name="ef_value" id="edit_ef_value" class="form-control"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">แหล่งข้อมูล (Source)</label>
                            <input type="text" name="source" id="edit_source" class="form-control">
                        </div>
                         <div class="mb-3">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="example" id="edit_example" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-secondary">บันทึกการเปลี่ยนแปลง</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.edit-ef-btn');
        const editForm = document.getElementById('editEFForm');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const value = this.getAttribute('data-value');
                const source = this.getAttribute('data-source');
                const example = this.getAttribute('data-example');

                // ตั้งค่า Action URL ของ Form ให้ตรงกับ ID ที่จะแก้ไข
                editForm.action = `/admin/ef/${id}`;

                // ใส่ข้อมูลลงใน Input ของ Modal
                document.getElementById('edit_material_name').value = name;
                document.getElementById('edit_ef_value').value = value;
                document.getElementById('edit_source').value = source;
                document.getElementById('edit_example').value = example;
            });
        });
    });
</script>
@endsection
