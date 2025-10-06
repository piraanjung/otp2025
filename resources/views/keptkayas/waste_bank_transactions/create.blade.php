@extends('layouts.keptkaya')

@section('title_page', 'บันทึกรายการรับซื้อขยะ')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-10 col-md-12 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6 class="mb-0">บันทึกรายการรับซื้อขยะรีไซเคิล</h6>
                    <p class="text-sm mb-0 text-muted">สำหรับสมาชิกธนาคารขยะ</p>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                            <span class="alert-text text-white"><strong>สำเร็จ!</strong> {{ session('success') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
                            <span class="alert-text text-white"><strong>เกิดข้อผิดพลาด!</strong> โปรดตรวจสอบข้อมูลที่กรอก</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form id="wastePurchaseForm" action="{{ route('waste_bank_transactions.store') }}" method="POST">
                        @csrf
                        <div class="p-3">
                            <!-- Section: Select Member -->
                            <div class="card card-body mb-4">
                                <h6 class="mb-3">1. ค้นหาและเลือกสมาชิก</h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="input-group input-group-static mb-3">
                                            <label>ค้นหาสมาชิก (ชื่อ, อีเมล, เบอร์โทร)</label>
                                            <select name="" id="memberSearchInput" class="form-control js-example-basic-single">
                                                <option value="">เลือก...</option>
                                                @foreach ($w_users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->user->firstname." ".$user->user->lastname ."|"."W10-".$user->id }}</option>
                                                @endforeach
                                                
                                            </select>
                                            {{-- <input type="text" class="form-control" id="memberSearchInput" placeholder="ค้นหา..."> --}}
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-center">
                                        <button type="button" class="btn bg-gradient-info mb-0 w-100" id="searchMemberBtn">ค้นหา</button>
                                    </div>
                                </div>
                                <div id="memberSearchResults" class="list-group mb-3" style="max-height: 200px; overflow-y: auto; display: none;">
                                    <!-- Search results will be loaded here dynamically by JS -->
                                </div>
                                <div id="selectedMemberDisplay" class="d-flex align-items-center mt-3 p-2 bg-gradient-light border-radius-sm" style="display: none;">
                                    <i class="fas fa-user-check text-success text-lg me-2"></i>
                                    <span class="text-sm font-weight-bold" id="selectedMemberName"></span>
                                    <input type="hidden" name="user_id" id="selectedMemberId" required>
                                    <button type="button" class="btn btn-link text-danger text-gradient px-0 mb-0 ms-auto" id="clearSelectedMember">
                                        <i class="fas fa-times me-1"></i> ยกเลิก
                                    </button>
                                </div>
                                <p class="text-sm text-muted mt-2">
                                    <a href="{{ route('w_users.create') }}" class="text-info text-gradient">หรือลงทะเบียนสมาชิกใหม่</a>
                                </p>
                            </div>

                            <!-- Section: Transaction Details -->
                            <div class="card card-body mb-4">
                                <h6 class="mb-3">2. รายละเอียดธุรกรรม</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-group-static mb-3">
                                            <label>วันที่ทำรายการ</label>
                                            <input type="date" class="form-control" name="transaction_date" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-static mb-3">
                                            <label>หมายเหตุ</label>
                                            <textarea class="form-control" name="notes" rows="1"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Waste Items -->
                            <div class="card card-body mb-4">
                                <h6 class="mb-3">3. รายการขยะที่รับซื้อ</h6>
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0" id="wasteItemsTable">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ประเภทขยะ</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">จำนวน/น้ำหนัก</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">หน่วย</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ราคาซื้อ/หน่วย (฿)</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">รวมจ่าย (฿)</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ราคาขาย/หน่วย (฿)</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">รวมรับ (฿)</th>
                                                <th class="text-secondary opacity-7"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamic rows will be added here -->
                                            <tr class="waste-item-row">
                                                <td>
                                                    <div class="input-group input-group-static my-auto">
                                                        <select class="form-control waste-type-select" name="items[0][waste_type_id]" required>
                                                            <option value="">เลือกประเภท</option>
                                                            @foreach($wasteTypes as $type)
                                                                <option value="{{ $type->id }}"
                                                                    data-kg-mem-price="{{ $type->member_buy_price_per_kg }}"
                                                                    data-kg-fact-price="{{ $type->factory_buy_price_per_kg }}"
                                                                    data-piece-mem-price="{{ $type->member_buy_price_per_piece }}"
                                                                    data-piece-fact-price="{{ $type->factory_buy_price_per_piece }}"
                                                                    data-default-unit="{{ $type->default_unit }}">
                                                                    {{ $type->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-static my-auto">
                                                        <input type="number" step="0.01" class="form-control quantity-input" name="items[0][quantity]" value="0" min="0" required>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-static my-auto">
                                                        <select class="form-control unit-select" name="items[0][unit_used]">
                                                            <option value="kg">กิโลกรัม</option>
                                                            <option value="piece">ชิ้น/ขวด</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" step="0.01" class="form-control member-price-input text-center" name="items[0][member_price_per_unit]" value="0.00" required>
                                                </td>
                                                <td class="text-center">
                                                    <span class="total-member-amount-display font-weight-bold">0.00</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="factory-price-display">0.00</span>
                                                    <input type="hidden" class="factory-price-hidden" name="items[0][factory_price_per_unit]" value="0.00">
                                                </td>
                                                <td class="text-center">
                                                    <span class="total-factory-amount-display font-weight-bold">0.00</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <button type="button" class="btn btn-link text-danger text-gradient px-0 mb-0 remove-item-btn">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn bg-gradient-success btn-sm mt-3" id="addItemBtn">
                                    <i class="fas fa-plus me-1"></i> เพิ่มรายการขยะ
                                </button>
                            </div>

                            <!-- Section: Summary -->
                            <div class="card card-body mb-4">
                                <h6 class="mb-3">4. สรุปยอดรวม</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="text-sm mb-2">ยอดรวมที่ต้องจ่ายให้สมาชิก: <span class="font-weight-bold text-primary text-lg" id="grandTotalMember">0.00 ฿</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-sm mb-2">ยอดรวมที่คาดว่าจะได้รับจากโรงงาน: <span class="font-weight-bold text-success text-lg" id="grandTotalFactory">0.00 ฿</span></p>
                                    </div>
                                    <div class="col-12">
                                        <p class="text-sm mb-0">กำไร/ขาดทุนโดยประมาณ: <span class="font-weight-bold text-info text-lg" id="estimatedProfit">0.00 ฿</span></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn bg-gradient-primary me-2">บันทึกรายการ</button>
                                <a href="{{ route('waste_bank_transactions.index') }}" class="btn bg-gradient-secondary">ยกเลิก</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
   
    let itemCounter = 0; // To keep track of dynamic row indices
    // $(document).on('click','#xxx',function(){
    //     console.log('xxx')
    // })
    document.addEventListener('DOMContentLoaded', function() {
        // --- Member Search & Selection Logic ---
        const memberSearchInput = document.getElementById('memberSearchInput');
        const searchMemberBtn = document.getElementById('searchMemberBtn');
        const memberSearchResults = document.getElementById('memberSearchResults');
        const selectedMemberDisplay = document.getElementById('selectedMemberDisplay');
        const selectedMemberName = document.getElementById('selectedMemberName');
        const selectedMemberId = document.getElementById('selectedMemberId');
        const clearSelectedMemberBtn = document.getElementById('clearSelectedMember');

        searchMemberBtn.addEventListener('click', function() {
            const query = memberSearchInput.value.trim();
            // if (query.length < 2) { // Require at least 2 characters for search
            //     memberSearchResults.innerHTML = '<p class="list-group-item text-muted">โปรดกรอกอย่างน้อย 2 ตัวอักษร</p>';
            //     memberSearchResults.style.display = 'block';
            //     return;
            // }

            // Perform AJAX search for users
            // This URL needs to be implemented in your UserController (e.g., /users/search)
            fetch(`/w_users/search/${query}`)
                .then(response => response.json())
                .then(users => {
                    memberSearchResults.innerHTML = '';
                    console.log('users.length',Object.keys(users).length)
                    if (Object.keys(users).length > 0) {
                        console.log('users',users)
                        users.forEach(user => {
                            const a = document.createElement('a');
                            a.href = "#";
                            a.classList.add('list-group-item', 'list-group-item-action');
                            a.dataset.userId = user.id;
                            a.dataset.userName = `${user.user.prefix || ''} ${user.user.firstname} ${user.user.lastname}`;
                            a.textContent = `${user.user.prefix || ''} ${user.user.firstname} ${user.user.lastname} (${user.user.email || user.user.phone})`;
                            memberSearchResults.appendChild(a);
                        });
                    } else {
                        const p = document.createElement('p');
                        p.classList.add('list-group-item', 'text-muted');
                        p.textContent = 'ไม่พบสมาชิก';
                        memberSearchResults.appendChild(p);
                    }
                    memberSearchResults.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error searching members:', error);
                    memberSearchResults.innerHTML = '<p class="list-group-item text-danger">เกิดข้อผิดพลาดในการค้นหา</p>';
                    memberSearchResults.style.display = 'block';
                });
        });

        memberSearchResults.addEventListener('click', function(e) {
            e.preventDefault();
            const target = e.target.closest('.list-group-item');
            if (target && target.dataset.userId) {
                selectedMemberId.value = target.dataset.userId;
                selectedMemberName.textContent = target.dataset.userName;
                selectedMemberDisplay.style.display = 'flex';
                memberSearchResults.style.display = 'none';
                memberSearchInput.value = ''; // Clear search input
            }
        });

        clearSelectedMemberBtn.addEventListener('click', function() {
            selectedMemberId.value = '';
            selectedMemberName.textContent = '';
            selectedMemberDisplay.style.display = 'none';
        });

        // --- Dynamic Waste Item Rows Logic ---
        const wasteItemsTableBody = document.querySelector('#wasteItemsTable tbody');
        const addItemBtn = document.getElementById('addItemBtn');

        // Function to create a new waste item row
        function createWasteItemRow() {
            itemCounter++; // Increment counter for new row
            const newRow = document.createElement('tr');
            newRow.classList.add('waste-item-row');
            newRow.innerHTML = `
                <td>
                    <div class="input-group input-group-static my-auto">
                        <select class="form-control waste-type-select" name="items[${itemCounter}][waste_type_id]" required>
                            <option value="">เลือกประเภท</option>
                            @foreach($wasteTypes as $type)
                                <option value="{{ $type->id }}"
                                    data-kg-mem-price="{{ $type->member_buy_price_per_kg }}"
                                    data-kg-fact-price="{{ $type->factory_buy_price_per_kg }}"
                                    data-piece-mem-price="{{ $type->member_buy_price_per_piece }}"
                                    data-piece-fact-price="{{ $type->factory_buy_price_per_piece }}"
                                    data-default-unit="{{ $type->default_unit }}">
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-static my-auto">
                        <input type="number" step="0.01" class="form-control quantity-input" name="items[${itemCounter}][quantity]" value="0" min="0" required>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-static my-auto">
                        <select class="form-control unit-select" name="items[${itemCounter}][unit_used]">
                            <option value="kg">กิโลกรัม</option>
                            <option value="piece">ชิ้น/ขวด</option>
                        </select>
                    </div>
                </td>
                <td class="text-center">
                    <input type="number" step="0.01" class="form-control member-price-input text-center" name="items[${itemCounter}][member_price_per_unit]" value="0.00" required>
                </td>
                <td class="text-center">
                    <span class="total-member-amount-display font-weight-bold">0.00</span>
                </td>
                <td class="text-center">
                    <span class="factory-price-display">0.00</span>
                    <input type="hidden" class="factory-price-hidden" name="items[${itemCounter}][factory_price_per_unit]" value="0.00">
                </td>
                <td class="text-center">
                    <span class="total-factory-amount-display font-weight-bold">0.00</span>
                </td>
                <td class="align-middle text-center">
                    <button type="button" class="btn btn-link text-danger text-gradient px-0 mb-0 remove-item-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
            wasteItemsTableBody.appendChild(newRow);
            attachRowEventListeners(newRow); // Attach event listeners to the new row
        }

        // Function to attach event listeners to a row
        function attachRowEventListeners(row) {
            const wasteTypeSelect = row.querySelector('.waste-type-select');
            const quantityInput = row.querySelector('.quantity-input');
            const unitSelect = row.querySelector('.unit-select');
            const memberPriceInput = row.querySelector('.member-price-input');
            const removeItemBtn = row.querySelector('.remove-item-btn');

            wasteTypeSelect.addEventListener('change', function() {
                updatePricesAndUnit(row);
                calculateRowTotals(row);
            });
            quantityInput.addEventListener('input', function() {
                calculateRowTotals(row);
            });
            unitSelect.addEventListener('change', function() {
                updatePricesAndUnit(row); // Update prices based on new unit
                calculateRowTotals(row);
            });
            memberPriceInput.addEventListener('input', function() {
                calculateRowTotals(row);
            });
            removeItemBtn.addEventListener('click', function() {
                row.remove();
                updateOverallTotals(); // Recalculate totals after removing a row
            });
        }

        // Function to update prices and default unit when waste type changes
        function updatePricesAndUnit(row) {
            const wasteTypeSelect = row.querySelector('.waste-type-select');
            const selectedOption = wasteTypeSelect.options[wasteTypeSelect.selectedIndex];
            const unitSelect = row.querySelector('.unit-select');
            const memberPriceInput = row.querySelector('.member-price-input');
            const factoryPriceDisplay = row.querySelector('.factory-price-display');
            const factoryPriceHidden = row.querySelector('.factory-price-hidden');

            const selectedWasteTypeId = wasteTypeSelect.value;
            // Get waste type data from the options' data attributes
            const kgMemPrice = parseFloat(selectedOption.dataset.kgMemPrice || 0);
            const kgFactPrice = parseFloat(selectedOption.dataset.kgFactPrice || 0);
            const pieceMemPrice = parseFloat(selectedOption.dataset.pieceMemPrice || 0);
            const pieceFactPrice = parseFloat(selectedOption.dataset.pieceFactPrice || 0);
            const defaultUnit = selectedOption.dataset.defaultUnit || 'kg';

            if (selectedWasteTypeId) {
                unitSelect.value = defaultUnit; // Set default unit

                // Update prices based on the default unit
                if (defaultUnit === 'kg') {
                    memberPriceInput.value = kgMemPrice.toFixed(2);
                    factoryPriceDisplay.textContent = kgFactPrice.toFixed(2);
                    factoryPriceHidden.value = kgFactPrice.toFixed(2);
                } else if (defaultUnit === 'piece') {
                    memberPriceInput.value = pieceMemPrice.toFixed(2);
                    factoryPriceDisplay.textContent = pieceFactPrice.toFixed(2);
                    factoryPriceHidden.value = pieceFactPrice.toFixed(2);
                }
            } else {
                // Reset if no waste type selected
                memberPriceInput.value = '0.00';
                factoryPriceDisplay.textContent = '0.00';
                factoryPriceHidden.value = '0.00';
            }
        }


        // Function to calculate totals for a single row
        function calculateRowTotals(row) {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const memberPrice = parseFloat(row.querySelector('.member-price-input').value) || 0;
            const factoryPrice = parseFloat(row.querySelector('.factory-price-hidden').value) || 0; // Use hidden input for factory price

            const totalMemberAmount = quantity * memberPrice;
            const totalFactoryAmount = quantity * factoryPrice;

            row.querySelector('.total-member-amount-display').textContent = totalMemberAmount.toFixed(2);
            row.querySelector('.total-factory-amount-display').textContent = totalFactoryAmount.toFixed(2);

            updateOverallTotals(); // Recalculate grand totals
        }

        // Function to update overall totals
        function updateOverallTotals() {
            let grandTotalMember = 0;
            let grandTotalFactory = 0;

            document.querySelectorAll('.waste-item-row').forEach(row => {
                grandTotalMember += parseFloat(row.querySelector('.total-member-amount-display').textContent) || 0;
                grandTotalFactory += parseFloat(row.querySelector('.total-factory-amount-display').textContent) || 0;
            });

            const estimatedProfit = grandTotalFactory - grandTotalMember;

            document.getElementById('grandTotalMember').textContent = grandTotalMember.toFixed(2) + ' ฿';
            document.getElementById('grandTotalFactory').textContent = grandTotalFactory.toFixed(2) + ' ฿';
            document.getElementById('estimatedProfit').textContent = estimatedProfit.toFixed(2) + ' ฿';

            // Change color of profit based on value
            const profitElement = document.getElementById('estimatedProfit');
            if (estimatedProfit > 0) {
                profitElement.classList.remove('text-danger', 'text-info');
                profitElement.classList.add('text-success');
            } else if (estimatedProfit < 0) {
                profitElement.classList.remove('text-success', 'text-info');
                profitElement.classList.add('text-danger');
            } else {
                profitElement.classList.remove('text-success', 'text-danger');
                profitElement.classList.add('text-info');
            }
        }

        // Add event listener to "Add Item" button
        addItemBtn.addEventListener('click', createWasteItemRow);

        // Initial setup for the first row
        attachRowEventListeners(wasteItemsTableBody.querySelector('.waste-item-row'));
        updateOverallTotals(); // Calculate totals on initial load
    });
</script>
@endsection
