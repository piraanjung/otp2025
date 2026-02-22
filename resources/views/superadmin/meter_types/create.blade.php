@extends('layouts.super-admin')
@section('nav-main')
    Manage Meter Types
@endsection
@section('nav-main-url')
    {{route('superadmin.meter_types.index')}}
@endsection
@section('nav-current')
    Create New Meter Type
@endsection
@section('nav-current-title')
    Create New Meter Type
@endsection
@section('content')
   
    <div class="card card-body p-2">
        <div class="row">
            <div class="col-12 col-lg-8 m-auto">
                <form action="{{ route('superadmin.meter_types.store') }}" method="POST">
                    @csrf
                    <div class="form-group is-focused">
                        <label for="exampleFormControlInput1">Name:</label>
                        <input type="text" class="form-control" id="name" name="meter_type_name" value="{{ old('meter_type_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Description:</label>
                        <textarea class="form-control" id="description" name="description"
                            rows="3">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Create Meter Type</button>
                </form>

            </div>
        </div>
    </div>



@endsection