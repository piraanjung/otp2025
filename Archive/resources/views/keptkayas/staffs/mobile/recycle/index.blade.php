@extends('layouts.tabwater_staff_mobile')
@section('style')
    <style>
        .card {
            box-shadow: 5px 5px lightblue;
        }
    </style>
@endsection
@section('content')
<div class="container-fluid py-4">
      <div class="row">
        <div class="col-lg-8 col-12">
          <div class="row">
            <div class="col-lg-4 col-12">
              <div class="card card-background card-background-mask-info h-100 tilt" data-tilt="" style="will-change: transform; transform: perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1);">
                <div class="full-background" style="background-image: url('../../assets/img/curved-images/white-curved.jpeg')"></div>
                <div class="card-body pt-4 text-center">
                  <h2 class="text-white mb-0 mt-2 up">Earnings</h2>
                  <h1 class="text-white mb-0 up">$15,800</h1>
                  <span class="badge badge-lg d-block bg-gradient-dark mb-2 up">+15% since last week</span>
                  <a href="javascript:;" class="btn btn-outline-white mb-2 px-5 up">View more</a>
                </div>
              </div>
            </div>
            
            <div class="col-lg-4 col-md-6 col-12 mt-4 mt-lg-0">
              <div class="card">
                <div class="card-body p-3">
                  <div class="d-flex">
                    <div>
                      <div class="icon icon-shape bg-gradient-dark text-center border-radius-md">
                        <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                    </div>
                    <div class="ms-3">
                      <div class="numbers">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Today's Users</p>
                        <h5 class="font-weight-bolder mb-0">
                          2,300
                          <span class="text-success text-sm font-weight-bolder">+3%</span>
                        </h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card mt-4">
                <div class="card-body p-3">
                  <div class="d-flex">
                    <div>
                      <div class="icon icon-shape bg-gradient-dark text-center border-radius-md">
                        <i class="ni ni-shop text-lg opacity-10" aria-hidden="true"></i>
                      </div>
                    </div>
                    <div class="ms-3">
                      <div class="numbers">
                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Sign-ups</p>
                        <h5 class="font-weight-bolder mb-0">
                          348
                          <span class="text-success text-sm font-weight-bolder">+12%</span>
                        </h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-12 mt-4 mt-lg-0">
          <div class="card">
            <div class="card-header p-3 pb-0">
              <div class="row">
                <div class="col-8 d-flex">
                  <div>
                    <img src="../../assets/img/team-3.jpg" class="avatar avatar-sm me-2" alt="avatar image">
                  </div>
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm">Lucas Prila</h6>
                    <p class="text-xs">2h ago</p>
                  </div>
                </div>
                <div class="col-4">
                  <span class="badge bg-gradient-info ms-auto float-end">Recommendation</span>
                </div>
              </div>
            </div>
            <div class="card-body p-3 pt-1">
              <h6>I need a Ruby developer for my new website.</h6>
              <p class="text-sm">The website was initially built in PHP, I need a professional ruby programmer to shift it.</p>
              <div class="d-flex bg-gray-100 border-radius-lg p-3">
                <h4 class="my-auto">
                  <span class="text-secondary text-sm me-1">$</span>3,000<span class="text-secondary text-sm ms-1">/ month </span>
                </h4>
                <a href="javascript:;" class="btn btn-outline-dark mb-0 ms-auto">Apply</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      
    </div>
@endsection