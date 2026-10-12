@extends('layouts.keptkaya')
@section('nav-header', 'ธนาคารขยะรีไซเคิล')
@section('page-topic', 'รายชื่อสมาชิกธนาคารขยะ')

@section('content')
    <div class="card mb-4">
        <div class="card-header pb-0">
            <h6 class="mb-0">รายชื่อสมาชิกธนาคารขยะ</h6>
        </div>
        <div class="card-body px-0 pt-3 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">#</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ชื่อ - นามสกุล</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ที่อยู่</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">โซน / โซนย่อย</th>
                            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">เงินสะสม (บาท)</th>
                            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">แต้มสะสม</th>
                            <th class="text-secondary opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $index => $member)
                            <tr>
                                <td class="ps-4"><p class="text-xs font-weight-bold mb-0">{{ $members->firstItem() + $index }}</p></td>
                                <td><h6 class="mb-0 text-sm">{{ $member->prefix }}{{ $member->firstname }} {{ $member->lastname }}</h6></td>
                                <td><p class="text-xs font-weight-bold mb-0">{{ $member->address ?: '-' }}</p></td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ optional($member->user_zone)->zone_name ?? '-' }}</p>
                                    <p class="text-xxs text-secondary mb-0">{{ optional($member->user_subzone)->subzone_name ?? '-' }}</p>
                                </td>
                                <td class="text-end text-sm font-weight-bold text-success">
                                    {{ number_format(optional($member->recycleBankAccount)->balance ?? 0, 2) }}
                                </td>
                                <td class="text-end text-sm font-weight-bold text-primary">
                                    {{ number_format(optional($member->recycleBankAccount)->points ?? 0) }}
                                </td>
                                <td class="align-middle text-end pe-4 text-nowrap">
                                    <a href="{{ route('keptkayas.recycle-bank.history', $member->id) }}"
                                        class="btn btn-link text-info font-weight-bold text-xs p-0 mb-0">
                                        <i class="fas fa-history me-1"></i> ประวัติการขายขยะ
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-secondary py-4">ไม่พบข้อมูลสมาชิก</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 pt-3">
                {{ $members->links() }}
            </div>
        </div>
    </div>
@endsection
