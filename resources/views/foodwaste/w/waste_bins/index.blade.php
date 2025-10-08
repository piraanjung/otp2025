@extends('layouts.foodwaste')

@section('title_page', 'ถังขยะเปียก')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>ถังขยะเปียก: {{ $w_user->firstname }} {{ $w_user->lastname }}</h6>
                    <a href="{{ route('foodwaste.waste_bins.create', $w_user->id) }}" class="btn bg-gradient-primary btn-sm mb-0">เพิ่มถังขยะใหม่</a>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                            <span class="alert-text text-white"><strong>สำเร็จ!</strong> {{ session('success') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                            <span class="alert-text text-white"><strong>เกิดข้อผิดพลาด!</strong> โปรดตรวจสอบข้อมูลอีกครั้ง</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">รหัสถัง</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ประเภทถัง</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ตำแหน่ง</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะถัง</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ใช้งานสำหรับรายปี?</th>
                                    <th class="text-secondary opacity-7">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($wasteBins as $bin)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $bin->bin_code ?? 'N/A' }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $bin->bin_type }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $bin->location_description ?? 'ไม่ระบุ' }}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-{{ $bin->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($bin->status) }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <form action="{{ route('foodwaste.waste_bins.update', $bin->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-check form-switch ps-0 d-inline-block">
                                                <input class="form-check-input ms-auto" type="checkbox"
                                                    id="active_bin_{{ $bin->id }}"
                                                    name="is_active_for_annual_collection" value="1"
                                                    {{ $bin->is_active_for_annual_collection ? 'checked' : '' }}
                                                    onchange="this.form.submit()">
                                            </div>
                                            <input type="hidden" name="status" value="{{ $bin->status }}">
                                            <input type="hidden" name="bin_code" value="{{ $bin->bin_code }}">
                                            <input type="hidden" name="bin_type" value="{{ $bin->bin_type }}">
                                            <input type="hidden" name="location_description" value="{{ $bin->location_description }}">
                                            <input type="hidden" name="latitude" value="{{ $bin->latitude }}">
                                            <input type="hidden" name="longitude" value="{{ $bin->longitude }}">
                                        </form>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('foodwaste.waste_bins.edit', $bin->id) }}" class="btn btn-link text-secondary font-weight-bold text-xs px-0 mb-0 me-2" data-toggle="tooltip" data-original-title="Edit bin">
                                            <i class="fas fa-edit me-1"></i> แก้ไข
                                        </a>
                                        <form action="{{ route('foodwaste.waste_bins.edit', $bin->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger text-gradient px-0 mb-0" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบถังขยะนี้?')">
                                                <i class="fas fa-trash-alt me-1"></i> ลบ
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">ไม่มีถังขยะสำหรับผู้ใช้นี้</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $wasteBins->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection