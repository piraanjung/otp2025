@extends('layouts.keptkaya')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">แก้ไขราคารับซื้อ</h1>
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
            <form action="{{ route('keptkaya.tbank.prices.update', $price->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('keptkaya.tbank.prices.form', ['price' => $price])
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">บันทึกการแก้ไข</button>
                    <a href="{{ route('keptkaya.tbank.prices.index') }}" class="btn btn-secondary">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
