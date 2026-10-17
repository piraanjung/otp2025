@extends('layouts.foodwaste')

@section('content')
    <div class="container">
        <h2>รายการถังขยะเศษอาหาร</h2>
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('foodwaste.bins.index') }}" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="ค้นหารหัสถังขยะ..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">-- ทุกสถานะ --</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                            <option value="removed" {{ request('status') == 'removed' ? 'selected' : '' }}>Removed</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> ค้นหา
                        </button>
                        <a href="{{ route('foodwaste.bins.index') }}" class="btn btn-outline-secondary w-100">
                            ล้างค่า
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <div class="container mt-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> {{ $errors->first('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

        </div>

        <a href="{{ route('foodwaste.bins.create') }}" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> ลงทะเบียนถังขยะเศษอาหาร
        </a>

        <form action="{{ route('foodwaste.bins.print_selected') }}" method="POST" target="_blank">
            @csrf
            <div class="mb-3">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-print"></i> พิมพ์ QR Code ที่เลือก
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>รหัสถังขยะ</th>
                            <th>ID</th>
                            <th>รหัสถังขยะ</th>
                            <th>ผู้ใช้งาน</th>
                            <th>สถานะ</th>
                            <th>หมายเหตุ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bins as $bin)
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_bins[]" value="{{ $bin->id }}">
                                </td>
                                <td>{{ $bin->id }}</td>
                                <td>{{ $bin->bin_code }}</td>
                                <td>{{ $bin->foodwaste_bin->fw_user_preference->user->firstname ?? ''}}
                                    {{ $bin->foodwaste_bin->fw_user_preference->user->lastname ?? '' }}
                                </td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'active' => 'bg-success',
                                            'inactive' => 'bg-warning',
                                            'damaged' => 'bg-danger',
                                            'removed' => 'bg-secondary'
                                        ][$bin->status] ?? 'bg-info';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($bin->status) }}</span>
                                </td>
                                <td>{{ $bin->latitude ?? 'N/A' }} / {{ $bin->longitude ?? 'N/A' }}</td>
                                <td class="d-flex justify-content-center">
                                    <a href="{{ route('foodwaste.bins.show', $bin->id) }}" class="btn btn-sm btn-info me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('foodwaste.bins.edit', $bin->id) }}" class="btn btn-sm btn-warning me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteBin({{ $bin->id }}, '{{ $bin->bin_code }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    {{-- <form action="{{ route('foodwaste.bins.destroy', $bin->id) }}" method="POST"
                                        onsubmit="return confirm('คุณต้องการลบถังขยะรหัส {{ $bin->bin_code }} หรือไม่?');">
                                        @csrf
                                        @method('DELETE') <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form> --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">ไม่พบข้อมูลถังขยะ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <form id="delete-form" action="" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $bins->appends(request()->query())->links() }}
    </div>
@endsection

@section('script')
    <script>
        // สคริปต์สำหรับติ๊กถูกทั้งหมด
        document.getElementById('select-all').onclick = function () {
            var checkboxes = document.getElementsByName('selected_bins[]');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }

        function deleteBin(id, code) {
    if (confirm('คุณต้องการลบถังขยะรหัส ' + code + ' หรือไม่?')) {
        // หาฟอร์มลบที่เราสร้างไว้ข้างนอก
        const form = document.getElementById('delete-form');
        // เปลี่ยน Action URL ให้ตรงกับ ID ที่จะลบ
        form.action = '/foodwaste/bins/' + id;
        // สั่ง Submit ฟอร์ม
        form.submit();
    }
}
    </script>
@endsection
