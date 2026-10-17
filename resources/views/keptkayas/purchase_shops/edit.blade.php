@extends('layouts.keptkaya')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">แก้ไขร้านรับซื้อ</h1>
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
                <form action="{{ route('keptkayas.purchase-shops.update', $shop->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('keptkayas.purchase_shops.form', ['shop' => $shop])
                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-success">บันทึกการแก้ไข</button>
                        <a href="{{ route('keptkayas.purchase-shops.index') }}" class="btn btn-secondary">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection