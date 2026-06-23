@extends('layouts.super-admin')
@section('nav-main')
    Create Pricing Type
@endsection
@section('nav-main-url')
    {{route('meter_types.index')}}
@endsection
@section('nav-current')
    Create New Pricing Type
@endsection
@section('nav-current-title')
    Create New Pricing Type
@endsection
@section('content')

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">Create New Pricing Type</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pricing_types.store') }}" method="POST">
                @csrf
                {{-- สำคัญ: ต้องส่ง PricingType instance ตัวเดียวเข้าไป --}}
                @include('superadmin.pricing_types.form', ['pricingType' => new \App\Models\Tabwater\TwPricingType()])
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">Create Pricing Type</button>
                </div>
            </form>
        </div>
    </div>
@endsection