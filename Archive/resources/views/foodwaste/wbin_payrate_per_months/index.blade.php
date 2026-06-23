@extends('layouts.keptkaya') {{-- ใช้ layout หลักของคุณ --}}

@section('nav-payment')
    {{-- ถ้าเมนูนี้เกี่ยวข้องกับการจัดการอัตราค่าบริการ --}}
@endsection
@section('nav-header')
    จัดการอัตราค่าบริการ
@endsection
@section('nav-main')
    <a href="{{ route('keptkayas.wbin_payrate_per_months.index') }}">อัตราค่าบริการรายปี</a>
@endsection
@section('nav-current')
    รายการอัตราค่าบริการรายปีต่อกลุ่มผู้ใช้
@endsection
@section('page-topic')
    รายการอัตราค่าบริการรายปีต่อกลุ่มผู้ใช้
@endsection

@section('style')
    {{-- DataTables CSS (ถ้าใช้) --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.dataTables.css">
    <style>
        /* สไตล์เพิ่มเติมสำหรับตาราง หรือ overrides */
        .table thead th {
            text-align: center;
        }

        .table tbody td {
            vertical-align: middle;
            text-align: center;
            /* จัดให้อยู่ตรงกลางตามต้องการ */
        }

        .table tbody td:nth-child(2),
        /* กลุ่มผู้ใช้ */
        .table tbody td:nth-child(4)

        /* อัตราค่าบริการ */
            {
            text-align: left;
            /* จัดชิดซ้ายสำหรับข้อความ */
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.4em 0.7em;
            border-radius: 0.375rem;
        }
    </style>
@endsection

@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">รายการอัตราค่าบริการรายปีต่อกลุ่มผู้ใช้</h3>
                    <div class="card-tools">
                        <a href="{{ route('keptkayas.wbin_payrate_per_months.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> เพิ่มอัตราค่าบริการใหม่
                        </a>
                    </div>
                </div>
                <div class="card-body">
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

                    <div class="table-responsive">
                        <table class="table table-hover align-items-center mb-0" id="payratesTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-center font-weight-bolder">#</th>
                                    <th class="text-uppercase text-secondary text-center font-weight-bolder ps-2">
                                        กลุ่มผู้ใช้</th>
                                    <th class="text-uppercase text-secondary text-center font-weight-bolder">ปีงบประมาณ</th>
                                    <th class="text-uppercase text-secondary text-center font-weight-bolder">
                                        อัตราค่าบริการต่อเดือน</th>
                                    <th class="text-uppercase text-secondary text-center font-weight-bolder">
                                        อัตราค่าบริการรายปี</th>
                                    <th class="text-uppercase text-secondary text-center font-weight-bolder">สถานะ</th>
                                    <th class="text-uppercase text-secondary text-center font-weight-bolder">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payrates as $payrate)
                                    <tr>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $payrate->id }}</p>
                                        </td>
                                        <td class="text-sm">{{ $payrate->kp_usergroup->usergroup_name ?? 'N/A' }}</td>
                                        <td class="text-sm">{{ $payrate->budgetyear->budgetyear_name ?? 'N/A' }}</td>
                                        <td class="text-sm text-center">{{ number_format($payrate->payrate_permonth, 2) }}</td>
                                        <td class="text-sm">{{ number_format($payrate->payrate_permonth * 12, 2) }}</td>
                                        <td>
                                            <span
                                                class="badge badge-sm {{ $payrate->status == 'active' ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                                                {{ $payrate->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('keptkayas.wbin_payrate_per_months.edit', $payrate->id) }}"
                                                class="btn btn-sm btn-warning mb-0 me-1">
                                                <i class="fas fa-edit"></i> แก้ไข
                                            </a>
                                            <form action="{{ route('keptkayas.wbin_payrate_per_months.destroy', $payrate->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger mb-0"
                                                    onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการนี้? การลบจะเป็นการปิดใช้งานข้อมูลนี้')">
                                                    <i class="fas fa-trash-alt"></i> ลบ
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $payrates->links() }} {{-- สำหรับแสดง Pagination Links --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>
    <script>
        $(document).ready(function () {
            // Optional: Initialize DataTable if you want client-side features like search/sort
            // Otherwise, rely on server-side pagination from Controller
            $('#payratesTable').DataTable({
                "paging": true, // Enable pagination
                "searching": true, // Enable search box
                "ordering": true, // Enable sorting
                "info": true, // Show "Showing X to Y of Z entries"
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "ทั้งหมด"]
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
                "responsive": true, // Make table responsive
                "autoWidth": false, // Disable auto width adjustment
                // columnDefs: [
                //     { targets: [5], orderable: false } // Disable sorting for 'การจัดการ' column
                // ]
            });
        });
    </script>
@endsection