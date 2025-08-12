@extends('layouts.keptkaya')

@section('nav-user')
    active
@endsection
@section('nav-header')
    สมาชิก
@endsection
@section('nav-current')
    {{-- รายชื่อผู้ใช้น้ำประปาที่ยังไม่ได้ชำระค่าเก็บขยะ --}}
@endsection
@section('page-topic')
    รายชื่อสมาชิก
@endsection
@section('style')
    <style>
        /* CSS ที่คุณมีอยู่แล้ว */
        .selected { background-color: lightblue; }
        .displayblock { display: block; }
        .displaynone, .hidden { display: none; }
        .modal-dialog { width: 75rem; margin: 30px auto; }
        .sup { color: blue; }
        .fs-7 { font-size: 0.7rem; }
        .table { border-collapse: collapse; }
        .table thead th {
            padding: 0.55rem 0.5rem;
            text-transform: capitalize;
            letter-spacing: 0;
            border-bottom: 1px solid #e9ecef;
            color: black;
            text-align: center;
        }
        .mt-025 { margin-top: 0.15rem; }
        .input-search-by-title {
            border-radius: 10px 10px;
            height: 1.65rem;
            border: 1px solid #2077cd;
        }
        @media (min-width:568px) {
            .modal {
                --bs-modal-margin: 1.75rem;
                --bs-modal-box-shadow: 0 0.3125rem 0.625rem 0 rgba(0, 0, 0, .12);
            }
            .modal-dialog {
                max-width: 75rem;
                margin-right: auto;
                margin-left: auto;
            }
        }

        /* --- Custom Styles for Soft UI Look (Added/Refined) --- */
        body { background-color: #f8f9fa; }
        .card {
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
            border: none; /* Remove default card border */
        }
        .card-header {
            background-color: #fff;
            border-bottom: none;
            padding: 1.25rem 1.5rem; /* Adjust padding */
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #344767;
            display: flex; /* Make title and toggle icon inline */
            align-items: center; /* Vertically align items */
        }
        .btn-link { /* Style for the toggle button/link */
            font-size: 0.9rem;
            font-weight: 600;
            color: #344767;
            text-decoration: none;
            padding: 0;
            margin-left: 0.5rem;
        }
        .btn-link:hover {
            color: #007bff;
            text-decoration: none;
        }
        .btn-primary {
            background-color: #007bff !important;
            border-color: #007bff !important;
            color: #fff;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }
        .btn-primary:hover {
            background-color: #0056b3 !important;
            border-color: #0056b3 !important;
        }

        /* Subzone Item Styling */
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
        .subzone-item .form-check {
            margin-right: 0.75rem; /* Space between checkbox and text */
            flex-shrink: 0; /* Prevent checkbox from shrinking */
        }
        .subzone-item .text-sm {
            font-size: 0.875rem !important;
        }
        .subzone-item .text-muted {
            color: #6c757d !important;
            margin-bottom: 0.1rem;
            line-height: 1;
        }
        .subzone-item .fw-bolder {
            font-weight: 700 !important;
            color: #344767;
            line-height: 1;
        }

        /* Icon rotation for collapse */
        .rotate-icon {
            transform: rotate(180deg);
            transition: transform 0.3s ease-in-out;
        }
        .rotate-icon.collapsed {
            transform: rotate(0deg);
        }
        /* Specific for bin icon */
        .toggle-bin-icon.collapsed {
            transform: rotate(0deg);
        }
        .toggle-bin-icon {
            transform: rotate(180deg);
            transition: transform 0.3s ease-in-out;
        }


        /* DataTables specific styles */
        #kp_user_table thead .filters th {
            padding: 0.3rem 0.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        #kp_user_table thead .filters input {
            width: 100%;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            box-shadow: none;
            outline: none;
            transition: all 0.2s ease-in-out;
        }
        #kp_user_table thead .filters input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .table thead th {
            vertical-align: bottom;
            padding-bottom: 0.75rem;
        }

        /* Style for bin cards in table */
        .card.card-primary.collapsed-card .card-header {
            background-color: #f8f9fa; /* Light background for table header */
            color: #344767;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-bottom: none;
            border-radius: 0.75rem; /* Make it rounded */
        }
        .card.card-primary.collapsed-card .card-tools .btn-tool {
            padding: 0.25rem 0.5rem;
            margin: 0 !important;
            border-radius: 0.375rem;
        }
        .card.card-primary.collapsed-card .card-body {
            padding: 0.5rem;
            border-top: 1px solid #e9ecef;
            background-color: #fff;
            border-radius: 0 0 0.75rem 0.75rem; /* Only bottom rounded */
        }
        .card.bg-primary {
            background-color: #007bff !important;
            border-color: #007bff !important;
            color: #fff;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem; /* Spacing between cards */
        }
        .card.bg-primary .icon-shape {
            background-color: #fff;
            color: #007bff;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            font-size: 1.2rem;
        }

        /* Added for the individual bin buttons */
        .btn-info {
            background-color: #17c1e8 !important; /* Soft UI Cyan */
            border-color: #17c1e8 !important;
            color: #fff;
        }
        .btn-info:hover {
            background-color: #0d8ca8 !important;
            border-color: #0d8ca8 !important;
        }
    </style>
