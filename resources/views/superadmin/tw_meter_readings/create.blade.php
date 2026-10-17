@extends('layouts.super-admin')
@section('nav-main')
    Manage Period
@endsection
@section('nav-main-url')
    {{route('superadmin.meter_types.index')}}
@endsection
@section('nav-current')
   Create Period
@endsection
@section('nav-current-title')
    Create New Period
@endsection
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="card-title mb-0 text-white">Add New Meter Reading</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('superadmin.tw_meter_readings.store') }}" method="POST">
                @csrf
                @include('superadmin.tw_meter_readings.form', ['reading' => new App\Models\Tabwater\TwMeterReading()])
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">Save Meter Reading</button>
                    <a href="{{ route('superadmin.tw_meter_readings.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection