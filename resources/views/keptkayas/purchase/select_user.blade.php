@if(auth()->user()->can('access waste bank mobile')) 
    @php $layout = 'layouts.keptkaya_mobile'; @endphp 
@else 
    @php $layout = 'layouts.keptkaya'; @endphp 
@endif 

@extends($layout) 

@section('nav-header', 'รับซื้อขยะรีไซเคิล') 
@section('nav-current', 'เลือกสมาชิก') 
@section('page-topic', 'ธุรกรรมรับซื้อ') 

@section('content') 
<style> 
    /* CSS สำหรับ Mobile Layout & Scroll Area */ 
    @media (max-width: 767px) { 
        .mobile-scroll-container { 
            height: calc(100vh - 310px); 
            overflow-y: auto; 
            overflow-x: hidden; 
            padding-bottom: 80px; 
            -webkit-overflow-scrolling: touch; 
        } 
        .mobile-scroll-container::-webkit-scrollbar { 
            width: 4px; 
        } 
        .mobile-scroll-container::-webkit-scrollbar-thumb { 
            background-color: #ccc; 
            border-radius: 4px; 
        } 
        .sticky-search-box { 
            position: sticky; 
            top: 0; 
            z-index: 1020; 
            background-color: #f8f9fa; 
            padding-top: 8px; 
            padding-bottom: 8px; 
        } 
    } 
    .cursor-pointer { cursor: pointer; } 
</style> 

