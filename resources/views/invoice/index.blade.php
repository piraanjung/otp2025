@extends('layouts.admin1') 

@section('nav-invoice') active @endsection 
@section('nav-header') จัดการใบแจ้งหนี้ @endsection 
@section('nav-main') <a href="{{ route('invoice.index') }}"> ออกใบแจ้งหนี้</a> @endsection 

@section('style') 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> 
<style> 
    :root { 
        --flat-primary: #34495e; 
        --flat-accent: #2980b9;
        --flat-success: #27ae60;
        --flat-warning: #f39c12;
        --flat-danger: #c0392b;
        --flat-info: #16a085;
        --flat-bg: #ecf0f1;
        --flat-card-bg: #ffffff;
        --flat-border: #dfe6e9;
        --card-radius: 6px; 
    } 

    body {
        background-color: var(--flat-bg) !important;
    }

    /* Sidebar Flat Styling */ 
    .nav-pills-custom .nav-link { 
        color: #2c3e50; 
        background: #ffffff; 
        position: relative; 
        font-weight: 500; 
        padding: 0.85rem 1rem; 
        border-radius: var(--card-radius); 
        margin-bottom: 6px; 
        transition: background 0.2s ease, color 0.2s ease; 
        border: 1px solid var(--flat-border); 
        box-shadow: none !important;
    } 
    .nav-pills-custom .nav-link:hover { 
        background: #f8f9fa; 
        color: var(--flat-accent); 
    } 
    .nav-pills-custom .nav-link.active { 
        background: var(--flat-primary) !important; 
        color: #ffffff !important; 
        border-color: var(--flat-primary); 
    } 
    .nav-pills-custom .nav-link.active .icon-shape {
        background: rgba(255,255,255,0.2) !important;
        color: #ffffff !important;
    }

    /* Main Card Flat Styling */ 
    .material-card { 
        border: 1px solid var(--flat-border); 
        border-radius: var(--card-radius); 
        box-shadow: none !important; 
        background: var(--flat-card-bg); 
        margin-bottom: 1.5rem; 
        overflow: hidden; 
    } 
    
    .card-header-custom { 
        background: #2c3e50; 
        padding: 1.25rem 1.5rem; 
        color: white; 
        border-radius: 0 !important; 
        border-bottom: 1px solid #1a252f;
    } 

    /* Action Boxes (Grid Flat UI) */ 
    .stat-box { 
        background: #ffffff; 
        border: 1px solid var(--flat-border); 
        border-radius: var(--card-radius); 
        padding: 1.25rem; 
        height: 100%; 
        position: relative; 
        transition: all 0.2s ease; 
        box-shadow: none !important;
    } 
    .stat-box:hover { 
        border-color: var(--flat-primary); 
        background: #fafbfc;
        transform: translateY(-2px); 
        cursor: pointer; 
    } 
    .stat-box.disabled { 
        opacity: 0.5; 
        cursor: not-allowed; 
        background: #fdfdfd; 
    } 
    .stat-box.disabled:hover {
        border-color: var(--flat-border);
        transform: none;
    }

    .stat-icon { 
        width: 36px; 
        height: 36px; 
        border-radius: 4px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        margin-bottom: 10px; 
        font-size: 1rem; 
    } 
    .stat-label { 
        font-size: 0.8rem; 
        color: #7f8c8d; 
        font-weight: 600; 
        text-transform: uppercase;
        letter-spacing: 0.5px;
    } 
    .stat-value { 
        font-size: 1.4rem; 
        font-weight: 700; 
        color: #2c3e50; 
    } 
    .stat-action-btn { 
        position: absolute; 
        top: 15px; 
        right: 15px; 
        opacity: 0; 
        transition: opacity 0.2s; 
    } 
    .stat-box:hover .stat-action-btn { 
        opacity: 1; 
    } 

    /* Flat Colors for Status Badges & Icons */ 
    .bg-flat-warning { background-color: #fef5e7; color: var(--flat-warning); } 
    .bg-flat-success { background-color: #e8f8f5; color: var(--flat-success); } 
    .bg-flat-info { background-color: #e8f6f3; color: var(--flat-info); } 
    .bg-flat-danger { background-color: #fadbd8; color: var(--flat-danger); } 

    /* Financial Footer Flat */ 
    .financial-footer { 
        background: #f8f9fa; 
        border-top: 1px solid var(--flat-border); 
        padding: 1.25rem 1.5rem; 
    } 
    .progress-flat { 
        height: 8px; 
        border-radius: 4px; 
        background-color: #e2e8f0;
        overflow: hidden;
    } 

    /* Flat Buttons */
    .btn-flat {
        border-radius: 4px;
        font-weight: 500;
        font-size: 0.85rem;
        padding: 0.45rem 0.9rem;
        border: 1px solid var(--flat-border);
        background: #ffffff;
        color: #2c3e50;
        box-shadow: none !important;
        transition: all 0.2s;
    }
    .btn-flat:hover {
        background: #f1f2f6;
        border-color: #b2bec3;
    }
</style> 
@endsection 

@section('nav-topic') ข้อมูลใบแจ้งหนี้แยกตามเส้นทางจัดเก็บ เดือน {{ $current_inv_period->inv_p_name }} @endsection 

@section('content') 
<div class="container-fluid my-3 py-3"> 
    <div class="row"> 
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 col-md-4 mb-4"> 
            <div class="position-sticky" style="top: 100px; z-index: 1020; max-height: calc(100vh - 120px); overflow-y: auto;"> 
                <div class="card shadow-none border-0 bg-transparent"> 
                    <div class="card-body p-0"> 
                        <h6 class="text-uppercase text-muted text-xs font-weight-bold mb-3 ps-2" style="letter-spacing: 1px;"> เส้นทางจดมิเตอร์ </h6> 
                        <div class="nav flex-column nav-pills-custom" id="v-pills-tab" role="tablist"> 
                            <?php $i = 0; ?> 
                            @foreach ($zones as $key => $zone) 
                            <a class="nav-link mb-2 d-flex justify-content-between align-items-center @if($i == 0) active @endif" href="#b{{ $i }}"> 
                                <div class="d-flex align-items-center text-truncate pe-2"> 
                                    <div class="icon icon-shape icon-xs bg-light text-center me-2 d-flex align-items-center justify-content-center text-secondary rounded" style="width: 28px; height: 28px;"> 
                                        <i class="fa fa-map-marker-alt text-xs"></i> 
                                    </div> 
                                    <span class="text-sm text-truncate">{{ $zone['zone_info']['undertake_subzone']['subzone_name'] }}</span> 
                                </div> 
                                @if ($zone['user_notyet_inv_info'] > 0) 
                                <span class="badge bg-danger rounded-0 px-2 py-1" style="font-size: 0.7rem;">{{$zone['user_notyet_inv_info']}}</span> 
                                @endif 
                            </a> 
                            <?php $i++; ?> 
                            @endforeach 
                        </div> 
                    </div> 
                </div> 
            </div> 
        </div> 

        <!-- Main Content Area -->
        <div class="col-lg-9 col-md-8"> 
            <?php $i = 0; ?> 
            @foreach ($zones as $zone) 
            <div class="card material-card" id="b{{ $i++ }}"> 
                <div class="card-header-custom d-flex justify-content-between align-items-center"> 
                    <div> 
                        <h5 class="text-white mb-0 font-weight-bold" style="font-size: 1.1rem;">{{ $zone['zone_info']['undertake_zone']['zone_name'] }}</h5> 
                        <span class="text-white-50 text-xs"> 
                            <i class="fa fa-route me-1"></i> {{ $zone['zone_info']['undertake_subzone']['subzone_name'] }} 
                        </span> 
                    </div> 
                    <div> 
                        <span class="badge bg-light text-dark rounded-0 px-2 py-1 font-weight-normal" style="font-size: 0.75rem;"> 
                            <i class="fa fa-users me-1 text-muted"></i> สมาชิก {{ $zone['members_count'] }} คน 
                        </span> 
                    </div> 
                </div> 

                <div class="card-body p-4"> 
                    <div class="row g-3"> 
                        <!-- Box 1: รอจดมิเตอร์ -->
                        <div class="col-6 col-lg-3"> 
                            @php $isDisabled = $zone['initTotalCount'] == 0; @endphp 
                            <div class="stat-box {{ $isDisabled ? 'disabled' : '' }}" onclick="{{ !$isDisabled ? "window.location.href='".route('invoice.zone_create', ['zone_id' => $zone['zone_info']['undertake_subzone_id'], 'curr_inv_prd' => $current_inv_period->id])."'" : '' }}"> 
                                <div class="stat-icon bg-flat-warning"> 
                                    <i class="fa fa-pen-alt"></i> 
                                </div> 
                                <div class="stat-value">{{ $zone['initTotalCount'] }}</div> 
                                <div class="stat-label">รอจดมิเตอร์</div> 
                                @if(!$isDisabled) <div class="stat-action-btn text-warning"><i class="fa fa-arrow-right"></i></div> @endif 
                            </div> 
                        </div> 

                        <!-- Box 2: บันทึกแล้ว -->
                        <div class="col-6 col-lg-3"> 
                            @php $isDisabled = $zone['invoiceTotalCount'] == 0; @endphp 
                            <div class="stat-box {{ $isDisabled ? 'disabled' : '' }}" onclick="{{ !$isDisabled ? "window.location.href='".route('invoice.zone_edit', ['subzone_id' => $zone['zone_info']['undertake_subzone_id'], 'curr_inv_prd' => $current_inv_period])."'" : '' }}"> 
                                <div class="stat-icon bg-flat-info"> 
                                    <i class="fa fa-check-double"></i> 
                                </div> 
                                <div class="stat-value">{{ $zone['invoiceTotalCount'] }}</div> 
                                <div class="stat-label">บันทึกแล้ว</div> 
                                @if(!$isDisabled) <div class="stat-action-btn text-info"><i class="fa fa-edit"></i></div> @endif 
                            </div> 
                        </div> 

                        <!-- Box 3: ชำระเงินแล้ว -->
                        <div class="col-6 col-lg-3"> 
                            @php $isDisabled = $zone['paidTotalCount'] == 0; @endphp 
                            <div class="stat-box {{ $isDisabled ? 'disabled' : '' }}" onclick="{{ !$isDisabled ? "window.location.href='".url('payment/paymenthistory/' . $current_inv_period->id . '/' . $zone['zone_info']['undertake_subzone_id'])."' " : '' }}"> 
                                <div class="stat-icon bg-flat-success"> 
                                    <i class="fa fa-file-invoice-dollar"></i> </div> 
                                <div class="stat-value">{{ $zone['paidTotalCount'] }}</div> 
                                <div class="stat-label">ชำระเงินแล้ว</div> 
                                @if(!$isDisabled) <div class="stat-action-btn text-success"><i class="fa fa-eye"></i></div> @endif 
                            </div> 
                        </div> 

                        <!-- Box 4: ไม่มีข้อมูลมิเตอร์ -->
                        <div class="col-6 col-lg-3"> 
                            @php $isDisabled = $zone['user_notyet_inv_info'] == 0; @endphp 
                            <div class="stat-box {{ $isDisabled ? 'disabled' : '' }}" onclick="{{ !$isDisabled ? "window.location.href='".route('invoice.zone_create', ['zone_id' => $zone['zone_info']['undertake_subzone_id'], 'curr_inv_prd' => $current_inv_period->id, 'new_user' => 1])."'" : '' }}"> 
                                <div class="stat-icon bg-flat-danger"> 
                                    <i class="fa fa-user-slash"></i> 
                                </div> 
                                <div class="stat-value">{{ $zone['user_notyet_inv_info'] }}</div> 
                                <div class="stat-label">ไม่มีข้อมูลมิเตอร์</div> 
                                @if(!$isDisabled) <div class="stat-action-btn text-danger"><i class="fa fa-plus"></i></div> @endif 
                            </div> 
                        </div> 
                    </div> 

                    <!-- Action Buttons -->
                    <div class="row mt-3 pt-3 border-top border-light"> 
                        <div class="col-12"> 
                            <div class="d-flex gap-2"> 
                                <a href="{{ route('invoice.export_excel', ['zone_id' => $zone['zone_info']['undertake_subzone_id'], 'curr_inv_prd' => $current_inv_period->id]) }}" class="btn btn-flat btn-sm mb-0"> 
                                    <i class="fa fa-file-excel me-1 text-success"></i> Export Excel 
                                </a> 
                                <a href="{{ route('invoice.print_invoice', ['zone_id' => $zone['zone_info']['undertake_subzone_id'], 'curr_inv_prd' => $current_inv_period->id]) }}" class="btn btn-flat btn-sm mb-0"> 
                                    <i class="fa fa-print me-1 text-primary"></i> พิมพ์ใบแจ้งหนี้ 
                                </a> 
                            </div> 
                        </div> 
                    </div> 
                </div> 

                <!-- Financial Footer (Flat Design) -->
                <div class="financial-footer"> 
                    <h6 class="text-xs font-weight-bold text-uppercase text-muted mb-3" style="letter-spacing: 0.5px;">สรุปยอดเงินประจำรอบบิล</h6> 
                    <div class="row align-items-end g-3"> 
                        <div class="col-md-4"> 
                            <span class="text-xs text-muted d-block mb-1">ยอดที่ต้องชำระทั้งหมด</span> 
                            <h5 class="mb-2 font-weight-bold text-dark">{{ number_format($zone['total_paid'], 2) }} <span class="text-xs text-muted font-weight-normal">บาท</span></h5> 
                            <div class="progress progress-flat"> 
                                <div class="progress-bar bg-secondary" role="progressbar" style="width: 100%"></div> 
                            </div> 
                        </div> 
                        <div class="col-md-4"> 
                            <span class="text-xs text-muted d-block mb-1">เก็บเงินได้แล้ว</span> 
                            <h5 class="mb-2 font-weight-bold" style="color: var(--flat-success);">{{ number_format($zone['paidTotalAmount'], 2) }} <span class="text-xs text-muted font-weight-normal">บาท</span></h5> 
                            <div class="progress progress-flat"> 
                                <div class="progress-bar" role="progressbar" style="background-color: var(--flat-success); width:{{ $zone['total_paid'] == 0 ? 0 : number_format(($zone['paidTotalAmount'] / $zone['total_paid']) * 100, 2) }}%"></div> 
                            </div> 
                        </div> 
                        <div class="col-md-4"> 
                            <span class="text-xs text-muted d-block mb-1">ค้างชำระ</span> 
                            <h5 class="mb-2 font-weight-bold" style="color: var(--flat-warning);">{{ number_format($zone['total_paid'] - $zone['paidTotalAmount'], 2) }} <span class="text-xs text-muted font-weight-normal">บาท</span></h5> 
                            <div class="progress progress-flat"> 
                                <div class="progress-bar" role="progressbar" style="background-color: var(--flat-warning); width:{{ $zone['total_paid'] == 0 ? 0 : number_format((($zone['total_paid'] - $zone['paidTotalAmount']) / $zone['total_paid']) * 100, 2) }}%"></div> 
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
    // Smooth scroll for nav links & active toggling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => { 
        anchor.addEventListener('click', function (e) { 
            e.preventDefault(); 
            document.querySelectorAll('.nav-pills-custom .nav-link').forEach(nav => nav.classList.remove('active')); 
            this.classList.add('active'); 
            document.querySelector(this.getAttribute('href')).scrollIntoView({ behavior: 'smooth', block: 'start' }); 
        }); 
    }); 

    $(document).ready(() => { 
        setTimeout(() => { 
            $('.alert').toggle('slow') 
        }, 2000) 
    }) 
</script> 
@endsection