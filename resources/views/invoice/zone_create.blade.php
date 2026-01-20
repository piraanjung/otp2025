@extends('layouts.admin1')

@section('nav-invoice')
    active
@endsection

@section('nav-header')
    ออกใบแจ้งหนี้
@endsection

@section('nav-current')
    เพิ่มผู้ใช้น้ำระหว่างรอบบิล
@endsection

@section('nav-topic')
    เพิ่มผู้ใช้น้ำระหว่างรอบบิล
@endsection

@section('style')
    <style>
        .hidden { display: none; }
        /* Style สำหรับแจ้งเตือน */
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
    </style>

    <script src="{{ asset('/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('/adminlte/plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.6/pagination/select.js"></script>
@endsection

@section('content')
    <div id="web_app" class="">
        <div class="card">
            <div class="card-body table-responsive">
                <form action="{{ route('invoice.store') }}" method="POST">
                    @csrf
                    <input type="submit" class="btn btn-primary col-2" id="print_multi_inv" value="บันทึก">
                    <input type="hidden" value="inv_create" name="inv_from_page">
                    <input type="hidden" value="{{ $subzone->zone_id }}" name="zone_id">
                    <input type="hidden" value="{{ $subzone->id }}" name="subzone_id">
                    <br><br>
                    
                    <table class="table table-striped datatable" id="DivIdToPrint">
                        <thead class="bg-light">
                            <tr>
                                <th></th>
                                <th class="text-center">เลขมิเตอร์</th>
                                <th class="text-center">Factory No.</th>
                                <th class="text-center">ชื่อ-สกุล</th>
                                <th class="text-center">บ้านเลขที่</th>
                                <th class="text-center">ยกยอดมา</th>
                                <th class="text-center">มิเตอร์<div>ปัจจุบัน</div></th>
                                <th class="text-center">ใช้น้ำ<div> (หน่วย)</div></th>
                                <th class="text-center">เป็นเงิน <div>(บาท)</div></th>
                                <th class="text-center">ค่ารักษา<div>มาตร (บาท)</div></th>
                                <th class="text-center">ภาษีมูลค่า<div>เพิ่ม 7% (บาท)</div></th>
                                <th class="text-center">รวมทั้งสิ้น <div>(บาท)</div></th>
                            </tr>
                        </thead>

                        <tbody id="app">
                            <?php $i = 1; ?>
                            
                            {{-- ================================================================================== --}}
                            {{-- LOOP 1: รายการใหม่ (New Records) --}}
                            {{-- ================================================================================== --}}
                            @if (collect($member_not_yet_recorded_present_inv_period)->count() > 0)
                                @foreach ($member_not_yet_recorded_present_inv_period[0] as $key => $invoice)
                                    @php
                                        // ดึง Config ค่าน้ำ
                                        $rateConfig = $invoice->meter_type->rateConfigs->first() ?? null;
                                        $pricingTypeId  = $rateConfig->pricing_type_id ?? 1; // 1=Fixed, 2=Progressive
                                        $pricePerUnit   = $rateConfig->fixed_rate_per_unit ?? 0;
                                        $minUsageCharge = $rateConfig->min_usage_charge ?? 0;
                                        $vatRate        = $rateConfig->vat ?? 7; 
                                        
                                        // แปลง Tiers เป็น JSON (เรียงตามลำดับ tier_order)
                                        $tiersJson = ($rateConfig && $rateConfig->Ratetiers) 
                                                     ? $rateConfig->Ratetiers->sortBy('tier_order')->values()->toJson() 
                                                     : '[]';
                                    @endphp

                                    <tr data-id="{{ $i }}" class="data">
                                        <td>{{ $i }}</td>
                                        <td class="border-0 text-center">
                                            {{ $invoice->meternumber }}
                                            <input type="hidden" value="{{ $invoice->meternumber }}"
                                                name="data[{{ $i }}][meternumber]" data-id="{{ $i }}"
                                                id="meternumber{{ $i }}"
                                                class="form-control text-right meternumber border-primary text-sm text-bold"
                                                readonly>
                                            <input type="hidden" value="new_inv" name="data[{{ $i }}][inv_id]">
                                            <input type="hidden" value="{{ $invoice->meter_id }}"
                                                name="data[{{ $i }}][meter_id]">
                                            <input type="hidden" value="{{ $pricePerUnit }}"
                                                name="data[{{ $i }}][fixed_rate_per_unit]">
                                        </td>
                                        <td>{{ $invoice->factory_no }}</td>

                                        <td class="border-0 text-left">
                                            <span class="username" data-user_id="{{ $invoice->user_id }}">
                                                <i class="fas fa-search-plus"></i>
                                                {{ ($invoice->user->firstname ?? '-') . ' ' . ($invoice->user->lastname ?? '') }}
                                            </span>
                                            <input type="hidden" name="data[{{ $i }}][user_id]"
                                                value="{{ $invoice->user_id }}">
                                        </td>
                                        <td class="text-center">
                                            {{ $invoice->user->address ?? '-' }}
                                            <input type="hidden" readonly class="form-control"
                                                value="{{ $invoice->user->address ?? '' }}" name="data[{{ $i }}][address]">
                                        </td>

                                        <td class="border-0">
                                            <input type="text" value="0" name="data[{{ $i }}][lastmeter]"
                                                data-id="{{ $i }}" id="lastmeter{{ $i }}"
                                                class="form-control text-end lastmeter">
                                        </td>
                                        
                                        <td class="border-0 text-right">
                                            {{-- Data Attributes สำหรับ JS --}}
                                            <input type="text" value="0" name="data[{{ $i }}][currentmeter]"
                                                data-id="{{ $i }}" id="currentmeter{{ $i }}"
                                                
                                                data-pricing-type="{{ $pricingTypeId }}"
                                                data-price="{{ $pricePerUnit }}" 
                                                data-tiers='{{ $tiersJson }}'
                                                data-vat="{{ $vatRate }}"
                                                data-reserve="{{ $minUsageCharge }}"
                                                
                                                class="form-control text-end currentmeter border-success">
                                        </td>
                                        
                                        <td class="border-0 text-right">
                                            <input type="text" readonly class="form-control text-end water_used_net bg-readonly"
                                                id="water_used_net{{ $i }}" value="">
                                        </td>
                                        <td class="border-0 text-right">
                                            <input type="text" readonly class="form-control text-end paid bg-readonly"
                                                id="paid{{ $i }}" value="">
                                        </td>
                                        <td class="border-0 text-right">
                                            <input type="text" readonly class="form-control text-end meter_reserve_price bg-readonly"
                                                name="data[{{ $i }}][meter_reserve_price]"
                                                id="meter_reserve_price{{ $i }}" value="{{ number_format($minUsageCharge, 2) }}">
                                        </td>
                                        <td class="border-0 text-right">
                                            <input type="text" readonly class="form-control text-end vat bg-readonly"
                                                id="vat{{ $i }}" value="">
                                        </td>
                                        <td class="border-0 text-right">
                                            <input type="text" readonly class="form-control text-end total bg-readonly"
                                                id="total{{ $i }}" value="">
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                            @endif

                            {{-- ================================================================================== --}}
                            {{-- LOOP 2: รายการที่มี Invoice แล้ว (Existing Records) --}}
                            {{-- ================================================================================== --}}
                            @foreach ($invoices as $invoice)
                                @php
                                    // เข้าถึง Model ผ่าน Relation
                                    $meterInfo = $invoice->tw_meter_infos; 
                                    // dd($meterInfo->meter_type->rateConfigs);


                                    $rateConfig = $meterInfo?->meter_type?->rateConfigs->first() ?? null;
                                    $pricingTypeId  = $rateConfig->pricing_type_id ?? 1;
                                    $pricePerUnit   = $rateConfig->fixed_rate_per_unit ?? 0;
                                    $minUsageCharge = $rateConfig->min_usage_charge ?? 0;
                                    $vatRate        = $rateConfig->vat ?? 7;
                                    
                                    // แปลง Tiers เป็น JSON
                                    $tiersJson = ($rateConfig && $rateConfig->Ratetiers) 
                                                 ? $rateConfig->Ratetiers->sortBy('tier_order')->values()->toJson() 
                                                 : '[]';

                                    $fullName = ($meterInfo?->user?->firstname ?? '-') . ' ' . ($meterInfo?->user?->lastname ?? '') . ' ' . ($meterInfo?->submeter_name ?? '');
                                @endphp

                                <tr data-id="{{ $i }}" class="data">
                                    <th class="text-center" width="2%">
                                        <a href="javascript:void(0)" class="btn btn-outline-warning delbtn2"
                                            onclick="del('{{ $invoice->meter_id_fk }}')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </th>
                                    <td class="border-0 text-center">
                                        {{ $meterInfo->meternumber ?? '-' }}
                                        <input type="hidden" value="{{ $meterInfo->meternumber ?? '' }}"
                                            name="data[{{ $i }}][meternumber]" data-id="{{ $i }}"
                                            id="meternumber{{ $i }}"
                                            class="form-control text-right meternumber border-primary text-sm text-bold"
                                            readonly>
                                        <input type="hidden" value="{{ $invoice->meter_id_fk }}"
                                            name="data[{{ $i }}][meter_id]">
                                        <input type="hidden" value="{{ $invoice->id }}"
                                            name="data[{{ $i }}][inv_id]">
                                        <input type="hidden" value="{{ $pricePerUnit }}"
                                            name="data[{{ $i }}][fixed_rate_per_unit]">
                                    </td>
                                    
                                    <td class="text-center">{{ $meterInfo->factory_no ?? '-' }}</td>

                                    <td class="border-0 text-left">
                                        <span class="username" data-user_id="{{ $meterInfo->user_id ?? 0 }}">
                                            <i class="fas fa-search-plus"></i> {{ $fullName }}
                                        </span>
                                        <input type="hidden" name="data[{{ $i }}][user_id]"
                                            value="{{ $meterInfo->user_id ?? '' }}">
                                    </td>
                                    
                                    <td class="text-center">
                                        {{ $meterInfo->user->address ?? '-' }}
                                        <input type="hidden" readonly class="form-control "
                                            value="{{ $meterInfo->user->address ?? '' }}"
                                            name="data[{{ $i }}][address]">
                                    </td>

                                    <td class="border-0">
                                        <input type="text" value="{{ $invoice->lastmeter }}"
                                            name="data[{{ $i }}][lastmeter]" data-id="{{ $i }}"
                                            id="lastmeter{{ $i }}"
                                            class="form-control text-end lastmeter">
                                    </td>
                                    
                                    <td class="border-0 text-right">
                                        {{-- Data Attributes --}}
                                        <input type="text"
                                            value="{{ !isset($invoice->currentmeter) ? 0 : $invoice->currentmeter }}"
                                            name="data[{{ $i }}][currentmeter]" data-id="{{ $i }}"
                                            id="currentmeter{{ $i }}"
                                            
                                            data-pricing-type="{{ $pricingTypeId }}"
                                            data-price="{{ $pricePerUnit }}" 
                                            data-tiers='{{ $tiersJson }}'
                                            data-vat="{{ $vatRate }}"
                                            data-reserve="{{ $minUsageCharge }}"

                                            class="form-control text-end currentmeter border-success">
                                    </td>
                                    
                                    <td class="border-0 text-right">
                                        <input type="text" readonly class="form-control text-end water_used_net bg-readonly"
                                            id="water_used_net{{ $i }}"
                                            value="{{ !isset($invoice->water_used) ? 0 : $invoice->water_used }}">
                                    </td>
                                    
                                    <td class="border-0 text-right">
                                        <input type="text" readonly class="form-control text-end paid bg-readonly"
                                            name="data[{{ $i }}][paid]" id="paid{{ $i }}"
                                            value="{{ !isset($invoice->paid) ? 0 : $invoice->paid }}">
                                    </td>
                                    
                                    <td class="border-0 text-right">
                                        <input type="text" readonly name="data[{{ $i }}][meter_reserve_price]"
                                            class="form-control text-end meter_reserve_price bg-readonly"
                                            id="meter_reserve_price{{ $i }}"
                                            value="{{ number_format($minUsageCharge, 2) }}">
                                    </td>

                                    <td class="border-0 text-right">
                                        @php
                                            $paidInit = $invoice->paid ?? 0;
                                            $vatInit = $paidInit * ($vatRate > 1 ? $vatRate/100 : $vatRate);
                                        @endphp
                                        <input type="text" readonly name="data[{{ $i }}][vat]"
                                            class="form-control text-end vat bg-readonly" data-id="{{ $i }}"
                                            id="vat{{ $i }}"
                                            value="{{ number_format($vatInit, 2) }}">
                                    </td>

                                    <td class="border-0 text-right">
                                        <input type="text" readonly class="form-control text-end total bg-readonly"
                                            name="data[{{ $i }}][totalpaid]" id="total{{ $i }}"
                                            value="{{ !isset($invoice->totalpaid) ? 0 : $invoice->totalpaid }}">
                                    </td>
                                </tr>
                                <?php $i++; ?>
                            @endforeach
                            
                        </tbody>
                    </table>
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
    // ------------------------------------------------------------------
    // 1. ฟังก์ชันคำนวณค่าน้ำ (Clean Version)
    // ------------------------------------------------------------------
    function calculateRow(water_used, pricing_type, fixed_price, tiers_json, vat_rate, reserve_price) {
        let safe_water = isNaN(parseFloat(water_used)) ? 0 : parseFloat(water_used);
        let water_cost = 0;

        // ถ้าใช้น้ำติดลบ ให้คิดราคาเนื้อน้ำเป็น 0
        if (safe_water < 0) {
            water_cost = 0;
        } else {
            // Case 1: แบบขั้นบันได (Progressive) 
            // เช็คว่ามีข้อมูล Tiers และไม่ได้บังคับเป็น Fixed (pricing_type != 1)
            if (tiers_json && Array.isArray(tiers_json) && tiers_json.length > 0 && pricing_type != 1) {
                
                let remaining_water = safe_water;
                
                // วนลูป Tiers (เรียงลำดับมาแล้วจาก PHP)
                for (let i = 0; i < tiers_json.length; i++) {
                    if (remaining_water <= 0) break;

                    let tier = tiers_json[i];
                    let min = parseFloat(tier.min_units);
                    let max = parseFloat(tier.max_units);
                    let rate = parseFloat(tier.rate_per_unit);

                    // คำนวณช่วงกว้างของ Tier นี้
                    // ถ้า max เป็น 0 ให้ถือว่าเป็น Infinity (ขั้นสุดท้าย)
                    let range_size = (max > 0) ? (max - min + (min === 0 ? 0 : 1)) : Infinity;

                    // ปรับจูน Range ตาม Logic ทั่วไป (เช่น 0-10 คือ 10 หน่วย)
                    if(min === 0 && max === 10) range_size = 10;
                    else if(max > 0) range_size = max - min + 1;

                    // จำนวนหน่วยที่จะคิดเงินใน Tier นี้
                    let units_in_tier = Math.min(remaining_water, range_size);
                    
                    water_cost += units_in_tier * rate;
                    remaining_water -= units_in_tier;
                }

            } else {
                // Case 2: แบบเหมาจ่าย (Fixed Rate) หรือไม่มี Tiers
                // console.log('Calculation Mode: Fixed Rate');
                water_cost = safe_water * parseFloat(fixed_price);
            }
        }

        let vat_multiplier = (vat_rate > 1) ? (vat_rate / 100) : vat_rate;
        let vat_amount = water_cost * vat_multiplier;
        let total = water_cost + vat_amount + parseFloat(reserve_price);

        return {
            paid: water_cost.toFixed(2),
            vat: vat_amount.toFixed(2),
            total: total.toFixed(2)
        };
    }

    function del(id) {
        if(confirm('ต้องการลบข้อมูลใช่หรือไม่ !!!')) {
            window.location.href = `/invoice/delete/${id}/ลบ`;
        }
    }

    $(document).ready(function() {
        
        // ------------------------------------------------------------------
        // 2. DataTables Init
        // ------------------------------------------------------------------
        var table = $('.datatable').DataTable({
            "destroy": true, 
            "pagingType": "listbox",
            "lengthMenu": [[10, 25, 50, 150, -1], [10, 25, 50, 150, "ทั้งหมด"]],
            "language": {
                "search": "ค้นหา:",
                "lengthMenu": "แสดง _MENU_ แถว",
                "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
                "infoEmpty": "แสดง 0 ถึง 0 จาก 0 แถว",
                "paginate": {}
            }
        });

        // แก้ไขข้อความ Paging
        $('.paginate_page').text('หน้า');
        let val = $('.paginate_of').text();
        $('.paginate_of').text(val.replace('of', 'จาก'));

        // Header Search Setup
        if ($('.datatable thead tr').length < 2) {
            $('.datatable thead tr').clone().appendTo('.datatable thead');
            $('.datatable thead tr:eq(1) th').each(function(index) {
                $(this).removeClass('sorting sorting_asc');
                if (index > 0 && index < 5) {
                    $(this).html(`<input type="text" data-id="${index}" class="col-md-12" id="search_col_${index}" placeholder="ค้นหา" />`);
                } else {
                    $(this).html('');
                }
            });
        }
        $('.datatable .dataTables_filter').remove();

        // Search Event
        $('.datatable thead input[type="text"]').off('keyup').on('keyup', function() {
            let that = $(this);
            var col = parseInt(that.data('id'));
            
            if (this.searchTimeout) clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(function() {
                let val = that.val();
                if (col === 4) {
                    var regexVal = $.fn.dataTable.util.escapeRegex(val);
                    table.column(col).search(regexVal ? '^' + regexVal + '.*$' : '', true, false).draw();
                } else {
                    table.column(col).search(val).draw();
                }
            }, 300);
        });

        // ------------------------------------------------------------------
        // 3. Logic การคำนวณ (Events)
        // ------------------------------------------------------------------
        
        // 3.1 Keyup: คำนวณ Real-time
        $(document).on('keyup', '.currentmeter, .lastmeter', function() {
            try {
                let id = $(this).data('id');
                let currentInput = $('#currentmeter' + id);
                let lastInput = $('#lastmeter' + id);
                
                let currentmeter = parseFloat(currentInput.val()) || 0;
                let lastmeter = parseFloat(lastInput.val()) || 0;
                let water_used = currentmeter - lastmeter;

                // ดึง Config
                let pricing_type = currentInput.data('pricing-type');
                let price_per_unit = parseFloat(currentInput.data('price')) || 0;
                let tiers_data = currentInput.data('tiers'); 
                let vat_rate = parseFloat(currentInput.data('vat')) || 0;
                let reserve_price = parseFloat(currentInput.data('reserve')) || 0;

                // แสดงผลหน่วยน้ำ
                let waterUsedField = $('#water_used_net' + id);
                waterUsedField.val(water_used);

                // CSS เตือนถ้าติดลบ
                if (water_used < 0) {
                    waterUsedField.addClass('input-error');
                } else {
                    waterUsedField.removeClass('input-error');
                }

                // เรียกฟังก์ชันคำนวณ
                const result = calculateRow(water_used, pricing_type, price_per_unit, tiers_data, vat_rate, reserve_price);

                $('#paid' + id).val(result.paid);
                $('#vat' + id).val(result.vat);
                $('#total' + id).val(result.total);
                $('#meter_reserve_price' + id).val(reserve_price.toFixed(2));

            } catch (error) {
                console.error("Calculation Error:", error);
            }
        });

        // 3.2 Change: แจ้งเตือนความผิดปกติ
        $(document).on('change', '.currentmeter, .lastmeter', function() {
            let id = $(this).data('id');
            let currentInput = $('#currentmeter' + id);
            let lastInput = $('#lastmeter' + id);

            let currentmeter = parseFloat(currentInput.val()) || 0;
            let lastmeter = parseFloat(lastInput.val()) || 0;
            let water_used = currentmeter - lastmeter;

            if (water_used < 0) {
                alert('❌ ผิดพลาด: จำนวนการใช้น้ำติดลบไม่ได้');
                setTimeout(() => currentInput.focus(), 100);
                return;
            }

            if (lastmeter > 0 && currentmeter > (lastmeter * 5)) {
                 let isConfirmed = confirm(`⚠️ แจ้งเตือน: มิเตอร์ปัจจุบัน (${currentmeter}) มากกว่าครั้งก่อน (${lastmeter}) ถึง 5 เท่า\nยืนยันข้อมูลนี้หรือไม่?`);
                 if (!isConfirmed) {
                    setTimeout(() => currentInput.focus().select(), 100);
                    currentInput.addClass('input-warning');
                 } else {
                    currentInput.removeClass('input-warning');
                 }
            }
        });

        // ------------------------------------------------------------------
        // 4. Child Row (User Details)
        // ------------------------------------------------------------------
        $(document).on('click', '.username', function() {
            let user_id = $(this).data('user_id');
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Loading State
                row.child('<div class="text-center p-2"><i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...</div>').show();
                tr.addClass('shown');

                $.get(`/api/users/user/${user_id}`)
                    .done(function(data) {
                        row.child(format(data)).show();
                    })
                    .fail(function() {
                        row.child('<div class="text-center text-danger p-2">โหลดข้อมูลไม่สำเร็จ</div>').show();
                    });
            }
        });

        function format(d) {
            let text = `<table class="table table-striped table-sm bg-white">
                    <thead><tr class="bg-light"><th>รอบบิล</th><th>วันที่</th><th>ยอดเดิม</th><th>ยอดใหม่</th><th>หน่วยที่ใช้</th><th>จำนวนเงิน</th><th>สถานะ</th></tr></thead><tbody>`;
            
            if(d[0] && d[0].usermeterinfos && d[0].usermeterinfos.invoice) {
                d[0].usermeterinfos.invoice.forEach(element => {
                    let _status = element.status === 'paid' 
                        ? '<span class="badge badge-success">จ่ายแล้ว</span>' 
                        : '<span class="badge badge-warning">ค้างชำระ</span>';
                    
                    if (element.status !== 'init') {
                        text += `<tr>
                            <td>${element.invoice_period?.inv_p_name || '-'}</td>
                            <td>${element.updated_at_th || '-'}</td>
                            <td>${element.lastmeter}</td>
                            <td>${element.currentmeter}</td>
                            <td>${element.currentmeter - element.lastmeter}</td>
                            <td>${element.totalpaid}</td>
                            <td>${_status}</td>
                        </tr>`;
                    }
                });
            } else {
                text += `<tr><td colspan="7" class="text-center text-muted">ไม่พบประวัติย้อนหลัง</td></tr>`;
            }
            text += `</tbody></table>`;
            return text;
        }

        // Check Screen Size
        let screenW = window.screen.availWidth;
        if (screenW < 860) {
            $('#web_app').addClass('hidden');
            $('#mobile').removeClass('hidden');
        } else {
            $('#web_app').removeClass('hidden');
            $('#mobile').addClass('hidden');
        }
    });
</script>
@endsection