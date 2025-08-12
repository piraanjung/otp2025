@extends('layouts.keptkaya')

@section('nav-header', 'การจัดการ')
@section('nav-main', 'สถานะรายเดือน')
@section('nav-current', 'รายชื่อผู้ใช้งาน')
@section('page-topic', 'รายชื่อผู้ใช้งานระบบธนาคารขยะ')

@section('content')
    <div class="container mt-5">
        <h1>จัดการสถานะรายเดือนของผู้ใช้งาน</h1>

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

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ชื่อ-สกุล</th>
                    <th>รหัสผู้ใช้งาน</th>
                    <th>ที่อยู่</th>
                    <th>หมู่ที่</th>
                    <th>จำนวนถังขยะ</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- วนลูปผ่าน $userKayaInfos ที่ส่งมาจาก Controller --}}
                @forelse ($userKayaInfos as $index => $userInfo)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        {{-- เข้าถึงข้อมูล User ผ่าน relationship kp_user --}}
                        <td>{{ $userInfo->kp_user->prefix . $userInfo->kp_user->firstname . ' ' . $userInfo->kp_user->lastname }}</td>
                        {{-- รหัสผู้ใช้งานคือ ID ของ KpUserKeptkayaInfos --}}
                        <td>{{ $userInfo->id ?? 'N/A' }}</td>
                        {{-- เข้าถึงที่อยู่ผ่าน relationship kp_user --}}
                        <td>{{ $userInfo->kp_user->address }}</td>
                        {{-- เข้าถึงหมู่ที่ผ่าน relationship kp_user และ kp_zone --}}
                        <td>{{ $userInfo->kp_user->kp_zone->zone_name ?? 'N/A' }}</td>
                        {{-- เข้าถึงจำนวนถังขยะผ่าน relationship kp_bins --}}
                        <td>
                            {{ $userInfo->kp_bins->count() }} ถัง
                        </td>
                        <td>
                            {{-- ส่ง KpUser Model ไปยัง route manage --}}
                            <a href="{{ route('keptkaya.user-monthly-status.manage', $userInfo->kp_user) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-calendar-alt me-1"></i> จัดการสถานะรายเดือน
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">ไม่พบข้อมูลผู้ใช้งานระบบธนาคารขยะ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('script')
    {{-- หากมี JavaScript ที่จำเป็นสำหรับ DataTables หรืออื่นๆ ในหน้านี้ ให้ใส่ที่นี่ --}}
    {{-- ตัวอย่าง: การตั้งค่า DataTables --}}
    {{-- <script>
        $(document).ready(function() {
            $('.table').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
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
            });
        });
    </script> --}}
@endsection
