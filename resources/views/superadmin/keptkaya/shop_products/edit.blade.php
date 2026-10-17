@extends('layouts.keptkaya')

@section('content')
<div class="container-fluid py-4">
    <div class="card mb-4">
        <div class="card-header pb-0">
            <h6>แก้ไขสินค้า</h6>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <form action="{{ route('keptkayas.shop-products.update', $shop_product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('superadmin.keptkaya.shop_products.form')
                <div class="d-flex justify-content-end me-4 mt-4">
                    <a href="{{ route('keptkayas.shop-products.index') }}" class="btn btn-secondary me-2">ยกเลิก</a>
                    <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Preview image before upload
        const imageInput = document.getElementById('image_path');
        const imagePreview = document.getElementById('image-preview');

        if (imageInput) {
            imageInput.addEventListener('change', function (e) {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        imagePreview.src = event.target.result;
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(e.target.files[0]);
                }
            });
        }
    });
</script>
@endsection