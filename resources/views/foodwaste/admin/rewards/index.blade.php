@extends('layouts.foodwaste') {{-- ปรับตาม Layout Admin ของคุณ --}}

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm border-0" style="border-radius: 1rem;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold text-success mb-0">
                        <i class="bi bi-sliders2-vertical me-2"></i> ตั้งค่าเกณฑ์การให้แต้ม (Food Waste)
                    </h5>
                    <p class="text-muted small">กำหนดจำนวนแต้มและเงินรางวัลสำหรับกิจกรรมต่างๆ ของสมาชิก</p>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('foodwaste.admin.fw_rewards.update') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>รายการเงื่อนไข</th>
                                        <th width="260">ค่าที่กำหนด</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($settings as $setting)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $setting->title }}</div>
                                            <div class="small text-muted">{{ $setting->description }}</div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" step="0.01"
                                                       name="settings[{{ $setting->key }}]"
                                                       class="form-control"
                                                       value="{{ $setting->value }}">
                                                <span class="bg-light text-secondary text-center" style="min-width: 60px;">
                                                    {{ strtoupper($setting->unit) }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg rounded-pill fw-bold">
                                <i class="bi bi-check-circle-fill me-2"></i> อัปเดตการตั้งค่าทั้งหมด
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
