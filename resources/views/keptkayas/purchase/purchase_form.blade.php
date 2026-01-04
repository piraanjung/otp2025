@if($user->can('access waste bank mobile'))
    @php
        $layout = 'layouts.keptkaya_mobile';
    @endphp
@else
    @php
        $layout = 'layouts.keptkaya';
     @endphp
@endif

@extends($layout)
@section('nav-header', 'รับซื้อขยะรีไซเคิล')
@section('nav-current', 'รับซื้อขยะรีไซเคิล')
@section('page-topic', 'รับซื้อขยะรีไซเคิล')

@section('content')
<div class="container-fluid px-0" style="max-width: 600px; margin: 0 auto;">

    {{-- Header แบบ Clean --}}
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2 px-2">
        <h4 class="fw-bold mb-0 text-dark">รับซื้อขยะ</h4>
        <span class="badge bg-light text-secondary rounded-pill border px-3 py-2">
            <i class="fa fa-user me-1"></i> {{ $user->firstname }}
        </span>
    </div>

    {{-- Alert Messages (ปรับให้ดูนุ่มขึ้น) --}}
    @if (session('success') || session('error') || $errors->any())
        <div class="px-2 mb-3">
            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-3 py-2"><i class="fa fa-check-circle me-1"></i> {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2"><i class="fa fa-exclamation-circle me-1"></i> {{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2">
                    <ul class="mb-0 ps-3 small">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                </div>
            @endif
        </div>
    @endif

    {{-- Main Form Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-body p-3">
            <form action="{{ route('keptkayas.purchase.add_to_cart') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">

                {{-- 1. Filter & QR Code Zone --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="fw-bold text-dark mb-0">หมวดหมู่ขยะ</label>
                    <button type="button" class="btn btn-sm btn-light text-primary rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
                        <i class="fa fa-qrcode me-1"></i> สแกน QR
                    </button>
                </div>
                
                {{-- Scrollable Filters --}}
                <div class="d-flex overflow-auto pb-3 hide-scrollbar" style="gap: 8px;">
                    <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 filter-btn" data-group="paper">กระดาษ</button>
                    <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 filter-btn" data-group="glass">แก้ว/ขวด</button>
                    <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 filter-btn" data-group="plastic">พลาสติก</button>
                    <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 filter-btn" data-group="metal">โลหะ</button>
                    <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 filter-btn" data-group="other">อื่นๆ</button>
                </div>

                {{-- 2. Item Grid Zone --}}
                <div class="bg-light rounded-4 p-3 mb-3" style="min-height: 200px;">
                    <div id="item-buttons-container" class="row g-2" style="max-height: 280px; overflow-y: auto;">
                        <div class="col-12 text-center text-muted py-5">
                            <i class="fa fa-hand-pointer-o fa-2x mb-2 opacity-50"></i><br>
                            <small>เลือกหมวดหมู่ด้านบนเพื่อเริ่มรายการ</small>
                        </div>
                    </div>
                </div>

                {{-- 3. Selected Item Display (แสดงเมื่อเลือก) --}}
                <div id="selected-item-display" class="alert alert-primary border-0 rounded-3 d-flex justify-content-between align-items-center mb-3 shadow-sm" style="display: none !important;">
                    <div>
                        <small class="text-primary-emphasis d-block">กำลังเลือก:</small>
                        <span id="selected-item-name" class="fw-bold fs-5 text-dark">...</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm" style="width: 30px; height: 30px;" onclick="resetSelection()">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                {{-- Hidden Inputs --}}
                <div class="d-none">
                    <select id="kp_itemscode" name="kp_itemscode" class="form-select recyclename" required>
                        <option value="">ชื่อขยะ</option>
                        @foreach ($recycleItems as $item)
                            @php
                                $name = $item->kp_itemsname;
                                $group = 'other';
                                if (strpos($name, 'กระดาษ') !== false || strpos($name, 'กล่อง') !== false || strpos($name, 'สมุด') !== false || strpos($name, 'หนังสือ') !== false) $group = 'paper';
                                elseif (strpos($name, 'แก้ว') !== false || strpos($name, 'ขวด') !== false) $group = 'glass';
                                elseif (strpos($name, 'พลาสติก') !== false || strpos($name, 'PET') !== false || strpos($name, 'PE') !== false || strpos($name, 'PVC') !== false) $group = 'plastic';
                                elseif (strpos($name, 'เหล็ก') !== false || strpos($name, 'อลูมิเนียม') !== false || strpos($name, 'ทองแดง') !== false || strpos($name, 'สังกะสี') !== false || strpos($name, 'ตะกั่ว') !== false || strpos($name, 'สแตนเลส') !== false || strpos($name, 'กระป๋อง') !== false) $group = 'metal';
                            @endphp
                            <option value="{{ $item->kp_itemscode }}" data-id="{{ $item->id }}" data-group="{{ $group }}" data-name="{{ $item->kp_itemsname }}">
                                {{ $item->kp_itemsname }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="kp_tbank_item_id" id="kp_tbank_item_id">
                </div>

                {{-- 4. Input & Action Zone (Grouped) --}}
                <div class="p-3 bg-white border rounded-4">
                    <div class="row g-2">
                        <div class="col-7">
                            <label class="small text-muted mb-1">จำนวน</label>
                            <input type="number" step="0.01" name="amount_in_units" id="amount_in_units" class="form-control form-control-lg bg-light border-0 fw-bold text-center" placeholder="0.00" required min="0.01">
                        </div>
                        <div class="col-5">
                            <label class="small text-muted mb-1">หน่วย</label>
                            <select id="kp_units_idfk" name="kp_units_idfk" class="form-select form-select-lg bg-light border-0" required>
                                <option value="">หน่วย</option>
                                @foreach ($allUnits as $unit)
                                    <option value="{{ $unit->id }}" {{ $unit->id == 1 ? 'selected' : '' }}>{{ $unit->unitname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-success w-100 py-3 rounded-3 shadow fw-bold">
                                <i class="fa fa-plus-circle me-1"></i> เพิ่มรายการ
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Floating Cart Button --}}
{{-- Cart Modal (ฉบับแก้ไข: ปุ่มลบชัดเจน) --}}
    @if(Session::has('purchase_cart') && count(Session::get('purchase_cart')) > 0)
        <button type="button" 
                class="btn btn-dark position-fixed rounded-circle shadow-lg d-flex justify-content-center align-items-center"
                style="bottom: 30px; right: 30px; width: 65px; height: 65px; z-index: 1000; border: 2px solid white;"
                data-bs-toggle="modal" 
                data-bs-target="#cartModal">
            <div class="position-relative">
                <i class="fa fa-shopping-basket fa-lg"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
                    {{ count(Session::get('purchase_cart')) }}
                </span>
            </div>
        </button>

        <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0">
                    <div class="modal-header border-0 pb-2">
                        <h5 class="modal-title fw-bold">
                            <i class="fa fa-shopping-cart text-success me-2"></i>รายการในตะกร้า
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light text-secondary small">
                                    <tr>
                                        <th class="ps-4">สินค้า</th>
                                        <th class="text-end">จำนวน</th>
                                        <th class="text-end">รวม (บาท)</th>
                                        <th class="text-center" style="width: 60px;">ลบ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grandTotal = 0; @endphp
                                    @foreach (Session::get('purchase_cart') as $index => $item)
                                        @php $grandTotal += $item['amount']; @endphp
                                        <tr class="border-bottom border-light">
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">{{ $item['item_name'] }}</div>
                                                <small class="text-muted">{{ number_format($item['price_per_unit'], 2) }} บ./หน่วย</small>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-light text-dark border">
                                                    {{ number_format($item['amount_in_units'], 2) }} {{ $item['unit_name'] }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold text-success fs-5">
                                                {{ number_format($item['amount'], 2) }}
                                            </td>
                                            <td class="text-center">
                                                {{-- ปุ่มลบที่ทำใหม่ --}}
                                                <form action="{{ route('keptkayas.purchase.remove_from_cart', $index) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm border-0 bg-white" style="width: 35px; height: 35px; border-radius: 50%;">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 justify-content-between p-3">
                        <div class="d-flex align-items-center">
                            <span class="text-muted me-2">ยอดสุทธิ:</span>
                            <span class="fw-bold text-dark fs-4">{{ number_format($grandTotal, 2) }} บ.</span>
                        </div>
                        <a href="{{ route('keptkayas.purchase.cart') }}" class="btn btn-dark rounded-pill px-4 shadow">
                            ยืนยัน <i class="fa fa-chevron-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

{{-- QR Scanner Modal --}}
<div class="modal fade" id="qrScannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">สแกนขยะ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0 pb-3">
                <div id="qr-reader" class="rounded-3 overflow-hidden mx-3"></div>
                <p class="text-muted small mt-2">ส่องกล้องไปที่ QR Code ของขยะ</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS เพิ่มเติมเพื่อให้ดูสะอาดตา */
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .form-control:focus, .form-select:focus { box-shadow: none; border: 1px solid #198754; }
    /* Item Card Styling */
    .item-card { transition: all 0.2s; border: none !important; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .item-card:active { transform: scale(0.95); }
</style>
@endsection

@section('script')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let allOptions = []; 

    $(document).ready(function(){
        // 1. เก็บ Data
        $('#kp_itemscode option').each(function(){
            if($(this).val() !== ""){
                allOptions.push({
                    val: $(this).val(),
                    text: $(this).text(),
                    id: $(this).data('id'),
                    group: $(this).data('group'),
                    name: $(this).data('name')
                });
            }
        });

        // 2. Filter Button Logic
        $('.filter-btn').on('click', function() {
            const group = $(this).data('group');
            const $container = $('#item-buttons-container');

            // Style ปุ่ม Filter (ทำให้ดูเหมือน App สมัยใหม่: Active = สีเข้ม, Inactive = ขอบบาง)
            $('.filter-btn').removeClass('btn-dark active').addClass('btn-outline-dark');
            $(this).removeClass('btn-outline-dark').addClass('btn-dark active');

            $container.empty();

            let found = false;
            allOptions.forEach(opt => {
                if (group === 'all' || opt.group === group) {
                    found = true;
                    // [DESIGN] แก้ HTML ปุ่มตรงนี้ให้เป็น Card ขาวสะอาด ลบ Border ออก
                    let btnHtml = `
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card h-100 item-card rounded-3" 
                                 onclick="selectItem('${opt.val}')" 
                                 id="card-${opt.val}"
                                 style="cursor: pointer;">
                                <div class="card-body p-2 text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 80px;">
                                    <span class="fw-bold text-dark item-text" style="font-size: 0.9rem;">${opt.name}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    $container.append(btnHtml);
                }
            });

            if(!found) {
                $container.html('<div class="col-12 text-center text-muted py-4"><i class="fa fa-inbox fa-2x mb-2 opacity-25"></i><br>ไม่พบรายการ</div>');
            }
        });

        $('#kp_itemscode').on('change', function(){
            let itemId = $(this).find(':selected').data('id');
            if(itemId) loadUnitsForSelectedItem(itemId);
        });
    });

    window.selectItem = function(val) {
        // Reset Style
        $('.item-card').removeClass('bg-success text-white ring-2 ring-success').addClass('bg-white');
        $('.item-card .item-text').removeClass('text-white').addClass('text-dark');
        
        // Active Style (สีเขียว สวยๆ)
        $(`#card-${val}`).removeClass('bg-white').addClass('bg-success text-white shadow');
        $(`#card-${val} .item-text`).removeClass('text-dark').addClass('text-white');

        $('#kp_itemscode').val(val).trigger('change');

        // Show Display Section
        const selectedOpt = allOptions.find(o => o.val === val);
        if(selectedOpt) {
            $('#selected-item-name').text(selectedOpt.name);
            $('#selected-item-display').fadeIn();
            $('#kp_tbank_item_id').val(selectedOpt.id);
            // Scroll ไปหาช่องกรอกจำนวนแบบนุ่มๆ
            $('html, body').animate({
                scrollTop: $("#amount_in_units").offset().top - 150
            }, 500);
            $('#amount_in_units').focus(); 
        }
    };

    window.resetSelection = function() {
        $('#kp_itemscode').val('').trigger('change');
        $('#selected-item-display').hide();
        $('.item-card').removeClass('bg-success text-white shadow').addClass('bg-white');
        $('.item-card .item-text').addClass('text-dark');
        $('#kp_tbank_item_id').val('');
    }

    function loadUnitsForSelectedItem(itemId) {
        const $kpUnitsSelect = $('#kp_units_idfk');
        $kpUnitsSelect.prop('disabled', true);
        
        // [UX] เปลี่ยน Text ระหว่างรอ
        const originalText = $kpUnitsSelect.find('option:first').text();
        $kpUnitsSelect.find('option:first').text('กำลังโหลด...');

        $.ajax({
            url: '{{ route('keptkayas.purchase.get_units', ['itemId' => 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', itemId),
            method: 'GET',
            success: function(response) {
                $kpUnitsSelect.empty().append('<option value="">หน่วย</option>');
                if (response.length > 0) {
                    response.forEach(function(unit) {
                        const newOption = new Option(unit.unit_name, unit.unit_id, false, false);
                        $kpUnitsSelect.append(newOption);
                    });
                    $kpUnitsSelect.find('option:eq(1)').prop('selected', true);
                }
                $kpUnitsSelect.prop('disabled', false);
            },
            error: function() {
                $kpUnitsSelect.find('option:first').text('โหลดไม่ได้');
                $kpUnitsSelect.prop('disabled', false);
            }
        });
    }

    // QR Code Scanner (เหมือนเดิม)
    document.addEventListener('DOMContentLoaded', function () {
        const qrScannerModal = document.getElementById('qrScannerModal');
        const html5QrCode = new Html5Qrcode("qr-reader");

        qrScannerModal.addEventListener('shown.bs.modal', () => {
            html5QrCode.start({ facingMode: "environment" }, 
            { fps: 10, qrbox: { width: 250, height: 250 } },
            (decodedText) => {
                html5QrCode.stop().then(() => {
                    bootstrap.Modal.getInstance(qrScannerModal).hide();
                    selectItem(decodedText.toString().toUpperCase());
                }).catch(err => console.error(err));
            },
            (errorMessage) => {}).catch(err => alert("ไม่สามารถเปิดกล้องได้"));
        });

        qrScannerModal.addEventListener('hidden.bs.modal', () => {
            if (html5QrCode.isScanning) html5QrCode.stop();
        });
    });
</script>
@endsection