@extends('layouts.admin1')
@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>📍 จัดการจุดตั้งตู้ Kiosk</h2>
            <a href="{{ route('keptkayas.kiosks.create') }}" class="btn btn-primary">+ เพิ่มตู้ใหม่</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>ชื่อจุดตั้ง</th>
                            <th>พิกัด (Lat, Lng)</th>
                            <th>สถานะ</th>
                            <th>Online ล่าสุด</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kiosks as $kiosk)
                            <tr>
                                <td><span class="badge bg-dark">{{ $kiosk->id }}</span></td>
                                <td>{{ $kiosk->name }}</td>
                                <td><small class="text-muted">{{ $kiosk->lat }}, {{ $kiosk->lng }}</small></td>
                                <td>
                                    @if($kiosk->status == 'active')
                                        <span class="badge bg-success">พร้อมใช้งาน</span>
                                    @elseif($kiosk->status == 'idle')
                                        <span class="badge bg-info">ว่าง</span>
                                    @else
                                        <span class="badge bg-danger">Offline</span>
                                    @endif
                                </td>
                                <td>{{ $kiosk->last_online_at ? \Carbon\Carbon::parse($kiosk->last_online_at)->diffForHumans() : '-' }}
                                </td>
                                <td>
                                    <a href="{{ route('keptkayas.kiosks.edit', $kiosk->id) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                                    <form action="{{ route('keptkayas.kiosks.destroy', $kiosk->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('ยืนยันลบตู้นี้?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">ลบ</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
