@extends('layouts.print')

@section('content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body bg-light rounded-3 p-3">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-geo-alt-fill text-danger fs-3 me-2"></i>
                <div>
                    <small class="text-muted d-block">พื้นที่ / องค์กรที่คุณกำลังใช้งาน</small>
                    <img src="{{ asset('logo/'.$currentOrg['org_logo_img']) }}" alt="" width="100" height="100">
                    <h5 class="mb-0 fw-bold text-primary" id="current-org-display">
                        {{ $currentOrg['orgType']['name'].$currentOrg['org_name'] ?? 'กรุณาเลือกองค์กร' }}
                    </h5>

                     <h5 class="mb-0 fw-bold text-primary" id="current-org-display">
                        {{ "ตำบล ".$currentOrg['tambons']['tambon_name'] }}
                        {{ "อำเภอ ".$currentOrg['districts']['district_name'] }}
                        {{ "จังหวัด ".$currentOrg['provinces']['province_name'] }}
                    </h5>
                </div>
            </div>

            <hr class="my-2">

            <!-- Dropdown สำหรับตรวจสอบและสลับ Org -->
            <div class="form-group mt-2">
                <label for="org_select" class="form-label small text-muted">
                    <i class="bi bi-exclamation-circle me-1"></i>หากพื้นที่ไม่ถูกต้อง กรุณาเลือกรัฐ/องค์กรของคุณที่นี่:
                </label>
                <select name="org_id_fk" id="org_select" class="form-select form-select-lg shadow-none"
                    onchange="switchOrganization(this.value)">
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}" {{ $org->id == $currentOrg['id'] ? 'selected' : '' }}>
                            {{ $org->orgType->name.$org->org_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="container py-4">
        <h4 class="text-center mb-4 font-weight-bold">🔔 เลือกหมวดหมู่ที่ต้องการแจ้งเหตุ</h4>

        <div class="row g-3">
            @foreach($categories as $cat)
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm border-{{ $cat->color }}">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <div>
                                <div class="display-4 mb-2">{{ $cat->icon }}</div>
                                <h5 class="card-title fw-bold text-{{ $cat->color }}">{{ $cat->title }}</h5>
                                <p class="card-text text-muted small">{{ $cat->description }}</p>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('tabwater.notify.create', ['system_type' => $cat->system_type]) }}"
                                    class="btn btn-{{ $cat->color }} w-100 fw-bold">
                                    แจ้งเรื่องหมวดนี้
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('script')
    <script>
        function switchOrganization(orgId) {
            console.log('xx')
            // รีโหลดหน้าใหม่พร้อมส่ง org_id ตัวใหม่ไป更新 Session
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('org_id', orgId);
            window.location.href = currentUrl.toString();
        }
    </script>
@endsection