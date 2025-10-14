@extends('layouts.foodwaste') 
@section('content')
<div class="container">
    <h2>รายละเอียด Food Waste IoT Box: {{ $iotbox->iotbox_code }}</h2>
    <hr>

    <div class="card">
        <div class="card-header bg-primary text-white">
            ข้อมูลอุปกรณ์ #{{ $iotbox->id }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>รหัส IoT Box:</strong>
                    <p class="form-control-static">{{ $iotbox->iotbox_code }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>ID:</strong>
                    <p class="form-control-static">{{ $iotbox->id }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <strong>Temp/Humid Sensor:</strong>
                    <p class="form-control-static">
                        <span class="badge {{ $iotbox->temp_humid_sensor == '1' ? 'bg-success' : 'bg-danger' }}">
                            {{ $iotbox->temp_humid_sensor == '1' ? 'เปิดใช้งาน (1)' : 'ปิดใช้งาน (0)' }}
                        </span>
                    </p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Gas Sensor:</strong>
                    <p class="form-control-static">
                        <span class="badge {{ $iotbox->gas_sensor == '1' ? 'bg-success' : 'bg-danger' }}">
                            {{ $iotbox->gas_sensor == '1' ? 'เปิดใช้งาน (1)' : 'ปิดใช้งาน (0)' }}
                        </span>
                    </p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Drop5 Weight Sensor:</strong>
                    <p class="form-control-static">
                        <span class="badge {{ $iotbox->weight_sensor == '1' ? 'bg-success' : 'bg-danger' }}">
                            {{ $iotbox->weight_sensor == '1' ? 'เปิดใช้งาน (1)' : 'ปิดใช้งาน (0)' }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <strong>วันที่สร้าง:</strong>
                    <p class="form-control-static">{{ $iotbox->created_at->format('Y-m-d H:i:s') }}</p>
                </div>
                <div class="col-md-6">
                    <strong>อัปเดตล่าสุด:</strong>
                    <p class="form-control-static">{{ $iotbox->updated_at->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('foodwaste.iotboxes.edit', $iotbox->id) }}" class="btn btn-warning"><i class="fas fa-edit"></i> แก้ไข</a>
            <a href="{{ route('foodwaste.iotboxes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> กลับสู่รายการ</a>
        </div>
    </div>
</div>
@endsection