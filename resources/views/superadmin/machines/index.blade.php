@extends('layouts.super-admin') {{-- สมมติว่ามี layout หลักชื่อ app.blade.php --}}

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Machine List</h1>
        <a href="{{ route('superadmin.machines.create') }}" class="btn btn-primary">
            Add New Machine
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Machine ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ready</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Heartbeat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($machines as $machine)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $machine->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $machine->machine_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if ($machine->status === 'online') bg-green-100 text-green-800
                                @elseif ($machine->status === 'busy') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif
                            ">
                                {{ $machine->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $machine->machine_ready ? 'Yes' : 'No' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $machine->last_heartbeat_at ? $machine->last_heartbeat_at->diffForHumans() : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('superadmin.machines.show', $machine) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">View</a>
                            <a href="{{ route('superadmin.machines.edit', $machine) }}" class="text-yellow-600 hover:text-yellow-900 mr-2">Edit</a>
                            
                            <form action="{{ route('superadmin.machines.destroy', $machine) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this machine?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection