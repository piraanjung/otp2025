@extends('layouts.admin1')

@section('style')
    {{-- เพิ่มเส้นทางจดมิเตอร์ พื้นที่ {{$zone[0]->zone_name}} --}}
@endsection
@section('nav')
<a href="{{url('zone')}}"> พื้นที่จดมิเตอร์น้ำประปา</a>
@endsection
@section('zone')
    active
@endsection


@section('content')

<form action="{{route('admin.subzone.update',$zone[0]->id)}}" method="POST" onsubmit="return checkZoneNameValues()">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card card-widget widget-user">
                <div class="card-footer">
                    <div id="routList">
                        <?php $i = 0; ?>
                        @foreach ($zone[0]->subzone as $item)
                            @if (collect($zone[0]->subzone)->count()== $i)
                                <div class="input-group mb-3 route ">
                                    <input type="text" class="form-control subzone_name" data-id="{{$i}}" name="subzone[{{$i}}][{{$item['id']}}][subzone_name]" id="routeNameTemp" value="{{$item['subzone_name']}}">
                                    <a href="javascript:void(0)" class="btn btn-primary permanentVal addRoute " data-subzone_id="{{$item['id']}}"  data-i="{{$i}}">เพิ่ม</a>
                                </div>
                            @else
                                <div class="input-group mb-3 route last">
                                    <input type="text" class="form-control" name="subzone[{{$i}}][{{$item['id']}}][subzone_name]" id="routeNameTemp{{$item['id']}}" value="{{$item['subzone_name']}}">
                                    <a href="javascript:void(0)" data-subzone_id="{{$item['id']}}" data-i="{{$i}}" id="a_del{{$i}}" class="btn btn-danger permanentVal removeRoute">ลบ</a>
                                </div>
                            @endif
                            <?php $i++; ?>
                        @endforeach
                        <div class="input-group mb-3 route last">
                            <input type="text" class="form-control subzone_name" data-id="{{$i}}" name="subzone[{{$i}}][new][subzone_name]" id="routeNameTemp" value="">
                            <a href="javascript:void(0)" class="btn btn-primary permanentVal addRoute last" data-subzone_id="new" id="a_del{{$i}}"  data-i="{{$i}}">เพิ่ม</a>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success submitBtn">บันทึก</button>
                </div>
              </div>

        </div>
    </div>

</form>
@endsection

@section('script')
    <script>
        let i = 1;
        //เพิ่ม list เส้นทาง
        $('body').on('click','.addRoute',function(e){
            if($(this).hasClass('last')){
                $(this).removeClass('last')
                i = $(this).data('i') +1
            }
            let text = `
                <div class="input-group mb-3 route last">
                    <input type="text" class="form-control subzone_name" data-id="${i}" name="subzone[${i}][new][subzone_name]"  value="">
                    <a href="javascript:void(0)" class="btn btn-primary addRoute last" id="a_del${i}" data-i="${i}">เพิ่ม</a>
                </div>
            `;

            $('#routList').append(text);
            i++;
            //เปลี่ยนคุณสมบัติ list  ทุกอัน ยกเว้น list สุดท้าย
            $('.addRoute').each(function(index, element){
                if(!$(this).hasClass('last')){
                    $(this).removeClass('addRoute');
                    $(this).addClass('removeRoute');
                    $(this).html('ลบ')
                    $(this).removeClass('btn-primary');
                    $(this).addClass('btn-danger')
                    $(this).removeClass('last')
                }else{
                    $(this).parent().siblings().val('')
                }
            });
        });

        //ลบ list เส้นทาง
        $("body").on("click",".removeRoute",function(e){
            var i = $(this).data('i')
            var subzone_id = $(this).data('subzone_id');
            console.log('subzone_id',subzone_id)
            if(subzone_id === "new" || typeof subzone_id === "undefined"){
                $(`#a_del${i}`).parent().remove();
                return
            }
            $.get(`/api/subzone/delete/${subzone_id}`, function(data){
                if(data == 1){
                    alert('ทำการลบข้อมูลเรียบร้อยแล้ว');
                    $(`#a_del${i}`).parent().remove();
                }else{
                    alert('ไม่สามรถลบข้อมูลได้ เนื่องจากมีข้อมูลผู้ใช้งานในพื้นที่นี้อยู่');
                }
            });
        });



        $('#zone').change(function(){
            if($('.submitBtn').hasClass('hidden')){
                $('.submitBtn').removeClass('hidden');
                $('.submitBtn').addClass('show');
            }
        });

        function checkZoneNameValues(){
            let res = true;
            let errTxt = '';
            let i = 0;
            let last_i = $('.subzone_name').last().data('id')
            $('.subzone_name').each(function(val){

                if($(this).val() === "" && $(this).data('id') !== last_i){
                    errTxt += '- ชื่อเส้นทางจัดเก็บต้องไม่เป็นค่าว่าง\n'
                    res =  false;
                    return false;
                }
            });

            if(res === false){
                alert(errTxt);
            }
            return res;


        }
        $(document).ready(() => {
            setTimeout(() => {
                $('.alert').toggle('slow')
            }, 2000)
        })
    </script>
@endsection
