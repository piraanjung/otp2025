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
                <form action="{{url('admin/zone')}}" method="POST" onSubmit="return checkZoneNameValues();">
    @csrf
    <div class="row">
        <div class="col-md-3"><label>ชื่อหมู่</label></div>
        <div class="col-md-5"><label>ที่อยู่</label></div>
        <div class="col-md-1"></div>
    </div>

    <div id="zonelist">
        <div class="row mb-1">
            <div class="col-md-3">
                <input type="text" name="zone[0][zonename]" class="form-control" required>
            </div>
            <div class="col-md-5">
                <input type="text" name="zone[0][zoneAddress]" id="zoneAddress" class="form-control" value="ที่อยู่เริ่มต้น">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-primary addZoneBtn">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="saveBtn">
        <hr>
        <button type="submit" class="btn btn-success">บันทึก</button>
    </div>
</‰form>
            </div>
          </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
       let i = 1; // เริ่มที่ 1 เพราะแถวแรกคือ 0
$(document).on('click', '.addZoneBtn', function(){
    let addressValue = $('#zoneAddress').val(); // ดึงค่าจากช่องแรกมาใส่ช่องใหม่
    let text = `
        <div class="row mb-1 zone-row">
            <div class="col-md-3">
                <input type="text" name="zone[${i}][zonename]" class="form-control" value="">
            </div>
            <div class="col-md-5">
                <input type="text" name="zone[${i}][zoneAddress]" class="form-control" value="${addressValue}">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger delZoneBtn">
                   <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
    `;
    $('#zonelist').append(text);
    i++;
});

// ฟังก์ชันลบแถว
$(document).on('click', '.delZoneBtn', function(){
    $(this).closest('.row').remove();
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
