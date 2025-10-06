@extends('layouts.admin1')

@section('mainheader')
เพิ่มข้อมูลพื้นที่จดมิเตอร์น้ำประปา
@endsection
@section('nav')
    <a href="{{'zone'}}">พื้นที่จดมิเตอร์น้ำประปา</a>
@endsection
@section('zone')
    active
@endsection

@section('style')
    <style>
        .show{
            display: block;
        }
        .hidden{
            display: none;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col">
          <div class="card shadow">

            <div class="card-body">
                <form action="{{url('zone/store')}}" method="POST" onSubmit="return checkZoneNameValues();">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                        <div class="form-group">
                            <label for="" class="label-control">ชื่อหมู่</label>
                        </div>
                        </div>
                        <div class="col-md-5">
                        <div class="form-group">
                            <label for="" class="label-control">ที่อยู่</label>
                        </div>
                        </div>
                        <div class="col-md-1">
                        <label for="" class="label-control">&nbsp;</label>

                        </div>
                    </div>
                    <div id="zonelist"></div>

                    <div class="row">
                        <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" name="zone[0][zonename]" id="zonename" class="form-control zonename">
                        </div>
                        </div>
                        <div class="col-md-5">
                        <div class="form-group">
                            <?php
                                // $tambonAddr = 'ต.'.$tambonInfos['tambon'].' อ.'.$tambonInfos['district'].' จ.'.$tambonInfos['province']  ?>
                            <input type="text" name="zone[0][zoneAddress]" id="zoneAddress" value="" class="form-control zoneAddress">
                        </div>
                        </div>
                        <div class="col-md-1">
                        <a href="javascript:void(0)" class="btn btn-outline-primary form-control addZoneBtn">
                            <i class="fa fa-plus "></i>
                        </a>
                        </div>
                    </div>


                    <div id="saveBtn">
                            <hr>
                            <button type="submit" class="btn btn-success  zonelistSaveBtn">บันทึก</button>
                    </div>
               </form>
            </div>
          </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        let i = 1;
        $('.addZoneBtn').click(function(){
            let text = `
                <div class="row mb-1 aa">
                    <div class="col-md-3 aaa">
                        <input type="text" name="zone[${i}][zonename]" class="form-control" value="">

                    </div>
                    <div class="col-md-5">
                        <input type="text" name="zone[${i}][zoneAddress]" class="form-control zoneAddress" value="${$('#zoneAddress').val()}">

                    </div>
                    <div class="col-md-1">
                        <a href="javascript:void(0)" class="btn btn-outline-danger form-control delZoneBtn">
                           <i class="fa fa-minus "></i>
                       </a>
                    </div>
                </div>
            `;
            $('#zonelist').prepend(text)
            i++;


        });

        //ลบ รายการ zone

       $("body").on("click",".delZoneBtn",function(e){
            checkEmptyZoneLinst()
            $(this).parent().parent().remove();
        });

        function checkEmptyZoneLinst(){
            $('.aa').each(function(index, element){
                console.log('index', index)
                if(index !== ''){
                    if($('#saveBtn').hasClass('hidden')){
                        $('#saveBtn').removeClass('hidden')
                        $('#saveBtn').addClass('show')
                    }
                }
            })
        }

        function checkZoneNameValues(){
            let res = true;
            let errTxt = '';
            let i = 0;
            $('.zonename').each(function(val){
                if($(this).val() === ""){
                    errTxt += '- ชื่อพื้นที่จัดเก็บต้องไม่เป็นค่าว่าง\n'
                    res =  false;
                    return false;
                }


            });
            $('.zoneAddress').each(function(val){
                if($(this).val() === ""){
                    errTxt += '- ที่อยู่ต้องไม่เป็นค่าว่าง';
                    res =  false;
                    return false;
                }
            });

            if(res === false){
                alert(errTxt);
            }
            return res;


        }
    </script>
@endsection