<div class="container-fluid px-0" style="max-width: 800px; margin: 0 auto;"> 

    {{-- [MOBILE ONLY] Header Bar --}} 
    <div class="d-md-none d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom px-2"> 
        <div class="d-flex align-items-center"> 
            <div class="icon icon-shape icon-sm bg-gradient-primary shadow text-center border-radius-md me-2"> 
                <i class="fas fa-users text-white opacity-10" style="font-size: 0.8rem; margin-top: 10px;"></i> 
            </div> 
            <div> 
                <h6 class="mb-0 font-weight-bolder text-dark">เลือกสมาชิก</h6> 
                <p class="text-xxs text-secondary mb-0">รายการรับซื้อขยะ</p> 
            </div> 
        </div> 
        <button class="btn btn-white shadow-sm p-2 mb-0 border" type="button" id="customSideNavToggler"> 
            <i class="fas fa-bars text-dark text-lg"></i> 
        </button> 
    </div> 

    {{-- Alert Messages --}} 
    @if (session('success') || session('error')) 
        <div class="px-2 mb-2"> 
            @if (session('success')) 
                <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 text-sm text-white"><i class="fas fa-check-circle me-1"></i> {{ session('success') }}</div> 
            @endif 
            @if (session('error')) 
                <div class="alert alert-warning border-0 shadow-sm rounded-3 py-2 text-sm text-white"><i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}</div> 
            @endif 
        </div> 
    @endif 

    {{-- SECTION 1: ROUTE CONTEXT BAR & SEARCH TOOLS --}} 
    <div class="sticky-search-box px-2"> 
        
        {{-- Active Route Info Badge --}} 
        <div class="card border-0 shadow-sm mb-2 bg-gradient-dark text-white rounded-3"> 
            <div class="card-body p-2 px-3 d-flex justify-content-between align-items-center"> 
                <div class="d-flex align-items-center overflow-hidden"> 
                    <i class="fas fa-map-marker-alt text-primary me-2 fs-5"></i> 
                    <div class="text-truncate"> 
                        <small class="text-xxs text-uppercase text-white-50 d-block">เขตรับซื้อปัจจุบัน</small> 
                        <span class="text-xs font-weight-bold text-white text-truncate"> 
                            @if(session()->has('purchase_route_id')) 
                                {{ session('purchase_route_name', 'เขตที่เลือกไว้') }} 
                            @else 
                                ทุกเขต / ไม่จำกัดโซน 
                            @endif 
                        </span> 
                    </div> 
                </div> 
                <a href="{{ route('keptkayas.purchase.select_route') }}" class="btn btn-xs btn-outline-white mb-0 rounded-pill px-3 ms-2 text-nowrap"> 
                    <i class="fas fa-sliders-h me-1"></i> เปลี่ยนเขต 
                </a> 
            </div> 
        </div> 

        {{-- Unified Search Box --}} 
        <div class="card border-0 shadow-sm rounded-3"> 
            <div class="card-body p-2"> 
                <form action="{{ route('keptkayas.purchase.select_user') }}" method="GET" id="user-search-form"> 
                    <div class="row g-2"> 
                        <div class="col-12"> 
                            <div class="input-group input-group-outline bg-white"> 
                                <input type="text" name="keyword" id="keyword_input" class="form-control" placeholder="ค้นชื่อ, เบอร์, เลขบัตร หรือ ID..." value="{{ request('keyword') ?? request('name_search') ?? request('username_search') }}"> 
                                
                                {{-- ปุ่ม QR Code --}} 
                                <button class="btn btn-outline-primary mb-0 px-3 z-index-2" type="button" data-bs-toggle="modal" data-bs-target="#qrScannerModal" title="สแกน QR Code"> 
                                    <i class="fas fa-qrcode text-lg"></i> 
                                </button> 
                                
                                {{-- ปุ่ม Search --}} 
                                <button class="btn bg-gradient-primary mb-0 px-3 z-index-2" type="submit"> 
                                    <i class="fas fa-search"></i> 
                                </button> 
                            </div> 
                        </div> 

                        {{-- In-place Override: Checkbox ค้นหาทุกเขต --}} 
                        @if(session()->has('purchase_route_id')) 
                            <div class="col-12 mt-2 px-1"> 
                                <div class="form-check form-switch ps-0 d-flex align-items-center justify-content-between"> 
                                    <label class="form-check-label text-xs text-secondary mb-0 cursor-pointer" for="search_all_zones"> 
                                        <i class="fas fa-globe me-1"></i> ค้นหาสมาชิกนอกเขตรับซื้อนี้ด้วย 
                                    </label> 
                                    <input class="form-check-input ms-auto fs-5 cursor-pointer" type="checkbox" name="search_all_zones" id="search_all_zones" value="1" {{ request('search_all_zones') ? 'checked' : '' }} onchange="document.getElementById('user-search-form').submit();"> 
                                </div> 
                            </div> 
                        @endif 
                    </div> 
                </form> 
            </div> 
        </div> 
    </div> 

    {{-- SECTION 2: DESKTOP TABLE VIEW --}} 
    <div class="card border-0 shadow-sm d-none d-md-block mt-3 mx-2"> 
        <div class="card-header bg-white pb-0 d-flex justify-content-between align-items-center"> 
            <h6 class="font-weight-bolder mb-0">รายชื่อสมาชิกที่พบ ({{ count($keptKayaMembers) }} คน)</h6> 
        </div> 
        <div class="card-body px-0 pt-2 pb-2"> 
            <div class="table-responsive p-0"> 
                <table class="table align-items-center mb-0"> 
                    <thead class="bg-light"> 
                        <tr> 
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 5%">#</th> 
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">สมาชิก</th> 
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">เขต/ที่อยู่</th> 
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ยอดขายวันนี้</th> 
                            <th class="text-secondary opacity-7 text-center">ทำรายการ</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        @forelse ($keptKayaMembers as $index => $member) 
                            @php 
                                $todayTrans = $member->purchaseTransactions; 
                            @endphp 
                            <tr> 
                                <td class="align-middle text-center"> 
                                    <span class="text-secondary text-xs font-weight-bold">{{ $index + 1 }}</span> 
                                </td> 
                                <td> 
                                    <div class="d-flex px-2 py-1 align-items-center"> 
                                        <div class="avatar avatar-sm bg-gradient-secondary rounded-circle me-2 text-white d-flex align-items-center justify-content-center"> 
                                            <span class="text-xs font-weight-bold">{{ mb_substr($member->firstname, 0, 1) }}</span> 
                                        </div> 
                                        <div class="d-flex flex-column justify-content-center"> 
                                            <h6 class="mb-0 text-sm font-weight-bold">{{ $member->firstname }} {{ $member->lastname }}</h6> 
                                            <p class="text-xs text-secondary mb-0">ID: {{ $member->username }} @if($member->phone) | {{ $member->phone }} @endif</p> 
                                        </div> 
                                    </div> 
                                </td> 
                                <td> 
                                    <p class="text-xs font-weight-bold mb-0 text-dark"> 
                                        {{ $member->user_zone->zone_name ?? '-' }} 
                                    </p> 
                                    <p class="text-xxs text-secondary mb-0 text-truncate" style="max-width: 200px;"> 
                                        {{ $member->address ?? '-' }} 
                                    </p> 
                                </td> 
                                <td class="align-middle"> 
                                    @if ($todayTrans->count() > 0) 
                                        <div class="d-flex align-items-center"> 
                                            <span class="badge badge-sm bg-gradient-success me-2"> 
                                                {{ number_format($todayTrans->sum('total_amount'), 2) }} ฿ 
                                            </span> 
                                            <a href="{{ route('keptkayas.purchase.receipt', $todayTrans->first()->id) }}" class="text-xs font-weight-bold text-primary" title="ดูใบเสร็จ"> 
                                                <i class="fas fa-receipt"></i> 
                                            </a> 
                                        </div> 
                                    @else 
                                        <span class="text-secondary text-xs">-</span> 
                                    @endif 
                                </td> 
                                <td class="align-middle text-center"> 
                                    <a href="{{ route('keptkayas.purchase.history', $member->id) }}" class="btn btn-link text-secondary mb-0 px-2" title="ประวัติ"> 
                                        <i class="fas fa-history text-lg"></i> 
                                    </a> 
                                    <a href="{{ route('keptkayas.purchase.start_purchase', $member->id) }}" class="btn btn-sm bg-gradient-primary mb-0 ms-1 px-3"> 
                                        <i class="fas fa-cart-plus me-1"></i> รับซื้อ 
                                    </a> 
                                </td> 
                            </tr> 
                        @empty 
                            <tr> 
                                <td colspan="5" class="text-center p-4"> 
                                    <i class="fas fa-user-slash text-secondary text-2xl mb-2 opacity-4"></i> 
                                    <p class="text-sm text-secondary mb-0">ไม่พบสมาชิกตามเงื่อนไขการค้นหา</p> 
                                </td> 
                            </tr> 
                        @endforelse 
                    </tbody> 
                </table> 
            </div> 
        </div> 
    </div> 

    {{-- SECTION 3: MOBILE LIST VIEW --}} 
    <div class="d-md-none mobile-scroll-container px-2 mt-1"> 
        <div class="row g-2"> 
            @forelse ($keptKayaMembers as $member) 
                @php 
                    $todayTrans = $member->purchaseTransactions ; 
                @endphp 
                <div class="col-12"> 
                    <div class="card shadow-sm border mb-1 rounded-3"> 
                        <div class="card-body p-3"> 
                            <div class="d-flex justify-content-between align-items-center"> 
                                {{-- Left Info --}} 
                                <div class="d-flex align-items-center" style="max-width: 68%;"> 
                                    <div class="avatar avatar-sm bg-gradient-primary rounded-circle me-2 text-white d-flex align-items-center justify-content-center shadow-sm"> 
                                        <span class="text-xs font-weight-bold">{{ mb_substr($member->firstname, 0, 1) }}</span> 
                                    </div> 
                                    <div class="overflow-hidden"> 
                                        <h6 class="text-sm font-weight-bold mb-0 text-truncate text-dark"> 
                                            {{ $member->firstname }} {{ $member->lastname }} 
                                        </h6> 
                                        <p class="text-xxs text-secondary mb-0 text-truncate"> 
                                            {{ $member->username }} @if($member->user_zone) | {{ $member->user_zone->zone_name }} @endif 
                                        </p> 
                                    </div> 
                                </div> 
                                {{-- Right Action --}} 
                                <div> 
                                    <a href="{{ route('keptkayas.purchase.start_purchase', $member->id) }}" class="btn btn-sm bg-gradient-primary mb-0 shadow-primary px-3 rounded-pill"> 
                                        <i class="fas fa-cart-plus me-1"></i> รับซื้อ 
                                    </a> 
                                </div> 
                            </div> 

                            {{-- Show Status if Transacted Today --}} 
                            @if ($todayTrans->count() > 0) 
                                <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center"> 
                                    <span class="text-xxs text-success font-weight-bold"> 
                                        <i class="fas fa-check-circle me-1"></i> วันนี้: {{ number_format($todayTrans->sum('total_amount'), 2) }} ฿ 
                                    </span> 
                                    <a href="{{ route('keptkayas.purchase.history', $member->id) }}" class="text-xxs text-secondary"> 
                                        ดูประวัติ <i class="fas fa-chevron-right ms-1"></i> 
                                    </a> 
                                </div> 
                            @endif 
                        </div> 
                    </div> 
                </div> 
            @empty 
                <div class="col-12 text-center py-5"> 
                    <i class="fas fa-user-slash text-secondary text-4xl mb-3 opacity-3"></i> 
                    <p class="text-sm text-secondary mb-0">ไม่พบข้อมูลสมาชิกในเขตนี้</p> 
                    @if(session()->has('purchase_route_id') && !request('search_all_zones')) 
                        <small class="text-xxs text-muted mt-1 d-block">ลองเปิดสวิตช์ "ค้นหาสมาชิกนอกเขตรับซื้อนี้ด้วย" ด้านบน</small> 
                    @endif 
                </div> 
            @endforelse 
        </div> 
    </div> 

    {{-- SECTION 4: QR SCANNER MODAL --}} 
    <div class="modal fade" id="qrScannerModal" tabindex="-1" aria-labelledby="qrScannerModalLabel" aria-hidden="true"> 
        <div class="modal-dialog modal-dialog-centered"> 
            <div class="modal-content rounded-4 border-0"> 
                <div class="modal-header border-bottom-0 pb-0"> 
                    <h6 class="modal-title font-weight-bold" id="qrScannerModalLabel"> 
                        <i class="fas fa-qrcode text-primary me-2"></i>สแกน QR Code สมาชิก 
                    </h6> 
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button> 
                </div> 
                <div class="modal-body text-center p-3"> 
                    <div class="overflow-hidden rounded-3 bg-dark position-relative" style="min-height: 260px;"> 
                        <div id="qr-reader" style="width: 100%;"></div> 
                    </div> 
                    <p class="text-xs text-secondary mt-3 mb-0">นำกล้องส่องไปที่ QR Code ของสมาชิก (สแกนได้ทั้งบัตรและถุงขยะ)</p> 
                </div> 
            </div> 
        </div> 
    </div> 

