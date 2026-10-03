@extends('inventory.inv_master')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>จัดการสายการอนุมัติ (Workflows)</h2>
        <a href="{{ route('admin.workflows.create') }}" class="btn btn-primary">+ สร้างสายอนุมัติใหม่</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ชื่อสายการอนุมัติ</th>
                <th>คำอธิบาย</th>
                <th>จำนวนขั้นตอน</th>
                <th>สถานะ</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($workflows as $wf)
                <tr>
                    <td>{{ $wf->name }}</td>
                    <td>{{ $wf->description }}</td>
                    <td>{{ $wf->steps_count }} ขั้นตอน
                       @foreach ($wf->steps as $step)
                           <div style="margin-left:30px">{{ $step->step_order.". ".$step->role_name }}</div>
                       @endforeach
                    </td>
                    <td>
                        <span class="badge bg-{{ $wf->is_active ? 'success' : 'secondary' }}">
                            {{ $wf->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.workflows.edit', $wf->id) }}" class="btn btn-warning btn-sm">ตั้งค่าขั้นตอน / แก้ไข</a>
                        <form action="{{ route('admin.workflows.destroy', $wf->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบ?')">ลบ</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">ยังไม่มีข้อมูลสายการอนุมัติ</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection