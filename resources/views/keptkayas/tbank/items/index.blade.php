@extends('layouts.keptkaya')
@section('nav-header', 'ขยะรีไซเคิล')
@section('nav-current', ' ข้อมูลขยะรีไซเคิล')
@section('page-topic', ' ข้อมูลขยะรีไซเคิล')
@section('content')

    <div class="card">
        <div class="card-header">
            <a class="btn btn-primary btn-sm" href="{{ route('keptkayas.tbank.items.create') }}">
                <i class="fas fa-folder">
                </i>
                สร้างข้อมูล
            </a>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>รหัส</th>
                        <th>ชื่อ</th>
                        <th>สถานะ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kp_tbank_items as $kp_tbank_item)
                        {{-- @dd($kp_tbank_item->image_path) --}}

                        <tr>
                            <td> <img src="{{ asset('keptkaya/items/' . $kp_tbank_item->image) }}" style="width: 40%" alt=""> </td>
                            <td>{{ $kp_tbank_item->kp_itemscode }}</td>
                            <td>{{ $kp_tbank_item->kp_itemsname }}</td>
                            <td>{{ $kp_tbank_item->status }}</td>
                            <td>
                            <td class="project-actions text-right">

                                <a class="btn btn-info btn-sm" href="#">
                                    <i class="fas fa-pencil-alt">
                                    </i>
                                    Edit
                                </a>
                                <a class="btn btn-danger btn-sm" href="#">
                                    <i class="fas fa-trash">
                                    </i>
                                    Delete
                                </a>
                            </td>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

    </div>
@endsection