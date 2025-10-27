@extends('layouts.keptkaya')
@section('page-topic', 'หน่วยนับสินค้า')
@section('nav-header', ' หน่วยนับสินค้า')
@section('nav-current', ' ข้อมูลหน่วยนับสินค้า')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('keptkayas.tbank.units.create') }}" class="btn btn-primary">เพิ่มหน่วยนับสินค้า</a>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>รหัส</th>
                        <th>ชื่อหน่วยนับ</th>
                        <th>สถานะ</th>
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
                            <td>
                                <a href="{{ route('keptkayas.tbank.units.show', $unit) }}" class="btn btn-info btn-sm">ดูรายละเอียด</a>
                                <a href="{{ route('keptkayas.tbank.units.edit', $unit) }}"
                                    class="btn btn-warning btn-sm">แก้ไข</a>
                                <form action="{{ route('keptkayas.tbank.units.destroy', $unit) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this unit?')">ลบ</button>
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