@extends('layouts.foodwaste') 
@section('content')
<div class="container">
    <h2>รายการ Food Waste IoT Boxes</h2>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('foodwaste.iotboxes.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> เพิ่มอุปกรณ์ใหม่
    </a>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>status</th>
                    <th>รหัส IoT Box</th>
                    <th>Temp/Humid Sensor</th>
                    <th>Gas Sensor</th>
                    <th> Weight Sensor</th>
                    <th>อัปเดตล่าสุด</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($iotboxes as $box)
                    <tr>
                        <td>{{ $box->id }}</td>
                        <td>{{ $box->status }}</td>
                        <td>{{ $box->iotbox_code }}</td>
                        <td>
                            <span class="badge {{ $box->temp_humid_sensor == '1' ? 'bg-success' : 'bg-danger' }}">
                                {{ $box->temp_humid_sensor == '1' ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $box->gas_sensor == '1' ? 'bg-success' : 'bg-danger' }}">
                                {{ $box->gas_sensor == '1' ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $box->weight_sensor == '1' ? 'bg-success' : 'bg-danger' }}">
                                {{ $box->weight_sensor == '1' ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                            </span>
                        </td>
                        <td>{{ $box->updated_at->format('Y-m-d H:i:s') }}</td>
                        <td class="d-flex justify-content-center">
                            <a href="{{ route('foodwaste.iotboxes.show', $box->id) }}" class="btn btn-sm btn-info me-1">
                                <i class="fas fa-eye"></i> ดู
                            </a>
                            <a href="{{ route('foodwaste.iotboxes.edit', $box->id) }}" class="btn btn-sm btn-warning me-1">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                            <form action="{{ route('foodwaste.iotboxes.destroy', $box->id) }}" method="POST" onsubmit="return confirm('คุณต้องการลบข้อมูลนี้หรือไม่?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> ลบ
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">ไม่พบข้อมูล IoT Box</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection