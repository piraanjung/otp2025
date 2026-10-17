@extends('layouts.admin1')

@section('nav-invoice') active @endsection
@section('nav-header') ออกใบแจ้งหนี้ @endsection
@section('nav-current') เพิ่มผู้ใช้น้ำระหว่างรอบบิล @endsection
@section('nav-topic') เพิ่มผู้ใช้น้ำระหว่างรอบบิล @endsection

@section('style')
    <style>
        .hidden {
            display: none;
        }

        .input-error {
            color: red !important;
            font-weight: bold;
            border: 2px solid red !important;
        }

        .input-warning {
            background-color: #fff3cd !important;
        }

        .bg-readonly {
            background-color: #e9ecef;
        }

        /* สไตล์สำหรับการ์ดและระบบ Filter */
        .water-card {
            transition: all 0.3s ease;
        }

        .water-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .row>* {

            padding-right: 5px !important;
            padding-left: 5px !important;
            padding-bottom: 4px;

        }

        .form-floating input {
            font-size: 1.2rem
        }
    </style>
    <script src="{{ asset('/adminlte/plugins/jquery/jquery.min.js') }}"></script>
@endsection

@section('content')
    <div id="web_app" class="">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('invoice.store') }}" method="POST" id="invoiceForm">
                    @csrf

                    <!-- Toolbar & Filter Search Bar -->
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <input type="submit" class="btn btn-primary btn-block" id="print_multi_inv"
                                value="บันทึกข้อมูลทั้งหมด">
                            <input type="hidden" value="inv_create" name="inv_from_page">
                            <input type="hidden" value="{{ $subzone->zone_id }}" name="zone_id">
                            <input type="hidden" value="{{ $subzone->id }}" name="subzone_id">
                        </div>

                        <!-- Filter Search Input -->
                        <div class="col-md-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" id="cardFilterInput" class="form-control"
                                    placeholder="ค้นหาตามเลขมิเตอร์, Factory No., ชื่อ-สกุล หรือบ้านเลขที่...">
                            </div>
                        </div>
                    </div>

                    <!-- Card Container (แทน Table เดิม) -->
                    <div class="row" id="cardContainer">
                        <?php $i = 1; ?>

                        {{-- ================================================================================== --}}
                        {{-- LOOP 1: รายการใหม่ (New Records) --}}
                        {{-- ================================================================================== --}}
                        @if (collect($member_not_yet_recorded_present_inv_period)->count() > 0)
                            @foreach ($member_not_yet_recorded_present_inv_period[0] as $key => $invoice)
                                @php 
                                    $rateConfig = $invoice->meter_type->rateConfigs->first() ?? null;
                                    $pricingTypeId = $rateConfig->pricing_type_id ?? 1;
                                    $pricePerUnit = $rateConfig->fixed_rate_per_unit ?? 0;
                                    $minUsageCharge = $rateConfig->min_usage_charge ?? 0;
                                    $vatRate = $rateConfig->vat ?? 0;
                                    $tiersJson = ($rateConfig && $rateConfig->Ratetiers) ? $rateConfig->Ratetiers->sortBy('tier_order')->values()->toJson() : '[]';
                                    $fullName = ($invoice->user->firstname ?? '-') . ' ' . ($invoice->user->lastname ?? '');
                                    $searchKeyword = strtolower($invoice->meternumber . ' ' . $invoice->factory_no . ' ' . $fullName . ' ' . ($invoice->user->address ?? ''));
                                @endphp

                                <div class="col-xl-4 col-md-6 mb-4 filter-item" data-search="{{ $searchKeyword }}">
                                    <div class="card water-card h-100 border-top border-primary">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                            <span class="badge badge-primary">ลำดับที่ {{ $i }} (ใหม่)</span>
                                            <small class="text-muted">Factory No: <b>{{ $invoice->factory_no }}</b></small>
                                        </div>
                                        <div class="card-body">
                                            <!-- ข้อมูลผู้ใช้ & มิเตอร์ -->
                                            <h5 class="card-title font-weight-bold text-dark mb-1">
                                                <span class="username text-primary" style="cursor: pointer;"
                                                    data-user_id="{{ $invoice->user_id }}">
                                                    <i class="fas fa-search-plus"></i> {{ $fullName }}
                                                </span>
                                            </h5>
                                            <p class="card-text text-muted small mb-3">
                                                <i class="fas fa-map-marker-alt text-danger"></i> บ้านเลขที่:
                                                {{ $invoice->user->address ?? '-' }}<br>
                                                <i class="fas fa-tachometer-alt text-success"></i> เลขมิเตอร์:
                                                <b>{{ $invoice->meternumber }}</b>
                                            </p>

                                            <!-- Hidden Inputs สำหรับส่งค่า Form -->
                                            <input type="hidden" value="{{ $invoice->meternumber }}"
                                                name="data[{{ $i }}][meternumber]" data-id="{{ $i }}" id="meternumber{{ $i }}"
                                                readonly>
                                            <input type="hidden" value="new_inv" name="data[{{ $i }}][inv_id]">
                                            <input type="hidden" value="{{ $invoice->meter_id }}" name="data[{{ $i }}][meter_id]">
                                            <input type="hidden" value="{{ $pricePerUnit }}"
                                                name="data[{{ $i }}][fixed_rate_per_unit]">
                                            <input type="hidden" readonly class="form-control"
                                                value="{{ $invoice->user->address ?? '' }}" name="data[{{ $i }}][address]">

                                            <hr class="my-2">

                                            <!-- ฟอร์มกรอกเลขมิเตอร์และการคำนวณ -->
                                            <div class="row">
                                                <div class="form-group col-6">
                                                    <label class="small text-muted mb-1">ยกยอดมา</label>
                                                    <input type="number" value="0" name="data[{{ $i }}][lastmeter]" data-id="{{ $i }}"
                                                        id="lastmeter{{ $i }}"
                                                        class="form-control form-control-sm text-end lastmeter" data-original="0">
                                                </div>
                                                <div class="form-group col-6">
                                                    <label class="small text-muted mb-1">มิเตอร์ปัจจุบัน</label>
                                                    <input type="number" value="0" name="data[{{ $i }}][currentmeter]"
                                                        data-id="{{ $i }}" id="currentmeter{{ $i }}"
                                                        data-pricing-type="{{ $pricingTypeId }}" data-price="{{ $pricePerUnit }}"
                                                        data-tiers='{{ $tiersJson }}' data-vat="{{ $vatRate }}"
                                                        data-reserve="{{ $minUsageCharge }}"
                                                        class="form-control form-control-sm text-end currentmeter border-success"
                                                        data-original="0">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-6">
                                                    <label class="small text-muted mb-1">ใช้น้ำ (หน่วย)</label>
                                                    <input type="text" readonly
                                                        class="form-control form-control-sm text-end water_used_net bg-readonly font-weight-bold"
                                                        id="water_used_net{{ $i }}" value="">
                                                </div>
                                                <div class="form-group col-6">
                                                    <label class="small text-muted mb-1">เป็นเงิน (บาท)</label>
                                                    <input type="text" readonly
                                                        class="form-control form-control-sm text-end paid bg-readonly"
                                                        id="paid{{ $i }}" value="">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-4">
                                                    <label class="small text-muted mb-1">ค่ารักษามาตร</label>
                                                    <input type="text" readonly
                                                        class="form-control form-control-sm text-end meter_reserve_price bg-readonly"
                                                        name="data[{{ $i }}][meter_reserve_price]" id="meter_reserve_price{{ $i }}"
                                                        value="{{ number_format($minUsageCharge, 2) }}">
                                                </div>
                                                <div class="form-group col-4">
                                                    <label class="small text-muted mb-1">ภาษี 7%</label>
                                                    <input type="text" readonly
                                                        class="form-control form-control-sm text-end vat bg-readonly"
                                                        id="vat{{ $i }}" value="">
                                                </div>
                                                <div class="form-group col-4">
                                                    <label
                                                        class="small text-muted mb-1 text-danger font-weight-bold">รวมทั้งสิ้น</label>
                                                    <input type="text" readonly
                                                        class="form-control form-control-sm text-end total bg-readonly font-weight-bold text-danger"
                                                        id="total{{ $i }}" value="">
                                                </div>
                                            </div>

                                            <!-- ส่วนแสดงประวัติเพิ่มเติม (Child Row เดิมปรับมาแสดงใน Card เมื่อคลิกชื่อ) -->
                                            <div id="user_history_{{ $i }}" class="mt-2"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php        $i++; ?>
                            @endforeach
                        @endif

                        {{-- ================================================================================== --}}
                        {{-- LOOP 2: รายการที่มี Invoice แล้ว (Existing Records) --}}
                        {{-- ================================================================================== --}}
                        @foreach ($invoices as $invoice)
                            @php 
                                $meterInfo = $invoice->tw_meter_infos;
                                $rateConfig = $meterInfo?->meter_type?->rateConfigs->first() ?? null;
                                $pricingTypeId = $rateConfig->pricing_type_id ?? 1;
                                $pricePerUnit = $rateConfig->fixed_rate_per_unit ?? 0;
                                $minUsageCharge = $rateConfig->min_usage_charge ?? 0;
                                $vatRate = $rateConfig->vat ?? 0;
                                $tiersJson = ($rateConfig && $rateConfig->Ratetiers) ? $rateConfig->Ratetiers->sortBy('tier_order')->values()->toJson() : '[]';
                                $fullName = ($meterInfo?->user?->firstname ?? '-') . ' ' . ($meterInfo?->user?->lastname ?? '') . ' ' . ($meterInfo?->submeter_name ?? '');

                                $paidInit = $invoice->paid ?? 0;
                                $vatInit = $paidInit * ($vatRate > 1 ? $vatRate / 100 : $vatRate);
                                $searchKeyword = strtolower(($meterInfo->meternumber ?? '') . ' ' . ($meterInfo->factory_no ?? '') . ' ' . $fullName . ' ' . ($meterInfo->user->address ?? ''));
                            @endphp

                            <div class="col-xl-4 col-md-6 mb-4 filter-item" data-search="{{ $searchKeyword }}">
                                <div class="card water-card h-100 border-top border-warning">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                        <span class="badge badge-warning text-dark">ลำดับที่ {{ $i }} (มีใบแจ้งหนี้แล้ว)</span>
                                        <a href="javascript:void(0)" class="btn btn-xs btn-outline-danger"
                                            onclick="del('{{ $invoice->meter_id_fk }}')">
                                            <i class="fas fa-trash"></i> ลบ
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title font-weight-bold text-dark mb-1">
                                            <span class="username text-primary" style="cursor: pointer;"
                                                data-user_id="{{ $meterInfo->user_id ?? 0 }}">
                                                <i class="fas fa-search-plus"></i> {{ $fullName }}
                                            </span>
                                        </h5>
                                        <p class="card-text text-muted small mb-3">
                                            <i class="fas fa-map-marker-alt text-danger"></i> บ้านเลขที่:
                                            {{ $meterInfo->user->address ?? '-' }}<br>
                                            <i class="fas fa-tachometer-alt text-success"></i> เลขมิเตอร์:
                                            <b>{{ $meterInfo->meternumber ?? '-' }}</b> | Factory No:
                                            <b>{{ $meterInfo->factory_no ?? '-' }}</b>
                                        </p>

                                        <!-- Hidden Inputs -->
                                        <input type="hidden" value="{{ $meterInfo->meternumber ?? '' }}"
                                            name="data[{{ $i }}][meternumber]" data-id="{{ $i }}" id="meternumber{{ $i }}"
                                            readonly>
                                        <input type="hidden" value="{{ $invoice->meter_id_fk }}"
                                            name="data[{{ $i }}][meter_id]">
                                        <input type="hidden" value="{{ $invoice->id }}" name="data[{{ $i }}][inv_id]">
                                        <input type="hidden" value="{{ $pricePerUnit }}"
                                            name="data[{{ $i }}][fixed_rate_per_unit]">
                                        <input type="hidden" readonly class="form-control"
                                            value="{{ $meterInfo->user->address ?? '' }}" name="data[{{ $i }}][address]">

                                        <hr class="my-2">

                                        <!-- Form Inputs -->
                                        <div class="row">
                                            <div class="form-floating col-6">
                                                <input type="number" value="{{ $invoice->lastmeter }}"
                                                    name="data[{{ $i }}][lastmeter]" data-id="{{ $i }}" id="lastmeter{{ $i }}"
                                                    class="form-control form-control-sm text-end lastmeter"
                                                    data-original="{{ $invoice->lastmeter }}">
                                                <label class="">ยกยอดมา</label>
                                            </div>
                                            <div class="form-floating col-6">
                                                <input type="number"
                                                    value="{{ !isset($invoice->currentmeter) ? 0 : $invoice->currentmeter }}"
                                                    name="data[{{ $i }}][currentmeter]" data-id="{{ $i }}"
                                                    id="currentmeter{{ $i }}" data-pricing-type="{{ $pricingTypeId }}"
                                                    data-price="{{ $pricePerUnit }}" data-tiers='{{ $tiersJson }}'
                                                    data-vat="{{ $vatRate }}" data-reserve="{{ $minUsageCharge }}"
                                                    class="form-control form-control-sm text-end currentmeter border-success"
                                                    data-original="{{ !isset($invoice->currentmeter) ? 0 : $invoice->currentmeter }}">
                                                <label class="small text-muted mb-1">มิเตอร์ปัจจุบัน</label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-floating col-6">
                                                <input type="text" readonly
                                                    class="form-control form-control-sm text-end water_used_net bg-readonly font-weight-bold"
                                                    id="water_used_net{{ $i }}"
                                                    value="{{ !isset($invoice->water_used) ? 0 : $invoice->water_used }}">
                                                <label class="small text-muted mb-1">ใช้น้ำ (หน่วย)</label>

                                            </div>
                                            <div class="form-floating col-6">
                                                <input type="text" readonly
                                                    class="form-control form-control-sm text-end paid bg-readonly"
                                                    name="data[{{ $i }}][paid]" id="paid{{ $i }}"
                                                    value="{{ !isset($invoice->paid) ? 0 : $invoice->paid }}">
                                                <label class="small text-muted mb-1">เป็นเงิน (บาท)</label>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-floating col-5">
                                                <input type="text" readonly name="data[{{ $i }}][meter_reserve_price]"
                                                    class="form-control form-control-sm text-end meter_reserve_price bg-readonly"
                                                    id="meter_reserve_price{{ $i }}"
                                                    value="{{ number_format($minUsageCharge, 2) }}">
                                                <label class="small text-muted mb-1">ค่ารักษามาตร</label>

                                            </div>
                                            <div class="form-floating col-3">
                                                <input type="text" readonly name="data[{{ $i }}][vat]"
                                                    class="form-control form-control-sm text-end vat bg-readonly"
                                                    data-id="{{ $i }}" id="vat{{ $i }}"
                                                    value="{{ number_format($vatInit, 2) }}">
                                                <label class="small text-muted mb-1">ภาษี 7%</label>

                                            </div>
                                            <div class="form-floating col-4">
                                                <input type="text" readonly
                                                    class="form-control form-control-sm text-end total bg-readonly font-weight-bold text-danger"
                                                    name="data[{{ $i }}][totalpaid]" id="total{{ $i }}"
                                                    value="{{ !isset($invoice->totalpaid) ? 0 : $invoice->totalpaid }}">
                                                <label
                                                    class="small text-muted mb-1 text-danger font-weight-bold">รวมทั้งสิ้น</label>

                                            </div>
                                        </div>

                                        <div id="user_history_{{ $i }}" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                            <?php    $i++; ?>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="mobile" class="hidden">
        <div class="alert alert-warning">กรุณาใช้งานในแนวนอน หรือจอคอมพิวเตอร์</div>
    </div>
