@if($user->can('access waste bank mobile'))
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
    /* CSS สำหรับ Mobile Scroll Area */
    @media (max-width: 767px) {
        .mobile-scroll-container {
            /* ความสูง = ความสูงหน้าจอ - (Header + Search Bar + Padding) */
            height: calc(100vh - 280px); 
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 80px; /* เผื่อที่ด้านล่าง */
            -webkit-overflow-scrolling: touch; /* ให้เลื่อนลื่นๆ บน iOS */
        }
        
        /* ซ่อน Scrollbar เดิมแต่ยังเลื่อนได้ (Optional) */
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
            background-color: #f8f9fa; /* สีพื้นหลังเดียวกับ Body เพื่อบังเนื้อหาตอนเลื่อน */
            padding-top: 10px;
            padding-bottom: 10px;
        }
    }
</style>

    {{-- [MOBILE ONLY] Header Bar --}}
    <div class="d-md-none d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
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

    {{-- SECTION 1: SEARCH TOOLS (Sticky on Mobile) --}}
    <div class="sticky-search-box">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <form action="{{ route('keptkayas.purchase.select_user') }}" method="GET" id="user-search-form">
                    <div class="row g-2">
                        {{-- รวมช่องค้นหาให้ประหยัดที่บนมือถือ --}}
                        <div class="col-12">
                            <div class="input-group input-group-outline bg-white">
                                <input type="text" name="keyword" class="form-control" 
                                    placeholder="ชื่อ, สกุล หรือ รหัสสมาชิก..." 
                                    value="{{ request('keyword') ?? request('name_search') ?? request('username_search') }}">
                                
                                {{-- ปุ่ม QR Code --}}
                                <button class="btn btn-outline-primary mb-0 px-3 z-index-2" type="button" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
                                    <i class="fas fa-qrcode text-lg"></i>
                                </button>
                                
                                {{-- ปุ่ม Search --}}
                                <button class="btn bg-gradient-primary mb-0 px-3 z-index-2" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    {{-- Hidden inputs เพื่อรองรับ Logic เดิมถ้าจำเป็น --}}
                    @if(request('username_search'))
                        <input type="hidden" name="username_search" id="username_search_hidden" value="{{ request('username_search') }}">
                    @else
                        <input type="hidden" name="username_search" id="username_search_hidden">
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- SECTION 2: DESKTOP TABLE VIEW (แสดงเฉพาะจอใหญ่) --}}
    <div class="card border-0 shadow-sm d-none d-md-block mt-4">
        {{-- ... (Code ส่วน Desktop เหมือนเดิม ไม่ต้องแก้) ... --}}
        <div class="card-header bg-white pb-0">
            <h6 class="font-weight-bolder mb-0">รายชื่อสมาชิก</h6>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width: 5%">Ref.</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">สมาชิก</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ที่อยู่</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ธุรกรรมวันนี้</th>
                            <th class="text-secondary opacity-7 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($keptKayaMembers as $member)
                            <tr>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">{{ $member->wastePreference->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $member->firstname }} {{ $member->lastname }}</h6>
                                            <p class="text-xs text-secondary mb-0">ID: {{ $member->username }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0 text-secondary text-truncate" style="max-width: 250px;">
                                        {{ $member->address ?? '-' }}
                                    </p>
                                </td>
                                <td class="align-middle">
                                    @if ($member->wastePreference->purchaseTransactions->count() > 0)
                                        @php $todayTransactions = $member->wastePreference->purchaseTransactions; @endphp
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-sm bg-gradient-success me-2">
                                                {{ number_format($todayTransactions->sum('total_amount'), 2) }} ฿
                                            </span>
                                            <a href="{{ route('keptkayas.purchase.receipt', $todayTransactions[0]->id) }}" 
                                               class="text-xs font-weight-bold text-primary" title="ดูใบเสร็จ">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-secondary text-xs">-</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <a href="{{ route('keptkayas.purchase.history', $member->wastePreference->id) }}" 
                                       class="btn btn-link text-secondary mb-0 px-2" title="ประวัติ">
                                        <i class="fas fa-history text-lg"></i>
                                    </a>
                                    <a href="{{ route('keptkayas.purchase.start_purchase', $member->wastePreference->id) }}" 
                                       class="btn btn-sm bg-gradient-primary mb-0 ms-2 px-3">
                                        <i class="fas fa-cart-plus me-1"></i> รับซื้อ
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-4">
                                    <p class="text-sm text-secondary mb-0">ไม่พบข้อมูลสมาชิก</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             {{-- Pagination Desktop --}}
            @if(method_exists($keptKayaMembers, 'links'))
                <div class="d-flex justify-content-center p-3">
                    {{ $keptKayaMembers->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    {{-- SECTION 3: MOBILE LIST VIEW (Scrollable Area) --}}
    <div class="d-md-none mobile-scroll-container mt-2">
        <div class="row g-2">
            @forelse ($keptKayaMembers as $member)
                <div class="col-12">
                    {{-- Compact Card Layout --}}
                    <div class="card shadow-sm border mb-1">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                {{-- Left: Info --}}
                                <div class="d-flex align-items-center" style="max-width: 65%;">
                                    <div class="avatar avatar-sm bg-gradient-secondary rounded-circle me-2 text-white d-flex align-items-center justify-content-center">
                                        <span class="text-xs font-weight-bold">{{ substr($member->firstname, 0, 1) }}</span>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="text-sm font-weight-bold mb-0 text-truncate">
                                            {{ $member->firstname }} {{ $member->lastname }}
                                        </h6>
                                        <p class="text-xs text-secondary mb-0 text-truncate">
                                            {{ $member->username }} 
                                            @if($member->address) | {{ Str::limit($member->address, 15) }} @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- Right: Action --}}
                                <div>
                                    <a href="{{ route('keptkayas.purchase.start_purchase', $member->wastePreference->id) }}" 
                                       class="btn btn-sm bg-gradient-primary mb-0 shadow-primary px-3">
                                       รับซื้อ
                                    </a>
                                </div>
                            </div>

                            {{-- Optional: Show Status if Transacted --}}
                            @if ($member->wastePreference->purchaseTransactions->count() > 0)
                            <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                                <span class="text-xxs text-success font-weight-bold">
                                    <i class="fas fa-check-circle"></i> วันนี้: {{ number_format($member->wastePreference->purchaseTransactions->sum('total_amount'), 2) }} ฿
                                </span>
                                <a href="{{ route('keptkayas.purchase.history', $member->wastePreference->id) }}" class="text-xxs text-secondary">
                                    ดูประวัติ <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-user-slash text-secondary text-4xl mb-3 opacity-3"></i>
                    <p class="text-sm text-secondary">ไม่พบสมาชิก</p>
                </div>
            @endforelse

            {{-- Mobile Pagination (อยู่ล่างสุดของ list) --}}
            @if(method_exists($keptKayaMembers, 'links'))
                <div class="col-12 mt-2 text-center">
                    {{-- ใช้ simple pagination บนมือถือจะสวยกว่า (ถ้าระบบรองรับ) --}}
                    {{ $keptKayaMembers->withQueryString()->onEachSide(0)->links('pagination::simple-bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    {{-- QR Scanner Modal (Code เดิม) --}}
    <div class="modal fade" id="qrScannerModal" tabindex="-1" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <h6 class="modal-title font-weight-bold" id="qrScannerModalLabel">สแกน QR Code</h6>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center p-3">
                    <div class="overflow-hidden rounded-3 bg-dark position-relative" style="min-height: 250px;">
                        <div id="qr-reader" style="width: 100%;"></div>
                    </div>
                    <p class="text-xs text-secondary mt-3 mb-0">นำกล้องส่องไปที่ QR Code ของสมาชิก</p>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // --- Logic เดิมของ QR Scanner ---
            const qrScannerModal = document.getElementById('qrScannerModal');
            // เนื่องจากเรารวม input search เป็นอันเดียว ให้แก้ ID ตรงนี้ให้ match
            const searchInput = document.querySelector('input[name="keyword"]'); 
            const hiddenUsernameInput = document.getElementById('username_search_hidden');
            const userSearchForm = document.getElementById('user-search-form');
            let html5QrCode = null;

            if (qrScannerModal) {
                qrScannerModal.addEventListener('shown.bs.modal', () => {
                    if (!html5QrCode) {
                        html5QrCode = new Html5Qrcode("qr-reader");
                    }
                    const config = { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 };
                    html5QrCode.start({ facingMode: "environment" }, config,
                        (decodedText, decodedResult) => {
                            let code = decodedText.trim();
                            if(code.includes("-")) { code = code.split("-")[0]; }
                            
                            // ใส่ค่าลงในช่องค้นหาหลัก และ hidden field
                            if(searchInput) searchInput.value = code;
                            if(hiddenUsernameInput) hiddenUsernameInput.value = code;

                            html5QrCode.stop().then(() => {
                                bootstrap.Modal.getInstance(qrScannerModal).hide();
                                userSearchForm.submit();
                            });
                        },
                        (errorMessage) => {}
                    ).catch(err => {
                        console.error(err);
                        alert("ไม่สามารถเปิดกล้องได้");
                        bootstrap.Modal.getInstance(qrScannerModal).hide();
                    });
                });
                qrScannerModal.addEventListener('hidden.bs.modal', () => {
                    if (html5QrCode && html5QrCode.isScanning) {
                        html5QrCode.stop().then(() => html5QrCode.clear());
                    }
                });
            }

            // --- Logic เดิมของ Menu Toggler ---
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
            document.addEventListener('click', function(event) {
                const sidenav = document.getElementById('sidenav-main');
                if (body.classList.contains(className) && sidenav && !sidenav.contains(event.target) && !customToggler.contains(event.target)) {
                    body.classList.remove(className);
                }
            });
        });
    </script>
@endsection