</div> 
@endsection 

@section('script') 
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script> 
<script> 
    document.addEventListener('DOMContentLoaded', function () { 
        const qrScannerModal = document.getElementById('qrScannerModal'); 
        const keywordInput = document.getElementById('keyword_input'); 
        const userSearchForm = document.getElementById('user-search-form'); 
        let html5QrCode = null; 

        if (qrScannerModal) { 
            qrScannerModal.addEventListener('shown.bs.modal', () => { 
                if (!html5QrCode) { 
                    html5QrCode = new Html5Qrcode("qr-reader"); 
                } 
                const config = { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 }; 
                
                html5QrCode.start( 
                    { facingMode: "environment" }, 
                    config, 
                    (decodedText) => { 
                        let code = decodedText.trim(); 
                        if(code.includes("-")) { 
                            code = code.split("-")[1]; 
                        } 
                        
                        if(keywordInput) keywordInput.value = code; 
                        
                        html5QrCode.stop().then(() => { 
                            bootstrap.Modal.getInstance(qrScannerModal).hide(); 
                            userSearchForm.submit(); 
                        }); 
                    }, 
                    (errorMessage) => {} 
                ).catch(err => { 
                    console.error(err); 
                    alert("ไม่สามารถเปิดกล้องได้ กรุณาตรวจสอบสิทธิ์การใช้งานกล้อง"); 
                    bootstrap.Modal.getInstance(qrScannerModal).hide(); 
                }); 
            }); 

            qrScannerModal.addEventListener('hidden.bs.modal', () => { 
                if (html5QrCode && html5QrCode.isScanning) { 
                    html5QrCode.stop().then(() => html5QrCode.clear()); 
                } 
            }); 
        } 

        // --- SideNav Toggler for Mobile --- 
        const customToggler = document.getElementById('customSideNavToggler'); 
        const body = document.getElementsByTagName('body')[0]; 
        const className = 'g-sidenav-pinned'; 

        if (customToggler) { 
            customToggler.addEventListener('click', function (e) { 
                e.stopPropagation(); 
                if (body.classList.contains(className)) { 
                    body.classList.remove(className); 
                } else { 
                    body.classList.add(className); 
                } 
            }); 
        } 
    }); 
</script> 
@endsection