@endsection


@section('script')
<script>
// 1. ฟังก์ชันคำนวณค่าน้ำ (รวม VAT ทั้งจากค่าน้ำและค่ารักษามาตร)
function calculateRow(water_used, pricing_type, fixed_price, tiers_json, vat_rate, reserve_price) {
    let safe_water = isNaN(parseFloat(water_used)) ? 0 : parseFloat(water_used);
    let water_cost = 0;
    if (safe_water < 0) {
        water_cost = 0;
    } else {
        if (tiers_json && Array.isArray(tiers_json) && tiers_json.length > 0 && pricing_type != 1) {
            let remaining_water = safe_water;
            for (let i = 0; i < tiers_json.length; i++) {
                if (remaining_water <= 0) break;
                let tier = tiers_json[i];
                let min = parseFloat(tier.min_units);
                let max = parseFloat(tier.max_units);
                let rate = parseFloat(tier.rate_per_unit);
                let range_size = (max > 0) ? (max - min + (min === 0 ? 0 : 1)) : Infinity;
                if(min === 0 && max === 10) range_size = 10;
                else if(max > 0) range_size = max - min + 1;
                let units_in_tier = Math.min(remaining_water, range_size);
                water_cost += units_in_tier * rate;
                remaining_water -= units_in_tier;
            }
        } else {
            water_cost = safe_water * parseFloat(fixed_price);
        }
    }

    let vat_multiplier = (vat_rate > 1) ? (vat_rate / 100) : vat_rate;
    
    // นำค่าน้ำ + ค่ารักษามาตร (reserve_price) มาคิด VAT ร่วมกันทั้งหมด
    let taxable_amount = water_cost + parseFloat(reserve_price);
    let vat_amount = taxable_amount * vat_multiplier;
    
    // ยอดรวมทั้งหมด = ค่าน้ำ + ค่ารักษามาตร + VAT ทั้งหมด
    let total = water_cost + parseFloat(reserve_price) + vat_amount;

    return { paid: water_cost.toFixed(2), vat: vat_amount.toFixed(2), total: total.toFixed(2) };
}

