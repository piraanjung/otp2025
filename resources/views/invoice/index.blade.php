@extends('layouts.admin1')

@section('nav-invoice')
    active
@endsection
@section('nav-header')
    จัดการใบแจ้งหนี้
@endsection
@section('nav-main')
    <a href="{{ route('invoice.index') }}"> ออกใบแจ้งหนี้</a>
@endsection

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #e91e63; /* Material Pink/Rose based on your gradient logic */
            --bg-light: #f8f9fa;
            --card-radius: 12px;
        }

        /* Sidebar Styling */
        .nav-pills-custom .nav-link {
            color: #555;
            background: #fff;
            position: relative;
            font-weight: 500;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .nav-pills-custom .nav-link:hover {
            background: #fdfdfd;
            color: var(--primary-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .nav-pills-custom .nav-link.active {
            background: #fff;
            color: var(--primary-color);
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        /* Main Card Styling */
        .material-card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            background: #fff;
            margin-bottom: 2rem;
            overflow: hidden;
            transition: transform 0.2s;
        }
        /* .material-card:hover { transform: translateY(-2px); } */

        .card-header-custom {
            background: linear-gradient(87deg, #344767 0%, #212529 100%);
            padding: 1.5rem;
            color: white;
            border-radius: var(--card-radius) var(--card-radius) 0 0;
        }

        /* Action Boxes (Grid) */
        .stat-box {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 1.25rem;
            height: 100%;
            position: relative;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        .stat-box:hover {
            border-color: transparent;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transform: translateY(-3px);
            cursor: pointer;
        }
        .stat-box.disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background: #fcfcfc;
        }
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        .stat-label { font-size: 0.85rem; color: #777; font-weight: 500; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: #333; }
        .stat-action-btn {
            position: absolute;
            top: 15px; right: 15px;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .stat-box:hover .stat-action-btn { opacity: 1; }

        /* Colors for Status */
        .bg-soft-warning { background-color: #fff3cd; color: #ffc107; }
        .bg-soft-success { background-color: #d4edda; color: #28a745; }
        .bg-soft-info { background-color: #d1ecf1; color: #17a2b8; }
        .bg-soft-danger { background-color: #f8d7da; color: #dc3545; }
        
        .text-gradient-warning { background: -webkit-linear-gradient(45deg, #ffc107, #ff9800); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        /* Progress Section */
        .financial-footer {
            background: #f8f9fa;
            border-top: 1px solid #eee;
            padding: 1.5rem;
        }
        .progress-thin { height: 6px; border-radius: 3px; }
    </style>
@endsection

@section('nav-topic')
    ข้อมูลใบแจ้งหนี้แยกตามเส้นทางจัดเก็บ เดือน {{ $current_inv_period->inv_p_name }}
@endsection

@section('content')
    <div class="container-fluid my-3 py-3">
        <div class="row">
            
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="position-sticky" style="top: 100px; z-index: 1020; max-height: calc(100vh - 120px); overflow-y: auto;">
                        <div class="card shadow-none border-0 bg-transparent">
                        <div class="card-body p-0">
                            <h6 class="text-uppercase text-muted text-xs font-weight-bolder opacity-7 mb-3 ps-2">
                                เส้นทางจดมิเตอร์
                            </h6>
                            <div class="nav flex-column nav-pills-custom" id="v-pills-tab" role="tablist">
                                <?php $i = 0; ?>
                                @foreach ($zones as $key => $zone)
                                    <a class="nav-link mb-2 d-flex justify-content-between align-items-center" href="#b{{ $i }}">
                                        <div class="d-flex align-items-center">
                                            <div class="icon icon-shape icon-xs shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center text-dark">
                                                <i class="fa fa-map-marker-alt text-xs"></i>
                                            </div>
                                            <span class="text-sm">{{ $zone['zone_info']['undertake_subzone']['subzone_name'] }}</span>
                                        </div>
                                        @if ($zone['user_notyet_inv_info'] > 0)
                                            <span class="badge bg-danger rounded-pill">{{$zone['user_notyet_inv_info']}}</span>
                                        @endif
                                    </a>
                                    <?php $i++; ?>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9 col-md-8">
                <?php $i = 0; ?>
                @foreach ($zones as $zone)
                    <div class="card material-card" id="b{{ $i++ }}">
                        
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-white mb-0">{{ $zone['zone_info']['undertake_zone']['zone_name'] }}</h5>
                                <span class="text-white-50 text-sm opacity-8">
                                    <i class="fa fa-route me-1"></i> {{ $zone['zone_info']['undertake_subzone']['subzone_name'] }}
                                </span>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-white text-dark shadow-sm">
                                    <i class="fa fa-users me-1"></i> สมาชิก {{ $zone['members_count'] }} คน
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="row g-3">
                                
                                <div class="col-6 col-lg-3">
                                    @php $isDisabled = $zone['initTotalCount'] == 0; @endphp
                                    <div class="stat-box {{ $isDisabled ? 'disabled' : '' }}" 
                                         onclick="{{ !$isDisabled ? "window.location.href='".route('invoice.zone_create', ['zone_id' => $zone['zone_info']['undertake_subzone_id'], 'curr_inv_prd' => $current_inv_period->id])."'" : '' }}">
                                        <div class="stat-icon bg-soft-warning">
                                            <i class="fa fa-pen-alt"></i>
                                        </div>
                                        <div class="stat-value">{{ $zone['initTotalCount'] }}</div>
                                        <div class="stat-label">รอจดมิเตอร์</div>
                                        @if(!$isDisabled)
                                            <div class="stat-action-btn text-warning"><i class="fa fa-arrow-right"></i></div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-6 col-lg-3">
                                    @php $isDisabled = $zone['invoiceTotalCount'] == 0; @endphp
                                    <div class="stat-box {{ $isDisabled ? 'disabled' : '' }}"
                                         onclick="{{ !$isDisabled ? "window.location.href='".route('invoice.zone_edit', ['subzone_id' => $zone['zone_info']['undertake_subzone_id'], 'curr_inv_prd' => $current_inv_period])."'" : '' }}">
                                        <div class="stat-icon bg-soft-info">
                                            <i class="fa fa-check-double"></i>
                                        </div>
                                        <div class="stat-value">{{ $zone['invoiceTotalCount'] }}</div>
                                        <div class="stat-label">บันทึกแล้ว</div>
                                        @if(!$isDisabled)
                                            <div class="stat-action-btn text-info"><i class="fa fa-edit"></i></div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-6 col-lg-3">
                                    @php $isDisabled = $zone['paidTotalCount'] == 0; @endphp
                                    <div class="stat-box {{ $isDisabled ? 'disabled' : '' }}"
                                         onclick="{{ !$isDisabled ? "window.location.href='".url('payment/paymenthistory/' . $current_inv_period->id . '/' . $zone['zone_info']['undertake_subzone_id'])."' " : '' }}">
                                        <div class="stat-icon bg-soft-success">
                                            <i class="fa fa-file-invoice-dollar"></i>
                                        </div>
                                        <div class="stat-value">{{ $zone['paidTotalCount'] }}</div>
                                        <div class="stat-label">ชำระเงินแล้ว</div>
                                        @if(!$isDisabled)
                                            <div class="stat-action-btn text-success"><i class="fa fa-eye"></i></div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-6 col-lg-3">
                                    @php $isDisabled = $zone['user_notyet_inv_info'] == 0; @endphp
                                    <div class="stat-box {{ $isDisabled ? 'disabled' : '' }}"
                                         onclick="{{ !$isDisabled ? "window.location.href='".route('invoice.zone_create', ['zone_id' => $zone['zone_info']['undertake_subzone_id'], 'curr_inv_prd' => $current_inv_period->id, 'new_user' => 1])."'" : '' }}">
                                        <div class="stat-icon bg-soft-danger">
                                            <i class="fa fa-user-slash"></i>
                                        </div>
                                        <div class="stat-value">{{ $zone['user_notyet_inv_info'] }}</div>
                                        <div class="stat-label">ไม่มีข้อมูลมิเตอร์</div>
                                        @if(!$isDisabled)
                                            <div class="stat-action-btn text-danger"><i class="fa fa-plus"></i></div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('invoice.export_excel', ['zone_id' => $zone['zone_info']['undertake_subzone_id'], 'curr_inv_prd' => $current_inv_period->id]) }}" 
                                           class="btn btn-outline-dark btn-sm mb-0">
                                            <i class="fa fa-file-excel me-1 text-success"></i> Export Excel
                                        </a>
                                        <a href="{{ route('invoice.print_invoice', ['zone_id' => $zone['zone_info']['undertake_subzone_id'], 'curr_inv_prd' => $current_inv_period->id]) }}" 
                                           class="btn btn-outline-dark btn-sm mb-0">
                                            <i class="fa fa-print me-1 text-primary"></i> พิมพ์ใบแจ้งหนี้
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="financial-footer">
                            <h6 class="text-xs font-weight-bold text-uppercase text-muted mb-3">สรุปยอดเงินประจำรอบบิล</h6>
                            <div class="row align-items-end">
                                
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <p class="text-sm mb-1 text-muted">ยอดที่ต้องชำระทั้งหมด</p>
                                    <h4 class="mb-2">{{ number_format($zone['total_paid'], 2) }} <span class="text-sm text-muted">บาท</span></h4>
                                    <div class="progress progress-thin">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3 mb-md-0">
                                    <p class="text-sm mb-1 text-muted">เก็บเงินได้แล้ว</p>
                                    <h4 class="text-success mb-2">{{ number_format($zone['paidTotalAmount'], 2) }} <span class="text-sm text-muted">บาท</span></h4>
                                    <div class="progress progress-thin bg-light">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width:{{ $zone['total_paid'] == 0 ? 0 : number_format(($zone['paidTotalAmount'] / $zone['total_paid']) * 100, 2) }}%">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <p class="text-sm mb-1 text-muted">ค้างชำระ</p>
                                    <h4 class="text-warning mb-2">{{ number_format($zone['total_paid'] - $zone['paidTotalAmount'], 2) }} <span class="text-sm text-muted">บาท</span></h4>
                                    <div class="progress progress-thin bg-light">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width:{{ $zone['total_paid'] == 0 ? 0 : number_format((($zone['total_paid'] - $zone['paidTotalAmount']) / $zone['total_paid']) * 100, 2) }}%">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Smooth scroll for nav links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                // Remove active class from all navs
                document.querySelectorAll('.nav-pills-custom .nav-link').forEach(nav => nav.classList.remove('active'));
                // Add active to current
                this.classList.add('active');

                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });

        // (Original JS Logic retained below)
        let i = 0;
        $('#meternumber').keyup(function() {
            // ... (Your existing JS logic remains unchanged) ...
        });
        
        // ... (Your existing JS logic remains unchanged) ...

        $(document).ready(() => {
            setTimeout(() => {
                $('.alert').toggle('slow')
            }, 2000)
        })
    </script>
@endsection