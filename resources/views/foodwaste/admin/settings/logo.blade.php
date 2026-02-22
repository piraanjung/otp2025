<div class="card">
    <div class="card-body">
        <div class="text-center">
            @if (isset($logo['values']))
                @if ($logo['values'] != '')
                    <img src="{{ asset('logo/' . $logo['values']) }}" width="200" />
                    <div class="h5 text-red mt-3">รูปตราสัญลักษณ์ของหน่วยงาน</div>
                @endif
            @else
                <img src="{{ asset('logo/init_logo2.png') }}" width="200" />
                <div class="h5 text-red mt-3">ยังไม่รูปตราสัญลักษณ์ของหน่วยงาน</div>
            @endif
        </div>

    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-secondary">
                <div class="card-body px-5 z-index-1 bg-cover">
                    <div class="row">
                        <div class="col-lg-6 col-12 my-auto">
                            <h4 class="text-white opacity-9">เลือกรูปตราสัญลักษณ์</h4>
                            <hr class="horizontal light mt-1 mb-1">
                            <div class="d-flex">
                                <input type="file" name="logo" id="logo" onchange="preview('0')" class="form-control mt-4">
                                <input type="hidden" name="logo_change_image" id="logo_change_image" value="0">
                                <input type="hidden" name="logo_old_image_name" id="logo_old_image_name" value="{{isset($logo['values']) ? $logo['values'] : 0}}">
                            </div>
                        </div>
                        <div class="col-lg-2 col-12 text-center"></div>
                        <div class="col-lg-4 col-12 my-auto">
                            <div class="d-flex">
                                <img id="frame0" src="" width="300px" class="border-4" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

