@extends('layouts.super-admin')

@section('content')
<div class="container mx-auto p-4 max-w-lg">
    <h1 class="text-2xl font-semibold mb-6">Create New Machine</h1>
    
    <form action="{{ route('superadmin.machines.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf

        {{-- Machine ID --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="machine_id">Machine ID</label>
            <input type="text" name="machine_id" id="machine_id" value="{{ old('machine_id') }}" required 
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('machine_id') border-red-500 @enderror">
            @error('machine_id')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Org ID FK (ตัวอย่างแบบง่าย: Input) --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="org_id_fk">Organization ID (FK)</label>
            <input type="number" name="org_id_fk" id="org_id_fk" value="{{ old('org_id_fk') }}" required 
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('org_id_fk') border-red-500 @enderror">
            @error('org_id_fk')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Status --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="status">Status</label>
            <select name="status" id="status" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="offline" {{ old('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                <option value="idle" {{ old('status') == 'idle' ? 'selected' : '' }}>Idle</option>
                <option value="busy" {{ old('status') == 'busy' ? 'selected' : '' }}>Busy</option>
                <option value="online" {{ old('status') == 'online' ? 'selected' : '' }}>Online</option>
            </select>
        </div>

        {{-- Machine Ready --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Machine Ready</label>
            <input type="checkbox" name="machine_ready" id="machine_ready" value="1" {{ old('machine_ready') ? 'checked' : '' }}
                   class="mr-2 leading-tight">
            <span class="text-sm">Is the machine ready to work?</span>
        </div>

        {{-- Pending Command (Optional) --}}
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="pending_command">Pending Command</label>
            <input type="text" name="pending_command" id="pending_command" value="{{ old('pending_command') }}"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Create Machine
            </button>
            <a href="{{ route('superadmin.machines.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection