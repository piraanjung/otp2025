@extends('inventory.inv_master')

@section('title', 'จัดการพื้นที่จัดเก็บ')
@section('header_title', 'จัดการพื้นที่จัดเก็บ (Locations)')

@section('content')
    <div class="row g-4">

        <!-- ฝั่งซ้าย: ฟอร์มเพิ่ม/แก้ไขพื้นที่จัดเก็บ -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="text-primary fw-bold mb-3">
                    <i class="material-icons-round align-middle me-1">add_location</i> เพิ่มพื้นที่จัดเก็บใหม่
                </h5>

                <form action="{{ route('inventory.locations.store') }}" method="POST">
                    @csrf

                    <!-- แผนก (ใช้เป็น Text ธรรมดาตามที่เราตกลงกันไว้ ไม่ต้องมีตารางแยก) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">ชื่อแผนก / หน่วยงาน</label>

                        <input type="text" class="form-control" name="department" list="departmentList"
                            placeholder="พิมพ์ชื่อแผนก หรือเลือกจากรายการ" required>

                        <!-- รายชื่อที่จะขึ้นมาแนะนำตอนพิมพ์ -->
                        <datalist id="departmentList">
                            @foreach($departments as $dept)
                                <option  value="{{ $dept }}">
                            @endforeach
                        </datalist>

                    </div>

                    <!-- ชื่อพื้นที่จัดเก็บ -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">ชื่อพื้นที่ / ตู้ / ชั้นวาง</label>
                        <input type="text" class="form-control" name="location" placeholder="เช่น ตู้เคมี A1, ชั้นวางของโซน B"
                            required>
                    </div>

                    <!-- รายละเอียดเพิ่มเติม -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">รายละเอียดเพิ่มเติม (ถ้ามี)</label>
                        <textarea class="form-control" name="description" rows="3"
                            placeholder="เช่น อยู่บริเวณห้องโถงชั้น 2"></textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-material w-100 py-2">
                            <i class="material-icons-round align-middle me-1">save</i> บันทึกพื้นที่จัดเก็บ
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ฝั่งขวา: ตารางแสดงรายการพื้นที่จัดเก็บที่มีอยู่ -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="text-primary fw-bold mb-3">
                    <i class="material-icons-round align-middle me-1">list_alt</i> รายการพื้นที่จัดเก็บทั้งหมด
                </h5>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="25%">แผนก</th>
                                <th width="30%">ชื่อพื้นที่จัดเก็บ</th>
                                <th width="30%">รายละเอียด</th>
                                <th width="10%" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($locations as $index => $loc)
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            {{ $loc->department ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark">{{ $loc->name }}</td>
                                    <td class="text-muted small">{{ $loc->description ?? '-' }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('inventory.locations.destroy', $loc->id) }}" method="POST"
                                            onsubmit="return confirm('คุณต้องการลบพื้นที่นี้ใช่หรือไม่?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                                <i class="material-icons-round fs-5">delete_outline</i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="material-icons-round fs-1 text-black-50 mb-2">inbox</i>
                                        <p class="mb-0">ยังไม่มีข้อมูลพื้นที่จัดเก็บ กรุณาเพิ่มข้อมูลทางฝั่งซ้าย</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- ถ้ามี Pagination สามารถใส่ตรงนี้ได้ -->
                @if(method_exists($locations, 'links'))
                    <div class="mt-3">
                        {{ $locations->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection