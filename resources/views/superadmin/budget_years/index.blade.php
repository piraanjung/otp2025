@extends('layouts.super-admin')
@section('nav-main')
    Manage Budget Years
@endsection
@section('nav-main-url')
    {{route('superadmin.budget_years.index')}}
@endsection
@section('nav-current')
    Budget Years
@endsection
@section('nav-current-title')
    Budget Years
@endsection
@section('content')


    <a href="{{ route('superadmin.budget_years.create') }}" class="btn btn-info">Create New Meter Type</a>

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
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Year</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Start Date</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">End Date</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Active</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Actions</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($budgetyears as $budgetYear)
                            <tr>
                                <td>{{ $budgetYear->id }}</td>
                                <td>{{ $budgetYear->year }}</td>
                                <td>{{ $budgetYear->start_date->format('Y-m-d') }}</td>
                                <td>{{ $budgetYear->end_date->format('Y-m-d') }}</td>
                                <td>
                                    <span class="{{ $budgetYear->is_active ? 'active-status' : 'inactive-status' }}">
                                        {{ $budgetYear->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td class="d-flex">
                                    <a href="{{ route('superadmin.budget_years.edit', $budgetYear->id)  }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('superadmin.budget_years.destroy', $budgetYear->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this budget year?')">Delete</button>
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