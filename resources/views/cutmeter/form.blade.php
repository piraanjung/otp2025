<div class="row">
    <div class="col-lg-3">
        <div class="card card-small mb-4 pt-3">
            <div class="card-header border-bottom text-center">
                <div class="mb-3 mx-auto">
                    <img class="rounded-circle" src="{{asset('/adminlte/dist/img/user.png')}}" alt="User Avatar"
                        width="110"> </div>
                <div class="mb-0">{{$user[0]->user_profile->name }}</div>
                <span class="text-muted d-block mb-2">สมาชิกผู้ใช้น้ำประปา</span>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-4">
                        {{$user[0]->user_profile->address}}
                        {{$user[0]->zone->zone_name}}
                        ต. {{$tambon_infos['tambon']}}<br>
                        อ. {{$tambon_infos['district']}}
                        จ. {{$tambon_infos['province']}}<br>
                        {{$tambon_infos['postcard']}}<br>
                    <span>โทร. {{$user[0]->user_profile->phone}}</span>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-lg-5">
        <form action="{{ url('/cutmeter/update/'.$user[0]->user_id) }}" method="POST">
            @csrf
            @method("PUT")
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"></h3>
                </div>
                <div class="card-body">
                    <h5 class="text-danger text-center">{{ $owe_count_text }}</h5>
                    <!-- Color Picker -->
                    <div class="form-group">
                        <label>สถานะ:</label>
                        <select class="form-control " name="status">
                            <option value="">เลือก...</option>
                            @foreach ($cutmeter_status as $key => $item)
                            <option value="{{$item['id']}}" {{ $item['id'] == $cutmeter_user_status ? 'selected' : '' }}>{{$item['value']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- /.form group -->

                    <!-- Color Picker -->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>วันที่:</label>

                                <div class="input-group">
                                    <input type="text" class="form-control datepicker" name="operate_date">

                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>เวลา:</label>

                                <div class="input-group">
                                    <input type="text" class="form-control timepicker" name="operate_time">

                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                                    </div>
                                </div>
                            </div>
                            <!-- /.input group -->
                        </div>
                        <!-- /.form group -->

                        <!-- time Picker -->
                        <div class="bootstrap-timepicker mt-3">
                            <div class="form-group">
                                <label>ผู้รับผิดชอบ :</label>

                                <div class="input-group row">
                                    <span class="form-control col-md-1 h5">1</span>
                                    <select class="form-control col-md-11" name="tabwaterman[][user_id]">
                                        <option value="">เลือก...</option>
                                        @foreach ($tabwatermans as $tabwaterman)
                                        <option value="{{ $tabwaterman->id }}">{{ $tabwaterman->user_profile->name }}
                                        </option>
                                        @endforeach
                                    </select>

                                </div>
                                <!-- /.input group -->
                            </div>
                            <!-- /.form group -->
                            <div class="form-group">

                                <div class="input-group row">
                                    <span class="form-control col-md-1 h5">2</span>
                                    <select class="form-control col-md-11" name="tabwaterman[][user_id]">
                                        <option value="">เลือก...</option>
                                        @foreach ($tabwatermans as $tabwaterman)
                                        <option value="{{ $tabwaterman->id }}">{{ $tabwaterman->user_profile->name }}
                                        </option>
                                        @endforeach
                                    </select>

                                </div>
                                <!-- /.input group -->
                            </div>
                            <!-- /.form group -->
                            <div class="form-group">

                                <div class="input-group row">
                                    <span class="form-control col-md-1 h5">3</span>
                                    <select class="form-control col-md-11" name="tabwaterman[][user_id]">
                                        <option value="">เลือก...</option>
                                        @foreach ($tabwatermans as $tabwaterman)
                                        <option value="{{ $tabwaterman->id }}">{{ $tabwaterman->user_profile->name }}
                                        </option>
                                        @endforeach
                                    </select>

                                </div>
                                <!-- /.input group -->
                            </div>
                            <!-- /.form group -->

                            <div class="form-group">

                                <div class="input-group">
                                    <textarea name="comment" class="form-control" rows="2"></textarea>
                                </div>
                                <!-- /.input group -->
                            </div>
                        </div>
                        <input type="submit" value="บันทึก" class="btn btn-success {{ $show_submit_btn }}"> 
                    </div>
                    <!-- /.card-body -->
                </div>
        </form>
    </div>

</div>


@section('script')
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

<script>
    $(document).ready(function(){
        $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                todayBtn: true,
                language: 'th',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
                thaiyear: true,
        }).datepicker("setDate", new Date());;  //กำหนดเป็นวันปัจุบัน


        $('.timepicker').timepicker({
            timeFormat: 'HH:mm น.',
            interval: 30,
            minTime: '8',
            maxTime: '6:00pm',
            defaultTime: '8',
            startTime: '8:00am',
            dynamic: false,
            dropdown: true,
            scrollbar: true
        });


    });//document

</script>
@endsection
