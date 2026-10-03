@php
    $statusMap = [
        'active' => ['ใช้งาน', 'success'],
        'inactive' => ['ไม่ใช้งาน', 'secondary'],
        'suspended' => ['ระงับ', 'warning'],
    ];
    $maxPermissions = 3;
@endphp
@forelse($staffs as $staff)
    @php
        $fullName = trim(($staff->prefix ?? '') . ' ' . ($staff->firstname ?? '') . ' ' . ($staff->lastname ?? ''));
        [$statusText, $statusColor] = $statusMap[$staff->status] ?? [ucfirst((string) $staff->status), 'warning'];
    @endphp
    <tr>
        <td>
            <div class="d-flex px-2 py-1">
                <div class="d-flex flex-column justify-content-center">
                    {{-- ผู้ใช้บางคนยังไม่มีชื่อ-นามสกุล ให้แสดง username แทนที่จะเป็น N/A --}}
                    <h6 class="mb-0 text-sm">{{ $fullName !== '' ? $fullName : ($staff->username ?? '-') }}</h6>
                    @if ($fullName !== '')
                        <p class="text-xs text-secondary mb-0">{{ $staff->username }}</p>
                    @endif
                </div>
            </div>
        </td>
        <td>
            <p class="text-sm mb-0">{{ $staff->email ?? '-' }}</p>
        </td>
        <td>
            @forelse($staff->roles as $role)
                <span class="badge badge-sm bg-gradient-info">{{ $role->name }}</span>
            @empty
                <span class="text-xs text-secondary">ไม่มีบทบาท</span>
            @endforelse
        </td>
        <td>
            @forelse($staff->permissions->take($maxPermissions) as $permission)
                <span class="badge badge-sm border border-info text-info bg-transparent">{{ $permission->name }}</span>
            @empty
                <span class="text-xs text-secondary">-</span>
            @endforelse
            @if ($staff->permissions->count() > $maxPermissions)
                <span class="badge badge-sm bg-gradient-secondary"
                    title="{{ $staff->permissions->pluck('name')->implode(', ') }}">+{{ $staff->permissions->count() - $maxPermissions }}</span>
            @endif
        </td>
        <td class="align-middle text-center text-sm">
            <span class="badge badge-sm bg-gradient-{{ $statusColor }}">{{ $statusText }}</span>
        </td>
        <td class="align-middle">
            <a href="{{ route('keptkayas.staffs.edit', $staff->id) }}"
                class="text-secondary font-weight-bold text-xs me-3">
                <i class="fas fa-edit me-1"></i> แก้ไข
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center text-secondary py-4">ไม่พบข้อมูลเจ้าหน้าที่</td>
    </tr>
@endforelse
