@extends('layouts.super-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>เลือกหน่วยงานเพื่อเข้าจัดการระบบ</h6>
                    <form action="{{ route('admin.org_selector') }}" method="GET" class="row mt-3">
                        <div class="col-md-4">
                            <select name="org_type" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">--- แสดงทุกประเภทหน่วยงาน ---</option>
                                @foreach($orgTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('org_type') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">หน่วยงาน</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">พื้นที่ (ตำบล/อำเภอ)</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($organizations as $org)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <img src="{{ asset('logo/'.$org->org_logo_img) }}" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $org->org_name }}</h6>
                                                <p class="text-xs text-secondary mb-0">ID: {{ $org->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $org->tambon_name }}</p>
                                        <p class="text-xs text-secondary mb-0">{{ $org->district_name }}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-success">Active</span>
                                    </td>
                                    <td class="align-middle">
                                        <form action="{{ route('admin.set_org_context') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="org_id" value="{{ $org->id }}">
                                            <button type="submit" class="btn btn-link text-primary font-weight-bold text-xs mb-0">
                                                <i class="fas fa-sign-in-alt me-1"></i> เข้าใช้งาน
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
