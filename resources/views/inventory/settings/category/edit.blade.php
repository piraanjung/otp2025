@extends('inventory.inv_master')

@section('title', 'แก้ไขหมวดหมู่')
@section('header_title', 'แก้ไขหมวดหมู่พัสดุ')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4 shadow-sm border-0">
            <h5 class="fw-bold mb-3 text-primary">
                <i class="material-icons-round align-middle">edit</i> แก้ไขหมวดหมู่: {{ $category->name }}
            </h5>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('inventory.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="name" value="{{ old('name', $category->name) }}" placeholder="ชื่อหมวดหมู่" required>
                    <label>ชื่อหมวดหมู่</label>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">สายการอนุมัติ (Workflow)</label>
                    <select name="approval_workflow_id" class="form-control">
                        <option value="">-- ใช้ค่าเริ่มต้น / ไม่ระบุ --</option>
                        @foreach($workflows as $wf)
                            <option value="{{ $wf->id }}" {{ $category->approval_workflow_id == $wf->id ? 'selected' : '' }}>
                                {{ $wf->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('inventory.categories.index') }}" class="btn btn-secondary">ย้อนกลับ</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons-round align-middle">save</i> บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection