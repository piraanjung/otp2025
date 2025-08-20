@extends('layouts.admin1')
@section('nav-main')
    Manage Meter Types
@endsection
@section('nav-main-url')
    {{-- {{route('superadmin.meter_types.index')}} --}}
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

    <a href="{{ route('admin.meter_rates.create') }}" class="btn btn-info">Create New Rate Configuration</a>
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
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Meter Type</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Pricing Type</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Min Usage Charge</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Fixed Rate/Unit</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Effective Date</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">End Date</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Active</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Comment</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rateConfigs as $config)
                    
                            <tr>
                                <td>{{ $config->id }}</td>
                                <td>{{ $config->meterType->meter_type_name ?? 'N/A' }}</td>
                                <td>{{ $config->pricingType->name ?? 'N/A' }}</td>
                                <td>{{ number_format($config->min_usage_charge, 2) }}</td>
                                <td>{{ $config->fixed_rate_per_unit ? number_format($config->fixed_rate_per_unit, 4) : 'N/A' }}
                                </td>
                                <td>{{ $config->effective_date->format('Y-m-d') }}</td>
                                <td>{{ $config->end_date ? $config->end_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>
                                    <span class="{{ $config->is_active ? 'active-status' : 'inactive-status' }}">
                                        {{ $config->is_active ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>{{ $config->comment }}</td>

                                <td class="d-flex justify-content-sm-between">
                                     <a href="{{ route('admin.meter_rates.show', $config->id) }}"
                                        class="btn btn-info btn-sm mr-1"><i class="fa fa-eye"></i></a>
                                    <a href="{{ route('admin.meter_rates.edit', $config->id) }}"
                                        class="btn btn-warning btn-sm mr-1"><i class="fa fa-edit"></i></a>
                                    <form action="{{ route('admin.meter_rates.destroy', $config->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this meter type?')">
                                        <i class="fa fa-trash"></i>
                                        </button>
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