@extends('layouts.admin1')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm" style="max-width: 600px; margin: auto;">
            <div class="card-header bg-white">
                <h4 class="mb-0">เพิ่มตู้ Kiosk ใหม่</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('keptkayas.kiosks.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label>Kiosk ID (Hardware ID)</label>
                        <input type="text" name="id" class="form-control" placeholder="เช่น SLAVE_01" required>
                        <small class="text-muted">ต้องตรงกับ Code ใน ESP8266</small>
                    </div>

                    <div class="mb-3">
                        <label>ชื่อจุดตั้ง/อาคาร</label>
                        <input type="text" name="name" class="form-control" placeholder="เช่น โรงอาหารกลาง" required>
                    </div>


                    @include('kiosk/form')
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100">บันทึกข้อมูล</button>
                        <a href="{{ route('keptkayas.kiosks.index') }}" class="btn btn-link w-100 mt-2">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
