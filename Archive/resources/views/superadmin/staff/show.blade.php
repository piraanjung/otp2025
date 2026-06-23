@extends('layouts.admin1')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title mb-0">Staff Member Details</h1>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID:</dt>
                <dd class="col-sm-9">{{ $staffMember->id }}</dd>

                <dt class="col-sm-3">Username:</dt>
                <dd class="col-sm-9">{{ $staffMember->username }}</dd>

                <dt class="col-sm-3">Name:</dt>
                <dd class="col-sm-9">{{ $staffMember->prefix }} {{ $staffMember->firstname }} {{ $staffMember->lastname }}</dd>

                <dt class="col-sm-3">Email:</dt>
                <dd class="col-sm-9">{{ $staffMember->email }}</dd>

                <dt class="col-sm-3">Phone:</dt>
                <dd class="col-sm-9">{{ $staffMember->phone ?? 'N/A' }}</dd>

                <dt class="col-sm-3">ID Card:</dt>
                <dd class="col-sm-9">{{ $staffMember->id_card ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Gender:</dt>
                <dd class="col-sm-9">{{ ucfirst($staffMember->gender ?? 'N/A') }}</dd>

                <dt class="col-sm-3">Status:</dt>
                <dd class="col-sm-9">
                    <span class="badge {{ $staffMember->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($staffMember->status) }}
                    </span>
                </dd>

                <dt class="col-sm-3">Address:</dt>
                <dd class="col-sm-9">{{ $staffMember->address ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Organization:</dt>
                <dd class="col-sm-9">{{ $staffMember->organization->org_name ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Zone:</dt>
                <dd class="col-sm-9">{{ $staffMember->zone->zone_name ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Zone Block:</dt>
                <dd class="col-sm-9">{{ $staffMember->zoneBlock->zone_block_name ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Province Code:</dt>
                <dd class="col-sm-9">{{ $staffMember->province_code ?? 'N/A' }}</dd>

                <dt class="col-sm-3">District Code:</dt>
                <dd class="col-sm-9">{{ $staffMember->district_code ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Tambon Code:</dt>
                <dd class="col-sm-9">{{ $staffMember->tambon_code ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Comment:</dt>
                <dd class="col-sm-9">{{ $staffMember->comment ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Created At:</dt>
                <dd class="col-sm-9">{{ $staffMember->created_at->format('Y-m-d H:i:s') }}</dd>

                <dt class="col-sm-3">Updated At:</dt>
                <dd class="col-sm-9">{{ $staffMember->updated_at->format('Y-m-d H:i:s') }}</dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ route('superadmin.staff.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection
