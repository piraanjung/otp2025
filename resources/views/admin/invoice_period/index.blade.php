@extends('layouts.admin1')

{{-- Config Navbar --}}
@section('budgetyear-show', 'show')
@section('nav-inv_prd', 'active')
@section('nav-header')
    <a href="{{ route('admin.invoice_period.index') }}">รอบบิล</a>
@endsection
@section('nav-main', 'จัดการรอบบิล')
@section('nav-topic', 'รายการรอบบิล')
@section('invoice_period', 'active')

@section('style')
    {{-- Import Fonts & Icons if needed --}}
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* Modern Theme Override */
        body {
            font-family: 'Prompt', sans-serif;
        }

        .modern-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            background-color: #fff;
        }

        .card-header-modern {
            background: #fff;
            padding: 25px 30px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Table Styling */
        .table-modern {
            width: 100%;
            border-collapse: collapse;
        }
        .table-modern thead th {
            border-top: none;
            border-bottom: 2px solid #f0f0f0;
            font-size: 0.85rem;
            color: #8898aa;
            text-transform: uppercase;
            font-weight: 600;
            padding: 15px 20px;
            letter-spacing: 0.5px;
        }
        .table-modern tbody td {
            vertical-align: middle;
            padding: 20px;
            border-bottom: 1px solid #f8f9fa;
            color: #525f7f;
            font-size: 0.95rem;
        }
        .table-modern tbody tr:hover {
            background-color: #fcfcfc;
            transform: scale(1.002);
            transition: all 0.2s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }

        /* Button & Badges */
        .btn-create-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(118, 75, 162, 0.4);
            transition: all 0.3s ease;
        }
        .btn-create-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(118, 75, 162, 0.6);
            color: white;
        }

        .badge-soft {
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .badge-soft-success {
            background-color: #e0f2f1;
            color: #00695c;
        }
        .badge-soft-secondary {
            background-color: #eceff1;
            color: #546e7a;
        }
        .badge-budget {
            background-color: #fff3e0;
            color: #ef6c00;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 6px;
            margin-left: 8px;
        }

        /* Date Range Style */
        .date-range {
            display: flex;
            flex-direction: column;
            font-size: 0.85rem;
        }
        .date-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 2px;
        }
        .date-item i {
            color: #cbd5e0;
            font-size: 0.8rem;
        }

        /* Dropdown Action */
        .action-btn {
            background: none;
            border: none;
            color: #a0aec0;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 5px;
            transition: all 0.2s;
        }
        .action-btn:hover {
            background-color: #edf2f7;
            color: #4a5568;
        }
    </style>
@endsection

