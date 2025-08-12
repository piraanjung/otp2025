@extends('layouts.keptkaya')

@section('content')
    <div class="card">
        <div class="card-header"> </div>

        <div class="card-body">

            <a class="btn btn-primary btn-sm" href="{{ route('keptkaya.tbank.items_group.create') }}">
                <i class="fas fa-folder">
                </i>
                สร้างข้อมูล
            </a>
            <table class="table">
                <thead>
                    <tr>
                        <th>ชื่อประเถทขยะ Recycle</th>
                        <th>สถานะ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kp_tbank_item_groups as $kp_item_group)
                        <tr>
                            <td>{{ $kp_item_group->kp_items_groupname }}</td>
                            <td>{{ $kp_item_group->status }}</td>
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