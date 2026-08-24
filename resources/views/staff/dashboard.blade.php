@extends('layouts.keptkaya_mobile2')

@section('style')
    <style>
        /* =========================================================================
           🟢 CSS ปรับแต่งพิเศษสำหรับหน้า Staff Dashboard (Mobile-First + Responsive PC)
           ========================================================================= */
        :root {
            --bs-primary-rgb: 13, 110, 253;
            --status-pending: #ffc107;
            --status-working: #0d6efd;
            --status-complete: #198754;
            --status-cancel: #dc3545;
        }

        .dashboard-container {
            max-width: 1200px; /* จำกัดความกว้างเมื่อเปิดบนหน้าจอ PC */
            margin: 0 auto;
            padding: 15px;
        }

        /* กล่องสรุปสถานะงาน (Stat Counters) */
        .stat-cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* มือถือแสดง 2 คอลัมน์ */
            gap: 12px;
            margin-bottom: 20px;
        }

        @media (min-width: 768px) {
            .stat-cards-grid {
                grid-template-columns: repeat(4, 1fr); /* จอคอม/แท็บเล็ตแสดง 4 คอลัมน์ */
                gap: 20px;
            }
        }

        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 5px solid #ccc;
        }

        .stat-card.card-pending { border-left-color: var(--status-pending); }
        .stat-card.card-working { border-left-color: var(--status-working); }
        .stat-card.card-complete { border-left-color: var(--status-complete); }
        .stat-card.card-all { border-left-color: #6c757d; }

        .stat-icon {
            font-size: 28px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-info .stat-number {
            font-size: 20px;
            font-weight: bold;
            line-height: 1.2;
            color: #212529;
        }

        .stat-info .stat-label {
            font-size: 11px;
            color: #6c757d;
            font-weight: bold;
        }

        /* แท็บบาร์สลับสถานะงาน */
        .job-tab-bar {
            display: flex;
            background: #e9ecef;
            padding: 4px;
            border-radius: 10px;
            margin-bottom: 15px;
            gap: 4px;
            overflow-x: auto;
        }

        .tab-item {
            flex: 1;
            padding: 10px 12px;
            border: none;
            background: transparent;
            color: #495057;
            font-weight: bold;
            font-size: 13px;
            border-radius: 8px;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
            text-align: center;
        }

        .tab-item.active {
            background: white;
            color: #1e3c72;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        /* การ์ดรายการงาน (Job Item Card) */
        .job-cards-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        @media (min-width: 992px) {
            .job-cards-list {
                display: grid;
                grid-template-columns: repeat(2, 1fr); /* บนจอ PC แบ่งการ์ดงานเป็น 2 คอลัมน์ */
            }
        }

        .job-card {
            background: white;
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
            position: relative;
            transition: transform 0.2s ease;
        }

        .job-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            border-bottom: 1px solid #f1f3f5;
            padding-bottom: 8px;
        }

        .job-id {
            font-size: 13px;
            font-weight: bold;
            color: #6c757d;
        }

        .job-badge {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: bold;
        }

        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-processing { background: #cff4fc; color: #055160; }
        .badge-complete { background: #d1e7dd; color: #0f5132; }

        .job-body {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
        }

        .job-photo {
            width: 85px;
            height: 85px;
            border-radius: 10px;
            object-fit: cover;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            flex-shrink: 0;
        }

        .job-details {
            flex: 1;
            font-size: 13px;
        }

        .job-title {
            font-size: 15px;
            font-weight: bold;
            color: #212529;
            margin-bottom: 4px;
        }

        .job-meta {
            color: #6c757d;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .job-actions {
            display: flex;
            gap: 8px;
            border-top: 1px solid #f1f3f5;
            padding-top: 10px;
        }

        .btn-job-action {
            flex: 1;
            padding: 10px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-accept { background: #0d6efd; color: white; }
        .btn-maps { background: #e9ecef; color: #495057; }
        .btn-finish { background: #198754; color: white; }
    </style>
@endsection

@section('content')
    <!-- Header ส่วนหัวแอป -->
    <div class="sub-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); padding: 18px 15px;">
        <button onclick="window.location.href='{{ route('staff.dashboard') }}'" class="btn-back">🔄</button>
        <div style="flex: 1;">
            <h4 style="margin: 0; color: white; font-weight: bold;">แผงควบคุมเจ้าหน้าที่ (Staff Dashboard)</h4>
            <small style="color: rgba(255,255,255,0.8); font-size: 12px;">จัดการรายการแจ้งเหตุท่อน้ำแตก/งานประปา</small>
        </div>
    </div>

    <div class="dashboard-container">
        
        <!-- แจ้งเตือน Flash Message จาก Session -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show my-2" role="alert" style="border-radius: 10px; font-size: 14px;">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show my-2" role="alert" style="border-radius: 10px; font-size: 14px;">
                ❌ {{ session('error') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show my-2" role="alert" style="border-radius: 10px; font-size: 14px;">
                ⚠️ {{ session('warning') }}
            </div>
        @endif

        <!-- 1. กล่องสรุปสถานะงาน (Stat Counters) -->
        <h4 class="section-title" style="margin-top: 10px;">สรุปสถานะงานทั้งหมด</h4>
        <div class="stat-cards-grid">
            <div class="stat-card card-pending">
                <div class="stat-icon">🚨</div>
                <div class="stat-info">
                    <div class="stat-number" id="countPending">{{ $pendingCount ?? 0 }}</div>
                    <div class="stat-label">รอดำเนินการ</div>
                </div>
            </div>
            <div class="stat-card card-working">
                <div class="stat-icon">🛠️</div>
                <div class="stat-info">
                    <div class="stat-number" id="countWorking">{{ $workingCount ?? 0 }}</div>
                    <div class="stat-label">กำลังซ่อมแซม</div>
                </div>
            </div>
            <div class="stat-card card-complete">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <div class="stat-number" id="countComplete">{{ $completeCount ?? 0 }}</div>
                    <div class="stat-label">เสร็จสิ้นแล้ว</div>
                </div>
            </div>
            <div class="stat-card card-all">
                <div class="stat-icon">📋</div>
                <div class="stat-info">
                    <div class="stat-number" id="countAll">{{ $totalCount ?? 0 }}</div>
                    <div class="stat-label">งานทั้งหมด</div>
                </div>
            </div>
        </div>

        <!-- 2. แท็บบาร์สำหรับสลับตัวกรองสถานะงาน -->
        <div class="job-tab-bar">
            <button class="tab-item active" onclick="filterJobs('all', this)">งานทั้งหมด</button>
            <button class="tab-item" onclick="filterJobs('pending', this)">🚨 รอรับงาน</button>
            <button class="tab-item" onclick="filterJobs('processing', this)">🛠️ กำลังทำ</button>
            <button class="tab-item" onclick="filterJobs('complete', this)">✅ เสร็จสิ้น</button>
        </div>

        <!-- 3. รายการการ์ดงาน (Job Cards Container) -->
        <div class="job-cards-list" id="jobCardsContainer">
            @forelse($notifies as $notify)
                <div class="job-card" data-status="{{ $notify->status }}">
                    <div class="job-card-header">
                        <span class="job-id">งาน #{{ $notify->id }} • {{ $notify->created_at ? $notify->created_at->format('d/m/Y H:i') : '-' }}</span>
                        @if($notify->status === 'pending')
                            <span class="job-badge badge-pending">🚨 รอดำเนินการ</span>
                        @elseif($notify->status === 'processing')
                            <span class="job-badge badge-processing">🛠️ กำลังดำเนินการ</span>
                        @elseif($notify->status === 'complete')
                            <span class="job-badge badge-complete">✅ เสร็จสิ้น</span>
                        @else
                            <span class="job-badge style-secondary">{{ $notify->status }}</span>
                        @endif
                    </div>

                    <div class="job-body">
                        <!-- ภาพถ่ายสถานที่เกิดเหตุ -->
                        <img src="{{ $notify->photo_path ? asset($notify->photo_path) : 'https://via.placeholder.com/150?text=No+Photo' }}" 
                             class="job-photo" 
                             alt="ภาพถ่ายจุดเกิดเหตุ"
                             onclick="viewFullImage('{{ $notify->photo_path ? asset($notify->photo_path) : '' }}')">

                        <div class="job-details">
                            <div class="job-title">
                                @switch($notify->issue_type)
                                    @case('pipe_burst') ท่อแตก / ท่อรั่ว @break
                                    @case('no_water') น้ำไม่ไหล @break
                                    @case('low_pressure') น้ำไหลอ่อน @break
                                    @case('dirty_water') น้ำขุ่น / มีกลิ่น @break
                                    @default {{ $notify->issue_type ?? 'แจ้งเหตุน้ำประปา' }}
                                @endswitch
                            </div>
                            <div class="job-meta">
                                📍 <strong>พิกัด:</strong> {{ number_format($notify->latitude, 5) }}, {{ number_format($notify->longitude, 5) }}
                            </div>
                            <div class="job-meta">
                                👤 <strong>ผู้แจ้ง:</strong> {{ $notify->user->firstname ?? 'ประชาชนทั่วไป' }} {{ $notify->user->lastname ?? '' }}
                            </div>
                            @if($notify->description)
                                <div class="job-meta style-italic text-muted">
                                    💬 {{ Str::limit($notify->description, 40) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- เช็กว่างานยังไม่ถูกยกเลิก/ซ่อมเสร็จ และ Staff ที่ล็อกอินอยู่ยังไม่ได้กดรับงานนี้ --}}
                    @php
                        $currentUserId = Auth::id();
                        // เช็กว่า Staff คนนี้เคยกดรับงานนี้ไปหรือยังผ่าน Relationship staffs
                        $isAlreadyAccepted = $notify->staffs->contains('id', $currentUserId);
                    @endphp
                    <!-- ปุ่มการทำงาน (Actions) -->
                    <div class="job-actions">
                        <!-- ปุ่มเปิดแผนที่นำทาง Google Maps -->
                        <a href="https://www.google.com/maps?q={{ $notify->latitude }},{{ $notify->longitude }}" 
                           target="_blank" 
                           class="btn-job-action btn-maps">
                            🗺️ นำทาง
                        </a>

                       <!-- เงื่อนไขปุ่มกดรับงาน: แสดงเมื่อ งานยังไม่ปิดเคส/ไม่ถูกยกเลิก และ Staff คนนี้ยังไม่ได้กดรับ -->
                        @if(in_array($notify->status, ['pending', 'processing']) && !$isAlreadyAccepted)
                            <a href="{{ route('staff.job.accept', ['notify' => $notify->id]) }}" 
                            class="btn-job-action btn-accept"
                            onclick="return confirm('คุณต้องการเข้าร่วมรับงาน #{{ $notify->id }} นี้ใช่หรือไม่?')">
                                🤝 เข้าร่วมรับงานนี้
                            </a>
                        @endif

                        <!-- แสดงปุ่มปิดงานเมื่อ Staff คนนี้เป็นหนึ่งในผู้รับงานแล้ว และงานอยู่ในสถานะ processing -->
                        @if($notify->status === 'processing' && $isAlreadyAccepted)
                            <button type="button" 
                                    class="btn-job-action btn-finish"
                                    onclick="completeJob({{ $notify->id }})">
                                ✅ บันทึกปิดงาน
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-white rounded-3 shadow-sm" style="grid-column: span 2;">
                    <div style="font-size: 40px; margin-bottom: 10px;">📭</div>
                    <h5 class="text-muted">ยังไม่มีรายการแจ้งเหตุในระบบ</h5>
                </div>
            @endforelse
        </div>

    </div>
@endsection

@section('script')
    <script>
        // ฟังก์ชันคัดกรองงานตามแท็บที่กด
        function filterJobs(status, btnElement) {
            // ปรับสถานะปุ่ม Active
            document.querySelectorAll('.tab-item').forEach(btn => btn.classList.remove('active'));
            btnElement.classList.add('active');

            // แสดง/ซ่อนการ์ดตาม Status
            const cards = document.querySelectorAll('.job-card');
            cards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                if (status === 'all' || cardStatus === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // ฟังก์ชันดูรูปใหญ่แบบป๊อปอัป
        function viewFullImage(src) {
            if (!src) return;
            const modalHtml = `
                <div id="imgModal" onclick="this.remove()" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:99999; display:flex; align-items:center; justify-content:center; padding:20px;">
                    <img src="${src}" style="max-width:100%; max-height:90vh; border-radius:10px; box-shadow:0 0 20px rgba(255,255,255,0.2);">
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        // ฟังก์ชันสำหรับกดปิดงาน (สามารถเพิ่ม AJAX ยิงเปลี่ยนสถานะเป็น complete ได้)
        function completeJob(notifyId) {
    const remark = prompt("กรุณาระบุรายละเอียดการซ่อมแซม/แก้ปัญหา (ถ้ามี):");
    if (remark !== null) {
        // สร้าง Dynamic Form เพื่อส่ง POST ไปยัง Route complete
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/staff/job/${notifyId}/complete`;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        const remarkInput = document.createElement('input');
        remarkInput.type = 'hidden';
        remarkInput.name = 'remark';
        remarkInput.value = remark;
        form.appendChild(remarkInput);

        document.body.appendChild(form);
        form.submit();
    }
}
    </script>
@endsection