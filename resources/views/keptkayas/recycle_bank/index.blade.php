@extends('layouts.keptkaya')

@section('content')
    
<div class="container my-4">
    <h3 class="mb-4">รายชื่อสมาชิกธนาคารขยะ</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>ชื่อ - นามสกุล</th>
                    <th>ที่อยู่</th>
                    <th>โซน (Zone)</th>
                    <th>โซนย่อย (Subzone)</th>
                    <th>จำนวนเงินสะสม (บาท)</th>
                    <th>จำนวนแต้มสะสม</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $index => $member)
                <tr>
                    <td>{{ $members->firstItem() + $index }}</td>
                    <td>{{ $member->prefix }}{{ $member->firstname }} {{ $member->lastname }}</td>
                    <td>{{ $member->address }}</td>
                    <td>{{ optional($member->user_zone)->zone_name ?? '-' }}</td>
                    <td>{{ optional($member->user_subzone)->subzone_name ?? '-' }}</td>
                    <td class="text-end fw-bold text-success">
                        {{ number_format(optional($member->recycleBankAccount)->balance ?? 0, 2) }}
                    </td>
                    <td class="text-end fw-bold text-primary">
                        {{ number_format(optional($member->recycleBankAccount)->points ?? 0) }}
                    </td>
                    <td class="text-center">
                        <a href="{{ route('keptkayas.recycle-bank.history', $member->id) }}" class="btn btn-info btn-sm text-white">
                            <i class="fas fa-history"></i> ประวัติการขายขยะ
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">ไม่พบข้อมูลสมาชิก</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $members->links() }}
    </div>
</div>
@endsection 
