<div class="row">
    <div class="col-lg-3">
        <div class="card card-small mb-4 pt-3">
            <div class="card-header border-bottom text-center">
                <div class="mb-3 mx-auto">
                    <img class="rounded-circle" src="{{asset('/shards/images/avatars/0.jpg')}}" alt="User Avatar"
                        width="110"> </div>
                <div class="mb-0">{{$invoice->users->user_profile->name }}</div>
                <span class="text-muted d-block mb-2">สมาชิกผู้ใช้น้ำประปา</span>
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
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">แก้ไข</h6>
            </div>
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
                                            {{$mode== 'paid'? 'readonly' : ''}} value="{{$invoice->currentmeter}}"> </div>
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
                                @if ($mode == 'paid')
                                <a href="{{url('/invoice/paid/'.$invoice->id)}}" class="btn btn-success">ชำระเงิน</a>
                                @else
                                <button type="submit" id="submit_btn" class="btn btn-success">บันทึก</button>
                            </form>
                            @endif

                        </div>
                    </div>
                </li>
            </ul>
        </div>
        @if ($invoice->owe != [])
        <div class="card card-small  mp4">
            <div class="card-header border-bottom">
                <h6 class="m-0">รายการค้างชำระ</h6>
            </div>
            <div class="card-body p-0 pb-3 text-center">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="border-0">#</th>
                            <th scope="col" class="border-0">รอบบิลที่</th>
                            <th scope="col" class="border-0">ยอดจดครั้งก่อน</th>
                            <th scope="col" class="border-0">ยอดจดปัจจุบัน</th>
                            <th scope="col" class="border-0">จำนวนน้ำที่ใช้</th>
                            <th scope="col" class="border-0">คิดเป็นเงิน</th>
                            <td scope="col" class="border-0"></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $index = 1;?>
                        @foreach ($invoice->owe as $owe)
                        <tr class="{{$invoice->id ==  $owe->id ? 'current_owe_list': ''}}">
                            <td>{{$index++}}</td>
                            <td>{{$owe->invoice_period->inv_period_name}}</td>
                            <td>{{$owe->lastmeter}}</td>
                            <td>{{$owe->currentmeter}}</td>
                            <td>{{$owe->used_water_net}}</td>
                            <td>{{$owe->must_paid}}</td>
                            <td scope="col" class="border-0">
                                @if ($invoice->id != $owe->id)
                                <a href="{{url('/invoice/edit/'.$owe->id)}}"
                                    class="mb-2 btn btn-sm btn-info mr-1">แก้ไข</a>
                                <a v-if="$invoice->status === 'invoice'" href="{{url('../invoice/paid/'.$owe->id)}}"
                                    class="mb-2 btn btn-sm btn-success mr-1">ชำระเงิน</a>
                                @endif

                                <a v-if="$invoice->status !== 'init'" href=""
                                    class="mb-2 btn btn-sm btn-primary mr-1">ปริ้นใบแจ้งหนี้</a>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @endif
    </div>

    <div v-if="currentComponent">
        <print-invoice-form :data="invoice"></print-invoice-form>
    </div>
</div>


@section('script')
    <script>
        //คำนวนเงินค่าใช้น้ำ
    $('#currentmeter').keyup(function(){
        let lastmeter = $('#lastmeter').val();
        let currentmeter = $(this).val();
        let total = (currentmeter - lastmeter) * 8;
        $('#must_paid ').val(total);
    });

    </script>
@endsection