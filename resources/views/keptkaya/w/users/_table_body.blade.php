@forelse($users as $user)
<tr>
    <td>
        <div class="d-flex px-2 py-1">
            <div class="d-flex flex-column justify-content-center">
                <h6 class="mb-0 text-sm">{{ $user->prefix }} {{ $user->firstname }} {{ $user->lastname }}</h6>
                <p class="text-xs text-secondary mb-0">{{ $user->username }}</p>
            </div>
        </div>
    </td>
    <td>
        <p class="text-xs font-weight-bold mb-0">{{ $user->email }}</p>
    </td>
    <td class="align-middle text-center text-sm">
        <span class="badge badge-sm bg-gradient-{{ $user->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($user->status) }}</span>
    </td>
    <td class="align-middle text-center">
        <input type="hidden" name="users[{{ $user->id }}][is_annual_collection]" value="0">
        <div class="form-check form-check-inline">
            <input class="form-check-input annual-coll-checkbox" type="checkbox" id="annual_coll_{{ $user->id }}" name="users[{{ $user->id }}][is_annual_collection]" value="1"
                {{ optional($user->wastePreference)->is_annual_collection ? 'checked' : '' }}
                >
            <label class="form-check-label" for="annual_coll_{{ $user->id }}">เก็บขยะรายปี</label>
        </div>
        <input type="hidden" name="users[{{ $user->id }}][is_waste_bank]" value="0">
        <div class="form-check form-check-inline">
            <input class="form-check-input waste-bank-checkbox" type="checkbox" id="waste_bank_{{ $user->id }}" name="users[{{ $user->id }}][is_waste_bank]" value="1"
                {{ optional($user->wastePreference)->is_waste_bank ? 'checked' : '' }}
                >
            <label class="form-check-label" for="waste_bank_{{ $user->id }}">ธนาคารขยะ</label>
        </div>
    </td>
    <td class="align-middle">
        
        @if(optional($user->wastePreference)->is_annual_collection)
            <a href="{{ route('keptkayas.waste_bins.index', $user->id) }}" class="btn btn-link text-info text-gradient px-0 mb-0 me-2">
                <i class="fas fa-trash-alt me-1"></i> จัดการถังขยะ
            </a>
        @endif
        <a href="{{ route('keptkayas.users.edit', $user->id) }}" class="btn btn-link text-secondary font-weight-bold text-xs px-0 mb-0 me-2">
                <i class="fas fa-edit me-1"></i> แก้ไข
            </a>
        <form action="{{ route('keptkayas.users.destroy', $user->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-link text-danger text-gradient px-0 mb-0" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบผู้ใช้งานนี้?')">
                <i class="fas fa-trash-alt me-1"></i> ลบ
            </button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center">ไม่มีผู้ใช้งานในระบบ</td>
</tr>
@endforelse
