@extends('layouts.super-admin')
@section('nav-main', 'ประเภทการชำระเงิน')
@section('nav-main-url')
    {{route('admin.pricing_types.index')}}
@endsection
@section('nav-current', 'แก้ไขประเภทการชำระเงิน')
@section('nav-current-title', 'แก้ไขประเภทการชำระเงิน')
@section('content')

     <div class="card">
        <div class="card-header pb-0">
            <h6 class="mb-0">แก้ไขประเภทการชำระเงิน</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pricing_types.update', $pricingType->id) }}" method="POST">
                @csrf
                @method('PUT')
                {{-- สำคัญ: ต้องส่ง PricingType instance ตัวเดียวเข้าไป --}}
                @include('superadmin.pricing_types.form', ['pricingType' => $pricingType])
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn bg-gradient-primary">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>


@endsection