@forelse($staffs as $staff)
    <tr>
        <td>
            <div class="d-flex px-2 py-1">
                <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm">{{ $staff->prefix ?? '' }} {{ $staff->firstname ?? 'N/A' }}
                        {{ $staff->lastname ?? '' }}
                    </h6>
                    <p class="text-xs text-secondary mb-0">{{ $staff->username ?? 'N/A' }}</p>
                </div>
            </div>
        </td>
        <td>
            {{ $staff->email ?? 'N/A' }}
        </td>
        <td>
            @foreach($staff->roles as $role)
                <span class="badge badge-xs bg-gradient-info">{{ $role->name }}</span>
            @endforeach
            @if($staff->roles->isEmpty())
                <span class="text-xs text-secondary">ไม่มีบทบาท</span>
            @endif
        </td>

        <td>
            @foreach($staff->permissions as $permission)
                <span class="badge badge-xs border border-info text-info bg-transparent">
                    {{ $permission->name }}
                </span>
            @endforeach
            @if($staff->permissions->isEmpty())
                <span class="text-xs text-muted">-</span>
            @endif
        </td>

        <td class="align-middle text-center text-sm">
            <span class="badge badge-sm bg-gradient-{{ $staff->status == 'active' ? 'success' : ($staff->status == 'inactive' ? 'secondary' : 'warning') }}">
                {{ ucfirst($staff->status) }}
            </span>
        </td>

        <td class="align-middle">
            <a href="{{ route('keptkayas.staffs.edit', $staff->id) }}"
                class="btn btn-link text-secondary font-weight-bold text-xs px-0 mb-0 me-2">
                <i class="fas fa-edit me-1"></i> แก้ไข
            </a>
        </td>
    </tr>
@empty
    @endforelse

{{-- @forelse($staffs as $staff)

    <tr>
        <td>
            <div class="d-flex px-2 py-1">
                <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm">{{ $staff->prefix ?? '' }} {{ $staff->firstname ?? 'N/A' }}
                        {{ $staff->lastname ?? '' }}
                    </h6>
                    <p class="text-xs text-secondary mb-0">{{ $staff->username ?? 'N/A' }}</p>
                </div>
            </div>
        </td>
        <td>
            <p class="text-xs font-weight-bold mb-0">{{ $staff->email ?? 'N/A' }}</p>
        </td>
        <td class="align-middle text-center text-sm">
            <span
                class="badge badge-sm bg-gradient-{{ $staff->status == 'active' ? 'success' : ($staff->status == 'inactive' ? 'secondary' : 'warning') }}">{{ ucfirst($staff->status) }}</span>
        </td>
        <td>
            @forelse($staffs as $staff)
                <tr>
                    <td>
                        @foreach($staff->roles as $role)
                            <span class="badge badge-xs bg-gradient-info">{{ $role->name }}</span>
                        @endforeach
                        @if($staff->roles->isEmpty())
                            <span class="text-xs text-secondary">ไม่มีบทบาท</span>
                        @endif
                    </td>

                    <td>
                        @foreach($staff->permissions as $permission)
                            <span class="badge badge-xs border border-info text-info bg-transparent">
                                {{ $permission->name }}
                            </span>
                        @endforeach
                        @if($staff->permissions->isEmpty())
                            <span class="text-xs text-muted">-</span>
                        @endif
                    </td>

                    <td class="align-middle text-center text-sm">
                        <span
                            class="badge badge-sm bg-gradient-{{ $staff->status == 'active' ? 'success' : ($staff->status == 'inactive' ? 'secondary' : 'warning') }}">
                            {{ ucfirst($staff->status) }}
                        </span>
                    </td>

                    <td class="align-middle">
                        <a href="{{ route('keptkayas.staffs.edit', $staff->id) }}"
                            class="btn btn-link text-secondary font-weight-bold text-xs px-0 mb-0 me-2">
                            <i class="fas fa-edit me-1"></i> แก้ไข
                        </a>
                    </td>
                </tr>
            @empty
    @endforelse
    </td>

    <td class="align-middle">
        <a href="{{ route('keptkayas.staffs.edit', $staff->id) }}"
            class="btn btn-link text-secondary font-weight-bold text-xs px-0 mb-0 me-2">
            <i class="fas fa-edit me-1"></i> แก้ไข
        </a>

    </td>
    </tr>

@empty
    <tr>
        <td colspan="5" class="text-center">ไม่มีข้อมูลเจ้าหน้าที่ในระบบ</td>
    </tr>
@endforelse --}}
