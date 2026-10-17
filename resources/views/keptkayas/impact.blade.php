@extends('layouts.keptkaya')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="javascript:history.back()" class="text-dark me-3"><i class="bi bi-chevron-left fs-4"></i></a>
        <h3 class="mb-0 fw-bold text-success">สถิติการช่วยโลก 🌍</h3>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-success text-white mb-4 overflow-hidden">
        <div class="card-body p-4 text-center position-relative">
            <div class="opacity-25 position-absolute top-0 end-0 m-2"><i class="bi bi-tree" style="font-size: 5rem;"></i></div>
            <p class="mb-1 opacity-75">คุณช่วยลดก๊าซเรือนกระจกสะสม</p>
            <h1 class="display-4 fw-bold mb-0">{{ number_format($totalCo2Saved, 4) }}</h1>
            <p class="fs-5 mb-0">kgCO2e (กิโลกรัมคาร์บอน)</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center">
                    <div class="bg-success-subtle rounded-circle d-inline-flex p-3 mb-2">
                        <i class="bi bi-tree-fill text-success fs-3"></i>
                    </div>
                    <h6 class="text-muted small">ปลูกต้นไม้</h6>
                    <h4 class="fw-bold mb-0">{{ number_format($trees, 1) }}</h4>
                    <small class="text-muted">ต้น/ปี</small>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center">
                    <div class="bg-warning-subtle rounded-circle d-inline-flex p-3 mb-2">
                        <i class="bi bi-fuel-pump-fill text-warning fs-3"></i>
                    </div>
                    <h6 class="text-muted small">ประหยัดน้ำมัน</h6>
                    <h4 class="fw-bold mb-0">{{ number_format($fuelSaved, 1) }}</h4>
                    <small class="text-muted">ลิตร</small>
                </div>
            </div>
        </div>
    </div>

    <h6 class="fw-bold mb-3">ที่มาของคาร์บอนที่ลดได้</h6>
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                    <i class="bi bi-basket text-success me-2 fs-5"></i>
                    <span>ขยะเปียก (Compost)</span>
                </div>
                <span class="fw-bold">{{ number_format($foodWasteCarbon, 2) }} kg</span>
            </div>
            <div class="progress rounded-pill" style="height: 10px;">
                @php $pFood = $totalCo2Saved > 0 ? ($foodWasteCarbon / $totalCo2Saved) * 100 : 0; @endphp
                <div class="progress-bar bg-success" style="width: {{ $pFood }}%"></div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                    <i class="bi bi-recycle text-primary me-2 fs-5"></i>
                    <span>ธนาคารขยะ (Recycle)</span>
                </div>
                <span class="fw-bold">{{ number_format($recycleCarbon, 2) }} kg</span>
            </div>
            <div class="progress rounded-pill" style="height: 10px;">
                @php $pRecycle = $totalCo2Saved > 0 ? ($recycleCarbon / $totalCo2Saved) * 100 : 0; @endphp
                <div class="progress-bar bg-primary" style="width: {{ $pRecycle }}%"></div>
            </div>
        </div>
    </div>

    <div class="alert alert-light border-0 shadow-sm rounded-4 p-3">
        <div class="d-flex">
            <i class="bi bi-info-circle-fill text-info me-2 fs-5"></i>
            <small class="text-muted">การคำนวณอ้างอิงตามค่า Emission Factor (EF) มาตรฐานองค์การบริหารจัดการก๊าซเรือนกระจก (TGO)</small>
        </div>
    </div>
</div>
 <div class="text-center mt-4">
<a href="{{ url('line/dashboard/'.$pref_id.'/'.Auth::user()->org_id_fk) }}"
   class="btn btn-outline-secondary">
   กลับหน้าหลัก
</a>
    </div>
@endsection
