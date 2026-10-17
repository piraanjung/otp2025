@extends('inventory.inv_master')

@section('title', 'ตั้งค่าหน่วยนับ')
@section('header_title', 'จัดการหน่วยนับ (Units)')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">เพิ่มหน่วยนับใหม่</h5>
            <form action="{{ route('inventory.units.store') }}" method="POST">
                @csrf
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="name" placeholder="ชื่อหน่วย" required>
                    <label>ชื่อหน่วยนับ (เช่น ขวด, กล่อง)</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="material-icons-round align-middle">add</i> บันทึก
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">รายการหน่วยนับทั้งหมด</h5>
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>ชื่อหน่วยนับ</th>
                        <th class="text-end" width="100">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                    <tr>
                        <td>{{ $unit->name }}</td>
                        <td class="text-end">
                            <form action="{{ route('inventory.units.destroy', $unit->id) }}" method="POST" 
                                  onsubmit="return confirm('ยืนยันการลบ?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-circle">
                                    <i class="material-icons-round fs-6">delete</i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-center text-muted">ยังไม่มีข้อมูล</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection