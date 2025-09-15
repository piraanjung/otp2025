@extends('layouts.tabwater_staff_mobile')

@section('content')
    <form action="{{ route('tabwater.staff.mobile.store') }}" method="POST">
        @csrf
        <input type="text" name="meter_id" value="{{$meter[0]->meter_id}}">
        <input type="text"  class="form-control" id="last_meter_recording" readonly value="{{$meter[0]->last_meter_recording}}">
        <input type="text" name="currentmeter" id="currentmeter" class="form-control">
        <input type="submit" class="btn btn-success" value="บันทึก">
    </form>
@endsection

@section('script')
    <script>
        $(document).on('keyup', '#currentmeter', function(){
            let last_meter_recording = $('#last_meter_recording').val()
            let currentmeter = $(this).val()

            // if(last_meter_recording < currrentmeter){
            //     $(this).val(0)
            // }

        });
    </script>
@endsection