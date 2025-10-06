@extends('layouts.keptkaya')

@section('nav-payment')
    active
@endsection
@section('nav-header')
    จัดการใบเสร็จรับเงิน
@endsection
@section('nav-main')
    <a href="{{ route('keptkayas.kp_payment.index') }}"> รับชำระค่าน้ำประปา</a>
@endsection
@section('nav-current')
    รายชื่อผู้ใช้น้ำประปาที่ยังไม่ได้ชำระค่าน้ำประปา
@endsection
@section('page-topic')
    รายชื่อผู้ใช้น้ำประปาที่ยังไม่ได้ชำระค่าน้ำประปา
@endsection

@section('style')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.dataTables.css">
    {{-- Font Awesome (if not already included by AdminLTE) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* General styling for better look and feel, similar to Soft UI */
        body {
            background-color: #f8f9fa;
            /* Light background for the page */
        }

        .card {
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: #fff;
            border-bottom: none;
            /* Remove default border */
            padding: 1.5rem 1.5rem 0.5rem 1.5rem;
            /* Adjust padding */
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #344767;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-check-input {
            margin-top: 0.35rem;
            /* Align checkbox better */
        }

        .form-check-label {
            font-size: 0.875rem;
            /* Smaller font for labels */
            color: #67748e;
            font-weight: 600;
            margin-left: 0.25rem;
        }

        .text-sm {
            font-size: 0.875rem !important;
        }

        .fw-bolder {
            font-weight: 700 !important;
            color: #344767;
            /* Stronger color for important text */
        }

        .btn-outline-danger {
            border-color: #ea0606;
            color: #ea0606;
        }

        .btn-outline-danger:hover {
            background-color: #ea0606;
            color: #fff;
        }

        /* DataTables specific styling */
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ced4da;
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            color: #495057;
            box-shadow: none;
            outline: none;
            transition: all 0.2s ease-in-out;
        }

        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: #007bff;
            /* Bootstrap primary color */
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.5rem;
            margin: 0 0.25rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            color: #67748e !important;
            border: 1px solid transparent;
            transition: all 0.2s ease-in-out;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #e9ecef !important;
            border-color: #e9ecef !important;
            color: #344767 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            color: #adb5bd !important;
            pointer-events: none;
        }


        .table thead th {
            background-color: #f8f9fa;
            /* Light background for table header */
            color: #67748e;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            /* Smaller font for table headers */
            border-bottom: 1px solid #e9ecef;
            padding: 0.75rem 1rem;
            /* Adjusted padding */
        }

        .table tbody td {
            color: #344767;
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-top: 1px solid #e9ecef;
        }

        /* Custom Styles for Subzone Checkboxes */
        .subzone-item {
            display: flex;
            align-items: center;
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            padding: 0.75rem 0.5rem;
            margin-top: 0.5rem;
            transition: all 0.2s ease-in-out;
            background-color: #fff;
        }

        .subzone-item:hover {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.07);
        }

        .subzone-item .col-1 {
            padding-right: 0.5rem;
        }

        .subzone-item .text-start {
            margin-bottom: 0.25rem;
        }

        /* Specific styles for the bin section in table */
        .card.collapsed-card .card-header {
            background-color: #f8f9fa;
            color: #344767;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-bottom: none;
        }

        .card.collapsed-card .card-tools .btn-tool {
            padding: 0.25rem 0.5rem;
            margin: 0 !important;
            border-radius: 0.375rem;
        }

        .card.collapsed-card .card-body {
            padding: 0.5rem;
            border-top: 1px solid #e9ecef;
            background-color: #fff;
        }

        .btn-group.btn-group-sm .btn {
            border-radius: 0.375rem !important;
            margin-right: 0.25rem;
        }

        .btn-info {
            background-color: #17c1e8 !important;
            border-color: #17c1e8 !important;
            color: #fff;
        }

        .btn-info:hover {
            background-color: #0d8ca8 !important;
            border-color: #0d8ca8 !important;
        }

        /* Modal specific styles */
        .modal-dialog {
            max-width: 1800px;
            /* Adjust modal width */
        }

        .modal-content {
            border-radius: 0.75rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            border-bottom: none;
            padding: 1.5rem;
            background-color: #fff;
        }

        .modal-title h5 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #344767;
        }

        .modal-body {
            padding: 1.5rem;
            background-color: #f8f9fa;
            /* Slightly different background for modal body */
        }

        /* User profile card in modal */
        .card-profile {
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .profile-user-img {
            border: 3px solid #dee2e6;
            padding: 0;
        }

        .profile-username {
            font-size: 1.3rem;
            font-weight: 700;
            color: #344767;
            margin-top: 0.5rem;
            margin-bottom: 0.25rem;
        }

        .list-group-item {
            border: none;
            /* Remove default list group borders */
            padding: 0.75rem 0;
            font-size: 0.875rem;
            color: #67748e;
            background-color: transparent;
        }

        .list-group-item b {
            color: #344767;
        }

        .list-group-item input[type="text"] {
            border: none;
            background-color: transparent;
            text-align: right;
            padding: 0;
            font-weight: 600;
            color: #344767;
            width: auto;
            /* Adjust width dynamically */
        }

        /* Info boxes in modal (for payment summary) */
        .info-box {
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            background-color: #fff;
            padding: 1rem;
            display: flex;
            align-items: center;
        }

        .info-box-icon {
            font-size: 2rem;
            color: #fff;
            width: 50px;
            height: 50px;
            border-radius: 0.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .info-box-content {
            flex-grow: 1;
        }

        .info-box-text {
            font-size: 0.875rem;
            color: #67748e;
            display: block;
            margin-bottom: 0.25rem;
        }

        .info-box-number {
            font-size: 1.25rem;
            font-weight: 700;
            color: #344767;
        }

        /* Specific info-box background colors */
        .info-box.bg-warning .info-box-icon {
            background-color: #ff9800;
        }

        /* Orange */
        .info-box.bg-info .info-box-icon {
            background-color: #17c1e8;
        }

        /* Cyan */
        .info-box.bg-success .info-box-icon {
            background-color: #4CAF50;
        }

        /* Green */


        /* Payment calculation boxes */
        .card.card-widget.widget-user-2 {
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            background-color: #fff;
        }

        .widget-user-header.bg-warning {
            background-color: #ffc107 !important;
            color: #fff;
            border-radius: 0.75rem 0.75rem 0 0;
            padding: 0.75rem 1rem;
        }

        .widget-user-header.bg-info {
            background-color: #0dcaf0 !important;
            color: #fff;
            border-radius: 0.75rem 0.75rem 0 0;
            padding: 0.75rem 1rem;
        }

        .widget-user-header h5 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .card-footer.p-0 .nav-link {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: #67748e;
            border-bottom: 1px solid #eee;
        }

        .card-footer.p-0 .nav-link:last-child {
            border-bottom: none;
        }

        .card-footer .badge {
            font-size: 0.8rem;
            padding: 0.4em 0.6em;
            border-radius: 0.375rem;
        }

        .input-group .input-group-prepend .input-group-text {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-right: none;
            color: #495057;
            font-size: 0.875rem;
            border-radius: 0.5rem 0 0 0.5rem;
        }

        .input-group .form-control {
            border-radius: 0 0.5rem 0.5rem 0;
            border-left: none;
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
            color: #344767;
            font-weight: 600;
        }

        .form-control[readonly] {
            background-color: #f8f9fa;
        }

        .cash_from_user {
            font-size: 1.1rem;
            font-weight: 700;
            color: #344767;
        }

        .progress-description {
            font-size: 0.875rem;
            color: #495057;
            margin-top: 0.5rem;
        }

        .progress-description h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0;
        }

        .btn-success {
            background-color: #2dce89 !important;
            border-color: #2dce89 !important;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 1rem;
        }

        .btn-success:hover {
            background-color: #26a978 !important;
            border-color: #26a978 !important;
        }

        .hidden {
            display: none !important;
        }
    </style>
@endsection

@section('content')
    <form action="{{ route('keptkayas.kp_payment.index_search_by_suzone') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">ค้นหา</h3>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="check_all" id="check-input-select-all">
                    <label class="form-check-label" for="check-input-select-all">เลือกทั้งหมด</label>
                </div>
                <div class="card-tools">
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-search me-1"></i> ค้นหา
                    </button>
                </div>
            </div>
            <div class="card-body row">
                @foreach ($subzones as $key => $subzone)
                    <div class="col-lg-2 col-md-3 col-sm-4 mt-2">
                        <div class="subzone-item">
                            <div class="col-1">
                                @if (isset($subzone_search_lists))
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="subzone_id_lists[]"
                                            value="{{ $subzone->id }}"
                                            {{ in_array($subzone->id, $subzone_search_lists) ? 'checked' : '' }}>
                                    </div>
                                @else
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="subzone_id_lists[]"
                                            value="{{ $subzone->id }}">
                                    </div>
                                @endif
                            </div>
                            <div class="col-10">
                                <div class="text-start text-sm">{{ $subzone->zone->zone_name }}</div>
                                <div class="text-start text-sm fw-bolder">{{ $subzone->zoneblockname }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </form>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ข้อมูลผู้ใช้น้ำประปาที่ยังไม่ได้ชำระค่าน้ำประปา</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="invoiceTable">
                            <thead>
                                <tr>
                                    <th class="text-center">เลขสมาชิก</th>
                                    <th class="text-center">ชื่อ</th>
                                    <th class="text-center">รหัสถังขยะ</th>
                                    <th class="text-center">บ้านเลขที่</th>
                                    <th class="text-center">หมู่</th>
                                    <th class="text-center">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($use_owed_bins as $userKeptKayaInfo)
                                    <?php $bin_count = collect($userKeptKayaInfo->kp_bins)->count(); ?>
                                    <tr>
                                        <td class="text-center">K000{{ $userKeptKayaInfo->id }}</td>
                                        <td>{{ $userKeptKayaInfo->kp_user->firstname . ' ' . $userKeptKayaInfo->kp_user->lastname }}
                                        </td>
                                        <td class="text-center">
                                            <div class="card card-primary collapsed-card mb-0"> {{-- Removed fixed margin-bottom --}}
                                                <div class="card-header">
                                                    <h3 class="card-title"><span>{{ $bin_count }}</span>&nbsp;<i
                                                            class="fas fa-trash"></i></h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool"
                                                            data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    @foreach ($userKeptKayaInfo->kp_bins as $bin)
                                                        <div class="btn-group btn-group-sm mb-1 me-1"> {{-- Added me-1 for horizontal spacing --}}
                                                            <a href="javascript:void(0)" class="btn btn-info btn-sm bin"
                                                                data-budgetyear_id="{{ $bin->kp_budgetyear_idfk }}"
                                                                data-bincode="{{ $bin->bincode }}">
                                                                <i class="fas fa-trash"></i> {{ $bin->bincode }}
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $bin->kp_user_keptkaya_infos->kp_user->address }}</td>
                                        <td class="text-center">หมู่ 1</td>
                                        <td>{{ $userKeptKayaInfo->comment }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('keptkayas.kp_payment.store') }}" method="post">
        @csrf
        <div class="modal fade" id="modal-success" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document"> {{-- Changed to modal-lg for larger modal --}}
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title" id="exampleModalLabel">
                            <h5 class="font-weight-bolder" ></h5>
                            <span class="text-sm" id="feInputAddress"></span>
                        </div>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card card-primary card-outline card-profile"> {{-- Added card-profile class --}}
                                    <div class="card-body box-profile text-center">
                                        <img class="profile-user-img img-fluid img-circle"
                                            src="{{ asset('soft-ui/assets/img/bruce-mars.jpg') }}"
                                            alt="User profile picture">
                                        <h3 class="profile-username" id="feFirstName">ทองรักษ์ พุฒจันทร์</h3>
                                        <p class="text-muted">สมาชิกเก็บค่าขยะรายปี</p>
                                        <ul class="list-group list-group-unbordered mb-3 text-start"> {{-- Added text-start for list alignment --}}
                                            <li class="list-group-item">
                                                <b>เลขสมาชิก</b> <span class="float-end">
                                                    <input type="text" name="kp_bin_id" id="kp_bin_id" value=""
                                                        readonly>
                                                </span>
                                            </li>
                                            <li class="list-group-item">
                                                <b>รหัสถังขยะ</b> <span class="float-end">
                                                    <input type="text" name="bincode" id="bincode" value=""
                                                        readonly>
                                                </span>
                                            </li>
                                            <li class="list-group-item">
                                                <b>บ้านเลขที่</b> <span class="float-end">7</span>
                                            </li>
                                            <li class="list-group-item">
                                                <b>หมู่</b> <span class="float-end">หมู่ 1</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row" id="month_infos"></div> {{-- This will be populated by JS --}}

                                <div class="row mt-3">
                                    <div class="col-md-4 col-sm-6 col-12">
                                        <div class="card card-widget widget-user-2">
                                            <div class="widget-user-header bg-warning">
                                                <h5 class="mb-0">ยอดค้างชำระ</h5>
                                            </div>
                                            <div class="card-footer p-0">
                                                <ul class="nav flex-column">
                                                    <li class="nav-item">
                                                        <a href="#" class="nav-link">
                                                            จำนวนเงิน <span class="float-end badge bg-primary"
                                                                id="notyetpaid">0.00</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="#" class="nav-link">
                                                            VAT 7% <span class="float-end badge bg-info"
                                                                id="notyetpaid_vat">0.00</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="#" class="nav-link">
                                                            รวมทั้งสิ้น <span class="float-end badge bg-success"
                                                                id="notyetpaid_total">0.00</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    

                                    <div class="col-md-4 col-sm-6 col-12">
                                        <div class="card card-widget widget-user-2">
                                            <div class="widget-user-header bg-info">
                                                <h5 class="mb-0">ต้องการชำระ</h5>
                                            </div>
                                            <div class="card-footer p-2">
                                                <ul class="nav flex-column">
                                                    <li class="nav-item mt-1">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"
                                                                    style="min-width: 100px;">จำนวนเงิน</span>
                                                            </div>
                                                            <input type="text" class="form-control text-end bg-white"
                                                                id="wantpaid" name="wantpaid" readonly>
                                                        </div>
                                                    </li>
                                                    <li class="nav-item mt-1">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"
                                                                    style="min-width: 100px;">VAT</span>
                                                            </div>
                                                            <input type="text" class="form-control text-end bg-white"
                                                                id="wantpaid_vat" name="wantpaid_vat" readonly>
                                                        </div>
                                                    </li>
                                                    <li class="nav-item mt-1">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"
                                                                    style="min-width: 100px;">รวมทั้งสิ้น</span>
                                                            </div>
                                                            <input type="text" class="form-control text-end bg-white"
                                                                id="wantpaid_total" name="wantpaid_total" readonly>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    

                                    <div class="col-md-4 col-sm-12 col-12 d-flex flex-column">
                                        <div class="info-box bg-gradient-success flex-grow-1"> {{-- Added flex-grow-1 --}}
                                            <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                            <div class="info-box-content d-flex flex-column justify-content-center">
                                                <span class="info-box-text">รับเงินมา</span>
                                                <input type="number" step="0.01"
                                                    class="form-control form-control-lg cash_from_user text-end"
                                                    placeholder="0.00">
                                                <div class="progress mt-2">
                                                    <div class="progress-bar" style="width: 100%"></div>
                                                </div>
                                                <span class="progress-description mt-2">
                                                    <div>เงินทอน </div>
                                                    <h3><span id="refund">0.00</span> บาท</h3>
                                                </span>
                                            </div>
                                        </div>
                                        <input type="submit"
                                            class="btn btn-success btn-lg btn-block mt-3 submitbtn hidden"
                                            value="บันทึกการชำระเงิน">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>
    <script>
        let table;

        $(document).ready(function() {
            // Initialize DataTable
            table = new DataTable('#invoiceTable', {
                "lengthMenu": [
                    [10, 25, 50, 150, -1],
                    [10, 25, 50, 150, "ทั้งหมด"]
                ],
                "language": {
                    "search": "ค้นหา:",
                    "lengthMenu": "แสดง _MENU_ แถว",
                    "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
                    "infoEmpty": "แสดง 0 ถึง 0 จาก 0 แถว",
                    "paginate": {
                        "previous": "ก่อนหน้า",
                        "next": "ถัดไป",
                    },
                    "zeroRecords": "ไม่พบข้อมูลที่ตรงกับการค้นหา"
                },
                "responsive": true,
                "autoWidth": false,
                "orderCellsTop": true,
                "fixedHeader": true,
               "columnDefs": [
                    { "orderable": false, "targets": [0, 1, 2] } // ปิดการเรียงลำดับคอลัมน์ Index 2 (รหัสถังขยะ) และ Index 5 (หมายเหตุ)
                    // คุณสามารถเพิ่ม Index คอลัมน์อื่นๆ ที่ต้องการปิดการเรียงลำดับได้ใน targets array เช่น [0, 2, 5]
                    // Index คอลัมน์: 0=เลขสมาชิก, 1=ชื่อ, 2=รหัสถังขยะ, 3=บ้านเลขที่, 4=หมู่, 5=หมายเหตุ
                ],
                "orderCellsTop": true, // <-- สำคัญ: ทำให้ DataTables รู้ว่ามี header แถวบน 2 แถว
                "fixedHeader": true,    // <-- (Optional) ถ้าต้องการให้ header search เลื่อนตาม scroll
            });

            // Pre-check "select all" on load if page is 'index'
            if ('{{ $page ?? '' }}' === 'index') {
                $('#check-input-select-all').prop('checked', true);
                $('.form-check-input').prop('checked', true);
            }

            // Handle "Select All" checkbox for subzones
            $('#check-input-select-all').on('change', function() {
                $('.form-check-input[name="subzone_id_lists[]"]').prop('checked', $(this).is(':checked'));
            });

            // --- ส่วนเพิ่ม Search Input ในแต่ละคอลัมน์ ---
            // สร้างแถว Header เพิ่มเติมสำหรับช่องค้นหา
            $('#invoiceTable thead tr')
                .clone(true) // โคลนแถวเดิม (รวม event handlers ด้วย)
                .addClass('filters') // เพิ่ม class 'filters' เพื่อระบุว่าเป็นแถว search
                .appendTo('#invoiceTable thead');

            // วนลูปผ่านแต่ละ Header cell ในแถว 'filters'
            $('#invoiceTable thead .filters th').each(function(i) {
                let title = $('#invoiceTable thead th').eq($(this).index()).text(); // ดึงข้อความ Header เดิม
                
                // ไม่ต้องสร้างช่อง search สำหรับคอลัมน์ 'รหัสถังขยะ' และ 'หมายเหตุ' หรืออื่นๆ ที่คุณไม่ต้องการ search
                // ในที่นี้ผมจะสร้างให้ทุกคอลัมน์ ยกเว้น 'รหัสถังขยะ' (Index 2) และ 'หมายเหตุ' (Index 5)
                if (i === 2 || i === 5) { // ตรวจสอบ index ของคอลัมน์ที่ไม่ต้องการ search
                    $(this).html(''); // ทิ้งว่างไว้
                } else {
                    $(this).html('<input type="text" placeholder="ค้นหา ' + title + '" class="form-control form-control-sm"/>'); // สร้าง input

                    // ผูก Event Listener เมื่อผู้ใช้พิมพ์
                    $('input', this).on('keyup change clear', function() {
                        if (table.column(i).search() !== this.value) {
                            table.column(i).search(this.value).draw();
                        }
                    });
                }
            });

            // Handle "Select All" checkbox for subzones
            $('#check-input-select-all').on('change', function() {
                $('.form-check-input[name="subzone_id_lists[]"]').prop('checked', $(this).is(':checked'));
            });

            // Handle click on individual bin for modal
            $(document).on('click', '.bin', function() {
                let budgetyear_id = $(this).data('budgetyear_id');
                let bincode = $(this).data('bincode');

                // Clear previous data
                $('#month_infos').html('');
                $('#notyetpaid, #notyetpaid_vat, #notyetpaid_total').html('0.00');
                $('#wantpaid, #wantpaid_vat, #wantpaid_total').val('0.00');
                $('.cash_from_user').val('');
                $('#refund').html('0.00');
                $('.submitbtn').addClass('hidden'); // Hide submit button initially
                $('#month_check_all').prop('checked', false);

                $('.modal').modal('show');

                $.get(`/keptkaya/kp_payment/get_kp_invoice/${budgetyear_id}/${bincode}`)
                    .done(function(data) {
                        console.log('API Data:', data);

                        let monthInfoHtml = `
                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="month_check_all">
                                    <label class="form-check-label" for="month_check_all">เลือกทั้งหมด</label>
                                    <input type="hidden" name="budgetyear_id" value="${budgetyear_id}">
                                </div>
                            </div>
                        `;
                        let notPaidSum = 0;

                        // ตรวจสอบว่า data.kp_bins_invoice มีข้อมูลและไม่ว่างเปล่า
                        if (Object.keys(data).length > 0 && data && data.kp_bins_invoice && data.kp_bins_invoice.length > 0) {
                            
                            // กรณีมีข้อมูลใบแจ้งหนี้จาก API
                            $('#kp_bin_id').val(data.kp_u_infos_id);
                            $('#bincode').val(data.bincode);
                            $('#feFirstName').text(data.kp_user.firstname + ' ' + data.kp_user.lastname);
                                console.log('กรณีมีข้อมูลใบแจ้งหนี้จาก API')
                            $('#feInputAddress').text(data.kp_user.address + ' หมู่ 1');

                            data.kp_bins_invoice.forEach(element => {
                                                               
                                let checkedAttr = "";
                                let classAdd = "";
                                let paidStatusText =
                                    `${parseFloat(element.paid).toFixed(2)} บาท`; // Format to 2 decimal places

                                // ตรวจสอบสถานะเพื่อกำหนด checked และ disabled
                                if (element.status === "paid" || element.status ===
                                    "be_tbank") {
                                    checkedAttr =
                                    "checked disabled"; // ทั้ง checked และ disabled
                                    classAdd =
                                    'checked-paid-item'; // คลาสสำหรับ item ที่ชำระแล้ว
                                    if (element.status === "paid") {
                                        paidStatusText =
                                            `<span class='text-sm text-info'>${paidStatusText} (ชำระแล้ว)</span>`;
                                    } else { // status === "be_tbank"
                                        paidStatusText =
                                            `<span class='text-sm text-success'><i class='fa fa-bank'></i> ธนาคารขยะ</span>`;
                                    }
                                } else {
                                    // หากยังไม่ได้ชำระ
                                    notPaidSum += parseFloat(element
                                    .paid); // รวมยอดเฉพาะที่ยังไม่ชำระ
                                    classAdd = 'not-paid-item'; // คลาสสำหรับ item ที่ยังไม่ชำระ
                                }
                                 let monthNumber = element.kp_invoice_periods.month_number+'|'+element.kp_invoice_periods.year_buddhist;
                                 let  InvId = element.status === 'paid' ? element.id : monthNumber
                                    monthInfoHtml += `
                                        <div class="col-md-3 col-sm-6 col-12 mb-3">
                                            <div class="info-box ${classAdd === "checked-paid-item" ? 'bg-gradient-success' : 'bg-gradient-light'}" style="cursor: pointer;">
                                                <span class="info-box-icon">
                                                    <input type="checkbox" name="${data.kp_bins_invoice.status === 'paid' ? 'inv_id[]' : 'new_inv_months[]'}" 
                                                    value="${InvId}"  
                                                    class="form-check-input month_checkbox ${classAdd}" 
                                                    data-id="${element.id}" data-amount="${parseFloat(element.paid).toFixed(2)}" ${checkedAttr}>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">เดือน ${element.kp_invoice_periods.kp_inv_p_name}</span>
                                                    <span class="info-box-number" id="pay_per_month_text${element.id}">${paidStatusText}</span>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                
                                                              

                            });
                        } else {
                            // กรณีไม่มีข้อมูลใบแจ้งหนี้, สร้าง checkbox 12 เดือนเริ่มต้นตามปีงบประมาณ (โค้ดส่วนนี้ยังคงเดิมจากที่เราปรับเมื่อกี้)
                            if (data) {
                                $('#kp_bin_id').val(data.kp_number);
                                $('#bincode').val(data.bincode);
                                $('#feFirstName').text(data.kp_user.firstname + ' ' + data.kp_user.lastname);
                                $('#feInputAddress').text(data.kp_user.address + ' หมู่ 1');
                            } else {
                                $('#kp_bin_id, #bincode').val('');
                                $('#feFirstName').text('ไม่พบข้อมูล');
                                $('#feInputAddress').text('');
                            }

                            const monthNames = [
                                "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
                                "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
                            ];

                            const defaultPayRate = 100.00; // ควรดึงจาก API

                            const currentGregorianYear = new Date().getFullYear();
                            const currentMonth = new Date().getMonth();
                            let fiscalYearBuddhist = (currentMonth >= 9) ? (currentGregorianYear + 1 +
                                543) : (currentGregorianYear + 543);

                            for (let i = 0; i < 12; i++) {
                                let monthIndex = (9 + i) % 12;
                                let displayMonthName = monthNames[monthIndex];

                                let displayGregorianYear;
                                if (monthIndex >= 9) {
                                    displayGregorianYear = fiscalYearBuddhist - 543 - 1;
                                } else {
                                    displayGregorianYear = fiscalYearBuddhist - 543;
                                }
                                let displayBuddhistYear = displayGregorianYear + 543;

                                const dummyId =
                                    `new_${budgetyear_id}_${bincode}_${monthIndex + 1}_${displayBuddhistYear}`;

                                monthInfoHtml += `
                                    <div class="col-md-3 col-sm-6 col-12 mb-3">
                                        <div class="info-box bg-gradient-light" style="cursor: pointer;">
                                            <span class="info-box-icon">
                                                <input type="checkbox" name="new_inv_months[]" value="${monthIndex + 1}|${displayBuddhistYear}" class="form-check-input month_checkbox not-paid-item" data-id="${dummyId}" data-amount="${defaultPayRate.toFixed(2)}">
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">เดือน ${displayMonthName} ปี ${displayBuddhistYear}</span>
                                                <span class="info-box-number" id="pay_per_month_text${dummyId}">${defaultPayRate.toFixed(2)} บาท (ยังไม่ได้ออกบิล)</span>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                notPaidSum += defaultPayRate;
                            }
                        }

                        $('#month_infos').html(monthInfoHtml);

                        // Update summary for not yet paid
                        $('#notyetpaid').html(notPaidSum.toFixed(2));
                        let notPaidVat = (notPaidSum * 0.07);
                        $('#notyetpaid_vat').html(notPaidVat.toFixed(2));
                        $('#notyetpaid_total').html((notPaidSum + notPaidVat).toFixed(2));

                        // Initial calculation for "ต้องการชำระ" based on all unpaid selected by default
                        checkboxClicked();

                        // เมื่อเดือนที่ชำระแล้วถูก disable, เราต้องแน่ใจว่า 'เลือกทั้งหมด' ไม่ได้เลือกเดือนที่ disable
                        // ดังนั้น ให้ uncheck 'เลือกทั้งหมด' ในตอนแรก หากมีเดือนที่ disable อยู่
                        $('#month_check_all').prop('checked', false);
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        console.error("Error fetching invoice data:", textStatus, errorThrown);
                        alert("เกิดข้อผิดพลาดในการดึงข้อมูลใบแจ้งหนี้ กรุณาลองใหม่อีกครั้ง");
                    });
            });
            // Handle keyup on cash_from_user input
            $('.cash_from_user').on('keyup', function() {
                let mustPaidTotal = parseFloat($('#wantpaid_total').val()) || 0;
                let cashFromUser = parseFloat($(this).val()) || 0;
                let cashback = cashFromUser - mustPaidTotal;

                $('#refund').html(cashback.toFixed(2));

                if (cashFromUser === 0 || isNaN(cashFromUser)) {
                    $('#refund').html("0.00");
                    $('.submitbtn').addClass('hidden');
                } else if (cashback >= 0 && mustPaidTotal > 0) {
                    $('.submitbtn').removeClass('hidden');
                } else {
                    $('.submitbtn').addClass('hidden');
                }
            });

            // Handle click on individual month checkbox (only for "not-paid-checkbox")
            $(document).on('change', '.not-paid-item', function() {
                checkboxClicked();
            });

            // Handle "Select All" for months in modal
            $(document).on('change', '#month_check_all', function() {
                let isChecked = $(this).is(':checked');
                $('.not-paid-checkbox').prop('checked', isChecked); // Only check/uncheck unpaid
                checkboxClicked();
            });

            // Close modal functionality
            $('.close').click(function() {
                $('.modal').modal('hide');
            });

            // Function to recalculate payment amounts
            function checkboxClicked() {
                let totalWantPaid = 0;
                $('.not-paid-item:checked').each(function() {
                    let amount = parseFloat($(this).data('amount'));
                    totalWantPaid += amount;
                });
                console.log('totalWantPaid',totalWantPaid)

                let wantPaidVat = (totalWantPaid * 0.07);
                let wantPaidTotal = totalWantPaid + wantPaidVat;

                $('#wantpaid').val(totalWantPaid.toFixed(2));
                $('#wantpaid_vat').val(wantPaidVat.toFixed(2));
                $('#wantpaid_total').val(wantPaidTotal.toFixed(2));

                // Re-calculate refund if cash_from_user has value
                let cashFromUser = parseFloat($('.cash_from_user').val()) || 0;
                let refund = cashFromUser - wantPaidTotal;
                $('#refund').html(refund.toFixed(2));

                // Control submit button visibility
                if (wantPaidTotal > 0 && refund >= 0) {
                    $('.submitbtn').removeClass('hidden');
                } else {
                    $('.submitbtn').addClass('hidden');
                }
            }
        });


    $(document).on('change', '.not-paid-item', function() {
        checkboxClicked();
    });

    // Handle "Select All" for months in modal
    $(document).on('change', '#month_check_all', function() {
        let isChecked = $(this).is(':checked');
        $('.not-paid-item').prop('checked', isChecked); // Only check/uncheck unpaid
        checkboxClicked();
    });

    // Function to recalculate payment amounts
    function checkboxClicked() {
        let totalWantPaid = 0;
        // วนลูปเฉพาะ checkbox ที่มีคลาส 'not-paid-item' และถูกเลือก (checked)
        $('.not-paid-item:checked').each(function() {
            let amount = parseFloat($(this).data('amount'));
            totalWantPaid += amount;
        });

        let wantPaidVat = (totalWantPaid * 0.07);
        let wantPaidTotal = totalWantPaid + wantPaidVat;

        // --- ส่วนนี้คือส่วนสำคัญที่อัปเดตผลรวมที่ต้องการชำระ ---
        $('#wantpaid').val(totalWantPaid.toFixed(2));
        $('#wantpaid_vat').val(wantPaidVat.toFixed(2));
        $('#wantpaid_total').val(wantPaidTotal.toFixed(2));
        // ----------------------------------------------------

        // Re-calculate refund if cash_from_user has value
        let cashFromUser = parseFloat($('.cash_from_user').val()) || 0;
        let refund = cashFromUser - wantPaidTotal;
        $('#refund').html(refund.toFixed(2));

        // Control submit button visibility
        if (wantPaidTotal > 0 && refund >= 0) {
            $('.submitbtn').removeClass('hidden');
        } else {
            $('.submitbtn').addClass('hidden');
        }
    }
    </script>
@endsection
