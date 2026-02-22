@extends('layouts.admin1')

@section('nav-payment-search', 'active')
@section('nav-header', 'จัดการใบเสร็จรับเงิน')
@section('nav-main')
    <a href="{{ route('payment.search') }}">ค้นหาใบเสร็จรับเงิน</a>
@endsection
@section('nav-topic', 'ค้นหาใบเสร็จรับเงิน')

@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* ปรับให้ Select2 เต็มความกว้างของ Container แม่ */
        .select2-container {
            width: 100% !important;
        }
        .select2-container .select2-selection--single {
            height: 45px; /* ปรับความสูงให้พอดีสวยงาม */
            padding-top: 8px;
            font-size: 1rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            top: 2px;
        }

        .hidden { display: none; }

        .budgetyear-div {
            cursor: pointer;
            transition: all 0.3s ease; /* Smooth transition */
        }
        .budgetyear-div:hover {
            transform: translateY(-5px); /* ขยับขึ้นเล็กน้อยเมื่อ Hover */
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        /* จัดตัวเลขในตารางให้ชิดขวา */
        .text-num {
            text-align: right;
        }
    </style>
@endsection

@section('content')
    {{-- Preloader --}}
    <div class="preloader-wrapper text-center my-3">
        <button class="btn btn-primary btn-sm mb-2" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status"></span>
            กำลังโหลดข้อมูล...
        </button>
    </div>

    {{-- Search Section --}}
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8 col-md-10 col-12">
            <div class="card card-body shadow-sm">
                <h6 class="mb-2"><i class="fa fa-search me-2"></i>ค้นหา : ชื่อ, ที่อยู่, เลขมิเตอร์</h6>
                <form action="{{ route('payment.search') }}" method="POST" id="searchform">
                    @csrf
                    <select class="js-example-basic-single form-control" name="user_info">
                        <option value="">-- กรุณาเลือกผู้ใช้น้ำ --</option>
                        @foreach ($users as $user)
                            @foreach ($user->usermeterinfos as $usermeterinfo)
                                <option value="{{ $usermeterinfo->meter_id }}" 
                                    {{ request('user_info') == $usermeterinfo->meter_id ? 'selected' : '' }}>
                                    
                                    {{ $user->prefix . $user->firstname . ' ' . $user->lastname }} 
                                    [บ้านเลขที่ {{ $user->address }} {{ optional($user->user_zone)->zone_name }}]
                                    - [มิเตอร์: {{ $usermeterinfo->meternumber }}]

                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    @if (collect($inv_by_budgetyear)->isNotEmpty())
        {{-- ดึงข้อมูล User คนแรกมาแสดงหัวข้อ (ใช้ optional กัน error) --}}
        @php
            $firstItem = $inv_by_budgetyear[0][0] ?? null;
            // dd($firstItem);
            $userInfo = $firstItem ? $firstItem['tw_meter_infos']['user'] : null;
            $meterInfo = $firstItem ? $firstItem['tw_meter_infos'] : null;
        @endphp

        <div class="container-fluid my-3 py-3">
            <div class="row">
                
                {{-- Left Sidebar: Budget Year Navigation --}}
                <div class="col-lg-3 mb-4">
                    <div class="position-sticky top-1" style="z-index: 99;">
                        <h6 class="text-secondary ps-2">เลือกปีงบประมาณ</h6>
                        @foreach ($inv_by_budgetyear as $budgetyear)
                            @php
                                $isActive = $budgetyear[0]['invoice_period']['budgetyear']['status'] == 'active';
                                $lastmeter_sum = collect($budgetyear)->sum('lastmeter');
                                $currentmeter_sum = collect($budgetyear)->sum('currentmeter');
                                $net = $currentmeter_sum - $lastmeter_sum;
                                $inv_period_count = collect($budgetyear)->count();
                            @endphp
                            
                            <div class="card mb-3 budgetyear-div">
                                <span class="mask {{ $isActive ? 'bg-gradient-info' : 'bg-gradient-dark' }} opacity-9 border-radius-xl"></span>
                                <div class="card-body p-3 position-relative">
                                    <a href="#by{{ $budgetyear[0]['invoice_period']['budgetyear_id'] }}" class="text-decoration-none">
                                        <div class="row">
                                            <div class="col-12 d-flex justify-content-between align-items-center">
                                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                                    <i class="ni ni-calendar-grid-58 text-dark text-gradient text-lg opacity-10"></i>
                                                </div>
                                                <div class="text-end">
                                                    <span class="text-white text-sm d-block opacity-8">ปีงบประมาณ</span>
                                                    <h5 class="text-white font-weight-bolder mb-0">
                                                        {{ $budgetyear[0]['invoice_period']['budgetyear']['budgetyear_name'] }}
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-3 pt-2 border-top border-white-50">
                                                <div class="d-flex justify-content-between text-white text-sm">
                                                    <span><i class="ni ni-paper-diploma me-1"></i> {{ $inv_period_count }} รอบบิล</span>
                                                    <span><i class="ni ni-drop me-1"></i> {{ number_format($net) }} ลิตร</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right Content: Invoices --}}
                <div class="col-lg-9">
                    
                    {{-- User Profile Card --}}
                    @if($userInfo)
                    <div class="card card-body mb-4 shadow-sm border-0" id="profile">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl position-relative me-3">
                                <img src="{{ asset('soft-ui/assets/img/bruce-mars.jpg') }}" alt="profile" class="w-100 border-radius-lg shadow-sm">
                            </div>
                            <div>
                                <h5 class="mb-1 font-weight-bolder text-dark">
                                    {{ $userInfo['firstname'] . ' ' . $userInfo['lastname'] }}
                                    <span class="text-muted text-sm fw-normal ms-2"><i class="fas fa-tachometer-alt"></i> {{ $meterInfo['meternumber'] }}</span>
                                </h5>
                                <p class="mb-0 text-sm text-secondary">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                    {{ $meterInfo['undertake_subzone']['undertake_subzone_name'] ?? '-' }} ::
                                    {{ $userInfo['address'] }}
                                    {{ $meterInfo['undertake_zone']['undertake_zone_name'] ?? '-' }}
                                    ต.{{ $userInfo['user_tambon']['tambon_name'] ?? '-' }}
                                    อ.{{ $userInfo['user_district']['district_name'] ?? '-' }}
                                    จ.{{ $userInfo['user_province']['province_name'] ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Invoice Lists by Budget Year --}}
                    @foreach ($inv_by_budgetyear as $budgetyear)
                        @php
                            $grouped = collect($budgetyear)->groupBy('acc_trans_id_fk');
                            $yearId = $budgetyear[0]['invoice_period']['budgetyear_id'];
                            $yearName = $budgetyear[0]['invoice_period']['budgetyear']['budgetyear_name'];
                            $isYearActive = $budgetyear[0]['invoice_period']['budgetyear']['status'] == 'active';
                        @endphp

                        <div class="mb-5 scroll-mt-5" id="by{{ $yearId }}" style="scroll-margin-top: 20px;">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge {{ $isYearActive ? 'bg-gradient-info' : 'bg-gradient-secondary' }} me-2 p-2">
                                    <i class="ni ni-bookmark-04 text-lg"></i>
                                </span>
                                <h4 class="mb-0 text-dark">ปีงบประมาณ {{ $yearName }}</h4>
                            </div>

                            @foreach ($grouped as $acc_trans_id => $group)
                                <div class="card mb-4 shadow-sm border-1">
                                    <div class="card-header bg-light p-3">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                                            <div>
                                                <h6 class="mb-0 text-primary"><i class="fas fa-file-invoice-dollar me-1"></i> ใบเสร็จเลขที่: {{ $acc_trans_id }}</h6>
                                                <small class="text-muted">วันที่ชำระ: {{ date('d/m/Y', strtotime($group[0]['updated_at'])) }}</small>
                                            </div>
                                            <div class="d-flex gap-2 mt-2 mt-sm-0">
                                                <a href="{{ route('payment.receipt_print_history', $acc_trans_id) }}" target="_blank" class="btn btn-outline-primary btn-sm mb-0">
                                                    <i class="fas fa-print me-1"></i> พิมพ์
                                                </a>
                                                <form action="{{ route('payment.destroy', $acc_trans_id) }}" method="post" onsubmit="return confirm('ยืนยันการยกเลิกใบเสร็จนี้? การกระทำนี้ไม่สามารถย้อนกลับได้');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm mb-0">
                                                        <i class="fas fa-ban me-1"></i> ยกเลิก
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table align-items-center mb-0 table-striped">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">สถานะ</th>
                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ใบแจ้งหนี้</th>
                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">รอบบิล</th>
                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-num">ก่อนจด</th>
                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-num">หลังจด</th>
                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-num">ใช้น้ำ</th>
                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-num">ค่าน้ำ</th>
                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-num">บริการ</th>
                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-num">Vat</th>
                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-num pe-4">รวม</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $totalpaid = 0; @endphp
                                                    @foreach ($group as $invoice)
                                                        @php $totalpaid += $invoice['totalpaid']; @endphp
                                                        <tr>
                                                            <td class="ps-2">
                                                                @if ($invoice['totalpaid'] == 0)
                                                                    <a href="javascript:;" class="text-danger del_dup_inv" data-inv_id="{{ $invoice['id'] }}" title="ลบรายการซ้ำ">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </a>
                                                                @else
                                                                    <i class="fas fa-check-circle text-success text-xs"></i>
                                                                @endif
                                                            </td>
                                                            <td class="text-center font-weight-bold">{{ $invoice['id'] }}</td>
                                                            <td>
                                                                <span class="badge badge-dot me-4">
                                                                    <i class="bg-info"></i>
                                                                    <span class="text-dark text-xs">{{ $invoice['invoice_period']['inv_p_name'] }}</span>
                                                                </span>
                                                            </td>
                                                            <td class="text-num text-sm">{{ $invoice['lastmeter'] }}</td>
                                                            <td class="text-num text-sm">{{ $invoice['currentmeter'] }}</td>
                                                            <td class="text-num text-sm fw-bold text-dark">{{ $invoice['water_used'] }}</td>
                                                            <td class="text-num text-sm">{{ $invoice['inv_type'] == 'u' ? number_format($invoice['paid'], 2) : '-' }}</td>
                                                            <td class="text-num text-sm">{{ $invoice['inv_type'] == 'r' ? number_format(10, 2) : '-' }}</td>
                                                            <td class="text-num text-sm">{{ number_format($invoice['vat'], 2) }}</td>
                                                            <td class="text-num fw-bold text-dark pe-4">{{ number_format($invoice['totalpaid'], 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr class="bg-gray-50">
                                                        <td colspan="9" class="text-end text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                            ยอดรวมสุทธิ
                                                        </td>
                                                        <td class="text-num font-weight-bolder text-primary text-sm pe-4">
                                                            {{ number_format($totalpaid, 2) }} ฿
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    @else
        {{-- กรณีไม่มีข้อมูล --}}
        @if(request()->has('user_info'))
            <div class="container text-center mt-5">
                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="150" alt="no data" class="opacity-5 mb-3">
                <h4 class="text-muted">ไม่พบประวัติการชำระเงิน</h4>
                <p>กรุณาลองค้นหาใหม่อีกครั้ง หรือผู้ใช้นี้ยังไม่มีการชำระเงินในระบบ</p>
            </div>
        @endif
    @endif

@endsection

@section('script')
    <script src="https://www.jqueryscript.net/demo/Export-Html-Table-To-Excel-Spreadsheet-using-jQuery-table2excel/src/jquery.table2excel.js"></script>
    {{-- <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script> --}} <script>
        $(document).ready(function() {
            // Setup Select2
            $('.js-example-basic-single').select2({
                placeholder: "ค้นหาผู้ใช้น้ำ...",
                allowClear: true,
                width: '100%' // สำคัญ: บังคับให้เต็มจอ
            });

            // Hide Preloader
            $('.preloader-wrapper').fadeOut();
        });

        // Event เมื่อเลือก User ใน Select2 ให้ Submit Form ทันที
        $('.js-example-basic-single').on('select2:select', function (e) {
            $('.preloader-wrapper').removeClass('hidden').fadeIn(); // โชว์ loading อีกรอบ
            $('#searchform').submit();
        });

        // Delete Logic
        $(document).on('click', '.del_dup_inv', function(){
            let inv_id = $(this).data('inv_id');
            if(confirm('ต้องการลบข้อมูลรายการนี้ใช่หรือไม่? (การกระทำนี้ไม่สามารถย้อนกลับได้)')) {
                // แก้ไข URL จาก invioice เป็น invoice
                $.get('/invoice/delete_duplicate_inv/' + inv_id)
                .done(function(res) {
                    alert('ลบข้อมูลสำเร็จ');
                    location.reload(); // รีโหลดหน้าเพื่ออัปเดตข้อมูล
                })
                .fail(function() {
                    alert('เกิดข้อผิดพลาดในการลบข้อมูล');
                });
            }
        });
    </script>
@endsection