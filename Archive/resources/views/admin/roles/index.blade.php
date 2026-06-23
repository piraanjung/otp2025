@extends('layouts.super-admin')

@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body p-3">
                    <div class="row">
                        @foreach ($roles as $role)
                            <div class="col-lg-4 col-md-4 col-12  mb-3 mt-md-0">
                                <div class="card"
                                    style="background-image: url('{{ asset('soft-ui/assets/img/curved-images/white-curved.jpeg') }}')">
                                    <span class="mask bg-gradient-dark opacity-9 border-radius-xl"></span>
                                    <div class="card-body p-3 position-relative">
                                        <div class="row">
                                            <div class="col-8 text-start">
                                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                                    <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10"
                                                        aria-hidden="true"></i>
                                                </div>
                                                <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                                    {{ $role->name }}
                                                </h5>
                                                <span class="text-white text-sm">Role</span>
                                            </div>
                                            <div class="col-4">
                                                <a href="{{ route('admin.roles.edit', $role->id) }}"
                                                    class="btn bg-warning btn-sm col-12 text-white ">แก้ไข</a>
                                                <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}"
                                                    class="col-12" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn bg-danger btn-sm col-12 text-white">ลบ</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        {{-- <div class="col-lg-4 col-md-4 col-12  mb-3 mt-md-0">
                            <div class="card"
                                style="background-image: url('{{ asset('soft-ui/assets/img/curved-images/white-curved.jpeg') }}')">
                                <span class="mask bg-gradient-dark opacity-9 border-radius-xl"></span>
                                <div class="card-body p-3 position-relative">
                                    <div class="row">
                                        <div class="col-8 text-start">
                                            <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                                <i class="ni ni-circle-08 text-dark text-gradient text-lg opacity-10"
                                                    aria-hidden="true"></i>
                                            </div>
                                            <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                                {{ $role->name }}
                                            </h5>
                                            <span class="text-white text-sm">Role</span>
                                        </div>
                                        <div class="col-4">
                                            <a href="{{ route('admin.roles.edit', $role->id) }}"
                                                class="btn bg-warning btn-sm col-12 text-white ">แก้ไข</a>
                                            <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}"
                                                class="col-12" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn bg-danger btn-sm col-12 text-white">ลบ</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <div class="col-lg-4 col-md-4 col-12  mb-3 ">
                            <div class="cardcard-plain border border-radius-xl">
                                <div class="card-body d-flex flex-column justify-content-center text-center">
                                    <a href="{{ route('admin.roles.create') }}">
                                        <i class="fa fa-plus text-secondary mb-4" aria-hidden="true"></i>
                                        <h5 class=" text-secondary">เพิ่ม Role </h5>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




@endsection
{{-- <x-admin-layout>
    <div class="py-12 w-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2">
                <div class="flex justify-end p-2">
                    <a href="{{ route('admin.roles.create') }}" class="px-4 py-2 bg-green-700 hover:bg-green-500 rounded-md">Create Role</a>
                </div>
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($roles as $role)
                                <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    {{ $role->name }}
                                </div>
                                </td>
                                <td>
                                    <div class="flex justify-end">
                                        <div class="flex space-x-2">
                                         <a href="{{ route('admin.roles.edit', $role->id) }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white rounded-md">Edit</a>
                                         <form class="px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded-md" method="POST" action="{{ route('admin.roles.destroy', $role->id) }}" onsubmit="return confirm('Are you sure?');">
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
