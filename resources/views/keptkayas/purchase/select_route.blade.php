@if(auth()->user()->can('access waste bank mobile'))
    @php $layout = 'layouts.keptkaya_mobile'; @endphp
@else
    @php $layout = 'layouts.keptkaya'; @endphp
@endif

@extends($layout)

@section('nav-header', 'เลือกเขตรับซื้อขยะ')
@section('nav-current', 'เลือกเขตรับซื้อ')
@section('page-topic', 'ออกหน่วยรับซื้อขยะ')

@section('content')
<div class="container-fluid px-0" style="max-width: 650px; margin: 0 auto;">

    @if (session('success'))
        <div class="px-3 mt-2">
            <div class="alert alert-success border-0 shadow-sm rounded-4 py-2 d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
        </div>
    @endif

    {{-- Active Route Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3 mx-2 mt-2 bg-primary text-white overflow-hidden">
        <div class="card-body p-4 position-relative">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold">
                    <i class="bi bi-geo-alt-fill me-1"></i>เขตรับซื้อปัจจุบัน
                </span>
                <span class="small text-white-50">{{ now()->format('d/m/Y') }}</span>
            </div>

            @php
                $activeRoute = $routes->firstWhere('id', $currentRouteId);
            @endphp

            @if($currentRouteId === 'all' || !$activeRoute)
                <h3 class="fw-bold mb-1">ทุกเขต / ไม่จำกัดโซน</h3>
                <p class="mb-0 text-white-50 small">ดึงสมาชิกทั้งหมดในองค์กร (รับซื้อ ณ จุดรับซื้อถาวร)</p>
            @else
                <h3 class="fw-bold mb-1">{{ $activeRoute->route_name }}</h3>
                <p class="mb-0 text-white-50 small">
                    ครอบคลุม: 
                    @foreach($activeRoute->zones as $zone)
                        <span class="badge bg-light text-dark me-1">{{ $zone->zonename }}</span>
                    @endforeach
                </p>
            @endif

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('keptkayas.purchase.select_user') }}" class="btn btn-light text-primary rounded-4 fw-bold px-4 shadow-sm me-auto">
                    <i class="bi bi-arrow-right-circle-fill me-1"></i> ไปหน้าเลือกสมาชิก
                </a>
                <button type="button" class="btn btn-outline-light rounded-4 d-md-none" data-bs-toggle="offcanvas" data-bs-target="#routeOffcanvas">
                    <i class="bi bi-sliders me-1"></i> เปลี่ยนเขต
                </button>
            </div>
        </div>
    </div>

    {{-- Routes List for Desktop View --}}
    <div class="card border-0 shadow-sm rounded-4 mx-2 d-none d-md-block">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-map me-2 text-primary"></i>รายชื่อเขตรับซื้อทั้งหมด</h6>
        </div>
        <div class="list-group list-group-flush border-top">
            <form action="{{ route('keptkayas.purchase.select_route') }}" method="POST">
                @csrf
                <input type="hidden" name="route_id" value="all">
                <button type="submit" class="list-group-item list-group-item-action py-3 px-3 d-flex align-items-center justify-content-between border-0 {{ $currentRouteId === 'all' ? 'bg-primary bg-opacity-10 fw-bold' : '' }}">
                    <div>
                        <div class="text-dark"><i class="bi bi-globe me-2 text-secondary"></i>ทุกเขต / ไม่จำกัดโซน</div>
                        <small class="text-muted">ดึงรายชื่อสมาชิกทั้งหมดในองค์กร</small>
                    </div>
                    @if($currentRouteId === 'all')
                        <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                    @endif
                </button>
            </form>

            @foreach ($routes as $route)
                <form action="{{ route('keptkayas.purchase.select_route') }}" method="POST">
                    @csrf
                    <input type="hidden" name="route_id" value="{{ $route->id }}">
                    <button type="submit" class="list-group-item list-group-item-action py-3 px-3 d-flex align-items-center justify-content-between border-top {{ $currentRouteId == $route->id ? 'bg-primary bg-opacity-10 fw-bold' : '' }}">
                        <div>
                            <div class="text-dark"><i class="bi bi-geo-alt me-2 text-primary"></i>{{ $route->route_name }}</div>
                            <small class="text-muted">
                                โซน: {{ $route->zones->pluck('zonename')->implode(', ') }}
                            </small>
                        </div>
                        @if($currentRouteId == $route->id)
                            <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    </div>

</div>

{{-- Mobile Bottom Sheet (Offcanvas) --}}
<div class="offcanvas offcanvas-bottom rounded-top-5 h-auto d-md-none" tabindex="-1" id="routeOffcanvas" aria-labelledby="routeOffcanvasLabel" style="max-height: 80vh;">
    <div class="offcanvas-header border-bottom py-3">
        <h6 class="offcanvas-title fw-bold text-dark" id="routeOffcanvasLabel">
            <i class="bi bi-geo-alt-fill text-primary me-2"></i>เลือกเขตการรับซื้อประจำวัน
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="list-group list-group-flush">
            <form action="{{ route('keptkayas.purchase.select_route') }}" method="POST">
                @csrf
                <input type="hidden" name="route_id" value="all">
                <button type="submit" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center justify-content-between border-0 {{ $currentRouteId === 'all' ? 'bg-primary bg-opacity-10 fw-bold text-primary' : '' }}">
                    <div>
                        <div><i class="bi bi-globe me-2"></i>ทุกเขต / ไม่จำกัดโซน</div>
                        <small class="text-muted d-block fw-normal">ดึงสมาชิกทั้งหมด (ไม่กรองโซน)</small>
                    </div>
                    @if($currentRouteId === 'all')
                        <i class="bi bi-check-lg fs-4 text-primary"></i>
                    @endif
                </button>
            </form>

            @foreach ($routes as $route)
                <form action="{{ route('keptkayas.purchase.select_route') }}" method="POST">
                    @csrf
                    <input type="hidden" name="route_id" value="{{ $route->id }}">
                    <button type="submit" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center justify-content-between border-top {{ $currentRouteId == $route->id ? 'bg-primary bg-opacity-10 fw-bold text-primary' : '' }}">
                        <div>
                            <div><i class="bi bi-geo-alt me-2"></i>{{ $route->route_name }}</div>
                            <small class="text-muted d-block fw-normal">
                                โซน: {{ $route->zones->pluck('zonename')->implode(', ') }}
                            </small>
                        </div>
                        @if($currentRouteId == $route->id)
                            <i class="bi bi-check-lg fs-4 text-primary"></i>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</div>
@endsection