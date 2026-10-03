@extends('layouts.print') {{-- ปรับตาม Master Layout ของคุณ --}}

@section('title', 'บันทึกการแจ้งเหตุสำเร็จ')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <!-- Card สรุปผล -->
            <div class="card border-0 shadow-sm rounded-4 text-center overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    
                    <!-- Header Icon สำเร็จ -->
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle" style="width: 80px; height: 80px;">
                            <i class="bi bi-check-circle-fill display-4"></i>
                        </div>
                    </div>

                    <h4 class="fw-bold text-success mb-1">บันทึกข้อมูลเรียบร้อยแล้ว!</h4>
                    <p class="text-muted small mb-4">ขอบคุณสำหรับการแจ้งเหตุ ระบบได้ส่งเรื่องไปยังหน่วยงานที่เกี่ยวข้องแล้ว</p>

                    <!-- Ticket / Tracking ID -->
                    <div class="bg-light rounded-3 p-3 mb-4 border">
                        <span class="text-muted small d-block mb-1">รหัสอ้างอิงการแจ้งเหตุ (Ticket ID)</span>
                        <span class="fs-4 fw-bold text-primary">#{{ str_pad($notify->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <!-- รายละเอียดข้อมูลที่แจ้ง (Summary Details) -->
                    <div class="text-start mb-4">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">
                            <i class="bi bi-file-text me-1 text-primary"></i> รายละเอียดการแจ้งเหตุ
                        </h6>

                        <div class="row g-2 small">
                            <!-- องค์กร/พื้นที่ -->
                            @if(isset($notify->organization))
                            <div class="col-4 text-muted">หน่วยงานรับเรื่อง:</div>
                            <div class="col-8 fw-semibold text-end">{{ $notify->organization->name ?? '-' }}</div>
                            @endif

                            <!-- ประเภทเหตุการณ์ -->
                            <div class="col-4 text-muted">ประเภทเรื่อง:</div>
                            <div class="col-8 fw-semibold text-end">{{ $notify->title ?? $notify->topic ?? 'แจ้งเหตุทั่วไป' }}</div>

                            <!-- วันเวลาที่แจ้ง -->
                            <div class="col-4 text-muted">วันที่แจ้งเหตุ:</div>
                            <div class="col-8 fw-semibold text-end">
                                {{ \Carbon\Carbon::parse($notify->created_at)->locale('th')->isoFormat('D MMM YYYY HH:mm') }} น.
                            </div>

                            <!-- รายละเอียดเพิ่มเติม -->
                            @if(!empty($notify->details) || !empty($notify->description))
                            <div class="col-12 mt-2">
                                <span class="text-muted d-block mb-1">รายละเอียดเพิ่มเติม:</span>
                                <div class="bg-white border rounded p-2 text-secondary">
                                    {{ $notify->details ?? $notify->description }}
                                </div>
                            </div>
                            @endif

                            <!-- สถานะการรับเรื่อง -->
                            <div class="col-4 text-muted mt-2">สถานะปัจจุบัน:</div>
                            <div class="col-8 text-end mt-2">
                                <span class="badge bg-warning text-dark px-2 py-1">
                                    <i class="bi bi-hourglass-split me-1"></i>รอดำเนินการ
                                </span>
                            </div>
                        </div>

                        <!-- ------------------------------------------------------------- -->
                        <!-- 🖼️ ส่วนแสดงรูปภาพที่ผูกกับ Notify ID -->
                        <!-- ------------------------------------------------------------- -->
                        
                        {{-- กรณีที่ 1: กรณีมีหลายรูปภาพ (Relationship แบบ HasMany เช่น $notify->images) --}}
                        @php
    $photos = [];
    if (!empty($notify->photo_path)) {
        // หากอยู่ในรูปแบบ JSON string จะแปลงค่าเป็น Array
        $photos = is_array($notify->photo_path) 
            ? $notify->photo_path 
            : json_decode($notify->photo_path, true);
    }
@endphp

@if(!empty($photos) && count($photos) > 0)
    <div class="mt-4">
        <span class="text-muted d-block small mb-2">
            <i class="bi bi-images me-1"></i> รูปภาพที่แนบ ({{ count($photos) }} รูป):
        </span>
        <div class="row g-2">
            @foreach($photos as $photo)
                <div class="{{ count($photos) == 1 ? 'col-12' : (count($photos) == 2 ? 'col-6' : 'col-4') }}">
                    <a href="{{ asset('/' . $photo) }}" target="_blank">
                        <img src="{{ asset('/' . $photo) }}" 
                             class="img-fluid rounded border shadow-sm {{ count($photos) == 1 ? 'max-img-height' : 'style-img-thumb' }}" 
                             alt="รูปถ่ายแจ้งเหตุ">
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif
                        <!-- ------------------------------------------------------------- -->

                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('tabwater.notify.index') }}" class="btn btn-primary btn-lg rounded-3 fw-bold">
                            <i class="bi bi-house-door-fill me-1"></i> กลับสู่หน้าหลัก
                        </a>
                        
                        <button type="button" onclick="window.print()" class="btn btn-outline-secondary rounded-3 border-0">
                            <i class="bi bi-printer me-1"></i> พิมพ์ใบรับเรื่อง
                        </button>
                    </div>

                </div>

                <!-- Footer Card -->
                <div class="card-footer bg-light border-0 py-3 text-muted small">
                    <i class="bi bi-shield-check text-success me-1"></i> ข้อมูลของคุณถูกบันทึกในระบบอย่างปลอดภัย
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Custom Style สำหรับควบคุมขนาดภาพ -->
<style>
.style-img-thumb {
    width: 100%;
    height: 90px;
    object-fit: cover;
    transition: transform 0.2s;
}
.style-img-thumb:hover {
    transform: scale(1.03);
}
.max-img-height {
    max-height: 250px;
    object-fit: contain;
}

@media print {
    body * {
        visibility: hidden;
    }
    .card, .card * {
        visibility: visible;
    }
    .card {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        border: none !important;
    }
    .btn, .card-footer {
        display: none !important;
    }
}
</style>
@endsection