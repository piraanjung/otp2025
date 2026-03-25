@extends('layouts.print')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header pb-0"><h6>ประเภทหน่วยงานทั้งหมด</h6></div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ชื่อประเภท</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">รหัส</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($types as $type)
                                <tr>
                                    <td><div class="d-flex px-3"><h6 class="mb-0 text-sm">{{ $type->name }}</h6></div></td>
                                    <td><p class="text-xs font-weight-bold mb-0">{{ $type->code ?: '-' }}</p></td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-gradient-{{ $type->status ? 'success' : 'secondary' }}">
                                            {{ $type->status ? 'ใช้งาน' : 'ปิด' }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <button class="btn btn-link text-secondary mb-0" data-bs-toggle="modal" data-bs-target="#editModal{{$type->id}}">แก้ไข</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header pb-0"><h6>เพิ่มประเภทใหม่</h6></div>
                <div class="card-body">
                    <form action="{{ route('admin.org-types.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">ชื่อประเภท (เช่น อบต.)</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">รหัสย่อ</label>
                            <input type="text" name="code" class="form-control">
                        </div>
                        <button type="submit" class="btn bg-gradient-primary w-100">บันทึกข้อมูล</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
