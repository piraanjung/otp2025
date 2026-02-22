<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('เพิ่มผู้ใช้เข้าสู่บริการขยะ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('users.enroll.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="service_type" class="block text-sm font-medium text-gray-700">เลือกประเภทบริการ:</label>
                            <select name="service_type" id="service_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">-- เลือกประเภทบริการ --</option>
                                <option value="annual_collection">บริการเก็บขยะรายปี</option>
                                <option value="waste_bank">สมาชิกธนาคารขยะ</option>
                            </select>
                        </div>

                        <h3 class="text-lg font-medium text-gray-900 mb-4">ผู้ใช้ที่ยังไม่เป็นสมาชิกบริการขยะ:</h3>
                        @if ($eligibleUsers->isEmpty())
                            <p class="text-gray-600">ไม่มีผู้ใช้ที่เข้าเกณฑ์ในขณะนี้</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                เลือก
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                ชื่อ
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                อีเมล
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                สถานะปัจจุบัน (รายปี)
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                สถานะปัจจุบัน (ธนาคารขยะ)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($eligibleUsers as $user)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $user->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $user->email }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if ($user->is_annual_collection === null || $user->is_annual_collection === false)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            ยังไม่เป็น
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            เป็นสมาชิก
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if ($user->is_waste_bank === null || $user->is_waste_bank === false)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            ยังไม่เป็น
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            เป็นสมาชิก
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6">
                                <x-primary-button type="submit">
                                    {{ __('บันทึกผู้ใช้ที่เลือก') }}
                                </x-primary-button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>