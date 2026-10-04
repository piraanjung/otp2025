@extends('layouts.super-admin')
@section('nav-main', 'อัตราการชำระตามประเภทมิเตอร์')
@section('nav-main-url', route('admin.meter_rates.index'))
@section('nav-current', 'แก้ไขอัตราการชำระ')
@section('nav-current-title', 'แก้ไขอัตราการชำระ')
@section('content')

    <div class="card card-body p-2">
        <div class="row">
            <div class="col-12 col-lg-8 m-auto">
                <form action="{{ route('admin.meter_rates.update', $meterRateConfig->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('superadmin.meter_rates.form')
                    <button type="submit" class="btn bg-gradient-primary">บันทึกการแก้ไข</button>
                </form>

            </div>
        </div>
    </div>



@endsection