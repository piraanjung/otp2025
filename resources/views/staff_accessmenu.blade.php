@extends('layouts.keptkaya_mobile')

@section('style')
    <style>
        .my-icon{
           font-size:2rem; 
           color:white;
           background: blue;
           border-radius:50% 50% ; 
           padding: 10px
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">

        <div class="col-xl-12 ms-auto mt-xl-0 mt-4">
            <div class="row">
                <div class="col-12">
                    <div class="card bg-primary">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8 my-auto">
                                    <div class="numbers">
                                        <p class="text-white text-sm mb-0 text-capitalize font-weight-bold opacity-7">
                                            เจ้าหน้าที่ธนาคารขยะรีไซเคิล
                                        <h5 class="text-white font-weight-bolder mb-0">
                                            {{$user->firstname . " " . $user->lastname}}
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <img class="w-50 border-radius-2xl" src="{{asset('soft-ui/assets/img/bruce-mars.jpg')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6 col-6">
                    <a href="{{route('keptkayas.purchase.select_user')}}">
                    <div class="card">
                        <div class="card-body text-center">
                            
                            <i class="fa fa-shopping-cart my-icon"></i>
                            <h2 class="text-gradient text-primary"><span id="status1">รับซื้อขยะรีไซเคิล</span> </h2>

                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-md-6 mt-md-0 col-6 ">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-search-dollar my-icon" ></i>
                            <h2 class="text-gradient text-primary"><span id="status1">ราคาขยะรีไซเคิล</span> </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6 col-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-cubes my-icon"></i>
                            
                            <h2 class="text-gradient text-primary"><span id="status1">วิธีแยกขยะรีไซเคิล</span> </h2>
                        </div>
                    </div>
                </div>
               
            </div>

            <!---  !-->
            <div class="card mt-4">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0">Consumption by room</h6>
                        <button type="button"
                            class="btn btn-icon-only btn-rounded btn-outline-secondary mb-0 ms-2 btn-sm d-flex align-items-center justify-content-center ms-auto"
                            data-bs-toggle="tooltip" data-bs-placement="bottom" aria-label="See the consumption per room"
                            data-bs-original-title="See the consumption per room">
                            <i class="fas fa-info"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-5 text-center">
                            <div class="chart">
                                <canvas id="chart-consumption" class="chart-canvas" height="394" width="345"
                                    style="display: block; box-sizing: border-box; height: 197px; width: 172px;"></canvas>
                            </div>
                            <h4 class="font-weight-bold mt-n8">
                                <span>471.3</span>
                                <span class="d-block text-body text-sm">WATTS</span>
                            </h4>
                        </div>
                        <div class="col-7">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-0">
                                                    <span class="badge bg-primary me-3"> </span>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">Living Room</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold"> 15% </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-0">
                                                    <span class="badge bg-gradient-secondary me-3"> </span>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">Kitchen</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold"> 20% </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-0">
                                                    <span class="badge bg-gradient-info me-3"> </span>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">Attic</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold"> 13% </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-0">
                                                    <span class="badge bg-gradient-success me-3"> </span>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">Garage</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold"> 32% </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-0">
                                                    <span class="badge bg-gradient-warning me-3"> </span>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">Basement</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold"> 20% </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection