@extends('inventory.inv_master')

@section('title', 'จัดการผู้จำหน่าย (Suppliers)')
@section('header_title', 'จัดการข้อมูลร้านค้า / ผู้จำหน่าย')

@section('content')
<div class="row g-4">
    
    <!-- ฝั่งซ้าย: ฟอร์มเพิ่ม หรือ แก้ไขข้อมูล -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="text-primary fw-bold mb-3">
                @isset($supplier)
                    <i class="material-icons-round align-middle me-1">edit</i> แก้ไขข้อมูลผู้จำหน่าย
                @else
                    <i class="material-icons-round align-middle me-1">storefront</i> เพิ่มผู้จำหน่ายใหม่
                @endisset
            </h5>

            <form action="{{ isset($supplier) ? route('admin.suppliers.update', $supplier->id) : route('admin.suppliers.store') }}" method="POST">
                @csrf
                @isset($supplier)
                    @method('PUT')
                @endisset

                <!-- แผนกที่รับผิดชอบ -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">แผนกที่รับผิดชอบ / เกี่ยวข้อง</label>
                    <input type="text" class="form-control" name="department" list="deptList" 
                           value="{{ old('department', $supplier->department ?? '') }}" placeholder="เช่น แผนกจัดซื้อ, ห้องแล็บ">
                    
                    <datalist id="deptList">
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}">
                        @endforeach
                    </datalist>
                    <div class="form-text">ระบุแผนกเพื่อจัดกลุ่มร้านค้า</div>
                </div>

                <!-- ชื่อร้านค้า -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">ชื่อร้านค้า / บริษัท <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" 
                           value="{{ old('name', $supplier->name ?? '') }}" placeholder="เช่น บจก. ซัพพลายไทย" required>
                </div>

                <!-- ผู้ติดต่อ -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">ชื่อผู้ติดต่อ</label>
                    <input type="text" class="form-control" name="contact_person" 
                           value="{{ old('contact_person', $supplier->contact_person ?? '') }}" placeholder="เช่น คุณสมชาย (ฝ่ายขาย)">
                </div>

                <!-- เบอร์โทร -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">เบอร์โทรศัพท์</label>
                    <input type="text" class="form-control" name="phone" 
                           value="{{ old('phone', $supplier->phone ?? '') }}" placeholder="เช่น 02-123-4567">
                </div>

                <!-- ที่อยู่ -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">ที่อยู่</label>
                    <textarea class="form-control" name="address" rows="3" placeholder="ที่อยู่สำหรับออกใบกำกับภาษี / จัดส่ง">{{ old('address', $supplier->address ?? '') }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    @isset($supplier)
                        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light w-50 py-2">ยกเลิก</a>
                        <button type="submit" class="btn btn-warning btn-material w-50 py-2 text-white">
                            <i class="material-icons-round align-middle me-1">update</i> บันทึกการแก้ไข
                        </button>
                    @else
                        <button type="submit" class="btn btn-primary btn-material w-100 py-2">
                            <i class="material-icons-round align-middle me-1">save</i> บันทึกข้อมูลผู้จำหน่าย
                        </button>
                    @endisset
                </div>
            </form>
        </div>
    </div>

    <!-- ฝั่งขวา: ตารางแสดงรายการผู้จำหน่าย -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="text-primary fw-bold mb-3">
                <i class="material-icons-round align-middle me-1">list_alt</i> รายการผู้จำหน่ายทั้งหมด
            </h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="35%">ชื่อร้านค้า / บริษัท & แผนก</th>
                            <th width="25%">ผู้ติดต่อ / เบอร์โทร</th>
                            <th width="25%">ที่อยู่</th>
                            <th width="10%" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $index => $sup)
                            <tr class="{{ isset($supplier) && $supplier->id == $sup->id ? 'table-warning' : '' }}">
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $sup->name }}</span>
                                    @if($sup->department)
                                        <span class="badge bg-light text-dark border px-2 py-1 mt-1">
                                            <i class="material-icons-round fs-6 align-middle text-muted">apartment</i> {{ $sup->department }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border px-2 py-1 mt-1">- ทั่วไป -</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small fw-bold text-secondary">{{ $sup->contact_person ?? '-' }}</div>
                                    <div class="small text-muted"><i class="material-icons-round fs-6 align-middle">phone</i> {{ $sup->phone ?? '-' }}</div>
                                </td>
                                <td class="text-muted small">{{ $sup->address ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- ปุ่มแก้ไข -->
                                        <a href="{{ route('admin.suppliers.edit', $sup->id) }}" class="btn btn-sm btn-outline-primary border-0" title="แก้ไข">
                                            <i class="material-icons-round fs-5">edit_note</i>
                                        </a>
                                        <!-- ปุ่มลบ -->
                                        <form action="{{ route('admin.suppliers.destroy', $sup->id) }}" method="POST" onsubmit="return confirm('คุณต้องการลบข้อมูลผู้จำหน่ายนี้ใช่หรือไม่?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="ลบ">
                                                <i class="material-icons-round fs-5">delete_outline</i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="material-icons-round fs-1 text-black-50 mb-2">store</i>
                                    <p class="mb-0">ยังไม่มีข้อมูลผู้จำหน่าย กรุณาเพิ่มข้อมูลทางฝั่งซ้าย</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection