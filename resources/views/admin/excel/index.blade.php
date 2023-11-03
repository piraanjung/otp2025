@extends('layouts.admin1')
@section('nav-excel')
    active
@endsection
@section('content')
<form method="post" action="{{ route('admin.settings.create_and_update') }}" enctype="multipart/form-data">
    @csrf
    <div class="container-fluid my-3 py-3">
        <div class="row mb-5">
            <div class="col-lg-3">
                <div class="card position-sticky top-1">
                    <?php $arrs = [['id' => 'org_info', 'text' => 'ผู้ใช้งานระบบ'],
                    ['id' => 'logo', 'text' => 'ข้อมูลองค์กร'],
                    ['id' => 'address', 'text' => 'ประเภทมิเตอร์'],
                    ['id' => 'sign', 'text' => 'พื้นที่จัดเก็บค่าน้ำประปา'],
                    ['id' => 'meter_code', 'text' => 'ปีงบประมาณ/รอบบิล']
                ];
                    ?>
                    <ul class="nav flex-column bg-white border-radius-lg p-3">
                        @foreach ($arrs as $arr)
                            <li class="nav-item">
                                <a class="nav-link text-body" data-scroll="" href="#{{ $arr['id'] }}">
                                    <div class="icon me-2">
                                        <svg class="text-dark mb-1" width="16px" height="16px" viewBox="0 0 40 40"
                                            version="1.1" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <title>spaceship</title>
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <g transform="translate(-1720.000000, -592.000000)" fill="#FFFFFF"
                                                    fill-rule="nonzero">
                                                    <g transform="translate(1716.000000, 291.000000)">
                                                        <g transform="translate(4.000000, 301.000000)">
                                                            <path class="color-background"
                                                                d="M39.3,0.706666667 C38.9660984,0.370464027 38.5048767,0.192278529 38.0316667,0.216666667 C14.6516667,1.43666667 6.015,22.2633333 5.93166667,22.4733333 C5.68236407,23.0926189 5.82664679,23.8009159 6.29833333,24.2733333 L15.7266667,33.7016667 C16.2013871,34.1756798 16.9140329,34.3188658 17.535,34.065 C17.7433333,33.98 38.4583333,25.2466667 39.7816667,1.97666667 C39.8087196,1.50414529 39.6335979,1.04240574 39.3,0.706666667 Z M25.69,19.0233333 C24.7367525,19.9768687 23.3029475,20.2622391 22.0572426,19.7463614 C20.8115377,19.2304837 19.9992882,18.0149658 19.9992882,16.6666667 C19.9992882,15.3183676 20.8115377,14.1028496 22.0572426,13.5869719 C23.3029475,13.0710943 24.7367525,13.3564646 25.69,14.31 C26.9912731,15.6116662 26.9912731,17.7216672 25.69,19.0233333 L25.69,19.0233333 Z">
                                                            </path>
                                                            <path class="color-background"
                                                                d="M1.855,31.4066667 C3.05106558,30.2024182 4.79973884,29.7296005 6.43969145,30.1670277 C8.07964407,30.6044549 9.36054508,31.8853559 9.7979723,33.5253085 C10.2353995,35.1652612 9.76258177,36.9139344 8.55833333,38.11 C6.70666667,39.9616667 0,40 0,40 C0,40 0,33.2566667 1.855,31.4066667 Z">
                                                            </path>
                                                            <path class="color-background"
                                                                d="M17.2616667,3.90166667 C12.4943643,3.07192755 7.62174065,4.61673894 4.20333333,8.04166667 C3.31200265,8.94126033 2.53706177,9.94913142 1.89666667,11.0416667 C1.5109569,11.6966059 1.61721591,12.5295394 2.155,13.0666667 L5.47,16.3833333 C8.55036617,11.4946947 12.5559074,7.25476565 17.2616667,3.90166667 L17.2616667,3.90166667 Z"
                                                                opacity="0.598539807"></path>
                                                            <path class="color-background"
                                                                d="M36.0983333,22.7383333 C36.9280725,27.5056357 35.3832611,32.3782594 31.9583333,35.7966667 C31.0587397,36.6879974 30.0508686,37.4629382 28.9583333,38.1033333 C28.3033941,38.4890431 27.4704606,38.3827841 26.9333333,37.845 L23.6166667,34.53 C28.5053053,31.4496338 32.7452344,27.4440926 36.0983333,22.7383333 L36.0983333,22.7383333 Z"
                                                                opacity="0.598539807"></path>
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>
                                    </div>
                                    <span class="text-sm">{{ $arr['text'] }}</span>
                                </a>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>
            <div class="col-lg-9 mt-lg-0 mt-4">

                <div class="card card-body" id="profile">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-sm-auto col-4">
                            <div class="avatar avatar-xl position-relative">
                                <img src="{{ asset('soft-ui/assets/img/bruce-mars.jpg') }}" alt="bruce"
                                    class="w-100 border-radius-lg shadow-sm">
                            </div>
                        </div>
                        <div class="col-sm-auto col-8 my-auto">
                            <div class="h-100">
                                <h5 class="mb-1 font-weight-bolder">
                                    {{-- Alec Thompson --}}
                                </h5>
                                <p class="mb-0 font-weight-bold text-sm">
                                    {{-- CEO / Co-Founder --}}
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-auto ms-sm-auto mt-sm-0 mt-3 d-flex">
                                <input type="submit" class="btn btn-success" value="บันทึกข้อมูล">
                        </div>
                    </div>
                </div>

                <div class="card mt-4" id="{{ $arrs[0]['id'] }}">
                    <div class="card-header">
                        <h5>{{ $arrs[0]['text'] }}</h5>

                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-12">
                                <form action="{{ route('admin.excel.store')}}" enctype="multipart/form-data" method="post">
                                    @csrf
                                    <label class="label-control">Upload file excel ->users</label>
                                    <input type="file" class="form-control" name="file" id="">
                                    <input type="submit" class="btn btn-success mt-2 d-flex mr-0 ml-auto d-lg-flex" value="import">
                                </form>
                                <table class="table">
                                    <tbody>
                                        @foreach ($users as $user)
                                        <tr>
                                            <td>{{$user->username}} </td>
                                            <td>{{$user->email}} </td>
                                            <td>{{$user->name}} </td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4" id="{{ $arrs[1]['id'] }}">
                    <div class="card-header">
                        <h5>{{ $arrs[1]['text'] }}</h5>
                    </div>
                    <div class="card-body pt-0">
                        <!-- organization infos -->
                        <form action="{{ route('admin.excel.store')}}" enctype="multipart/form-data" method="post">
                            @csrf
                            <label class="label-control">Upload file excel ->ข้อมูลองค์กร</label>
                            <input type="file" class="form-control" name="file" id="">
                            <input type="submit" class="btn btn-success mt-2 d-flex mr-0 ml-auto d-lg-flex" value="import">
                        </form>
                    </div>
                </div>

                <div class="card mt-4" id="{{ $arrs[2]['id'] }}">
                    <div class="card-header d-flex">
                        <h5 class="mb-0">{{ $arrs[2]['text'] }}</h5>
                    </div>
                    <div class="card-body">
                          <!-- organization infos -->
                          <form action="{{ route('admin.excel.store')}}" enctype="multipart/form-data" method="post">
                            @csrf
                            <label class="label-control">Upload file excel ->ประเภทมิเตอร์</label>
                            <input type="file" class="form-control" name="file" id="">
                            <input type="submit" class="btn btn-success mt-2 d-flex mr-0 ml-auto d-lg-flex" value="import">
                        </form>
                    </div>
                </div>

                <div class="card mt-4" id="{{ $arrs[3]['id'] }}">
                    <div class="card-header">
                        <h5>{{ $arrs[3]['text'] }}</h5>
                    </div>
                    <div class="card-body pt-0">
                          <!-- organization infos -->
                          <form action="{{ route('admin.excel.store')}}" enctype="multipart/form-data" method="post">
                            @csrf
                            <label class="label-control">Upload file excel ->พื้นที่จัดเก็บค่าน้ำประปา</label>
                            <input type="file" class="form-control" name="file" id="">
                            <input type="submit" class="btn btn-success mt-2 d-flex mr-0 ml-auto d-lg-flex" value="import">
                        </form>
                    </div>
                </div>

                <div class="card mt-4" id="{{ $arrs[4]['id'] }}">
                    <div class="card-header">
                        <h5>{{ $arrs[4]['text'] }}</h5>
                    </div>
                    <div class="card-body pt-0">
                          <!-- organization infos -->
                          <form action="{{ route('admin.excel.store')}}" enctype="multipart/form-data" method="post">
                            @csrf
                            <label class="label-control">Upload file excel ->พื้นที่จัดเก็บค่าน้ำประปา</label>
                            <input type="file" class="form-control" name="file" id="">
                            <input type="submit" class="btn btn-success mt-2 d-flex mr-0 ml-auto d-lg-flex" value="import">
                        </form>
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
            console.log('preview', id)
            $(`#img_name${id}`).val(1)

            $(`#frame${id}`).attr('src', URL.createObjectURL(event.target.files[0]));
            if (id == 0) {
                $('#logo_change_image').val(1)
            }
        }

        function previewforsign(id) {
            console.log('preview', id)
            $(`#img_name${id}`).val(1)

            $(`#frameforsign${id}`).attr('src', URL.createObjectURL(event.target.files[0]));
            if (id == 0) {
                $('#logo_change_image').val(1)
            }
        }

        let preview_count = 0;
        $(document).on('click', '.append_sign_form_btn', () => {
            let text = `
                <div class="card bg-gradient-secondary mb-2" id="form${preview_count}">
                    <div class="card-body">
                        <div class="form-group row" >
                            <div class="col-sm-1  trash_div" onclick="del('${preview_count}')">
                                <label for="organize_address" class="col-form-label ">&nbsp;</label>
                                <i class="fas fa-trash-alt text-danger form-control"></i>
                            </div>
                            <div class="col-sm-6 row">
                                <div class="col-md-12">
                                    <label for="organize_address" class=" col-form-label">ชื่อ-สกุล</label>
                                        <input type="text" class="form-control" name="new_sign[${preview_count}][name]" id="new_sign${preview_count}">
                                </div>
                                <div class="col-md-12">
                                    <label for="organize_address" class=" col-form-label">ตำแหน่ง</label>
                                        <input type="text" class="form-control" name="new_sign[${preview_count}][position]">
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <img id="frameforsign${preview_count}" src="" class="mb-2" width="200px" />
                                <input type="hidden" name="new_sign[${preview_count}][img_name]" id="img_name${preview_count}" value="${preview_count}">
                                <label for="organize_address" class="col-sm-2 col-form-label">&nbsp;</label>

                                <input type="file" onchange="previewforsign(${preview_count})" class="form-control filenames" style="" name="new_sign_file[${preview_count}]">
                            </div>
                        </div>
                    </div>
                </div>

            `;
            $('#preview_count').val(preview_count)
            $('#append_sign_form').append(text)
            preview_count = preview_count + 1;
        });

        $('#logo_old_image_name').change(() => {
            alert()
        })


        function del(id) {
            let c = confirm("Are you sure?")
            if(c){
                $(`#form${id}`).remove()
            }else{
                return false;
            }
        }
    </script>
@endsection
