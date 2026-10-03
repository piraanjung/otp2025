@extends('inventory.inv_master')

@section('title', 'เบิกพัสดุ')
@section('header_title', 'เบิกใช้งานพัสดุ')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card p-4 text-center mb-3">
                @if($item->image_path)
                    <img src="{{ asset($item->image_path) }}" class="img-fluid rounded mb-3" style="max-height: 150px;">
                @else
                    <i class="material-icons-round display-1 text-muted">inventory_2</i>
                @endif
                <h5 class="fw-bold">{{ $item->firstname }}</h5>
                <p class="text-muted">{{ $item->code }}</p>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>คงเหลือรวม:</span>
                    <span class="fw-bold text-success">{{ number_format($item->total_stock) }} {{ $item->unit }}</span>

                </div>
                {{-- <div class="d-flex justify-content-between">
                    <span>จำนวนขวดที่มี:</span>
                    <span class="fw-bold text-primary">{{ $item->active_bottles_count }} ขวด</span>
                </div> --}}
            </div>
        </div>

        <div class="col-md-8">
            <form action="{{ route('inventory.withdraw.store') }}" method="POST">
                @csrf

                <h5 class="fw-bold mb-3">1. เลือกที่ต้องการหยิบ <small
                        class="text-muted fw-normal">(เรียงตามวันหมดอายุ)</small></h5>

                <div class="row g-2 mb-4" style="max-height: 400px; overflow-y: auto;">
                    @forelse($active_bottles as $bottle)
                        <div class="col-md-6">
                            <label class="card p-3 border h-100 cursor-pointer position-relative select-card">
                                {{-- เปลี่ยนจาก radio เป็น checkbox และเปลี่ยน name เป็น array (detail_ids[]) --}}
                                <input type="checkbox" name="detail_ids[]" value="{{ $bottle->id }}"
                                    class="btn-check select-bottle-checkbox" data-max="{{ $bottle->current_qty }}"
                                    onchange="updateSelectedBottles()">

                                <div class="card-content">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary">LOT: {{ $bottle->lot_number ?? 'N/A' }}</span>
                                        @if($bottle->expire_date)
                                            <small class="text-danger">หมดอายุ:
                                                {{ \Carbon\Carbon::parse($bottle->expire_date)->format('d/m/Y') }}</small>
                                        @endif
                                    </div>
                                    <h4 class="fw-bold text-dark mb-0">
                                        {{ number_format($bottle->current_qty) }} <small
                                            class="fs-6 text-muted">{{ $item->unit }}</small>
                                    </h4>
                                    <small class="text-muted">รับเข้า:
                                        {{ \Carbon\Carbon::parse($bottle->received_date)->format('d/m/Y') }}</small>
                                </div>

                                <div
                                    class="active-border position-absolute top-0 start-0 w-100 h-100 border border-3 border-primary rounded d-none">
                                </div>
                                <i
                                    class="material-icons-round position-absolute top-0 end-0 m-2 text-primary d-none check-icon">check_circle</i>
                            </label>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-danger">ไม่มีสินค้าในสต็อก (หมด)</div>
                        </div>
                    @endforelse
                </div>

                <h5 class="fw-bold mb-3">2. รายละเอียดการเบิก</h5>
                <div class="card p-3 bg-light">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">จำนวนที่เบิก ({{ $item->unit }}) <span
                                    class="text-danger">*</span></label>
                            <input type="number" step="1" name="withdraw_qty" id="withdraw_qty" class="form-control"
                                required min="1">
                            <div class="form-text text-danger" id="max-warning">
                                * เบิกได้สูงสุด <span
                                    id="max-val">{{ number_format($item->total_stock) . " " . $item->unit }}</span>
                                <input type="hidden" id="max-val-hidden" value="{{ number_format($item->total_stock) }}">
                            </div>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">วัตถุประสงค์ <span class="text-danger">*</span></label>
                            <input type="text" name="purpose" class="form-control" placeholder="เช่น ใช้ทำแล็บ 301"
                                required>
                        </div>

                        <div class="col-12">
                            <hr>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">ชื่อผู้เบิก (Requester) <span class="text-danger">*</span></label>

                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="requester_type" id="type_internal"
                                        value="internal" checked onchange="toggleRequesterInput()">
                                    <label class="form-check-label" for="type_internal">บุคลากรภายใน</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="requester_type" id="type_external"
                                        value="external" onchange="toggleRequesterInput()">
                                    <label class="form-check-label" for="type_external">บุคคลภายนอก / อื่นๆ</label>
                                </div>
                            </div>

                            <div class="input-group">
                                <span class="input-group-text bg-white"><i
                                        class="material-icons-round fs-6">person</i></span>
                                <select id="requester_select" class="form-select requester-input"
                                    onchange="syncRequesterName()">
                                    <option value="">-- เลือกรายชื่อ --</option>
                                    @foreach($requesters as $person)
                                        <option value="{{ $person->id }}" {{ Auth::user()->id == $person->id ? 'selected' : '' }}>
                                            {{ $person->firstname .' '.$person->lastname}}
                                            {{-- แสดงข้อมูลเพิ่มในวงเล็บถ้ามี --}}
                                            @if(isset($person->position)) ({{ $person->position }}) @endif
                                        </option>
                                    @endforeach
                                </select>

                                <input type="text" id="requester_text" class="form-control requester-input"
                                    placeholder="ระบุชื่อ-นามสกุล" style="display: none;" oninput="syncRequesterName()">
                            </div>

                            <input type="hidden" name="requester_name" id="final_requester_name"
                                value="{{ Auth::user()->firstname ." ".Auth::user()->lastname }}">

                            <div class="form-text" id="requester_help">เลือกรายชื่อผู้ที่มีสิทธิ์เบิกในระบบ</div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('inventory.items.index') }}" class="btn btn-light me-2">ยกเลิก</a>
                    <button type="submit" class="btn btn-primary btn-material px-4">ยืนยันการเบิก</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Custom Style ให้การ์ดตอนเลือกดูสวย */
        .btn-check:checked+.card-content+.active-border,
        .btn-check:checked+.card-content+.active-border+.check-icon {
            display: block !important;
        }

        .select-card {
            cursor: pointer;
            transition: 0.2s;
        }

        .select-card:hover {
            background-color: #f8f9fa;
        }
    </style>
