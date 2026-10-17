@extends('layouts.admin1')
@section('nav-main')
    Manage Period
@endsection
@section('nav-main-url')
    {{route('superadmin.budget_years.index')}}
@endsection
@section('nav-current')
    Period
@endsection
@section('nav-current-title')
    Period
@endsection
@section('content')


    <a href="{{ route('superadmin.tw_periods.create') }}" class="btn btn-info">Create New Period</a>

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
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Period Name</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Budget Year</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Start Date</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">End Date</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Status</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Actions</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($periods as $period)
                            <tr>
                                <td>{{ $period->id }}</td>
                                <td>{{ $period->period_name }}</td>
                                <td>{{ $period->budgetYear->year ?? 'N/A' }}</td>
                                <td>{{ $period->start_date->format('Y-m-d') }}</td>
                                <td>{{ $period->end_date->format('Y-m-d') }}</td>
                                <td>
                                    <span class="status-{{ $period->status }}">
                                        {{ ucfirst($period->status) }}
                                    </span>
                                </td>
                                <td class="d-flex">
                                    <a href="{{ route('superadmin.tw_periods.edit', $period->id)  }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    @if ($period->status === 'draft')

                                        <form action="{{ route('superadmin.tw_periods.destroy', $period->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this period? This action cannot be undone if invoices are generated.')">Delete</button>
                                        </form>
                                    @else
                                        <button disabled style="opacity: 0.5;" class="btn btn-danger">Delete</button>
                                    @endif

                                    {{-- ปุ่มสำหรับสร้าง Invoice (แสดงเฉพาะ Period ที่ published และยังไม่มี Invoice) --}}
                                    @if ($period->status === 'published')
                                        <form action="{{ route('superadmin.tw_periods.generate_invoices', $period->id) }}"
                                            method="POST" style="display:inline-block;" >
                                            @csrf
                                            <button type="submit" class="btn btn-success"
                                                onclick="return confirm('Are you sure you want to generate invoices for this period? This might take some time.')">Generate
                                                Invoices</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>



@endsection