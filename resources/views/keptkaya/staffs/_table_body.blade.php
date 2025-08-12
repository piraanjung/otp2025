@forelse($staffs as $staff)
<tr>
    <td>
        <div class="d-flex px-2 py-1">
            <div class="d-flex flex-column justify-content-center">
                <h6 class="mb-0 text-sm">{{ $staff->user->prefix ?? '' }} {{ $staff->user->firstname ?? 'N/A' }} {{ $staff->user->lastname ?? '' }}</h6>
                <p class="text-xs text-secondary mb-0">{{ $staff->user->username ?? 'N/A' }}</p>
            </div>
        </div>
    </td>
    <td>
        <p class="text-xs font-weight-bold mb-0">{{ $staff->user->email ?? 'N/A' }}</p>
    </td>
    <td class="align-middle text-center text-sm">
        <span class="badge badge-sm bg-gradient-{{ $staff->status == 'active' ? 'success' : ($staff->status == 'inactive' ? 'secondary' : 'warning') }}">{{ ucfirst($staff->status) }}</span>
    </td>
    <td class="align-middle text-center text-sm">
        @if($staff->user->hasPermissionTo('access waste bank module'))
            <span class="badge badge-sm bg-gradient-info me-1">ธนาคารขยะ</span>
        @endif
        @if($staff->user->hasPermissionTo('access annual collection module'))
            <span class="badge badge-sm bg-gradient-success">เก็บรายปี</span>
        @endif
        @if(!$staff->user->hasPermissionTo('access waste bank module') && !$staff->user->hasPermissionTo('access annual collection module'))
            <span class="text-xs text-muted">ไม่มีสิทธิ์</span>
        @endif
    </td>
    <td class="align-middle">
        <a href="{{ route('keptkaya.staffs.edit', $staff->user_id) }}" class="btn btn-link text-secondary font-weight-bold text-xs px-0 mb-0 me-2">
            <i class="fas fa-edit me-1"></i> แก้ไข
        </a>
        <form action="{{ route('keptkaya.staffs.destroy', $staff->user_id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-link text-danger text-gradient px-0 mb-0" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบเจ้าหน้าที่นี้?')">
                <i class="fas fa-trash-alt me-1"></i> ลบ
            </button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center">ไม่มีข้อมูลเจ้าหน้าที่ในระบบ</td>
</tr>
@endforelse