function del(id) {
    if(confirm('ต้องการลบข้อมูลใช่หรือไม่ !!!')) {
        window.location.href = '/invoice/delete/' + id + '/ลบ';
    }
}

function formatHistory(d) {
    let text = '<div class="table-responsive mt-2"><table class="table table-striped table-sm bg-white border"><thead><tr class="bg-light"><th style="font-size:11px;">รอบบิล</th><th style="font-size:11px;">ยอดเดิม</th><th style="font-size:11px;">ยอดใหม่</th><th style="font-size:11px;">หน่วย</th><th style="font-size:11px;">เงิน</th><th style="font-size:11px;">สถานะ</th></tr></thead><tbody>';
    if(d[0] && d[0].usermeterinfos && d[0].usermeterinfos.invoice) {
        d[0].usermeterinfos.invoice.forEach(element => {
            let _status = element.status === 'paid' ? '<span class="badge badge-success">จ่ายแล้ว</span>' : '<span class="badge badge-warning">ค้าง</span>';
            if (element.status !== 'init') {
                let invName = element.invoice_period ? (element.invoice_period.inv_p_name || '-') : '-';
                let waterUsed = (element.currentmeter || 0) - (element.lastmeter || 0);
                text += '<tr>' +
                    '<td style="font-size:11px;">' + invName + '</td>' +
                    '<td style="font-size:11px;">' + (element.lastmeter || 0) + '</td>' +
                    '<td style="font-size:11px;">' + (element.currentmeter || 0) + '</td>' +
                    '<td style="font-size:11px;">' + waterUsed + '</td>' +
                    '<td style="font-size:11px;">' + (element.totalpaid || 0) + '</td>' +
                    '<td style="font-size:11px;">' + _status + '</td>' +
                    '</tr>';
            }
        });
    } else {
        text += '<tr><td colspan="6" class="text-center text-muted small">ไม่พบประวัติย้อนหลัง</td></tr>';
    }
    text += '</tbody></table></div>';
    return text;
}

