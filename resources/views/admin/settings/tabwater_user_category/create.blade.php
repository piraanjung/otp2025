@extends('layouts.admin')

@section('content')
<div class="page-header row no-gutters py-4">
    <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
        <span class="text-uppercase page-subtitle">Settings</span>
        <h3 class="page-title"><span style="font-size:1rem">ประเภทผู้ใช้น้ำ -</span> เพิ่มข้อมูลประเภทผู้ใช้น้ำ</h3>
    </div>
</div>
<div class="card card-small mb-4">
    <div class="card-header border-bottom">
        <h6 class="m-0"></h6>
    </div>
    <div class="card-body pt-0">
        <div>
            <div class="col-sm-12 col-md-12">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <strong class="text-muted d-block mb-2">ชื่อประเภทผู้ใช้น้ำ</strong>
                            <input type="text" class="form-control is-valid row0" id="tabwatermetertype"
                                placeholder="ตัวอย่าง : อัตราก้าวหน้า, อัตราคงที่ , รักษามิเตอร์" name="tabwatermetertype" required>
                        </div>
                        <div class="form-group col-md-2">
                            <strong class="text-muted d-block mb-2">ราคาต่อหน่วย</strong>
                            <input type="text" class="form-control is-valid row0" id="price_per_unit" placeholder="ตัวอย่าง : 8.25"
                                name="price_per_unit" required>
                        </div>
                        <div class="form-group col-md-2">
                            <strong class="text-muted d-block mb-2">จำนวนหน่วย</strong>
                            <input type="text" class="form-control is-valid row0" id="unit_num" placeholder="ตัวอย่าง : 1, 2.5 , 10"
                                name="unit_num" required>
                        </div>
                        <div class="form-group col-md-2">
                            <strong class="text-muted d-block mb-2">ชื่อหน่วย</strong>
                            <input type="text" class="form-control is-valid row0" id="unit_name" placeholder="ตัวอย่าง : ลิตร, วัน, เดือน"
                                name="unit_name" required>
                        </div>
                        <div class="form-group col-md-2">
                            <strong class="text-muted d-block mb-2">&nbsp;</strong>
                            <button class="btn btn-primary add">เพิ่ม</button>
                            {{-- <button v-on:click="updateData()" class="btn btn-primary">บันทึก</button> --}}
                        </div>
                    </div>
                    <form action="{{url('tabwater_user_category/store')}}" method="POST">
                        @csrf
                        <div class="row listDiv">
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">บันทึก</button> 
                    </form>

            </div>
        </div>
    </div>
</div>

@endsection


ิ @section('script')
    <script>
        let i = 0;
        $('.add').click(function(){
            let text = `
            <div class="col-sm-12 col-md-12">
            <div class="form-row">
                        <div class="form-group col-md-4">
                            <input type="text" class="form-control" name="tabwatermetertype[${i}][type]"
                               value="${$('#tabwatermetertype').val()}">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" class="form-control" name="tabwatermetertype[${i}][price_per_unit]" 
                            value="${$('#price_per_unit').val()}">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" class="form-control" name="tabwatermetertype[${i}][unit_num]" 
                                value="${$('#unit_num').val()}">
                        </div>
                        <div class="form-group col-md-2">
                            <input type="text" class="form-control" name="tabwatermetertype[${i}][unit_name]"
                                value="${$('#unit_name').val()}">
                        </div>
                        <div class="form-group col-md-2">
                            <button  class="btn btn-danger">ลบ</button>
                        </div>
                    </div>
                </div>
            `;

            $('.listDiv').append(text)
            i++;
            $('.row0').each(function(){
                $(this).val('')
            });

            $('.btn-danger').click(function(){
                $(this).parent().parent().remove();
            });
        });
    </script>
@endsection