@endsection

@section('scripts')
    <script>
        // JS: เมื่อเลือกขวด -> อัปเดตค่า Max ที่ช่องกรอกจำนวน
        function updateSelectedBottles() {
            let totalMaxQty = 0;

            // วนลูปเช็คทุก checkbox ที่ผู้ใช้ติ๊กเลือก
            $('.select-bottle-checkbox').each(function () {
                let $card = $(this).closest('.select-card');
                let $border = $card.find('.active-border');
                let $icon = $card.find('.check-icon');

                if ($(this).is(':checked')) {
                    // บวกสะสมจำนวนสูงสุดจากแต่ละขวดที่เลือก
                    totalMaxQty += parseFloat($(this).data('max')) || 0;

                    // แสดงกรอบและไอคอนติ๊กถูกเมื่อเลือก
                    $border.removeClass('d-none');
                    $icon.removeClass('d-none');
                } else {
                    // ซ่อนกรอบและไอคอนเมื่อไม่ได้เลือก
                    $border.addClass('d-none');
                    $icon.addClass('d-none');
                }
            });

            // อัปเดตค่า Max รวม และแสดงผลที่หน้าจอ
            $('#withdraw_qty').val(totalMaxQty);
            // $('#max-val').text(totalMaxQty);

            if (totalMaxQty > 0) {
                $('#max-warning').show();
            } else {
                $('#max-warning').hide();
            }

            // เคลียร์ค่าช่องกรอกจำนวนเบิกเมื่อมีการเปลี่ยนการเลือก เพื่อป้องกันค่าค้างเกินจำนวนรวมใหม่
            // $('#withdraw_qty').val('');
        }

        $('#withdraw_qty').keyup(function () {
            console.log('xx')
            let val = $(this).val()
            let max_val = $('#max-val-hidden').val()
            if (parseInt(val) > parseInt(max_val)) {
                alert('ไม่สามารถเบิกเกินได้')
                $('#max-val').val(0)
            }
        })

        function toggleRequesterInput() {
            const isInternal = document.getElementById('type_internal').checked;
            const selectBox = document.getElementById('requester_select');
            const textBox = document.getElementById('requester_text');
            const helpText = document.getElementById('requester_help');

            if (isInternal) {
                selectBox.style.display = 'block';
                selectBox.setAttribute('required', 'required');

                textBox.style.display = 'none';
                textBox.removeAttribute('required');

                helpText.innerText = 'เลือกรายชื่อผู้ที่มีสิทธิ์เบิกในระบบ';
            } else {
                selectBox.style.display = 'none';
                selectBox.removeAttribute('required');

                textBox.style.display = 'block';
                textBox.setAttribute('required', 'required');
                textBox.focus(); // เอากระพริบไปที่ช่องกรอก

                helpText.innerText = 'ระบุชื่อหน่วยงาน หรือชื่อบุคคลภายนอกที่มารับของ';
            }

            // สลับโหมดแล้วให้อัปเดตค่าทันที
            syncRequesterName();
        }

        function syncRequesterName() {
            const isInternal = document.getElementById('type_internal').checked;
            const finalInput = document.getElementById('final_requester_name');

            if (isInternal) {
                finalInput.value = document.getElementById('requester_select').value;
            } else {
                finalInput.value = document.getElementById('requester_text').value;
            }
        }

        // เรียกทำงานครั้งแรกเผื่อมีการ Refresh หน้าจอแล้ว Browser จำค่า Radio เดิมไว้
        document.addEventListener("DOMContentLoaded", function () {
            toggleRequesterInput();
        });
    </script>
@endsection