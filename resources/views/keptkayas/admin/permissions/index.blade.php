@extends('layouts.admin1')

@section('content')
    <div class="row">
        @foreach ($permissions as $key => $permission)
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-6 d-flex align-items-center">
                                <h6 class="mb-0"></h6>
                            </div>
                            <div class="col-6 text-end">
                                <button class="btn btn-outline-primary btn-sm mb-0">{{ $key }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3 pb-0">
                        <ul class="list-group">
                            @foreach ($permission as $item)
                                <li
                                    class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-1 text-dark font-weight-bold text-sm">{{ $item->name }}</h6>
                                        <span class="text-xs">{{ $item->description }}</span>
                                    </div>
                                    <div class="d-flex align-items-center text-sm">
                                        <div class="">
                                            <a href="{{ route('admin.permissions.edit', $item->id) }}"
                                                class="badge badge-secondary badge-sm text-black">แก้ไข</a>
                                            <form class="" method="POST"
                                                action="{{ route('admin.permissions.destroy', $item->id) }}"
                                                onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="badge badge-primary border-0 badge-sm text-black"
                                                    type="submit">ลบข้อมูล</button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

{{-- <x-admin-layout>

    <div class="py-12 w-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2">
                <div class="flex justify-end p-2">
                    <a href="{{ route('admin.permissions.create') }}"
                        class="px-4 py-2 bg-green-700 hover:bg-green-500 rounded-md">Create Permission</a>
                </div>
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Name</th>
                                            <th scope="col" class="relative px-6 py-3">
                                                <span class="sr-only">Edit</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($permissions as $permission)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        {{ $permission->name }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        {{ $permission->description }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        {{ $permission->permission_group }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flex justify-end">
                                                        <div class="flex space-x-2">
                                                            <a href="{{ route('admin.permissions.edit', $permission->id) }}"
                                                                class="px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white rounded-md">Edit</a>
                                                            <form
                                                                class="px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded-md"
                                                                method="POST"
                                                                action="{{ route('admin.permissions.destroy', $permission->id) }}"
                                                                onsubmit="return confirm('Are you sure?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout> --}}
