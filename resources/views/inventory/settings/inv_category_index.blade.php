@extends('inventory.inv_master')

@section('title', 'จัดการหมวดหมู่')
@section('header_title', 'หมวดหมู่พัสดุ (Categories)')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card p-4 shadow-sm border-0">
            <h5 class="fw-bold mb-3 text-primary">
                <i class="material-icons-round align-middle">create_new_folder</i> เพิ่มหมวดหมู่
            </h5>
            <form action="{{ route('inventory.categories.store') }}" method="POST">
                @csrf
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="name" placeholder="ชื่อหมวดหมู่" required>
                    <label>ชื่อหมวดหมู่ (เช่น เครื่องแก้ว, สารเคมี)</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="material-icons-round align-middle">save</i> บันทึก
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card p-4 shadow-sm border-0">
            <h5 class="fw-bold mb-3">รายการหมวดหมู่ทั้งหมด</h5>
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>ชื่อหมวดหมู่</th>
                        <th class="text-center">จำนวนพัสดุ</th>
                        <th class="text-end" width="100">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td>
                            <i class="material-icons-round text-muted align-middle me-2">folder</i>
                            {{ $cat->name }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">
                                {{ $cat->items->count() }} รายการ
                            </span>
                        </td>
                        <td class="text-end">
                            <form action="{{ route('inventory.categories.destroy', $cat->id) }}" method="POST" 
                                  onsubmit="return confirm('ยืนยันการลบ?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-circle" title="ลบ">
                                    <i class="material-icons-round fs-6">delete</i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">ยังไม่มีข้อมูล</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection