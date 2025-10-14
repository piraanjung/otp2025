@extends('layouts.foodwaste') 

@section('content')
<div class="container">
    <h2>เพิ่ม Food Waste IoT Box ใหม่</h2>
    <hr>

    <form action="{{ route('foodwaste.iotboxes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="iotbox_code" class="form-label">รหัส IoT Box</label>
            <input type="text" class="form-control @error('iotbox_code') is-invalid @enderror" id="iotbox_code" name="iotbox_code" value="{{ old('iotbox_code') }}" required maxlength="50">
            @error('iotbox_code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="temp_humid_sensore" class="form-label">สถานะ Temp/Humid Sensor</label>
                <select class="form-select @error('temp_humid_sensore') is-invalid @enderror" id="temp_humid_sensor" name="temp_humid_sensor" required>
                    <option value="1" {{ old('temp_humid_sensor') == '1' ? 'selected' : '' }}>1 - เปิดใช้งาน</option>
                    <option value="0" {{ old('temp_humid_sensor') == '0' ? 'selected' : '' }}>0 - ปิดใช้งาน</option>
                </select>
                @error('temp_humid_sensor')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="gas_sensor" class="form-label">สถานะ Gas Sensor</label>
                <select class="form-select @error('gas_sensor') is-invalid @enderror" id="gas_sensor" name="gas_sensor" required>
                    <option value="1" {{ old('gas_sensor') == '1' ? 'selected' : '' }}>1 - เปิดใช้งาน</option>
                    <option value="0" {{ old('gas_sensor') == '0' ? 'selected' : '' }}>0 - ปิดใช้งาน</option>
                </select>
                @error('gas_sensor')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="weight_sensor" class="form-label">สถานะ  Weight Sensor</label>
                <select class="form-select @error('weight_sensor') is-invalid @enderror" id="weight_sensor" name="weight_sensor" required>
                    <option value="1" {{ old('weight_sensor') == '1' ? 'selected' : '' }}>1 - เปิดใช้งาน</option>
                    <option value="0" {{ old('weight_sensor') == '0' ? 'selected' : '' }}>0 - ปิดใช้งาน</option>
                </select>
                @error('weight_sensor')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
        <a href="{{ route('foodwaste.iotboxes.index') }}" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
@endsection