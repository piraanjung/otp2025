@extends('layouts.admin1')

@section('user-show', 'show')
@section('nav-user', 'active')
@section('nav-main')
    <a href="{{ route('admin.users.index') }}"> ผู้ใช้น้ำประปา</a>
@endsection
@section('nav-header', 'ผู้ใช้งานระบบ')
@section('nav-current', 'ข้อมูลผู้ใช้น้ำประปา')

@section('style')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap5.min.css">

    <style>
        /* Custom UI Improvements */
        .table thead th {
            padding: 0.75rem 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e9ecef !important;
        }
        .table tbody td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Highlight selected rows */
        table.dataTable tbody tr.selected > * {
            box-shadow: inset 0 0 0 9999px rgba(var(--bs-primary-rgb), 0.1) !important;
            color: #344767 !important;
        }
        
        /* Badges for Multiple Meters */
        .meter-badge {
            display: inline-block;
            padding: 4px 8px;
            margin-bottom: 2px;
            background-color: #e9ecef;
            border-radius: 4px;
            font-size: 0.85em;
            color: #344767;
            text-decoration: none;
            transition: all 0.2s;
        }
        .meter-badge:hover {
            background-color: #cb0c9f; /* Theme primary color */
            color: white;
        }
        .meter-badge.cancelled {
            color: #a0a0a0;
            text-decoration: line-through;
        }

        /* Preloader */
        .preloader-wrapper {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        .fade-out-animation {
            opacity: 0;
            visibility: hidden;
        }

        /* DataTables Customization */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #d2d6da;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
        }
        .dataTables_wrapper .dt-buttons .btn {
            margin-bottom: 0;
            border-radius: 0.375rem;
        }
        
        /* Tab Customization */
        .nav-tabs .nav-link {
            border: none;
            color: #67748e;
            font-weight: 600;
            padding: 1rem 1.5rem;
            transition: all 0.3s;
        }
        .nav-tabs .nav-link.active {
            color: #cb0c9f; /* Theme Color */
            border-bottom: 3px solid #cb0c9f;
            background: transparent;
        }
    </style>
@endsection

