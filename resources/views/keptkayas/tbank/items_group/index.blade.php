@extends('layouts.keptkaya')
@section('page-topic', 'ประเภทขยะรีไซเคิล')
@section('nav-current', 'รายการประเภทขยะรีไซเคิล')
@section('nav-header', 'ตั้งค่าข้อมูล')

@section('content')
    <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">รายการประเภทขยะรีไซเคิล</h6>
            <a class="btn bg-gradient-primary btn-sm mb-0" href="{{ route('keptkayas.tbank.items_group.create') }}">
                <i class="fas fa-plus me-1"></i> สร้างข้อมูล
            </a>
        </div>
        <div class="card-body px-0 pt-3 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">ชื่อประเภทขยะรีไซเคิล</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">รหัสประเภท</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kp_tbank_item_groups as $kp_item_group)
                            <tr>
                                <td class="ps-4"><h6 class="mb-0 text-sm">{{ $kp_item_group->kp_items_groupname }}</h6></td>
                                <td><p class="text-xs font-weight-bold mb-0">{{ $kp_item_group->kp_items_group_code }}</p></td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-sm bg-gradient-{{ $kp_item_group->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ $kp_item_group->status == 'active' ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-secondary py-4">ยังไม่มีประเภทขยะรีไซเคิล</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
