@extends('layouts.super-admin')
@section('nav-main')
    Manage Meter Types
@endsection
@section('nav-main-url')
    {{route('superadmin.meter_types.index')}}
@endsection
@section('nav-current')
    Edit Meter Type
@endsection
@section('nav-current-title')
    Edit Meter Type
@endsection
@section('content')
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="error">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card card-body p-2">
        <div class="row">
            <div class="col-12 col-lg-8 m-auto">
              <form action="{{ route('superadmin.meter_types.update', $meterType->id) }}" method="POST">
        @csrf
        @method('PUT')

                    <div class="form-group is-focused">
                        <label for="exampleFormControlInput1">Name:</label>
                        <input type="text" class="form-control" id="name" name="name"  value="{{ old('name', $meterType->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Description:</label>
                        <textarea class="form-control" id="description" name="description"
                            rows="3">{{ old('description', $meterType->description) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Edit Meter Type</button>
                </form>

            </div>
        </div>
    </div>



@endsection