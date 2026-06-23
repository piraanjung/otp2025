@extends('layouts.keptkaya')

@section('nav-header', 'การจัดการ')
@section('nav-main', 'สถานะรายเดือน')
@section('nav-current', 'จัดการสถานะรายเดือน')
@section('page-topic', 'จัดการสถานะรายเดือนผู้ใช้งาน')
@section('style')
    <style>
        .month-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .month-card.waste-bank-member {
            background-color: #e6ffe6; /* Light green for waste bank member */
            border-color: #28a745;
        }
        .month-card.annual-collection {
            background-color: #ffe6e6; /* Light red for annual collection */
            border-color: #dc3545;
        }
        .month-card.paid-month {
            background-color: #f0f0f0; /* Grey out paid months when fully paid */
            border-color: #ccc;
            opacity: 0.7;
            cursor: not-allowed;
        }
        .month-card.partially-paid-month { /* New style for partially paid */
            background-color: #fff3cd; /* Light yellow for partially paid */
            border-color: #ffc107;
        }
        .bin-selection {
            display: none; /* Hidden by default */
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #eee;
        }
        .bin-checkbox-group {
            max-height: 150px; /* Limit height for scrollable bin list */
            overflow-y: auto;
            border: 1px solid #eee;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid mt-5">
        <h1>จัดการสถานะรายเดือนสำหรับ: {{ $user->prefix . $user->firstname }} {{ $user->lastname }}</h1>
        <p>รหัสผู้ใช้งาน: {{ $user->user_kaya_infos->id ?? 'N/A' }} | ที่อยู่: {{ $user->address }} หมู่ {{ $user->kp_zone->zone_name ?? 'N/A' }}</p>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

         <div class="mb-3">
            <a href="{{ route('keptkayas.user-monthly-status.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> กลับไปหน้ารายชื่อผู้ใช้งาน</a>
            {{-- ลบส่วนฟอร์มเลือกปีออกไป --}}
            <span class="ms-3 fw-bold">ปีงบประมาณ: {{ $currentFiscalYearStartYear }} - {{ $currentFiscalYearStartYear + 1 }}</span>
        </div>

        <form action="{{ route('keptkayas.user-monthly-status.save', $user) }}" method="POST">
            @csrf
            <input type="hidden" name="fiscal_year_start_year" value="{{ $currentFiscalYearStartYear }}">

            <div class="row">
                @foreach ($months as $index => $month)
                    @php
                        // Key for fetching existing status: month_number-year
                        $statusKey = $month['number'] . '-' . $month['display_year'];
                        $monthlyStatus = $monthlyStatuses->get($statusKey);

                        $currentStatusType = $monthlyStatus ? $monthlyStatus->status_type : 'annual_collection'; // Default to annual_collection
                        $exemptedBins = $monthlyStatus ? $monthlyStatus->binExemptions->pluck('bincode')->toArray() : [];

                        // Check payment status for this month
                        $paidBinsCount = isset($paidInvoices[$statusKey]) ? $paidInvoices[$statusKey]['paid_count'] : 0;
                        $isMonthFullyPaid = ($paidBinsCount > 0 && $paidBinsCount === $totalUserBinsCount);
                        $isMonthPartiallyPaid = ($paidBinsCount > 0 && $paidBinsCount < $totalUserBinsCount);

                        // Determine card class and disabled state
                        $cardClass = '';
                        $disabledAttribute = '';

                        if ($isMonthFullyPaid) {
                            $cardClass = 'paid-month';
                            $disabledAttribute = 'disabled';
                        } elseif ($isMonthPartiallyPaid) {
                            $cardClass = 'partially-paid-month';
                            // Partially paid months are still editable, so no 'disabled' attribute here
                        } else {
                            // Not paid at all
                            $cardClass = $currentStatusType === 'waste_bank_member' ? 'waste-bank-member' : 'annual-collection';
                        }
                    @endphp
                    <div class="col-md-3 col-sm-6 mb-4 month-card-container" data-month-index="{{ $index }}">
                        <div class="month-card {{ $cardClass }}">
                            <div class="card-header">
                                <h4 class="mb-3">{{ $month['name'] }} {{ $month['display_year'] }}
                                    @if($isMonthFullyPaid)
                                        <span class="badge bg-dark float-end">{{ $paidBinsCount }}/{{ $totalUserBinsCount }} ถังชำระแล้ว</span>
                                    @elseif($isMonthPartiallyPaid)
                                        <span class="badge bg-warning float-end">{{ $paidBinsCount }}/{{ $totalUserBinsCount }} ถังชำระบางส่วน</span>
                                    @endif
                                </h4>
                            </div>
                            <div class="card-body">
                                {{-- Hidden inputs for month number and year for this entry --}}
                                <input type="hidden" name="monthly_data[{{ $index }}][month_number]" value="{{ $month['number'] }}">
                                <input type="hidden" name="monthly_data[{{ $index }}][year]" value="{{ $month['display_year'] }}">

                                <div class="mb-3">
                                    <label class="form-label">สถานะ:</label>
                                    <div class="form-check">
                                        <input class="form-check-input status-radio" type="radio"
                                            name="monthly_data[{{ $index }}][status_type]"
                                            id="status_annual_{{ $month['number'] }}_{{ $month['display_year'] }}"
                                            value="annual_collection"
                                            data-month="{{ $month['number'] }}"
                                            data-display-year="{{ $month['display_year'] }}"
                                            {{ $currentStatusType === 'annual_collection' ? 'checked' : '' }}
                                            {{ $disabledAttribute }}>
                                        <label class="form-check-label" for="status_annual_{{ $month['number'] }}_{{ $month['display_year'] }}">
                                            เก็บค่าขยะรายปี
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input status-radio" type="radio"
                                            name="monthly_data[{{ $index }}][status_type]"
                                            id="status_waste_bank_{{ $month['number'] }}_{{ $month['display_year'] }}"
                                            value="waste_bank_member"
                                            data-month="{{ $month['number'] }}"
                                            data-display-year="{{ $month['display_year'] }}"
                                            {{ $currentStatusType === 'waste_bank_member' ? 'checked' : '' }}
                                            {{ $disabledAttribute }}>
                                        <label class="form-check-label" for="status_waste_bank_{{ $month['number'] }}_{{ $month['display_year'] }}">
                                            สมาชิกธนาคารขยะ
                                        </label>
                                    </div>
                                </div>

                                <div class="bin-selection" id="bin_selection_{{ $month['number'] }}_{{ $month['display_year'] }}"
                                    style="display: {{ $currentStatusType === 'waste_bank_member' && !$isMonthFullyPaid ? 'block' : 'none' }};">
                                    <label class="form-label">ยกเว้นการจัดเก็บขยะรายปี (เลือกถัง):</label>
                                    @if ($userBins->isNotEmpty())
                                        <div class="bin-checkbox-group">
                                            @foreach ($userBins as $bin)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="monthly_data[{{ $index }}][exempted_bins][]"
                                                        value="{{ $bin->bincode }}"
                                                        id="bin_{{ $month['number'] }}_{{ $month['display_year'] }}_{{ $bin->bincode }}"
                                                        {{ in_array($bin->bincode, $exemptedBins) ? 'checked' : '' }}
                                                        {{ $disabledAttribute }}>
                                                    <label class="form-check-label" for="bin_{{ $month['number'] }}_{{ $month['display_year'] }}_{{ $bin->bincode }}">
                                                        {{ $bin->bincode }} (ถังที่ {{ $bin->bin_number ?? 'N/A' }})
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">ไม่พบถังขยะสำหรับผู้ใช้งานนี้</p>
                                    @endif
                                </div>
                                {{-- @if($isMonthFullyPaid)
                                    <p class="text-muted mt-2 mb-0 text-center">
                                        <i class="fas fa-info-circle me-1"></i> สถานะนี้ไม่สามารถเปลี่ยนแปลงได้เนื่องจากมีการชำระเงินครบถ้วนแล้ว
                                    </p>
                                @elseif($isMonthPartiallyPaid)
                                    <p class="text-muted mt-2 mb-0 text-center">
                                        <i class="fas fa-info-circle me-1"></i> ชำระบางส่วน ({{ $paidBinsCount }}/{{ $totalUserBinsCount }} ถัง)
                                    </p>
                                @endif --}}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mb-3 text-center">
                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save me-2"></i>บันทึกสถานะทั้งหมด</button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to toggle bin selection visibility
            function toggleBinSelection(monthNumber, displayYear, statusType, isMonthFullyPaid) {
                const binSelectionDiv = document.getElementById(`bin_selection_${monthNumber}_${displayYear}`);
                if (binSelectionDiv) {
                    if (statusType === 'waste_bank_member' && !isMonthFullyPaid) { // Only show if not fully paid
                        binSelectionDiv.style.display = 'block';
                    } else {
                        binSelectionDiv.style.display = 'none';
                        // Optionally uncheck all bins when status changes or month is fully paid
                        const checkboxes = binSelectionDiv.querySelectorAll('input[type="checkbox"]');
                        checkboxes.forEach(checkbox => checkbox.checked = false);
                    }
                }
            }

            // Function to update visibility and disabled state of month cards
            function updateMonthCardVisibility() {
                const monthCardContainers = document.querySelectorAll('.month-card-container');
                let shouldHideSubsequentUnpaidMonths = false; // Flag to control hiding based on 'waste_bank_member'

                console.log("--- Running updateMonthCardVisibility ---");

                monthCardContainers.forEach((container, index) => {
                    const monthName = container.querySelector('h4').textContent.trim(); // For logging
                    const monthCard = container.querySelector('.month-card');
                    const isMonthFullyPaid = monthCard.classList.contains('paid-month'); // Check if card is marked as fully paid
                    const radioButtons = container.querySelectorAll('.status-radio');
                    const checkboxes = container.querySelectorAll('input[type="checkbox"]');
                    const currentSelectedStatusType = container.querySelector('.status-radio:checked')?.value;

                    console.log(`Processing ${monthName} (Index ${index}):`);
                    console.log(`  isMonthFullyPaid: ${isMonthFullyPaid}`);
                    console.log(`  currentSelectedStatusType: ${currentSelectedStatusType}`);
                    console.log(`  shouldHideSubsequentUnpaidMonths (before): ${shouldHideSubsequentUnpaidMonths}`);

                    if (isMonthFullyPaid) {
                        // Fully paid months are always visible and disabled.
                        container.style.display = 'block';
                        radioButtons.forEach(radio => radio.disabled = true);
                        checkboxes.forEach(checkbox => checkbox.disabled = true);
                        shouldHideSubsequentUnpaidMonths = false; // A fully paid month resets the hiding chain
                        console.log(`  Action: Fully Paid, visible. shouldHideSubsequentUnpaidMonths = ${shouldHideSubsequentUnpaidMonths}`);
                    } else {
                        // Unpaid or partially paid months
                        if (shouldHideSubsequentUnpaidMonths) {
                            // If a previous 'waste_bank_member' month has triggered hiding, hide this one
                            container.style.display = 'none';
                            radioButtons.forEach(radio => radio.disabled = true);
                            checkboxes.forEach(checkbox => checkbox.disabled = true);
                            radioButtons.forEach(radio => radio.checked = false); // Uncheck if hidden
                            checkboxes.forEach(checkbox => checkbox.checked = false); // Uncheck if hidden
                            console.log(`  Action: Unpaid/Partially Paid, hidden due to previous waste_bank_member. shouldHideSubsequentUnpaidMonths = ${shouldHideSubsequentUnpaidMonths}`);
                        } else {
                            // This month is visible and editable (unless partially paid, then only status is shown)
                            container.style.display = 'block';
                            // Radio buttons and checkboxes are disabled only if fully paid, otherwise they are enabled
                            radioButtons.forEach(radio => radio.disabled = false);
                            checkboxes.forEach(checkbox => checkbox.disabled = false);


                            // If this month's *selected* status is 'waste_bank_member',
                            // then subsequent unpaid/partially paid months should be hidden.
                            if (currentSelectedStatusType === 'waste_bank_member') {
                                shouldHideSubsequentUnpaidMonths = true;
                                console.log(`  Action: Unpaid/Partially Paid, visible. Status: waste_bank_member. shouldHideSubsequentUnpaidMonths = ${shouldHideSubsequentUnpaidMonths}`);
                            } else {
                                // Status is 'annual_collection' or null (initial state before selection)
                                // In this case, subsequent months should NOT be hidden.
                                shouldHideSubsequentUnpaidMonths = false;
                                console.log(`  Action: Unpaid/Partially Paid, visible. Status: annual_collection. shouldHideSubsequentUnpaidMonths = ${shouldHideSubsequentUnpaidMonths}`);
                            }
                        }
                    }

                    // Re-run toggleBinSelection for the current card to ensure bin selection visibility is correct
                    const monthNumber = radioButtons[0]?.dataset.month;
                    const displayYear = radioButtons[0]?.dataset.displayYear;
                    if (monthNumber && displayYear) {
                        toggleBinSelection(monthNumber, displayYear, currentSelectedStatusType, isMonthFullyPaid);
                    }
                });
                console.log("--- Finished updateMonthCardVisibility ---");
            }

            // Add event listeners to all status radio buttons
            document.querySelectorAll('.status-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    // Original toggle for bin selection
                    const monthNumber = this.dataset.month;
                    const displayYear = this.dataset.displayYear;
                    const statusType = this.value;
                    // Check if the card is marked as fully paid
                    const isMonthFullyPaid = this.closest('.month-card').classList.contains('paid-month');
                    toggleBinSelection(monthNumber, displayYear, statusType, isMonthFullyPaid);

                    // Update card background based on selected status (only if not fully paid)
                    const monthCard = this.closest('.month-card');
                    if (monthCard && !isMonthFullyPaid) {
                        monthCard.classList.remove('waste-bank-member', 'annual-collection', 'partially-paid-month'); // Remove all status classes
                        if (statusType === 'waste_bank_member') {
                             monthCard.classList.add('waste-bank-member');
                        } else {
                             monthCard.classList.add('annual-collection');
                        }
                    }

                    // Call the new visibility logic after any radio button change
                    updateMonthCardVisibility();
                });
            });

            // Initial check on page load for existing statuses
            updateMonthCardVisibility(); // Call once on DOMContentLoaded
        });
    </script>
@endsection
