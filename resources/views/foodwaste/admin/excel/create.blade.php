@extends('layouts.admin1')
@section('nav-excel')
    active
@endsection
@section('content')
    <form action="{{ route('admin.excel.store')}}" enctype="multipart/form-data" method="post">
        @csrf
        <input type="file" class="form-control" name="file" id="">
        <input type="submit" class="btn btn-success mt-2 d-flex mr-0 ml-auto d-lg-flex" value="import">
    </form>
@endsection
