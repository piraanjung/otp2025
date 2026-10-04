@extends('layouts.super-admin')
@section('nav-main', 'ประเภทการชำระเงิน')
@section('nav-main-url')
    {{route('meter_types.index')}}
@endsection
@section('nav-current', 'เพิ่มประเภทการชำระเงิน')
@section('nav-current-title', 'เพิ่มประเภทการชำระเงิน')
@section('content')

    <div class="card">
        <div class="card-header pb-0">
            <h6 class="mb-0">เพิ่มประเภทการชำระเงิน</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pricing_types.store') }}" method="POST">
                @csrf
                {{-- สำคัญ: ต้องส่ง PricingType instance ตัวเดียวเข้าไป --}}
                @include('superadmin.pricing_types.form', ['pricingType' => new \App\Models\Tabwater\TwPricingType()])
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn bg-gradient-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
@endsection