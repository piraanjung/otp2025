@extends('inventory.inv_master')

@section('title', 'ระดับอันตราย')
@section('header_title', 'ระดับอันตราย/GHS (Hazard Levels)')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-3 text-danger">เพิ่มระดับอันตราย</h5>
            <form action="{{ route('inventory.hazards.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">ชื่อระดับอันตราย <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="เช่น สารไวไฟ (Flammable)" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">รหัส (Code)</label>
                    <input type="text" name="code" class="form-control" placeholder="เช่น GHS02">
                </div>
                <div class="mb-3">
                    <label class="form-label">รายละเอียด</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">รูปไอคอน/สัญลักษณ์</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-danger w-100 text-white">บันทึก</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card p-4">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>รูป</th>
                        <th>ชื่อ/รหัส</th>
                        <th>รายละเอียด</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hazards as $h)
                    <tr>
                        <td>
                            @if($h->image_path)
                                <img src="{{ asset('storage/'.$h->image_path) }}" width="40">
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold">{{ $h->name }}</div>
                            <small class="text-muted">{{ $h->code }}</small>
                        </td>
                        <td><small>{{ $h->description }}</small></td>
                        <td>
                            <form action="{{ route('inventory.hazards.destroy', $h->id) }}" method="POST" onsubmit="return confirm('ลบ?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="material-icons-round fs-6">delete</i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted">ยังไม่มีข้อมูล</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection