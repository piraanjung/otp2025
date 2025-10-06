@extends('layouts.payment')


@section('content')
<div class="container-fluid mt--7">
    
    <div class="card card-stats mb-4 mb-lg-0">
        <div class="card-body"> 
            <div class="nav-wrapper">
                <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-1-tab" data-toggle="tab"
                            href="#tabs-icons-text-1" role="tab" aria-controls="tabs-icons-text-1"
                            aria-selected="true"><i class="ni ni-cloud-upload-96 mr-2"></i>ตั้งค่าปีงบประมาณ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-2-tab" data-toggle="tab"
                            href="#tabs-icons-text-2" role="tab" aria-controls="tabs-icons-text-2"
                            aria-selected="false"><i class="ni ni-bell-55 mr-2"></i>ตั้งค่ารอบบิล</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-3-tab" data-toggle="tab"
                            href="#tabs-icons-text-3" role="tab" aria-controls="tabs-icons-text-3"
                            aria-selected="false"><i class="ni ni-calendar-grid-58 mr-2"></i>ตั้งค่าราคาต่อหน่วย</a>
                    </li>
                </ul>
            </div>
            {{-- <div class="card shadow">
                <div class="card-body"> --}}

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active mt-5" id="tabs-icons-text-1" role="tabpanel"
                            aria-labelledby="tabs-icons-text-1-tab">
                            <div class="row">
                                <div class="col-3"></div>
                                <div class="col-xl-6 order-xl-2 mb-5 mb-xl-0">
                                    <div class="card card-profile shadow">
                                        {{-- <div class="row justify-content-center">
                                            <div class="col-lg-3 order-lg-2">
                                                <div class="card-profile-image">
                                                    <a href="#">
                                                        <img src="{{asset('argon/img/theme/team-4-800x800.jpg')}}"
                                                            class="rounded-circle">
                                                    </a>
                                                </div>
                                            </div>
                                        </div> --}}
                                        {{-- <div class="card-header text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4">
                                            <div class="d-flex justify-content-between"> --}}
                                                {{-- <a href="#" class="btn btn-sm btn-info mr-4">Connect</a>
                                                <a href="#" class="btn btn-sm btn-default float-right">Message</a> --}}
                                            {{-- </div>
                                        </div> --}}
                                        <div class="card-body pt-0 pt-md-4">
                                            {{-- <hr class="my-4"> --}}
                                            <h6 class="heading-small text-muted mb-4">ปีงบประมาณ</h6>
                                            <div class="pl-lg-4">
                                                <div class="row">
                                                    <div class="col-lg-5">
                                                        <div class="form-group focused">
                                                            <input type="text" id="start_budgetyear"
                                                                class="form-control datepicker2 form-control-alternative"
                                                                value="" >
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1"><h2 class="text-center mt-2">/</h2></div>
                                                    <div class="col-lg-5">
                                                        <div class="form-group focused">
                                                            <input type="text" id="end_budgetyear"
                                                                class="form-control datepicker2 form-control-alternative"
                                                                value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-center">
                                                    <button type="button" class="btn btn-primary my-4 budgetyear-btn">บันทึก</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tabs-icons-text-2" role="tabpanel"
                            aria-labelledby="tabs-icons-text-2-tab">
                            <p class="description">Cosby sweater eu banh mi, qui irure terry richardson ex squid.
                                Aliquip placeat salvia cillum iphone. Seitan aliquip quis cardigan american apparel,
                                butcher voluptate nisi qui.</p>
                        </div>
                        <div class="tab-pane fade" id="tabs-icons-text-3" role="tabpanel"
                            aria-labelledby="tabs-icons-text-3-tab">
                            <p class="description">Raw denim you probably haven't heard of them jean shorts Austin.
                                Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor,
                                williamsburg carles vegan helvetica. Reprehenderit butcher retro keffiyeh dreamcatcher
                                synth.</p>
                        </div>
                    </div>
                </div>
            {{-- </div>
        </div> --}}
    </div>

</div>
@endsection