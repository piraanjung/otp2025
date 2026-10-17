@extends('layouts.app') {{-- หรือ layout หลักของระบบคุณ --}}

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            
            <!-- Card หลัก -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-file-earmark-text me-2"></i>รายละเอียดการแจ้งเหตุ #{{ $notify->id }}
                    </h5>
                    <!-- Badge แสดงสถานะ -->
                    @php
                        $statusBadge = [
                            'pending'     => ['class' => 'bg-warning text-dark', 'text' => '⏳ รอดำเนินการ'],
                            'in_progress' => ['class' => 'bg-info text-dark', 'text' => '🔄 กำลังดำเนินการ'],
                            'completed'   => ['class' => 'bg-success', 'text' => '✅ ดำเนินการแล้ว'],
                            'canceled'    => ['class' => 'bg-danger', 'text' => '❌ ยกเลิก'],
                        ][$notify->status] ?? ['class' => 'bg-secondary', 'text' => $notify->status];
                    @endphp
                    <span class="badge {{ $statusBadge['class'] }} fs-6">{{ $statusBadge['text'] }}</span>
                </div>

                <div class="card-body p-4">
                    
                    <!-- 1. ข้อมูลผู้แจ้ง -->
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-person-circle me-1"></i> ข้อมูลผู้แจ้งเหตุ
                    </h6>
                    <div class="row mb-3">
                        <div class="col-sm-6 mb-2">
                            <span class="text-muted d-block">ชื่อ-นามสกุล ผู้แจ้ง:</span>
                            <span class="fw-bold">{{ $notify->reporter_name ?? '-' }}</span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <span class="text-muted d-block">เบอร์โทรศัพท์:</span>
                            <a href="tel:{{ $notify->reporter_phone }}" class="text-decoration-none fw-bold">
                                {{ $notify->reporter_phone ?? '-' }}
                            </a>
                        </div>
                    </div>

                    <!-- 2. รายละเอียดปัญหา -->
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3 mt-4">
                        <i class="bi bi-exclamation-triangle me-1"></i> รายละเอียดการแจ้งเหตุ
                    </h6>
                    <div class="row mb-3">
                        <div class="col-sm-6 mb-2">
                            <span class="text-muted d-block">ระบบประปา / สายงาน:</span>
                            <span class="badge bg-light text-dark border">{{ $notify->system_type }}</span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <span class="text-muted d-block">ประเภทปัญหา:</span>
                            <span class="fw-bold text-danger">{{ $notify->issue_type }}</span>
                        </div>
                        <div class="col-12 mt-2">
                            <span class="text-muted d-block">รายละเอียดเพิ่มเติม:</span>
                            <div class="p-3 bg-light rounded border mt-1">
                                {{ $notify->description ?? 'ไม่มีรายละเอียดเพิ่มเติม' }}
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-muted">
                                วันที่แจ้งเรื่อง: {{ $notify->created_at ? $notify->created_at->format('d/m/Y H:i น.') : '-' }}
                            </small>
                        </div>
                    </div>

                    <!-- 3. รูปภาพประกอบ (ดึงจาก JSON Array) -->
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3 mt-4">
                        <i class="bi bi-images me-1"></i> รูปภาพประกอบ
                    </h6>
                    
                    @php
                        // แปลงค่า photo_path เป็น array (หากไม่ได้ตั้ง $casts ใน Model)
                        $photos = is_string($notify->photo_path) ? json_decode($notify->photo_path, true) : $notify->photo_path;
                    @endphp

                    @if(!empty($photos) && is_array($photos) && count($photos) > 0)
                        <div class="row g-2 mb-3">
                            @foreach($photos as $path)
                                <div class="col-6 col-sm-4 col-md-3">
                                    <a href="{{ asset('storage/' . $path) }}" target="_blank" class="d-block text-center border rounded p-1 bg-light shadow-sm hover-shadow">
                                        <img src="{{ asset('storage/' . $path) }}" 
                                             class="img-fluid rounded" 
                                             alt="รูปภาพแจ้งเหตุ" 
                                             style="height: 120px; width: 100%; object-fit: cover;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted fst-italic">ไม่มีรูปภาพแนบมาด้วย</p>
                    @endif

                    <!-- 4. พิกัดสถานที่ (Google Maps) -->
                    @if($notify->latitude && $notify->longitude)
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3 mt-4">
                            <i class="bi bi-geo-alt me-1"></i> ตำแหน่งสถานที่เกิดเหตุ
                        </h6>
                        <div class="mb-3">
                            <p class="mb-2 text-muted">
                                <strong>พิกัด:</strong> {{ $notify->latitude }}, {{ $notify->longitude }}
                            </p>
                            <!-- ปุ่มเปิด Google Maps -->
                            <a href="https://maps.google.com/?q={{ $notify->latitude }},{{ $notify->longitude }}" 
                               target="_blank" 
                               class="btn btn-outline-danger btn-sm rounded-pill">
                                <i class="bi bi-map me-1"></i> เปิดดูบน Google Maps
                            </a>
                        </div>
                    @endif

                </div>

                <!-- Footer ปุ่มกดย้อนกลับ -->
                <div class="card-footer bg-light py-3 d-flex justify-content-between">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection