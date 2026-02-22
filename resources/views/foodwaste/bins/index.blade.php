@extends('layouts.foodwaste') 

@section('content')
<div class="container">
    <h2>รายการถังขยะ Food Waste ทั้งหมด</h2>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('foodwaste.bins.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> เพิ่มถังขยะใหม่
    </a>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>รหัสถังขยะ</th>
                    <th>ผู้ใช้งาน</th>
                    <th>สถานะ</th>
                    <th>หมายเหตุ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bins as $bin)
                    <tr>
                        <td>{{ $bin->id }}</td>
                        <td>{{ $bin->bin_code }}</td>
                        <td>{{ $bin->foodwaste_bin->fw_user_preference->user->firstname ?? ''}} {{ $bin->foodwaste_bin->fw_user_preference->user->lastname ?? '' }}</td>
                        <td>
                            @php
                                $statusClass = [
                                    'active' => 'bg-success', 
                                    'inactive' => 'bg-warning', 
                                    'damaged' => 'bg-danger', 
                                    'removed' => 'bg-secondary'
                                ][$bin->status] ?? 'bg-info';
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ ucfirst($bin->status) }}</span>
                        </td>
                        <td>{{ $bin->latitude ?? 'N/A' }} / {{ $bin->longitude ?? 'N/A' }}</td>
                        <td class="d-flex justify-content-center">
                            <a href="{{ route('foodwaste.bins.show', $bin->id) }}" class="btn btn-sm btn-info me-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('foodwaste.bins.edit', $bin->id) }}" class="btn btn-sm btn-warning me-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('foodwaste.bins.destroy', $bin->id) }}" method="POST" onsubmit="return confirm('คุณต้องการลบถังขยะรหัส {{ $bin->bin_code }} หรือไม่?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">ไม่พบข้อมูลถังขยะ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection