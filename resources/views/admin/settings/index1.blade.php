@extends('layouts.admin1')

@section('mainheader')
ตั้งค่าทั่วไป
@endsection
@section('nav')
<a href="{{'settings'}}">ตั้งค่า</a>
@endsection
@section('settings')
active
@endsection
@section('style')
<style>
    .hidden {
        display: none
    }

</style>
@endsection

@section('content')
<form class="m-2" method="post" action="{{ url('settings/create_and_update') }}" enctype="multipart/form-data">
  @csrf
  <div class="card">
    <div class="card-header">
      <input type="submit" class="btn btn-info col-2" value="บันทึก">
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-5 col-sm-3">
                <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist"
                    aria-orientation="vertical">
                    <a class="nav-link active" id="organization_name-tab" data-toggle="pill" href="#organization_name"
                        role="tab" aria-controls="organization_name" aria-selected="true">ชื่อหน่วยองค์กรและหน่วยงาน</a>
                    <a class="nav-link " id="logo-tab" data-toggle="pill" href="#logo" role="tab" aria-controls="logo"
                        aria-selected="false">ตราสัญลักษณ์</a>
                    <a class="nav-link" id="address-tab" data-toggle="pill" href="#address" role="tab"
                        aria-controls="address" aria-selected="false">ที่อยู่</a>
                    <a class="nav-link" id="sign-tab" data-toggle="pill" href="#sign" role="tab" aria-controls="sign"
                        aria-selected="false">ลายเซ็นต์</a>
                    <a class="nav-link" id="meternumber-tab" data-toggle="pill" href="#meternumber" role="tab"
                        aria-controls="meternumber" aria-selected="false">รหัสเลขมิเตอร์</a>
                    <a class="nav-link" id="inv_period-tab" data-toggle="pill" href="#inv_period" role="tab"
                        aria-controls="inv_period" aria-selected="false">เกี่ยวกับใบแจ้งหนี้</a>
                </div>
            </div>

            <div class="col-7 col-sm-9">
                <div class="tab-content" id="vert-tabs-tabContent">
                    <div class="tab-pane text-left fade  active show " id="organization_name" role="tabpanel"
                        aria-labelledby="organization_name-tab">
                        {{-- ชื่อองค์กร --}}
                        @include('admin.settings.organization_name')
                    </div>

                    <div class="tab-pane fade  text-center" id="logo" role="tabpanel" aria-labelledby="logo-tab">
                        {{-- ตราสัญลักษณ์  --}}
                        @include('admin.settings.logo')
                    </div>
                    <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
                        {{-- ที่อยู่ --}}
                        @include('admin.settings.address')
                    </div>
                    <div class="tab-pane fade " id="sign" role="tabpanel" aria-labelledby="sign-tab">
                        {{-- ลายเซ็นต์ --}}
                        @include('admin.settings.sign')
                    </div>
                    <div class="tab-pane fade" id="meternumber" role="tabpanel" aria-labelledby="meternumber-tab">
                        {{-- ลายเซ็นต์ --}}
                        @include('admin.settings.meternumber')
                    </div>
                    <div class="tab-pane fade" id="inv_period" role="tabpanel" aria-labelledby="inv_period-tab">
                        {{-- ลายเซ็นต์ --}}
                        @include('admin.settings.inv_period')
                    </div>

                </div>
            </div>
        </div>
    </div>
  </div>
</form>
@endsection


@section('script')
<script>
 function preview(id) {
            $(`#img_name${id}`).val(1)

            $(`#frame${id}`).attr('src', URL.createObjectURL(event.target.files[0]));
            if(id == 0){
                $('#logo_change_image').val(1)
            }
        }

        let preview_count = 1;
        $(document).on('click','.append_sign_form_btn',()=>{
             preview_count = parseInt($('#preview_count').val())+1;
            let text = `
            <div class="form-group row" id="form${preview_count}">
                        <div class="col-sm-1  trash_div" onclick="del('${preview_count}')">
                            <label for="organize_address" class="col-form-label ">&nbsp;</label>
                            <i class="fas fa-trash-alt text-danger form-control"></i>
                        </div>
                        <div class="col-sm-6 row">
                            <div class="col-md-12">
                                <label for="organize_address" class=" col-form-label">ชื่อ-สกุล</label>
                                    <input type="text" class="form-control" name="sign[${preview_count}][name]" id="sign${preview_count}">
                            </div>
                            <div class="col-md-12">
                                <label for="organize_address" class=" col-form-label">ตำแหน่ง</label>
                                    <input type="text" class="form-control" name="sign[${preview_count}][position]">
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <label for="organize_address" class="col-sm-2 col-form-label">&nbsp;</label>
                            <img id="frame${preview_count}" src="" class="mt-4" width="200px" />
                            <input type="text" name="sign[${preview_count}][img_name]" id="img_name${preview_count}" value="0">

                            <input type="file" onchange="preview(${preview_count})" class="form-control filenames" style="position: absolute;
                                bottom: 0px;" name="sign[${preview_count}][filenames]">
                        </div>

                    </div>

            `;
            $('#preview_count').val(preview_count)
            $('#append_sign_form').append(text)
            preview_count = preview_count+1;
        });

        $('#logo_old_image_name').change(()=>{
            alert()
        })


        function del(id){
            $(`#form${id}`).remove()
        }
</script>
@endsection
