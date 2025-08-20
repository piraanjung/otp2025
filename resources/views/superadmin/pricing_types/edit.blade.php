@extends('layouts.super-admin')
@section('nav-main')
    Edit Pricing Type
@endsection
@section('nav-main-url')
    {{route('superadmin.meter_types.index')}}
@endsection
@section('nav-current')
    Edit Pricing Type
@endsection
@section('nav-current-title')
    Edit Pricing Type
@endsection
@section('content')

     <div class="card">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">Edit Pricing Type</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('superadmin.pricing_types.update', $pricingType->id) }}" method="POST">
                @csrf
                @method('PUT')
                {{-- สำคัญ: ต้องส่ง PricingType instance ตัวเดียวเข้าไป --}}
                @include('superadmin.pricing_types.form', ['pricingType' => $pricingType])
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">Update Pricing Type</button>
                </div>
            </form>
        </div>
    </div>


@endsection