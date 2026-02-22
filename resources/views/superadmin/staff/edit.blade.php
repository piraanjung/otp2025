@extends('layouts.admin1')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">Edit Staff Member</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('superadmin.staff.update', $staff->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('superadmin.staff.form', ['staffMember' => $staff])
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">Update Staff</button>
                    <a href="{{ route('superadmin.staff.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
