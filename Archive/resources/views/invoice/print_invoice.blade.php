@extends('layouts.admin1')
@section('nav-invoice')
    active
@endsection
@section('nav-header')
    จัดการใบแจ้งหนี้
@endsection
@section('nav-main')
    <a href="{{ route('invoice.index') }}"> ออกใบแจ้งหนี้</a>
@endsection
@section('nav-current')
    ปริ้นใบแจ้งหนี้
@endsection
@section('nav-topic')
    ปริ้นใบแจ้งหนี้
@endsection
@section('content')
    <div class="card">
        <div class="card-header"></div>
        <div class="card-body">
            <form action="{{route('invoice.invoice_bill_print')}}" method="post">
                @csrf
                <table class="table" id="invoiceTable">
                    <thead>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="check-input-select-all"
                                        id="check-input-select-all">
                                    <label class="custom-control-label" for="customRadio1">เลือกทั้งหมด</label>
                                </div>
                            </td>
                            <td>name</td>
                            <td>printed_time</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 0;
                        @endphp

                        <input type="submit" value="print" class="btn btn-success">
                        @foreach ($usermeter_infos as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" name="a[{{$i}}]" class="subzone_checkbox form-check-input"
                                        value="{{$user->meter_id}}">
                                </td>
                                <td>{{$user->meter_id}}</td>
                                <td>{{$user->user->firstname . " " . $user->user->lastname }}</td>
                                <td>{{$user->invoice[0]->printed_time }}</td>

                            </tr>
                            @php
                                $i++;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
            </form>

        </div>
    </div>

@endsection

@section('script')
    <script>

        table = $('#invoiceTable').DataTable({
            responsive: true,

            "pagingType": "listbox",
            "lengthMenu": [
                [10, 25, 50, 150, -1],
                [10, 25, 50, 150, "ทั้งหมด"]
            ],
            "language": {
                "search": "ค้นหา:",
                "lengthMenu": "แสดง _MENU_ แถว",
                "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
                "infoEmpty": "แสดง 0 ถึง 0 จาก 0 แถว",
                "paginate": {
                    // "info": "แสดง _MENU_ แถว",
                },

            },
            select: true,
        });


        $('#check-input-select-all').on('click', function () {
            if (!$(this).is(':checked')) {
                $('.subzone_checkbox').prop('checked', false)
            } else {
                $('.subzone_checkbox').prop('checked', true)
            }
        });

    </script>
@endsection