@extends('layouts.super-admin')
@section('nav-main')
    Pricing Types
@endsection
@section('nav-main-url')
    {{route('meter_types.index')}}
@endsection
@section('nav-current')
    Pricing Types
@endsection
@section('nav-current-title')
    Pricing Types
@endsection
@section('content')


    <a href="{{ route('admin.pricing_types.create') }}" class="btn btn-info">Create New Pricing Type</a>
    <div class="card mb-4">
        <div class="card-header pb-0">
            <h6>Authors table</h6>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">ID</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Name</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Description</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Actions</th>
                           
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($pricingTypes as $pricingType)
                            <tr>
                                <td>{{ $pricingType->id }}</td>
                    <td>{{ $pricingType->name }}</td>
                    <td>{{ $pricingType->description }}</td>
                                <td class="d-flex">
                                    <a href="{{ route('admin.pricing_types.edit', $pricingType->id) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('admin.pricing_types.destroy', $pricingType->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this pricing type? This might affect existing rate configurations.')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>



@endsection