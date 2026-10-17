@extends('layouts.super-admin')
@section('nav-main')
    Manage Budget Year
@endsection
@section('nav-main-url')
    {{route('superadmin.budget_years.index')}}
@endsection
@section('nav-current')
    Edit Budget Year
@endsection
@section('nav-current-title')
    Edit Budget Year
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
                <form action="{{ route('superadmin.budget_years.update', $budgetYear->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('superadmin.budget_years.form', ['budgetYear' => $budgetYear])
                    <button type="submit" class="btn btn-info">Update Budget Year</button>
                </form>

            </div>
        </div>
    </div>



@endsection