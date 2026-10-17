@extends('layouts.foodwaste')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">อุปกรณ์ IoT Box</h6>
                    <p class="text-sm text-secondary mb-0">รายการกล่องเซนเซอร์สำหรับถังขยะเปียก</p>
                </div>
                <a href="{{ route('foodwaste.iotboxes.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                    <i class="fas fa-plus me-1"></i> เพิ่มอุปกรณ์
                </a>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">รหัสอุปกรณ์</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">อุณหภูมิ/ความชื้น</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ก๊าซ</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">น้ำหนัก</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($iotboxes as $box)
                                <tr>
                                    <td class="ps-4"><h6 class="mb-0 text-sm">{{ $box->iotbox_code }}</h6></td>
                                    @foreach (['temp_humid_sensor', 'gas_sensor', 'weight_sensor'] as $sensor)
                                        <td class="text-center">
                                            <span class="badge badge-sm bg-gradient-{{ $box->$sensor ? 'success' : 'secondary' }}">
                                                {{ $box->$sensor ? 'มี' : 'ไม่มี' }}
                                            </span>
                                        </td>
                                    @endforeach
                                    <td class="align-middle text-end pe-4 text-nowrap">
                                        <a href="{{ route('foodwaste.iotboxes.edit', $box->id) }}"
                                            class="text-secondary font-weight-bold text-xs me-3">
                                            <i class="fas fa-edit me-1"></i> แก้ไข
                                        </a>
                                        <form action="{{ route('foodwaste.iotboxes.destroy', $box->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('ยืนยันการลบอุปกรณ์ &quot;{{ $box->iotbox_code }}&quot; ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0 mb-0">
                                                <i class="fas fa-trash me-1"></i> ลบ
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-secondary py-4">ยังไม่มีอุปกรณ์</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
