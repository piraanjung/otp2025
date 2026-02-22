@extends('layouts.super-admin')
@section('nav-main')
    Create Budget Year
@endsection
@section('nav-main-url')
    {{route('superadmin.meter_types.index')}}
@endsection
@section('nav-current')
   Create New Budget Year
@endsection
@section('nav-current-title')
    Create New Budget Year
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
                <form action="{{ route('superadmin.budget_years.store') }}" method="POST">
                    @csrf
                    @include('superadmin.budget_years.form', ['budgetYear' => new App\Models\BudgetYear()])
                    <button type="submit" class="btn btn-info">Create Budget Year</button>
                </form>

            </div>
        </div>
    </div>



@endsection