@if($user->can('access waste bank mobile')) 
    @php $layout = 'layouts.keptkaya_mobile'; @endphp 
@else 
    @php $layout = 'layouts.keptkaya'; @endphp 
@endif 

@extends($layout) 

@section('nav-header', 'รับซื้อขยะรีไซเคิล') 
@section('nav-current', 'รับซื้อขยะรีไซเคิล') 
@section('page-topic', 'รับซื้อขยะรีไซเคิล') 

@section('content') 
<div class="container-fluid px-0" style="max-width: 650px; margin: 0 auto;"> 

    {{-- Header Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-2 mt-2 px-2"> 
        <div>
            <span class="text-xxs text-muted text-uppercase d-block">ทำรายการให้</span>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-user-circle text-primary me-1"></i> {{ $user->firstname }} {{$user->lastname }}
            </h5> 
        </div>
        <a href="{{ route('keptkayas.purchase.select_user') }}" class="btn btn-xs btn-outline-secondary rounded-pill px-3">
            <i class="fas fa-exchange-alt me-1"></i> เปลี่ยนคน
        </a>
    </div> 

    {{-- Alert Messages --}} 
    @if (session('success') || session('error') || $errors->any()) 
        <div class="px-2 mb-2"> 
            @if (session('success')) 
                <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 text-sm text-white"><i class="fas fa-check-circle me-1"></i> {{ session('success') }}</div> 
            @endif 
            @if (session('error')) 
                <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 text-sm text-white"><i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}</div> 
            @endif 
            @if ($errors->any()) 
                <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 text-sm text-white"> 
                    <ul class="mb-0 ps-3 small">@foreach ($errors->all() as $error) <li>{{$error }}</li> @endforeach</ul> 
                </div> 
            @endif 
        </div> 
    @endif 

    {{-- Main Working Form Card --}} 
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 mx-2"> 
        <div class="card-body p-3"> 
            <form action="{{ route('keptkayas.purchase.add_to_cart') }}" method="POST" id="add-to-cart-form"> 
                @csrf 
                <input type="hidden" name="user_id" value="{{ $user->id }}"> 
                <input type="hidden" name="kp_tbank_item_id" id="kp_tbank_item_id">

                {{-- 1. Filter Category & QR Scan --}} 
                <div class="d-flex justify-content-between align-items-center mb-2"> 
                    <label class="fw-bold text-dark text-xs mb-0"><i class="fas fa-tags text-primary me-1"></i>เลือกหมวดหมู่ขยะ</label> 
                    <button type="button" class="btn btn-xs bg-gradient-primary text-white rounded-pill px-3 mb-0" data-bs-toggle="modal" data-bs-target="#qrScannerModal"> 
                        <i class="fas fa-qrcode me-1"></i> สแกนขยะ 
                    </button> 
                </div> 

                {{-- Horizontal Scroll Category Filter --}} 
                <div class="d-flex overflow-auto pb-2 pt-1 hide-scrollbar ps-1 mb-2" style="gap: 12px;"> 
                    <div class="d-flex flex-column align-items-center filter-item cursor-pointer" onclick="filterItemsByGroup('all', this)"> 
                        <button type="button" class="btn btn-dark text-white rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm filter-icon-btn mb-1" style="width: 48px; height: 48px;"> 
                            <i class="fas fa-border-all fs-6"></i> 
                        </button> 
                        <small class="text-dark fw-bold text-xxs">ทั้งหมด</small> 
                    </div>
                    @foreach ($itemsGroups as $group) 
                        <div class="d-flex flex-column align-items-center filter-item cursor-pointer" onclick="filterItemsByGroup('{{ $group->id }}', this)"> 
                            <button type="button" class="btn btn-outline-dark text-dark rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm filter-icon-btn mb-1" style="width: 48px; height: 48px;"> 
                                <i class="fas fa-recycle fs-6"></i> 
                            </button> 
                            <small class="text-muted fw-bold text-xxs text-nowrap">{{ $group->kp_items_groupname }}</small> 
                        </div> 
                    @endforeach 
                </div> 

                {{-- 2. Item Grid Selection Zone --}} 
                <div class="bg-light rounded-3 p-2 mb-3" style="min-height: 110px;"> 
                    <div id="item-buttons-container" class="row g-2" style="max-height: 160px; overflow-y: auto;"> 
                        {{-- JavaScript จะเป็นคนเรนเดอร์การ์ดขยะตรงนี้ --}}
                    </div> 
                </div> 

                {{-- Select Dropdown Hidden (ใช้เก็บ State ขยะที่เลือก) --}}
                <select id="kp_itemscode" name="kp_itemscode" class="d-none" required>
                    <option value="">เลือกขยะ</option>
                    @foreach ($recycleItems as $item)
                        <option value="{{ $item->kp_itemscode }}" 
                                data-id="{{ $item->id }}" 
                                data-group="{{ $item->kp_items_group_idfk }}" 
                                data-name="{{ $item->kp_itemsname }}">
                            {{ $item->kp_itemsname }}
                        </option>
                    @endforeach
                </select>

                {{-- 3. Selected Item Display Banner --}} 
                <div id="selected-item-display" class="card bg-gradient-primary text-white border-0 rounded-3 mb-3 shadow-sm d-none"> 
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div> 
                            <small class="text-white-50 text-xxs d-block">รายการที่เลือกอยู่:</small> 
                            <h5 id="selected-item-name" class="fw-bold text-white mb-0">...</h5> 
                        </div> 
                        <button type="button" class="btn btn-link text-white p-0 mb-0 fs-4" onclick="resetSelection()"> 
                            <i class="fas fa-times-circle"></i> 
                        </button> 
                    </div>
                </div> 

                {{-- 4. Quantity Input & Mobile Quick Buttons Zone --}} 
                <div class="p-3 bg-white border rounded-4 shadow-none"> 
                    <div class="row g-2"> 
                        <div class="col-7"> 
                            <label class="text-xxs font-weight-bold text-muted mb-1 d-block">ปริมาณ / น้ำหนัก</label> 
                            <input type="number" step="0.01" name="amount_in_units" id="amount_in_units" class="form-control form-control-lg bg-light border-0 fw-bold text-center text-dark" placeholder="0.00" required min="0.01" style="font-size: 2rem; height: 55px;"> 
                        </div> 
                        <div class="col-5"> 
                            <label class="text-xxs font-weight-bold text-muted mb-1 d-block">หน่วยนับ</label> 
                            <select id="kp_units_idfk" name="kp_units_idfk" class="form-select form-select-lg bg-light border-0 text-dark fw-bold" style="height: 55px;" required> 
                                @foreach ($allUnits as $unit) 
                                    <option value="{{ $unit->id }}" {{ $unit->id == 1 ? 'selected' : '' }}>{{$unit->unitname }}</option> 
                                @endforeach 
                            </select> 
                        </div> 

                        {{-- Quick Weight Add Buttons (ช่วยสตาฟหน้างาน) --}}
                        <div class="col-12 mt-2">
                            <div class="d-flex gap-1 justify-content-between">
                                <button type="button" class="btn btn-xs btn-outline-secondary w-100 mb-0 py-2 rounded-2" onclick="addWeight(0.5)">+0.5</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary w-100 mb-0 py-2 rounded-2" onclick="addWeight(1.0)">+1.0</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary w-100 mb-0 py-2 rounded-2" onclick="addWeight(5.0)">+5.0</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary w-100 mb-0 py-2 rounded-2" onclick="addWeight(10.0)">+10.0</button>
                                <button type="button" class="btn btn-xs btn-outline-danger mb-0 px-3 py-2 rounded-2" onclick="clearWeight()" title="ลบตัวเลข"><i class="fas fa-backspace"></i></button>
                            </div>
                        </div>

                        {{-- Submit Add to Cart Button --}}
                        <div class="col-12 mt-3"> 
                            <button type="submit" class="btn bg-gradient-success w-100 py-3 rounded-3 shadow-success fw-bold text-sm mb-0"> 
                                <i class="fas fa-cart-plus me-2 fs-6"></i> เพิ่มรายการเข้าตะกร้า 
                            </button> 
                        </div> 
                    </div> 
                </div> 
            </form> 
        </div> 
    </div> 
</div> 

{{-- Floating Cart Button & Modal Summary --}} 
@if(Session::has('purchase_cart') && count(Session::get('purchase_cart')) > 0) 
    <button type="button" class="btn bg-gradient-dark position-fixed rounded-circle shadow-lg d-flex justify-content-center align-items-center" style="bottom: 25px; right: 25px; width: 60px; height: 60px; z-index: 1050; border: 2px solid white;" data-bs-toggle="modal" data-bs-target="#cartModal"> 
        <div class="position-relative"> 
            <i class="fas fa-shopping-basket fs-4 text-white"></i> 
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light text-xxs"> 
                {{ count(Session::get('purchase_cart')) }} 
            </span> 
        </div> 
    </button> 

    {{-- Quick Cart Modal --}}
    <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true"> 
        <div class="modal-dialog modal-dialog-centered modal-lg"> 
            <div class="modal-content rounded-4 border-0"> 
                <div class="modal-header border-bottom py-3"> 
                    <h6 class="modal-title font-weight-bold text-dark"> 
                        <i class="fas fa-shopping-cart text-success me-2"></i>รายการในตะกร้าขยะ 
                    </h6> 
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button> 
                </div> 
                <div class="modal-body p-0"> 
                    <div class="table-responsive"> 
                        <table class="table align-middle mb-0"> 
                            <thead class="bg-light text-secondary text-xxs font-weight-bolder"> 
                                <tr> 
                                    <th class="ps-3">รายการขยะ</th> 
                                    <th class="text-end">จำนวน</th> 
                                    <th class="text-end">รวมเงิน</th> 
                                    <th class="text-center" style="width: 50px;">ลบ</th> 
                                </tr> 
                            </thead> 
                            <tbody> 
                                @php $grandTotal = 0; @endphp 
                                @foreach (Session::get('purchase_cart') as $index =>$item) 
                                    @php $grandTotal +=$item['amount']; @endphp 
                                    <tr class="border-bottom border-light"> 
                                        <td class="ps-3 py-2"> 
                                            <div class="fw-bold text-dark text-xs">{{ $item['item_name'] }}</div> 
                                            <small class="text-muted text-xxs">{{ number_format($item['price_per_unit'], 2) }} ฿/หน่วย</small> 
                                        </td> 
                                        <td class="text-end"> 
                                            <span class="badge badge-sm bg-gradient-light text-dark border"> 
                                                {{ number_format($item['amount_in_units'], 2) }} {{$item['unit_name'] }} 
                                            </span> 
                                        </td> 
                                        <td class="text-end fw-bold text-success text-xs"> 
                                            {{ number_format($item['amount'], 2) }} ฿ 
                                        </td> 
                                        <td class="text-center"> 
                                            <form action="{{ route('keptkayas.purchase.remove_from_cart', $index) }}" method="POST"> 
                                                @csrf 
                                                @method('DELETE') 
                                                <button type="submit" class="btn btn-link text-danger p-0 mb-0" title="ลบ"> 
                                                    <i class="fas fa-trash-alt"></i> 
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
                    <div> 
                        <small class="text-muted text-xxs d-block">ยอดเงินสุทธิรวม</small> 
                        <span class="fw-bold text-success fs-5">{{ number_format($grandTotal, 2) }} ฿</span> 
                    </div> 
                    <a href="{{ route('keptkayas.purchase.cart') }}" class="btn bg-gradient-dark rounded-pill px-4 shadow mb-0 text-xs"> 
                        สรุปและคิดเงิน <i class="fas fa-chevron-right ms-1"></i> 
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
            <div class="modal-header border-bottom-0 pb-0"> 
                <h6 class="modal-title font-weight-bold"><i class="fas fa-qrcode text-primary me-2"></i>สแกนขยะ</h6> 
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"></button> 
            </div> 
            <div class="modal-body text-center p-3"> 
                <div class="overflow-hidden rounded-3 bg-dark position-relative" style="min-height: 250px;"> 
                    <div id="qr-reader" style="width: 100%;"></div> 
                </div> 
                <p class="text-xs text-secondary mt-2 mb-0">นำกล้องส่องไปที่ QR Code ของประเภทขยะ</p> 
            </div> 
        </div> 
    </div> 
</div> 

<style> 
    .hide-scrollbar::-webkit-scrollbar { display: none; } 
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; } 
    .cursor-pointer { cursor: pointer; } 
    .item-card { transition: all 0.15s ease-in-out; } 
    .item-card:active { transform: scale(0.96); } 
</style> 
@endsection 

@section('script') 
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script> 
<script> 
    let allOptions = []; 

    $(document).ready(function(){ 
        // 1. ดึง Data จาก Dropdown มาเก็บใน Memory
        $('#kp_itemscode option').each(function(){ 
            if($(this).val() !== ""){ 
                allOptions.push({ 
                    val: $(this).val(), 
                    id: $(this).data('id'), 
                    group: $(this).data('group').toString(), 
                    name: $(this).data('name') 
                }); 
            } 
        }); 

        // แสดงรายการขยะทั้งหมดตอนเปิดหน้าจอครั้งแรก
        filterItemsByGroup('all', null);

        // เมื่อเปลี่ยนค่าใน dropdown ให้ดึงหน่วยนับมาอัปเดต
        $('#kp_itemscode').on('change', function(){ 
            let itemId = $(this).find(':selected').data('id'); 
            if(itemId) loadUnitsForSelectedItem(itemId); 
        }); 
    }); 

    // ฟังก์ชันกรองขยะตามหมวดหมู่
    window.filterItemsByGroup = function(groupId, element) { 
        console.log(groupId)
        if (element) {
            $('.filter-item .filter-icon-btn').removeClass('btn-dark text-white').addClass('btn-outline-dark text-dark');$('.filter-item small').removeClass('text-dark').addClass('text-muted'); 

            $(element).find('.filter-icon-btn').removeClass('btn-outline-dark text-dark').addClass('btn-dark text-white');$(element).find('small').removeClass('text-muted').addClass('text-dark'); 
        }

        const $container =$('#item-buttons-container'); 
        $container.empty(); 
        let found = false; 

        allOptions.forEach(opt => { 
            if (groupId === 'all' || opt.group === groupId) { 
                found = true; 
                let btnHtml = ` 
                    <div class="col-6 col-md-4"> 
                        <div class="card h-100 item-card border rounded-3 bg-white shadow-none" onclick="selectItem('${opt.val}')" id="card-${opt.val}" style="cursor: pointer;"> 
                            <div class="card-body p-2 text-center d-flex align-items-center justify-content-center"> 
                                <span class="fw-bold text-dark text-xs item-text">${opt.name}</span> 
                            </div> 
                        </div> 
                    </div> 
                `; 
                $container.append(btnHtml); 
            } 
        }); 

        if(!found) { 
            $container.html('<div class="col-12 text-center text-muted py-3 text-xs"><i class="fas fa-inbox me-1"></i>ไม่พบรายการขยะในหมวดหมู่นี้</div>'); 
        } 
    };

    // เลือกขยะที่ต้องการชั่ง
    window.selectItem = function(val) { 
        $('.item-card').removeClass('bg-gradient-primary text-white shadow-primary').addClass('bg-white');$('.item-card .item-text').removeClass('text-white').addClass('text-dark'); 

        $(`#card-${val}`).removeClass('bg-white').addClass('bg-gradient-primary text-white shadow-primary'); 
        $(`#card-${val} .item-text`).removeClass('text-dark').addClass('text-white'); 

        $('#kp_itemscode').val(val).trigger('change'); 

        const selectedOpt = allOptions.find(o => o.val === val); 
        if(selectedOpt) { 
            $('#selected-item-name').text(selectedOpt.name); 
            $('#selected-item-display').removeClass('d-none'); 
            $('#kp_tbank_item_id').val(selectedOpt.id); 

            // เลื่อนไปโฟกัสที่ช่องกรอกน้ำหนัก
            $('#amount_in_units').focus(); 
        } 
    }; 

    // รีเซ็ตขยะที่เลือก
    window.resetSelection = function() { 
        $('#kp_itemscode').val('').trigger('change'); 
        $('#selected-item-display').addClass('d-none'); 
        $('.item-card').removeClass('bg-gradient-primary text-white shadow-primary').addClass('bg-white'); 
        $('.item-card .item-text').removeClass('text-white').addClass('text-dark');$('#kp_tbank_item_id').val(''); 
    };

    // ปุ่มกดเพิ่มน้ำหนักด่วนบนมือถือ
    window.addWeight = function(val) {
        let current = parseFloat($('#amount_in_units').val()) || 0;
        let updated = (current + val).toFixed(2);
        $('#amount_in_units').val(updated);
    };

    window.clearWeight = function() {
        $('#amount_in_units').val('');
    };

    // โหลดหน่วยนับของขยะผ่าน AJAX
    function loadUnitsForSelectedItem(itemId) { 
        const $kpUnitsSelect =$('#kp_units_idfk'); 
        $kpUnitsSelect.prop('disabled', true); 

        $.ajax({ 
            url: '{{ route('keptkayas.purchase.get_units', ['itemId' => 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', itemId), 
            method: 'GET', 
            success: function(response) { 
                $kpUnitsSelect.empty(); 
                if (response.length > 0) { 
                    response.forEach(function(unit) { 
                        const newOption = new Option(unit.unit_name, unit.unit_id, false, false); 
                        $kpUnitsSelect.append(newOption); 
                    }); 
                    $kpUnitsSelect.find('option:first').prop('selected', true); 
                } 
                $kpUnitsSelect.prop('disabled', false); 
            }, 
            error: function() { 
                $kpUnitsSelect.prop('disabled', false); 
            } 
        }); 
    } 

    // QR Code Scanner Logic
    document.addEventListener('DOMContentLoaded', function () { 
        const qrScannerModal = document.getElementById('qrScannerModal'); 
        let html5QrCode = null; 

        if (qrScannerModal) {
            qrScannerModal.addEventListener('shown.bs.modal', () => { 
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("qr-reader"); 
                }
                html5QrCode.start(
                    { facingMode: "environment" }, 
                    { fps: 10, qrbox: { width: 220, height: 220 } }, 
                    (decodedText) => { 
                        html5QrCode.stop().then(() => { 
                            bootstrap.Modal.getInstance(qrScannerModal).hide(); 
                            selectItem(decodedText.toString().toUpperCase()); 
                        }); 
                    }, 
                    (errorMessage) => {}
                ).catch(err => alert("ไม่สามารถเปิดกล้องได้")); 
            }); 

            qrScannerModal.addEventListener('hidden.bs.modal', () => { 
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.stop(); 
                }
            }); 
        }
    }); 
</script> 
@endsection