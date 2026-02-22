@extends('layouts.admin1')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Staff Management</h1>
            <a href="{{ route('superadmin.staff.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus-fill me-1"></i> Add New Staff
            </a>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">All Staff Members</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Zone</th>
                                <th style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($staffMembers as $staff)
                                <tr>
                                    <td>{{ $staff->id }}</td>
                                    <td>{{ $staff->username }}</td>
                                    <td>{{ $staff->prefix }} {{ $staff->firstname }} {{ $staff->lastname }}</td>
                                    <td>{{ $staff->email }}</td>
                                    <td>{{ $staff->phone }}</td>
                                    <td>
                                        @forelse($staff->roles as $role)
                                            <span class="badge bg-primary me-1">{{ $role->display_name ?? $role->name }}</span>
                                        @empty
                                            <span class="badge bg-secondary">No Role</span>
                                        @endforelse
                                    </td>
                                    <td>{{ $staff->zone->zone_name ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('superadmin.staff.show', $staff->id) }}"
                                            class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('superadmin.staff.edit', $staff->id) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                              @forelse($staff->roles as $role)
                                        <form action="{{ route('superadmin.staff.destroy', $staff->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="role_name" value="{{$role->name}}">
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to remove staff role from this user?')">Remove
                                                {{$role->name}} Role</button>
                                        </form>
                                        @empty
                                            <span class="badge bg-secondary">No Role</span>
                                        @endforelse
                                        
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No staff members found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $staffMembers->links() }}
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('superadmin.dashboard') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>
                Back to Dashboard</a>
        </div>
    </div>
@endsection