$(document).ready(function() {
    // 2. Filterable Card Search
    $('#cardFilterInput').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#cardContainer .filter-item').filter(function() {
            let searchData = $(this).data('search') || '';
            $(this).toggle(searchData.indexOf(value) > -1);
        });
    });

    // 🌟 6. เซ็ตค่าเริ่มต้นตอนโหลดหน้าเว็บให้เป็น 0 ทั้งหมด (ยังไม่คำนวณจนกว่าจะพิมพ์)
    $('.currentmeter').each(function() {
        let id = $(this).data('id');
        let reserve_price = parseFloat($(this).data('reserve')) || 0;

        $('#water_used_net' + id).val('0');
        $('#paid' + id).val('0.00');
        $('#vat' + id).val('0.00');
        $('#total' + id).val('0.00');
        $('#meter_reserve_price' + id).val('0.00'); // เก็บค่า reserve ไว้ใช้ตอนคำนวณทีหลัง
    });

    // เมื่อคลิกช่อง input ให้ล้างค่า '0' เป็นค่าว่างเพื่อให้พิมพ์ง่าย
    $(document).on('focus', '.currentmeter, .lastmeter', function() {
        if ($(this).val() === '0') {
            $(this).val('');
        }
    });

    // เมื่อคลิกออก (blur) แล้วปล่อยว่างไว้ ให้คืนค่าเป็น '0'
    $(document).on('blur', '.currentmeter, .lastmeter', function() {
        if ($(this).val().trim() === '') {
            $(this).val('0');
            $(this).trigger('keyup');
        }
    });

    // 3. Logic การคำนวณ (ทำงานเมื่อมีการ Keyup หรือพิมพ์แก้ไขข้อมูล)
    $(document).on('keyup', '.currentmeter, .lastmeter', function() {
        try {
            let id = $(this).data('id');
            let currentInput = $('#currentmeter' + id);
            let lastInput = $('#lastmeter' + id);
            let currentmeter = parseFloat(currentInput.val()) || 0;
            let lastmeter = parseFloat(lastInput.val()) || 0;
            let water_used = currentmeter - lastmeter;
            let pricing_type = currentInput.data('pricing-type');
            let price_per_unit = parseFloat(currentInput.data('price')) || 0;
            let tiers_data = currentInput.data('tiers');
            let vat_rate = parseFloat(currentInput.data('vat')) || 0;
            let reserve_price = parseFloat(currentInput.data('reserve')) || 0;

            let waterUsedField = $('#water_used_net' + id);
            waterUsedField.val(water_used);

            if (water_used < 0) {
                waterUsedField.addClass('input-error');
            } else {
                waterUsedField.removeClass('input-error');
            }

            const result = calculateRow(water_used, pricing_type, price_per_unit, tiers_data, vat_rate, reserve_price);
            $('#paid' + id).val(result.paid);
            $('#vat' + id).val(result.vat);
            $('#total' + id).val(result.total);
            $('#meter_reserve_price' + id).val(reserve_price.toFixed(2));
        } catch (error) {
            console.error("Calculation Error:", error);
        }
    });

    $(document).on('change', '.currentmeter, .lastmeter', function() {
        let id = $(this).data('id');
        let currentInput = $('#currentmeter' + id);
        let lastInput = $('#lastmeter' + id);
        let currentmeter = parseFloat(currentInput.val()) || 0;
        let lastmeter = parseFloat(lastInput.val()) || 0;
        let water_used = currentmeter - lastmeter;

        if (water_used < 0) {
            alert('❌ ผิดพลาด: จำนวนการใช้น้ำติดลบไม่ได้');
            setTimeout(function() {
                currentInput.focus();
            }, 100);
            return;
        }

        if (lastmeter > 0 && currentmeter > (lastmeter * 5)) {
            let isConfirmed = confirm('⚠️ แจ้งเตือน: มิเตอร์ปัจจุบัน (' + currentmeter + ') มากกว่าครั้งก่อน (' + lastmeter + ') ถึง 5 เท่า\nยืนยันข้อมูลนี้หรือไม่?');
            if (!isConfirmed) {
                setTimeout(function() {
                    currentInput.focus().select();
                }, 100);
                currentInput.addClass('input-warning');
            } else {
                currentInput.removeClass('input-warning');
            }
        }
    });

    // 4. History Toggle
    $(document).on('click', '.username', function() {
        let user_id = $(this).data('user_id');
        let cardBox = $(this).closest('.card-body');
        let historyDiv = cardBox.find('[id^="user_history_"]');
        if (historyDiv.html().trim() !== '') {
            historyDiv.html('');
            return;
        }
        historyDiv.html('<div class="text-center p-2"><i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</div>');
        $.get('/api/users/user/' + user_id)
            .done(function(data) {
                historyDiv.html(formatHistory(data));
            })
            .fail(function() {
                historyDiv.html('<div class="text-center text-danger p-2">โหลดข้อมูลไม่สำเร็จ</div>');
            });
    });

    // 5. ดักจับการ submit ฟอร์ม (กรองส่งเฉพาะการ์ดที่มียอดรวม totalpaid > 0)
    $('#invoiceForm').on('submit', function(e) {
        let hasChange = false;

        $('#cardContainer .filter-item').each(function() {
            let card = $(this);
            let currentInput = card.find('.currentmeter');
            let lastInput = card.find('.lastmeter');
            let totalInput = card.find('.total'); // ช่องรวมทั้งสิ้น (totalpaid)

            let originalCurrent = parseFloat(currentInput.attr('data-original')) || 0;
            let originalLast = parseFloat(lastInput.attr('data-original')) || 0;

            let currentVal = parseFloat(currentInput.val()) || 0;
            let lastVal = parseFloat(lastInput.val()) || 0;
            let totalVal = parseFloat(totalInput.val()) || 0;

            let isChanged = (currentVal !== originalCurrent) || (lastVal !== originalLast);

            // เงื่อนไขผ่าน: มีการคีย์ข้อมูลเปลี่ยนและยอดรวมมากกว่า 0
            if (totalVal > 0) {
                hasChange = true;
            } else {
                // ถ้าไม่เข้าเงื่อนไข ให้ disable input ทิ้งเพื่อไม่ให้ส่งไปบันทึก
                card.find('input, select, textarea').prop('disabled', true);
            }
        });

        if (!hasChange) {
            alert('⚠️ ไม่พบข้อมูลที่มีการเปลี่ยนแปลงและมียอดเงินรวมมากกว่า 0 กรุณาตรวจสอบข้อมูลก่อนบันทึก');
            $('#cardContainer .filter-item').find('input, select, textarea').prop('disabled', false);
            e.preventDefault();
        }
    });
});
</script>
@endsection
