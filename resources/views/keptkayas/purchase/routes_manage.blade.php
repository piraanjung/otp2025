@if(auth()->user()->can('access waste bank mobile'))
    @php $layout = 'layouts.keptkaya_mobile'; @endphp
@else
    @php $layout = 'layouts.keptkaya'; @endphp
@endif

@extends($layout)

@section('nav-header', 'ตั้งค่าเขตรับซื้อขยะ')
@section('nav-current', 'จัดการเขตรับซื้อ')
@section('page-topic', 'ตั้งค่าระบบธนาคารขยะ')

@section('content')
<div class="container-fluid px-2 px-md-4">

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 text-sm text-white mb-3">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 text-sm text-white mb-3">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row g-3">
        
        {{-- ฝั่งซ้าย: ฟอร์มสร้าง/แก้ไขเขตรับซื้อ --}}
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="font-weight-bolder mb-0 text-dark" id="form-title">
                        <i class="fas fa-plus-circle text-primary me-2"></i>เพิ่มเขตการรับซื้อใหม่
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('keptkayas.purchase.routes.save') }}" method="POST" id="route-form">
                        @csrf
                        <input type="hidden" name="route_id" id="route_id" value="">

                        {{-- ชื่อเขต --}}
                        <div class="mb-3">
                            <label class="form-label text-xs font-weight-bold text-dark">ชื่อเขตการรับซื้อ <span class="text-danger">*</span></label>
                            <input type="text" name="route_name" id="route_name" class="form-control" placeholder="เช่น เขตที่ 1 (สายหมู่ 1 - หมู่ 4)" required>
                        </div>

                        {{-- สถานะ --}}
                        <div class="mb-3">
                            <label class="form-label text-xs font-weight-bold text-dark">สถานะการใช้งาน</label>
                            <select name="status" id="status" class="form-select">
                                <option value="active">เปิดใช้งาน (Active)</option>
                                <option value="inactive">ปิดใช้งาน (Inactive)</option>
                            </select>
                        </div>

                        {{-- ติ๊กเลือกโซนย่อย --}}
                        <div class="mb-3">
                            <label class="form-label text-xs font-weight-bold text-dark d-block">
                                ติ๊กเลือกโซนที่ต้องการยุบรวมเข้าเขตนี้:
                            </label>
                            <div class="border rounded-3 p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                @forelse($allZones as $zone)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input zone-checkbox" type="checkbox" name="zone_ids[]" value="{{ $zone->id }}" id="zone_{{ $zone->id }}">
                                        <label class="form-check-label text-xs text-dark cursor-pointer fw-bold" for="zone_{{ $zone->id }}">
                                            {{ $zone->zone_name }}
                                        </label>
                                    </div>
                                @empty
                                    <small class="text-muted d-block text-center py-2">ยังไม่มีข้อมูลโซนในระบบ</small>
                                @endforelse
                            </div>
                            <small class="text-xxs text-secondary mt-1 d-block">* สามารถเลือกกี่โซนก็ได้มารวมกันเป็น 1 เขตรับซื้อ</small>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn bg-gradient-primary w-100 mb-0">
                                <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                            </button>
                            <button type="button" class="btn btn-light w-50 mb-0 d-none" id="btn-cancel-edit" onclick="resetForm()">
                                ยกเลิก
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ฝั่งขวา: รายการเขตรับซื้อที่มีในระบบ --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bolder mb-0 text-dark">
                        <i class="fas fa-list me-2 text-primary"></i>รายการเขตรับซื้อทั้งหมด ({{ count($routes) }})
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">ชื่อเขตรับซื้อ</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">โซนที่ครอบคลุม</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">สถานะ</th>
                                    <th class="text-secondary opacity-7 text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($routes as $route)
                                    <tr>
                                        <td class="ps-3 align-middle">
                                            <span class="text-sm font-weight-bold text-dark d-block">{{ $route->route_name }}</span>
                                        </td>
                                        <td class="align-middle">
                                            @forelse($route->zones as $z)
                                                <span class="badge bg-gradient-secondary text-xxs me-1 mb-1">{{ $z->zone_name }}</span>
                                            @empty
                                                <span class="text-xxs text-muted">- ไม่ได้ผูกโซน -</span>
                                            @endforelse
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($route->status == 'active')
                                                <span class="badge badge-sm bg-gradient-success">Active</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            {{-- ปุ่มแก้ไข --}}
                                            <button type="button" class="btn btn-link text-primary mb-0 px-2" title="แก้ไข" onclick='editRoute(@json($route))'>
                                                <i class="fas fa-edit text-lg"></i>
                                            </button>
                                            
                                            {{-- ปุ่มลบ --}}
                                            <form action="{{ route('keptkayas.purchase.routes.delete', $route->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบเขตรับซื้อนี้?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger mb-0 px-2" title="ลบ">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-secondary text-sm">
                                            ยังไม่มีการตั้งค่าเขตรับซื้อ (ระบบจะดึงสมาชิกทั้งหมดให้อัตโนมัติ)
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script>
    // กดปุ่มแก้ไขแล้วเติมข้อมูลลงฟอร์ม
    function editRoute(route) {
        document.getElementById('form-title').innerHTML = '<i class="fas fa-edit text-warning me-2"></i>แก้ไขเขตการรับซื้อ';
        document.getElementById('route_id').value = route.id;
        document.getElementById('route_name').value = route.route_name;
        document.getElementById('status').value = route.status;

        // เคลียร์ Checkbox ทั้งหมดก่อน
        document.querySelectorAll('.zone-checkbox').forEach(cb => cb.checked = false);

        // ติ๊กเลือก Checkbox เฉพาะโซนที่ผูกกับเขตนี้
        if (route.zones && route.zones.length > 0) {
            route.zones.forEach(z => {
                const cb = document.getElementById('zone_' + z.id);
                if (cb) cb.checked = true;
            });
        }

        document.getElementById('btn-cancel-edit').classList.remove('d-none');
    }

    // รีเซ็ตฟอร์มกลับเป็นโหมดสร้างใหม่
    function resetForm() {
        document.getElementById('form-title').innerHTML = '<i class="fas fa-plus-circle text-primary me-2"></i>เพิ่มเขตการรับซื้อใหม่';
        document.getElementById('route-form').reset();
        document.getElementById('route_id').value = '';
        document.querySelectorAll('.zone-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('btn-cancel-edit').classList.add('d-none');
    }
</script>
@endsection