@section('content')

    <div class="container-fluid py-4">
        {{-- เช็คว่ามี Error ส่งมาจาก Controller ไหม --}}
    @if(isset($error_message))
        <div class="alert alert-warning d-flex align-items-center shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2" style="font-size: 1.5rem;"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1">ยังไม่สามารถจัดการรอบบิลได้</h5>
                <p class="mb-0">
                    {{ $error_message }} 
                    กรุณาไปที่เมนู 
                    <a href="{{ route('admin.budgetyear.index') }}" class="alert-link text-decoration-underline">จัดการปีงบประมาณ</a> 
                    เพื่อสร้างและเปิดใช้งานปีงบประมาณปัจจุบันก่อนครับ
                </p>
            </div>
        </div>
      
    @endif
        <div class="card modern-card">
            {{-- Header --}}
            <div class="card-header-modern">
                <div>
                    <h5 class="mb-0 fw-bold text-dark">📅 จัดการรอบบิล (Invoice Periods)</h5>
                    <small class="text-muted">รายการรอบบิลและการตั้งค่าช่วงเวลา</small>
                </div>

                @if(!isset($error_message))
                <a href="{{ route('admin.invoice_period.create') }}" class="btn btn-create-modern">
                    <i class="fas fa-plus me-2"></i> สร้างรอบบิลใหม่
                </a>
                @endif
            </div>

            {{-- Body --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">#</th>
                                <th width="35%">ชื่อรอบบิล / ปีงบประมาณ</th>
                                <th width="25%">ช่วงเวลา (Timeline)</th>
                                <th class="text-center" width="20%">สถานะ</th>
                                <th class="text-end" width="15%">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice_periods as $index => $invoice_period)
                                <tr>
                                    <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="fw-bold text-dark fs-6">{{ $invoice_period->inv_p_name }}</div>
                                                <div class="mt-1">
                                                    <span class="badge-budget">
                                                        <i class="fas fa-tag me-1"></i> 
                                                        {{ $invoice_period->budgetyear->budgetyear_name ?? '-' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="date-range">
                                            <div class="date-item text-success">
                                                <i class="fas fa-play-circle"></i> 
                                                <span>เริ่ม: {{ $invoice_period->startdate }}</span>
                                            </div>
                                            <div class="date-item text-danger">
                                                <i class="fas fa-stop-circle"></i> 
                                                <span>สิ้นสุด: {{ $invoice_period->enddate }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($invoice_period->status == 'active')
                                            <span class="badge-soft badge-soft-success">
                                                <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true" style="width: 0.5rem; height: 0.5rem;"></span>
                                                รอบบิลปัจจุบัน
                                            </span>
                                        @else
                                            <span class="badge-soft badge-soft-secondary">
                                                <i class="fas fa-history me-1"></i> สิ้นสุดแล้ว
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        @if ($invoice_period->status == 'active')
                                            <div class="dropdown dropstart">
                                                <button class="action-btn" type="button" id="dropdownMenu{{ $invoice_period->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu shadow border-0" aria-labelledby="dropdownMenu{{ $invoice_period->id }}">
                                                    <li>
                                                        <a class="dropdown-item py-2" href="{{ route('admin.invoice_period.edit', $invoice_period->id) }}">
                                                            <i class="fas fa-edit text-warning me-2"></i> แก้ไขข้อมูล
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        {{-- Form สำหรับลบ --}}
                                                        <form id="form-delete-{{ $invoice_period->id }}" 
                                                              action="{{ route('admin.invoice_period.destroy', $invoice_period->id) }}" 
                                                              method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            {{-- ใช้ class btn-delete-check เพื่อดัก Event Click ใน JS --}}
                                                            <button type="button" 
                                                                    class="dropdown-item py-2 text-danger btn-delete-check" 
                                                                    data-id="{{ $invoice_period->id }}">
                                                                <i class="fas fa-trash-alt me-2"></i> ลบข้อมูล
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        @else
                                            <span class="text-muted small"><i class="fas fa-lock"></i> ล็อค</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Footer (ถ้ามี pagination ใส่ตรงนี้) --}}
            <div class="p-3 bg-light text-center text-muted border-top">
                <small>แสดงผลทั้งหมด {{ count($invoice_periods) }} รายการ</small>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // ดักจับการคลิกปุ่มลบที่มี class 'btn-delete-check'
            $('.btn-delete-check').on('click', function(e) {
                e.preventDefault(); // ห้าม submit form ทันที
                
                let btn = $(this);
                let inv_period_id = btn.data('id');
                let form = $('#form-delete-' + inv_period_id);

                // ถามยืนยันก่อน
                if (confirm('⚠️ คำเตือน: คุณต้องการลบรอบบิลนี้ใช่หรือไม่?')) {
                    
                    // เรียก API เช็คว่ามี Invoice ค้างอยู่ไหม
                    $.ajax({
                        url: '/api/invoice/checkInvoice/' + inv_period_id,
                        method: 'GET',
                        beforeSend: function() {
                            // อาจจะเปลี่ยน cursor เป็น loading
                            btn.css('opacity', '0.5').text('กำลังตรวจสอบ...');
                        },
                        success: function(data) {
                            // ตรวจสอบ data ที่ส่งกลับมา (Logic เดิมของคุณ data > 0 คือมี Invoice)
                            if (data > 0) {
                                alert('⛔ ไม่สามารถลบได้!\nเนื่องจากมี "ใบแจ้งหนี้" ผูกกับรอบบิลนี้อยู่\nกรุณาลบใบแจ้งหนี้ออกก่อน');
                                btn.css('opacity', '1').html('<i class="fas fa-trash-alt me-2"></i> ลบข้อมูล'); // คืนค่าปุ่ม
                            } else {
                                // ถ้าไม่มี Invoice -> Submit Form จริงๆ
                                form.submit();
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('เกิดข้อผิดพลาดในการตรวจสอบข้อมูล Server (' + error + ')');
                            btn.css('opacity', '1').html('<i class="fas fa-trash-alt me-2"></i> ลบข้อมูล');
                        }
                    });
                }
            });
        });
    </script>
@endsection