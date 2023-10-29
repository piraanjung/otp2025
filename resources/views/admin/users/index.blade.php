@extends('layouts.admin1')

@section('content')

<div class="card">
    <div class="table-responsive">
      <table class="table align-items-center mb-0">
        <thead>
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Project</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Budget</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Completion</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <div class="d-flex px-2">
                <div>
                  <img src="https://demos.creative-tim.com/soft-ui-design-system-pro/assets/img/logos/small-logos/logo-spotify.svg" class="avatar avatar-sm rounded-circle me-2">
                </div>
                <div class="my-auto">
                  <h6 class="mb-0 text-xs">Spotify</h6>
                </div>
              </div>
            </td>
            <td>
              <p class="text-xs font-weight-bold mb-0">$2,500</p>
            </td>
            <td>
              <span class="badge badge-dot me-4">
                <i class="bg-info"></i>
                <span class="text-dark text-xs">working</span>
              </span>
            </td>
            <td class="align-middle text-center">
              <div class="d-flex align-items-center">
                <span class="me-2 text-xs">60%</span>
                <div>
                  <div class="progress">
                    <div class="progress-bar bg-info" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                  </div>
                </div>
              </div>
            </td>

            <td class="align-middle">
              <button class="btn btn-link text-secondary mb-0">
                <i class="fa fa-ellipsis-v text-xs" aria-hidden="true"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
</div>

@endsection
{{-- <x-admin-layout>
    <div class="py-12 w-full">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2">
                <a href="{{ route('admin.users.create') }}"
                 class="bg-blue-600 hover:bg-blue-300 text-black justify-end p-2 rounded-md">Create</a>

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
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Email</th>
                                            <th scope="col" class="relative px-6 py-3">
                                                <span class="sr-only">Edit</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($users as $user)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        {{ $user->name }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        {{ $user->email }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flex justify-end">
                                                        <div class="flex space-x-2">
                                                            <a href="{{ route('admin.users.show', $user->id) }}"
                                                                class="px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white rounded-md">Roles</a>
                                                            <form
                                                                class="px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded-md"
                                                                method="POST"
                                                                action="{{ route('admin.users.destroy', $user->id) }}"
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