@section('content')
    <div class="preloader-wrapper">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="mt-2 text-muted font-weight-bold">กำลังโหลดข้อมูล...</div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center bg-white">
            <div>
                <h5 class="mb-0 font-weight-bolder">ทะเบียนผู้ใช้น้ำประปา</h5>
                <p class="text-sm mb-0 text-muted">จัดการข้อมูลผู้ใช้งานและมิเตอร์</p>
            </div>
            <div>
                <a href="{{ route('admin.users.create') }}" class="btn bg-gradient-success btn-sm mb-0">
                    <i class="fas fa-plus me-1"></i> เพิ่มผู้ใช้งานใหม่
                </a>
            </div>
        </div>

        <div class="card-body px-0 pt-0 pb-2">
            <div class="nav-wrapper position-relative end-0 px-4 pt-2">
                <ul class="nav nav-tabs" id="userTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="active-tab" data-bs-toggle="tab" href="#active-content" role="tab" aria-controls="active" aria-selected="true">
                            <i class="fas fa-check-circle text-success me-1"></i> ใช้งานปัจจุบัน
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="deleted-tab" data-bs-toggle="tab" href="#deleted-content" role="tab" aria-controls="deleted" aria-selected="false">
                            <i class="fas fa-trash-alt text-secondary me-1"></i> ยกเลิกแล้ว
                        </a>
                    </li>
                </ul>
            </div>

            <div class="tab-content p-4" id="userTabsContent">
                
                <div class="tab-pane fade show active" id="active-content" role="tabpanel" aria-labelledby="active-tab">
                    <div class="table-responsive p-0">
                        <table class="table table-hover align-items-center mb-0" id="example">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-secondary text-xxs font-weight-bolder opacity-7">ชื่อ-นามสกุล</th>
                                    <th class="text-secondary text-xxs font-weight-bolder opacity-7">Factory No.</th>
                                    <th class="text-secondary text-xxs font-weight-bolder opacity-7">เลขมิเตอร์</th>
                                    <th class="text-secondary text-xxs font-weight-bolder opacity-7">มิเตอร์ย่อย</th>
                                    <th class="text-secondary text-xxs font-weight-bolder opacity-7">โซน/สาย</th>
                                    <th class="text-secondary text-xxs font-weight-bolder opacity-7">หมู่</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user_active as $u_active)
                                    <tr>
                                        <td><span class="text-secondary text-xs font-weight-bold">{{ $u_active[0]->user_id }}</span></td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $u_active[0]->user->prefix . $u_active[0]->user->firstname . ' ' . $u_active[0]->user->lastname }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @foreach ($u_active as $item)
                                                <div class="text-xs text-secondary mb-1">{{ $item['factory_no'] }}</div>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($u_active as $item)
                                                <a class="meter-badge" href="{{ route('admin.users.edit', ['user_id' => $item->meter_id]) }}" title="แก้ไขมิเตอร์">
                                                    <i class="fas fa-tachometer-alt text-xs me-1"></i> {{ $item['meternumber'] }}
                                                </a>
                                                <br>
                                            @endforeach
                                        </td>
                                        <td class="text-xs font-weight-bold text-secondary">{{ $u_active[0]->submeter_name }}</td>
                                        <td class="text-xs font-weight-bold text-secondary">{{ $u_active[0]->undertake_zone->zone_name }}</td>
                                        <td class="text-xs font-weight-bold text-secondary">{{ $u_active[0]->undertake_subzone->subzone_name }}</td>
                                        <td class="align-middle">
                                            <div class="dropstart">
                                                <a href="javascript:;" class="text-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v text-xs"></i>
                                                </a>
                                                <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5">
                                                    <li><a class="dropdown-item border-radius-md" href="{{ route('admin.users.edit', ['user_id' => $item->meter_id, 'addmeter' => 'addmeter']) }}">เพิ่มมิเตอร์ใหม่</a></li>
                                                    <li><a class="dropdown-item border-radius-md" href="{{ route('usermeter_infos.edit_invoices', ['meter_id' => $item->meter_id]) }}">แก้ไขเลขมิเตอร์</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="deleted-content" role="tabpanel" aria-labelledby="deleted-tab">
                    <div class="table-responsive p-0">
                        <table class="table table-hover align-items-center mb-0" id="example2" style="width:100%">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-secondary text-xxs font-weight-bolder opacity-7">ชื่อ-นามสกุล</th>
                                    <th class="text-secondary text-xxs font-weight-bolder opacity-7">Factory No.</th>
                                    <th class="text-secondary text-xxs font-weight-bolder opacity-7">เลขมิเตอร์</th>
                                    <th class="text-secondary text-xxs font-weight-bolder opacity-7">มิเตอร์ย่อย</th>
                                    <th class="text-secondary text-xxs font-weight-bolder opacity-7">โซน/สาย</th>
                                    <th class="text-secondary text-xxs font-weight-bolder opacity-7">หมู่</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user_deleted as $user)
                                    <tr>
                                        <td><span class="text-secondary text-xs font-weight-bold">{{ $user[0]->user_id }}</span></td>
                                        <td>
                                            <h6 class="mb-0 text-sm text-secondary">{{ $user[0]->user->prefix . $user[0]->user->firstname . ' ' . $user[0]->user->lastname }}</h6>
                                        </td>
                                        <td>
                                            @foreach ($user as $item)
                                                <div class="text-xs text-secondary mb-1">{{ $item['factory_no'] }}</div>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($user as $item)
                                                @if($item->status == 'active')
                                                    <a class="meter-badge" href="{{ route('admin.users.edit', ['user_id' => $item->meter_id]) }}">
                                                        {{ $item['meternumber'] }}
                                                    </a>
                                                @else
                                                    <span class="meter-badge cancelled" title="ยกเลิกการใช้งาน">
                                                        {{ $item['meternumber'] }}
                                                    </span>
                                                @endif
                                                <br>
                                            @endforeach
                                        </td>
                                        <td class="text-xs text-secondary">{{ $user[0]->submeter_name }}</td>
                                        <td class="text-xs text-secondary">{{ $user[0]->undertake_zone->zone_name }}</td>
                                        <td class="text-xs text-secondary">{{ $user[0]->undertake_subzone->subzone_name }}</td>
                                        <td class="align-middle">
                                            <div class="dropstart">
                                                <a href="javascript:;" class="text-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v text-xs"></i>
                                                </a>
                                                <ul class="dropdown-menu px-2 py-3">
                                                    <li><a class="dropdown-item border-radius-md" href="{{ route('admin.users.edit', ['user_id' => $item->meter_id, 'addmeter' => 'addmeter']) }}">เพิ่มมิเตอร์ใหม่</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>

    <script>
        $(document).ready(function() {
            let preloaderwrapper = document.querySelector('.preloader-wrapper');

            // Common Config for both tables
            const dtConfig = {
                "sPaginationType": "full_numbers",
                "lengthMenu": [[10, 25, 50, 150, -1], [10, 25, 50, 150, "All"]],
                "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                       "<'row'<'col-sm-12'tr>>" +
                       "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>B", // Clean DOM layout
                "language": {
                    "search": "",
                    "searchPlaceholder": "ค้นหาข้อมูล...",
                    "paginate": {
                        "first": '<i class="fas fa-angle-double-left"></i>',
                        "last": '<i class="fas fa-angle-double-right"></i>',
                        "next": '<i class="fas fa-angle-right"></i>',
                        "previous": '<i class="fas fa-angle-left"></i>'
                    }
                },
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel me-1"></i> Excel',
                        className: 'btn btn-sm btn-outline-success mb-0',
                        exportOptions: { rows: ['.selected'] }
                    },
                    {
                        text: '<i class="fas fa-check-square me-1"></i> เลือกทั้งหมด',
                        className: 'btn btn-sm btn-outline-primary mb-0 select-all-btn',
                        action: function ( e, dt, node, config ) {
                            dt.rows().select();
                        }
                    },
                    {
                        text: '<i class="fas fa-square me-1"></i> ยกเลิกเลือก',
                        className: 'btn btn-sm btn-outline-secondary mb-0 deselect-all-btn',
                        action: function ( e, dt, node, config ) {
                            dt.rows().deselect();
                        }
                    }
                ],
                "select": {
                    style: 'multi'
                },
                "deferRender": true,
                "orderClasses": false,
                "processing": true
            };

            // Initialize Tables
            const table1 = $('#example').DataTable(dtConfig);
            const table2 = $('#example2').DataTable(dtConfig);

            // Styling adjustments after init
            $('.dataTables_length select').addClass('form-select form-select-sm').css('width', 'auto').css('display', 'inline-block');
            $('.dt-buttons').addClass('d-flex gap-2 mb-3 mt-2');
            
            // Fix Tab Display Issue: When switching tabs, adjust columns
            $('button[data-bs-toggle="tab"], a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                table1.columns.adjust().draw();
                table2.columns.adjust().draw();
            });

            // Hide Preloader
            setTimeout(() => {
                preloaderwrapper.classList.add('fade-out-animation');
                setTimeout(() => { preloaderwrapper.style.display = 'none'; }, 500);
            }, 500);
        });
    </script>
@endsection