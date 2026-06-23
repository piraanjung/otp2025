@extends('layouts.super-admin')
@section('nav-main')
    Manage Meter Types
@endsection
@section('nav-main-url')
    {{route('superadmin.meter_types.index')}}
@endsection
@section('nav-current')
    Meter Types Table
@endsection
@section('nav-current-title')
    Meter Types Table
@endsection
@section('content')

    @if (session('success'))
        <div class="text-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="text-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('superadmin.meter_types.create') }}" class="btn btn-info">Create New Meter Type</a>

    <div class="card mb-4">
        <div class="card-header pb-0">
            <h6>Authors table</h6>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($meterTypes as $meterType)
                            <tr>
                                <td>{{ $meterType->id }}</td>
                                <td>{{ $meterType->meter_type_name }}</td>
                                <td>{{ $meterType->description }}</td>
                                <td class="d-flex">
                                    <a href="{{ route('superadmin.meter_types.edit', $meterType->id) }}" class="btn btn-warning btn-sm">Edit</a> 
                                    <form action="{{ route('superadmin.meter_types.destroy', $meterType->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this meter type?')">Delete</button>
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