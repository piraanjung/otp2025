@extends('layouts.super-admin')
@section('nav-main')
    Manage Period
@endsection
@section('nav-main-url')
    {{route('superadmin.budget_years.index')}}
@endsection
@section('nav-current')
    Edit Period
@endsection
@section('nav-current-title')
    Edit Period
@endsection
@section('content')
    <div class="card card-body p-2">
        <div class="row">
            <div class="col-12 col-lg-8 m-auto">
                <form action="{{ route('superadmin.tw_periods.update', $period->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('superadmin.tw_periods.form', ['period' => $period])
                    <button type="submit" class="btn btn-info">Update Period</button>
                </form>
            </div>
        </div>
    </div>



@endsection