@extends('inventory.inv_master')

@section('title', 'เบิกพัสดุหลายรายการ')
@section('header_title', 'เบิกใช้งานพัสดุ (ระบบตะกร้าสินค้า)')

@section('content')
<form action="{{ route('inventory.withdraw.store_multiple') }}" method="POST" id="withdrawForm">
    @csrf
    <div class="row">
        <!-- ฝั่งซ้าย: รายการสินค้าในคลังให้เลือก -->
        <div class="col-md-7">
            <div class="card p-3 mb-3">
                <h5 class="fw-bold mb-3">1. ค้นหาและเลือกพัสดุที่ต้องการเบิก</h5>
                
                <!-- ช่องค้นหาพัสดุอย่างง่าย -->
                <div class="mb-3">
                    <input type="text" id="searchItemInput" class="form-control" placeholder="พิมพ์ชื่อพัสดุ หรือรหัสพัสดุเพื่อค้นหา...">
                </div>

                <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                    <table class="table table-hover align-middle" id="itemsTable">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>พัสดุ</th>
                                <th>คงเหลือ</th>
                                <th class="text-center">เลือก</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr class="item-row">
                                    <td>
                                        <div class="fw-bold">
                                            <img src="{{ asset($item->image_path) }}" style="width:40px" alt=""> 
                                            {{ $item->name }}
                                        </div>
                                        <small class="text-muted">{{ $item->code }}  หน่วย: {{ $item->unit }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ number_format($item->total_stock) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-add-item"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}"
                                            data-code="{{ $item->code }}"
                                            data-unit="{{ $item->unit }}"
                                            data-max="{{ $item->total_stock }}">
                                            <i class="material-icons-round fs-6">add</i> เพิ่ม
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ฝั่งขวา: ตะกร้าสินค้าที่เลือก & ฟอร์มบันทึก -->
        <div class="col-md-5">
            <div class="card p-4 shadow-sm mb-3">
                <h5 class="fw-bold mb-3">2. ตะกร้าพัสดุที่เลือกเบิก</h5>

                <div class="table-responsive mb-3" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-sm align-middle" id="cartTable">
                        <thead>
                            <tr>
                                <th>รายการ</th>
                                <th style="width: 100px;">จำนวน</th>
                                <th class="text-end">ลบ</th>
                            </tr>
                        </thead>
                        <tbody id="cartTableBody">
                            <tr id="emptyCartRow">
                                <td colspan="3" class="text-center text-muted py-4">ยังไม่ได้เลือกพัสดุในตะกร้า</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>

                <!-- รายละเอียดการเบิกเพิ่มเติม -->
                <div class="mb-3">
                    <label class="form-label">วัตถุประสงค์การเบิก <span class="text-danger">*</span></label>
                    <input type="text" name="purpose" class="form-control" placeholder="เช่น ใช้ทำแล็บ 301" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">ชื่อผู้เบิก (Requester) <span class="text-danger">*</span></label>
                    <div class="mb-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="requester_type" id="type_internal" value="internal" checked onchange="toggleRequesterInput()">
                            <label class="form-check-label" for="type_internal">บุคลากรภายใน</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="requester_type" id="type_external" value="external" onchange="toggleRequesterInput()">
                            <label class="form-check-label" for="type_external">บุคคลภายนอก</label>
                        </div>
                    </div>
                    <select id="requester_select" class="form-select mb-2" onchange="syncRequesterName()">
                        <option value="">-- เลือกรายชื่อ --</option>
                        @foreach($requesters as $person)
                            <option value="{{ $person->id}}" {{ Auth::user()->id == $person->id ? 'selected' : '' }}>
                                {{ $person->firstname .' '.$person->lastname}}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" id="requester_text" class="form-control mb-2" placeholder="ระบุชื่อ-นามสกุล" style="display: none;" oninput="syncRequesterName()">
                    <input type="hidden" name="requester_name" id="final_requester_name" value="{{ Auth::user()->firstname ." ".Auth::user()->lastname }}">
                </div>

                <div class="text-end mt-3">
                    <a href="{{ route('inventory.items.index') }}" class="btn btn-light me-2">ยกเลิก</a>
                    <button type="submit" class="btn btn-primary px-4" id="submitBtn" disabled>ยืนยันการเบิกทั้งหมด</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // ค้นหาพัสดุในตารางซ้ายแบบ Real-time
    document.getElementById('searchItemInput').addEventListener('keyup', function() {
        let keyword = this.value.toLowerCase();
        let rows = document.querySelectorAll('.item-row');
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(keyword) ? '' : 'none';
        });
    });

    let cart = {};

    // กดปุ่มเพิ่มสินค้าลงตะกร้า
    document.querySelectorAll('.btn-add-item').forEach(button => {
        button.addEventListener('click', function() {
            let id = this.getAttribute('data-id');
            let name = this.getAttribute('data-name');
            let unit = this.getAttribute('data-unit');
            let max = parseFloat(this.getAttribute('data-max'));

            if (max <= 0) {
                alert('สินค้านี้หมดสต็อกแล้ว');
                return;
            }

            if (!cart[id]) {
                cart[id] = { id, name, unit, max, qty: 1 };
            } else {
                if (cart[id].qty < max) {
                    cart[id].qty++;
                } else {
                    alert('จำนวนเกินสต็อกคงเหลือที่มี');
                }
            }
            renderCart();
        });
    });

    // วาดข้อมูลลงในตารางตะกร้าฝั่งขวา
    function renderCart() {
        let tbody = document.getElementById('cartTableBody');
        tbody.innerHTML = '';
        let keys = Object.keys(cart);

        if (keys.length === 0) {
            tbody.innerHTML = `<tr id="emptyCartRow"><td colspan="3" class="text-center text-muted py-4">ยังไม่ได้เลือกพัสดุในตะกร้า</td></tr>`;
            document.getElementById('submitBtn').disabled = true;
            return;
        }

        document.getElementById('submitBtn').disabled = false;

        keys.forEach(id => {
            let item = cart[id];
            let tr = document.createElement('tr');
            console.log('item',item)
            tr.innerHTML = `
                <td>
                    <div class="fw-bold text-truncate" style="max-width: 130px;" title="${item.name}">${item.name}</div>
                    <small class="text-muted">สูงสุด: ${item.max} ${item.unit}</small>
                </td>
                <td>
                    <input type="hidden" name="items[${id}][item_id]" value="${id}">
                    <input type="number" name="items[${id}][qty]" value="${item.qty}" min="1" max="${item.max}" class="form-control form-control-sm" onchange="updateQty('${id}', this.value)">
                </td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm text-danger p-0" onclick="removeItem('${id}')"><i class="material-icons-round fs-6">delete</i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // อัปเดตจำนวนเมื่อแก้ตัวเลขในตะกร้า
    function updateQty(id, val) {
        let qty = parseInt(val);
        if (qty > cart[id].max) {
            alert('เบิกเกินจำนวนสต็อกที่มี (' + cart[id].max + ')');
            cart[id].qty = cart[id].max;
            renderCart();
        } else if (qty < 1 || isNaN(qty)) {
            cart[id].qty = 1;
        } else {
            cart[id].qty = qty;
        }
    }

    // ลบรายการออกจากตะกร้า
    function removeItem(id) {
        delete cart[id];
        renderCart();
    }

    // จัดการฟอร์มผู้เบิก
    function toggleRequesterInput() {
        const isInternal = document.getElementById('type_internal').checked;
        document.getElementById('requester_select').style.display = isInternal ? 'block' : 'none';
        document.getElementById('requester_text').style.display = isInternal ? 'none' : 'block';
        syncRequesterName();
    }

    function syncRequesterName() {
        const isInternal = document.getElementById('type_internal').checked;
        document.getElementById('final_requester_name').value = isInternal ? 
            document.getElementById('requester_select').value : 
            document.getElementById('requester_text').value;
    }
</script>
@endsection