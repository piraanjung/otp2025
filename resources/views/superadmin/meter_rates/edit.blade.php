@extends('layouts.admin1')
@section('nav-main')
    Manage Meter Types
@endsection
@section('nav-main-url')
    {{route('meter_types.index')}}
@endsection
@section('nav-current')
    Edit Meter Rate Configuration
@endsection
@section('nav-current-title')
    Edit Meter Rate Configuration
@endsection
@section('content')

    <div class="card card-body p-2">
        <div class="row">
            <div class="col-12 col-lg-8 m-auto">
                <form action="{{ route('admin.meter_rates.update', $meterRateConfig->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('superadmin.meter_rates.form')
                    <button type="submit" class="btn btn-success">Update Rate Configuration</button>
                </form>

            </div>
        </div>
    </div>



@endsection