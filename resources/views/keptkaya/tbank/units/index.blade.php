@extends('layouts.keptkaya')

@section('content')
    <div class="card">
        <div class="card-header"></div>
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('keptkaya.tbank.units.create') }}" class="btn btn-primary">Create New Unit</a>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Unit Name</th>
                        <th>Status</th>
                        <th>Deleted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($units as $unit)
                        <tr>
                            <td>{{ $unit->id }}</td>
                            <td>{{ $unit->unitname }}</td>
                            <td>{{ $unit->unit_short_name }}</td>
                            <td>{{ $unit->status ? 'Active' : 'Inactive' }}</td>
                            <td>{{ $unit->deleted ? 'Yes' : 'No' }}</td>
                            <td>
                                <a href="{{ route('keptkaya.tbank.units.show', $unit) }}" class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('keptkaya.tbank.units.edit', $unit) }}"
                                    class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('keptkaya.tbank.units.destroy', $unit) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this unit?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No units found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>

@endsection