@endsection

@section('content')
    <a href="{{ route('keptkaya.admin.kp_user.create') }}" class="btn bg-gradient-success mb-3">เพิ่มผู้ใช้งานระบบ</a>

    <form action="{{ route('keptkaya.kp_payment.index_search_by_suzone') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    ค้นหาจากหมู่บ้าน
                    <a class="btn btn-link text-dark p-0 ms-2" data-bs-toggle="collapse" href="#collapseSearchFilters" role="button" aria-expanded="false" aria-controls="collapseSearchFilters">
                        <i class="fas fa-chevron-down ms-1" id="toggleSearchIcon"></i>
                    </a>
                </h3>
                <div class="card-tools">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1" aria-hidden="true"></i> ค้นหา
                    </button>
                </div>
            </div>
            
            <div class="card-body collapse" id="collapseSearchFilters">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="check-input-select-all" id="check-input-select-all">
                            <label class="form-check-label" for="check-input-select-all">เลือกทั้งหมด</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @foreach ($subzones as $key => $subzone)
                        <div class="col-lg-2 col-md-3 col-sm-4 mt-2">
                            <div class="subzone-item">
                                <div class="form-check me-2">
                                    @if (isset($subzone_search_lists))
                                        <input class="form-check-input" type="checkbox" name="subzone_id_lists[]"
                                            value="{{ $subzone->id }}"
                                            {{ in_array($subzone->id, $subzone_search_lists) == true ? 'checked' : '' }}>
                                    @else
                                        <input class="form-check-input" type="checkbox" name="subzone_id_lists[]"
                                            value="{{ $subzone->id }}">
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm text-muted">{{ $subzone->zone->zone_name }}</div>
                                    <div class="text-sm fw-bolder">{{ $subzone->zone_block_name }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </form>


    <form action="{{ route('keptkaya.admin.kp_user.store') }}" method="POST">
        @csrf
        @if ($checkBinsEmpty > 0)
            <input type="submit" value="บันทึก" class="btn btn-info">
        @endif
        <table class="table projects" id="kp_user_table">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 25%">ชื่อ-สกุล</th>
                    <th style="width: 10%">รหัสผู้ใช้งาน</th>
                    <th style="width: 10%">ที่อยู่</th>
                    <th style="width: 10%">หมู่ที่</th>
                    <th style="width: 20%">จำนวนถัง</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                @foreach ($userKeptKayaInfos as $userKeptKayaInfo)
                    <?php $bin_count = collect($userKeptKayaInfo->kp_bins)->count(); ?>
                    <tr>
                        <td>
                            {{ $i }}
                        </td>
                        <td>
                            {{ $userKeptKayaInfo->kp_user->prefix . '' . $userKeptKayaInfo->kp_user->firstname . ' ' . $userKeptKayaInfo->kp_user->lastname }}
                        </td>
                        <td>{{ $userKeptKayaInfo->kp_u_infos_id }}</td>
                        <td>
                            {{ $userKeptKayaInfo->kp_user->address }}
                        </td>
                        {{-- @dd($userKeptKayaInfo->kp_user) --}}
                        <td>
                            
                            {{ $userKeptKayaInfo->kp_user->kp_zone->zone_name }}
                        </td>
                        <td style="width: 20%">
                            @if ($bin_count == 0)
                                {{-- หากไม่มีถังขยะ ให้แสดง Input เดิม --}}
                                <input type="text" name="kp_u_info[{{ $i }}][kp_u_infos_id]"
                                    value="{{ $userKeptKayaInfo->kp_u_infos_id }}">
                                <input type="text" name="kp_u_info[{{ $i }}][kp_u_id]"
                                    value="{{ $userKeptKayaInfo->kp_user_id_fk }}">
                                <input type="text" class="form-control" name="kp_u_info[{{ $i }}][bins]"
                                    value="{{ $bin_count }}">
                            @else
                                {{-- แสดง Card สรุปจำนวนถังพร้อมปุ่ม Toggle --}}
                                <div class="card card-primary mb-0">
                                    <div class="card-header p-2 d-flex justify-content-between align-items-center">
                                        <h3 class="card-title text-sm mb-0">
                                            <i class="fas fa-trash-alt me-1"></i> {{ $bin_count }} ถัง
                                        </h3>
                                        <div class="card-tools">
                                            {{-- ปุ่ม Toggle ที่จะแสดง/ซ่อน รายการ Bincode --}}
                                            <button type="button" class="btn btn-tool" data-bs-toggle="collapse"
                                                data-bs-target="#collapseBins-{{ $userKeptKayaInfo->kp_u_infos_id }}"
                                                aria-expanded="false"
                                                aria-controls="collapseBins-{{ $userKeptKayaInfo->kp_u_infos_id }}">
                                                <i class="fas fa-chevron-down toggle-bin-icon" id="toggleBinIcon-{{ $userKeptKayaInfo->kp_u_infos_id }}"></i>
                                            </button>
                                        </div>
                                    </div>
                                    {{-- ส่วนที่ถูก Collapse: รายการปุ่ม Bincode แต่ละถัง --}}
                                    <div class="card-body p-2 collapse" id="collapseBins-{{ $userKeptKayaInfo->kp_u_infos_id }}">
                                        @foreach ($userKeptKayaInfo->kp_bins as $bin)
                                            <div class="btn-group btn-group-sm mb-1 me-1">
                                                <a href="javascript:void(0)" class="btn btn-info btn-sm bin-button"
                                                    data-budgetyear_id="{{ $bin->kp_budgetyear_idfk }}"
                                                    data-bincode="{{ $bin->bincode }}">
                                                    <i class="fas fa-trash"></i> {{ $bin->bincode }}
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="project-actions text-right">
                            <a class="btn btn-primary btn-sm" href="#">
                                <i class="fas fa-plus"></i> ขอเพิ่มถังขยะ
                            </a>
                            <a class="btn btn-info btn-sm" href="{{ route('keptkaya.admin.kp_user.edit', $userKeptKayaInfo) }}">
                                <i class="fas fa-user"></i> แก้ไขมูล
                            </a>
                             <a class="btn btn-warning btn-sm" href="{{ route('keptkaya.user-monthly-status.manage', $userKeptKayaInfo) }}">
                                <i class="fas fa-trash"></i> ขอเข้าร่วมธนาคารขยะ
                            </a>
                        </td>
                    </tr>
                    <?php $i++; ?>
                @endforeach
            </tbody>
        </table>
    </form>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(function() {
            var table = $("#kp_user_table").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
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
                "orderCellsTop": true,
                "fixedHeader": true,
                "ordering": false,
                "searching": true,
                "columnDefs": [
                    { "orderable": false, "targets": [0, 2, 5, 6] }
                ]
            });

            // Individual Column Searching
            $('#kp_user_table thead tr').clone(true).appendTo('#kp_user_table thead');
            $('#kp_user_table thead tr:eq(1) th').each(function(i) {
                var title = $(this).text();
                if (i === 1 || i === 3 || i === 4) {
                    $(this).html('<input type="text" placeholder="ค้นหา ' + title + '" class="form-control form-control-sm"/>');
                    $('input', this).on('keyup change clear', function() {
                        if (table.column(i).search() !== this.value) {
                            table.column(i).search(this.value).draw();
                        }
                    });
                } else {
                    $(this).html('');
                }
            });

            // --- Toggle Collapse for Search Filters (ที่คุณมีอยู่แล้ว) ---
            const collapseSearchFilters = $('#collapseSearchFilters');
            const toggleSearchIcon = $('#toggleSearchIcon');

            collapseSearchFilters.on('show.bs.collapse', function () {
                toggleSearchIcon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });
            collapseSearchFilters.on('hide.bs.collapse', function () {
                toggleSearchIcon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            });

            // --- Toggle Collapse for Bin List ---
            // Event listener สำหรับการแสดง/ซ่อนรายการ Bin
            $(document).on('show.bs.collapse', '.collapse', function () {
                const targetId = $(this).attr('id');
                const iconId = 'toggleBinIcon-' + targetId.replace('collapseBins-', '');
                $('#' + iconId).removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });

            $(document).on('hide.bs.collapse', '.collapse', function () {
                const targetId = $(this).attr('id');
                const iconId = 'toggleBinIcon-' + targetId.replace('collapseBins-', '');
                $('#' + iconId).removeClass('fa-chevron-up').addClass('fa-chevron-down');
            });


            // --- Your existing JavaScript logic ---
            let ref_rate_payment_per_year = 0;
            let ref_bin_quantity = 0;
            let ref_id = 0;
            $(document).on('click', '.payment_per_year_and_qty', function() {
                ref_rate_payment_per_year = $(this).parents().find('.rate_payment_per_year').val();
                ref_bin_quantity = $(this).parents().find('.bin_quantity').val();
                ref_id = $(this).parents().find('.rate_payment_per_year').prop('id');
                console.log('ref_bin_quantity', ref_bin_quantity);
            });

            $(document).on('keyup', '.payment_per_year_and_qty', function(e) {
                e.preventDefault();
                let user_id = $(this).data('user_id');
                if ($(`#bin_quantity${user_id}`).val() < ref_bin_quantity) {
                    $(`#bin_quantity${user_id}`).val(ref_bin_quantity);
                    alert('ไม่สามารถทำได้ต้องใช้จำนวนถังเดิมจนสิ้นปี');
                    return;
                }
                $(`#price_per_budgetyear${user_id}`).val(1);
                let total = $(`#bin_quantity${user_id}`).val() * $(`#payment_per_year${user_id}`).val();
                $(`#total_payment_per_year${user_id}`).val(total);
            });

            $(document).on('click', '.checkbox', function() {
                checkboxclicked();
            });

            function checkboxclicked() {
                let _total = 0;
                $('.checkbox').each(function(index, element) {
                    if ($(this).is(":checked")) {
                        let id = $(this).data('inv_id');
                        let sum = $(`#total${id}`).text();
                        _total += parseFloat(sum);
                    }
                });

                if (_total === 0) {
                    $('.cash_form_user').attr('readonly', true);
                    $('.submitbtn').hide();
                } else {
                    $('.cash_form_user').removeAttr('readonly');
                }

                if ($('.cash_from_user').val() > 0) {
                    let remain = parseFloat($('.cash_from_user').val()) - _total;
                    if (remain >= 0) {
                        $('.submitbtn').show();
                    } else {
                        $('.submitbtn').hide();
                    }
                    $('.cashback').val(remain);
                } else {
                     $('.submitbtn').hide();
                     $('.cashback').val("");
                }

                $('.paidsum').html(_total);
                $('#paidsum').val(_total);
                $('#mustpaid').val(_total);
                $('.mustpaid').text(_total);
            }

            function check() {
                $('.submitbtn').prop('disabled', true);
                let checkboxChecked = false;
                let cashbackRes = false;
                let errText = '';
                $('.checkbox:checked').each(function(index, element) {
                    checkboxChecked = true;
                });
                if (checkboxChecked === false) {
                    errText += '- ยังไม่ได้เลือกรายการค้างชำระ\n';
                }
                let mustpaid = parseFloat($('.mustpaid').val());
                let cash_from_user = parseFloat($('.cash_from_user').val());
                let cashback = cash_from_user - mustpaid;
                if (!isNaN(cashback) && cashback >= 0) {
                    cashbackRes = true;
                } else {
                    errText += '- ใส่จำนวนเงินไม่ถูกต้อง';
                }
                if (checkboxChecked === false || cashbackRes === false) {
                    $('.submitbtn').prop('disabled', false);
                    alert(errText);
                    return false;
                } else {
                    return true;
                }
            }

            // --- ส่วนของ Modal ที่ยังขาดอยู่จากโค้ดเดิม ---
            // ถ้าคุณมี Modal ที่เปิดโดยปุ่ม '.bin' หรือ '.bin-button'
            // คุณต้องนำโค้ดนั้นมาไว้ในไฟล์นี้ด้วย
            // ตัวอย่าง:
            $(document).on('click', '.bin-button', function() { // เปลี่ยนจาก .bin เป็น .bin-button
                let budgetyear_id = $(this).data('budgetyear_id');
                let bincode = $(this).data('bincode');
                // โค้ดสำหรับเปิด Modal และดึงข้อมูล
                // เช่น $('.modal').modal('show');
                // เรียก AJAX เพื่อดึงข้อมูลสำหรับ Modal
                // $.get(`/kp_payment/get_kp_invoice/${budgetyear_id}/${bincode}`).done(function(data){ ... });
                console.log('Bin button clicked:', budgetyear_id, bincode);
                $('#modal-success').modal('show'); // สมมติว่า modal ของคุณมี ID เป็น modal-success
            });
            // ------------------------------------------

            $('.close').click(() => {
                $('.modal').modal('hide'); // ใช้ hide สำหรับ Bootstrap 5 modal
            });
        });
    </script>
@endsection