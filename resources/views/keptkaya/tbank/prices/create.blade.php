@extends('layouts.keptkaya')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">เพิ่มราคารับซื้อใหม่</h1>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('keptkayas.tbank.prices.store') }}" method="POST">
                    @csrf
                    @include('keptkayas.tbank.prices.form', ['price' => new App\Models\KeptKaya\KpTbankItemsPriceAndPoint()])
                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-success">บันทึกราคา</button>
                        <a href="{{ route('keptkayas.tbank.prices.index') }}" class="btn btn-secondary">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection