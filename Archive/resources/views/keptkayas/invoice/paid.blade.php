@extends('layouts.adminlte')

@section('mainheader')
    จัดการบิลน้ำประปา
@endsection
@section('presentheader')
    ชำระค่าน้ำประปา
@endsection
@section('content')
<div class="row">
    <div class="col-lg-3">
        <div class="card card-small mb-4 pt-3">
            <div class="card-header border-bottom text-center">
                <div class="mb-3 mx-auto">
                    <img class="rounded-circle" src="{{asset('/shards/images/avatars/0.jpg')}}" alt="User Avatar"
                        width="110"> </div>
                <div class="mb-0">{{$invoice->users->user_profile->name }}</div>
                <span class="text-muted d-block mb-2">สมาชิกผู้ใช้น้ำประปา</span>
                <!-- <button type="button" class="mb-2 btn btn-sm btn-pill btn-outline-primary mr-2">
                        <i class="material-icons mr-1">person_add</i>Follow</button> -->
            </div>
            <ul class="list-group list-group-flush">

                <li class="list-group-item p-4">
                    <strong class="text-muted d-block mb-2">{{$invoice->users->user_profile->address}}</strong>
                    <span>โทร. {{$invoice->users->user_profile->phone}}</span>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-lg-9">
        <ul class="list-group list-group-flush">
            <li class="list-group-item p-3">
                <div class="row">
                    <div class="col">
                        <form action="{{url('invoice/update/'.$invoice->id)}}" method="POST">
                            @method('PUT')
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="feFirstName">รอบบิลที่</label>
                                    <input type="text" class="form-control" readonly
                                        value="{{$invoice->invoice_period->inv_period_name}}"> </div>
                                <div class="form-group col-md-6">
                                    <label for="feLastName">ยอดจดครั้งก่อน</label>
                                    <input type="text" class="form-control" name="lastmeter" id="lastmeter"
                                        value="{{$invoice->lastmeter}}" readonly> </div>
                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-3">
                                    <label for="feEmailAddress">ยอดจดปัจจุบัน</label>
                                    <input type="text" class="form-control" name="currentmeter" id="currentmeter"
                                        readonly value="{{$invoice->currentmeter}}"> </div>
                                <div class="form-group col-md-3">
                                    <label for="fePassword">จำนวนน้ำที่ใช้</label>
                                    <input type="text" class="form-control check" name="used_water_net"
                                        id="used_water_net" value="{{$invoice->used_water_net}}" readonly> </div>
                                <div class="form-group col-md-3">
                                    <label for="feInputAddress">ราคา:หน่วย</label>
                                    <input type="text" class="form-control" name="price_per_unit" id="price_per_unit"
                                        value="{{$invoice->users->usermeter_info->counter_unit}}" readonly> </div>
                                <div class="form-group col-md-3">
                                    <label for="feInputCity">คิดเป็นเงิน</label>
                                    <input type="text" class="form-control check" name="must_paid" id="must_paid"
                                        value="{{$invoice->must_paid}}" readonly> </div>
                            </div>
                            <input type="hidden" name="status" value="paid">
                            <button type="submit" id="submit_btn" class="btn btn-success">ชำระเงิน</button>
                        </form>

                    </div>
                </div>
            </li>
        </ul>
    </div>
@endsection