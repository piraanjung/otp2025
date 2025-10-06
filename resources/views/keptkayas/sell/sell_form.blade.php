    @extends('layouts.keptkaya')

    @section('content')
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">บันทึกการขายขยะ</h1>
                <a href="{{ route('keptkayas.purchase.select_user') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i> กลับหน้าหลัก
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <h6>โปรดแก้ไขข้อผิดพลาดดังต่อไปนี้:</h6>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('keptkayas.sell.store') }}" method="POST">
                @csrf
                <div class="row">
                    {{-- Sell Transaction Header --}}
                    <div class="card mb-3 shadow-sm col-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">ข้อมูลการขาย</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="shop_name" class="form-label">ชื่อร้านรับซื้อ:</label>
                                    <select name="shop_name" id="shop_name" class="form-control" required>
                                        <option value="">เลือก..</option>
                                        @foreach ($shops as $shop)
                                            <option value="{{ $shop->id }}"> {{ $shop->shop_name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-md-12">
                                    <label for="sell_date" class="form-label">วันที่ขาย:</label>
                                    <input type="date" name="sell_date" id="sell_date" class="form-control"
                                        value="{{ old('sell_date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label for="recorder_id" class="form-label">ผู้บันทึก:</label>
                                    <select name="recorder_id" id="recorder_id" class="form-select" required>
                                        <option value="">เลือกผู้บันทึก</option>
                                        @foreach ($staffs as $staff)
                                            <option value="{{ $staff->user_id }}"
                                                {{ old('recorder_id', Auth::id()) == $staff->user_id ? 'selected' : '' }}>
                                                {{ $staff->user->firstname }} {{ $staff->user->lastname }} ({{ $staff->user->username }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sell Details Table (Dynamic) --}}
                    <div class="card mb-9 shadow-sm col-9">
                        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">รายการขยะที่ขาย</h5>
                            <button type="button" class="btn btn-warning btn-sm" id="add-sell-item"><i
                                    class="fa fa-plus-circle me-1"></i> เพิ่มรายการ</button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>รายการขยะ</th>
                                            <th>น้ำหนัก/ปริมาณ</th>
                                            <th>ราคา/หน่วย</th>
                                            <th>เป็นเงิน</th>
                                            <th>หมายเหตุ</th>
                                            <th style="width: 80px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="sell-items-container">
                                        @php $index = 0; @endphp
                                        @if (old('details'))
                                            @foreach (old('details') as $old_index => $detail)
                                                <tr class="sell-item-row">
                                                    <td>
                                                        <select name="details[{{ $old_index }}][kp_recycle_item_id]"
                                                            class="form-select item-select" required>
                                                            <option value="">เลือกรายการขยะ</option>
                                                            @foreach ($recycleItems as $item)
                                                                <option value="{{ $item->id }}"
                                                                    data-units="{{ json_encode($item->items_price_and_point_infos[0]->kp_units_info->id) }}"
                                                                    {{ $detail['kp_recycle_item_id'] == $item->id ? 'selected' : '' }}>
                                                                    {{ $item->kp_itemsname }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" step="0.01"
                                                            name="details[{{ $old_index }}][weight]"
                                                            class="form-control weight-input" value="{{ $detail['weight'] }}"
                                                            required min="0.01"></td>
                                                    <td><input type="number" step="0.01"
                                                            name="details[{{ $old_index }}][price_per_unit]"
                                                            class="form-control price-input"
                                                            value="{{ $detail['price_per_unit'] }}" required min="0">
                                                    </td>
                                                    <td><input type="text" class="form-control amount-display"  name="details[{{ $old_index }}][amount]"
                                                            value="{{ number_format($detail['weight'] * $detail['price_per_unit'], 2) }}"
                                                            readonly></td>
                                                    <td><input type="text" name="details[{{ $old_index }}][comment]"
                                                            class="form-control" value="{{ $detail['comment'] }}"></td>
                                                    <td><button type="button" class="btn btn-danger btn-sm remove-item"><i
                                                                class="fa fa-trash"></i></button></td>
                                                </tr>
                                                @php $index++; @endphp
                                            @endforeach
                                        @else
                                            <tr class="sell-item-row">
                                                <td>
                                                    <select name="details[0][kp_recycle_item_id]"
                                                        class="form-select item-select" required>
                                                        <option value="">เลือกรายการขยะ</option>
                                                        @foreach ($recycleItems as $item)
                                                            <option value="{{ $item->id }}"
                                                                data-units="{{ json_encode($item->items_price_and_point_infos[0]->kp_units_info->id) }}">
                                                                {{ $item->kp_itemsname }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" step="0.01" name="details[0][weight]"
                                                        class="form-control weight-input" value="" required
                                                        min="0.01"></td>
                                                <td><input type="number" step="0.01" name="details[0][price_per_unit]"
                                                        class="form-control price-input" value="" required
                                                        min="0"></td>
                                                <td><input type="text" class="form-control amount-display" value="" name="details[0][amount]"
                                                        readonly></td>
                                                <td><input type="text" name="details[0][comment]" class="form-control"
                                                        value=""></td>
                                                <td><button type="button" class="btn btn-danger btn-sm remove-item"
                                                        disabled><i class="fa fa-trash"></i></button></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">ยอดรวมทั้งหมด:</th>
                                            <th colspan="2" class="total-amount-display">{{ number_format(0, 2) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fa fa-save me-1"></i> บันทึกการขาย
                        </button>
                    </div>
            </form>
    @endsection
    @section('script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('sell-items-container');
                const addButton = document.getElementById('add-sell-item');
                const totalAmountDisplay = document.querySelector('.total-amount-display');
                let itemIndex = container.querySelectorAll('.sell-item-row').length;

                const updateTotals = () => {
                    let totalAmount = 0;
                    container.querySelectorAll('.sell-item-row').forEach(row => {
                        const weight = parseFloat(row.querySelector('.weight-input').value) || 0;
                        const price = parseFloat(row.querySelector('.price-input').value) || 0;
                        const amount = weight * price;
                        row.querySelector('.amount-display').value = amount.toFixed(2);
                        totalAmount += amount;
                    });
                    totalAmountDisplay.textContent = totalAmount.toFixed(2);
                };

                const addRow = () => {
                    const newRow = document.createElement('tr');
                    newRow.classList.add('sell-item-row');
                    newRow.innerHTML = `
                    <td>
                        <select name="details[${itemIndex}][kp_recycle_item_id]" class="form-select item-select" required>
                            <option value="">เลือกรายการขยะ</option>
                            @foreach ($recycleItems as $item)
                                <option value="{{ $item->id }}" data-units="{{ json_encode($item->units) }}">{{ $item->kp_itemsname }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="details[${itemIndex}][weight]" class="form-control weight-input" required min="0.01"></td>
                    <td><input type="number" step="0.01" name="details[${itemIndex}][price_per_unit]" class="form-control price-input" required min="0"></td>
                    <td><input type="text" class="form-control amount-display" name="details[${itemIndex}][amount]" readonly></td>
                    <td><input type="text" name="details[${itemIndex}][comment]" class="form-control"></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fa fa-trash"></i></button></td>
                `;
                    container.appendChild(newRow);
                    itemIndex++;
                    updateTotals();
                };

                const removeRow = (e) => {
                    if (container.querySelectorAll('.sell-item-row').length > 1) {
                        e.target.closest('.sell-item-row').remove();
                        updateTotals();
                    } else {
                        alert('ต้องมีรายการขายอย่างน้อยหนึ่งรายการ');
                    }
                };

                // Event listeners
                addButton.addEventListener('click', addRow);
                container.addEventListener('input', (e) => {
                    if (e.target.classList.contains('weight-input') || e.target.classList.contains(
                            'price-input')) {
                        updateTotals();
                    }
                });
                container.addEventListener('click', (e) => {
                    if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
                        removeRow(e);
                    }
                });

                // Initial calculation
                updateTotals();
            });
        </script>
    @endsection
