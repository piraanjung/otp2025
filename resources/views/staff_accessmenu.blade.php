@extends('layouts.keptkaya_mobile')

@section('style')
    <style>
        .my-icon {
            font-size: 2rem;
            color: white;
            background: blue;
            border-radius: 50% 50%;
            padding: 10px
        }
    </style>
@endsection

@section('content')

    <div class="container-fluid">
        <div class="accordion-1">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 mx-auto text-center">
                        <img src="{{ asset('logo/ko_envsogo.png') }}" width="50%">
                        <h2>ENVSOGO::STAFF</h2>
                        {{-- <p>A lot of people don’t appreciate the moment until it’s passed. I'm not trying my hardest,
                            and I'm not trying to do </p> --}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10 mx-auto">
                        <div class="accordion" id="accordionRental">
                            <div class="accordion-item mb-3">
                                <h5 class="accordion-header" id="headingOne">
                                    <button class="accordion-button border-bottom font-weight-bold collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false"
                                        aria-controls="collapseOne">
                                        ธนาคารขยะรีไซเคิล
                                        <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3"
                                            aria-hidden="true"></i>
                                        <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3"
                                            aria-hidden="true"></i>
                                    </button>
                                </h5>
                                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                    data-bs-parent="#accordionRental" style="">
                                    <div class="accordion-body text-sm opacity-8">
                                        <div class="row">
                                            <div class="col-lg-6 col-sm-6">
                                                <div class="card  mb-4">
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="col-8">
                                                                <div class="numbers">
                                                                    <p
                                                                        class="text-sm mb-0 text-capitalize font-weight-bold">
                                                                        Today's Money</p>
                                                                    <h5 class="font-weight-bolder mb-0">
                                                                        รับซื้อขยะรีไซเคิล
                                                                        {{-- <span
                                                                            class="text-success text-sm font-weight-bolder">+55%</span>
                                                                        --}}
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                            <div class="col-4 text-end">
                                                                <a href="{{route('keptkayas.purchase.select_user')}}">
                                                                    <div
                                                                        class="icon icon-shape bg-primary shadow text-center border-radius-md">
                                                                        <i class="ni ni-money-coins text-lg opacity-10"
                                                                            aria-hidden="true"></i>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card ">
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="col-8">
                                                                <div class="numbers">
                                                                    <p
                                                                        class="text-sm mb-0 text-capitalize font-weight-bold">
                                                                        Today's Users</p>
                                                                    <h5 class="font-weight-bolder mb-0">
                                                                        ราคาขยะรีไซเคิล
                                                                        {{-- <span
                                                                            class="text-success text-sm font-weight-bolder">+3%</span>
                                                                        --}}
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                            <div class="col-4 text-end">
                                                                <div
                                                                    class="icon icon-shape bg-primary shadow text-center border-radius-md">
                                                                    <i class="ni ni-world text-lg opacity-10"
                                                                        aria-hidden="true"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-6 mt-sm-0 mt-4">
                                                <div class="card  mb-4">
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="col-9">
                                                                <div class="numbers">
                                                                    <p
                                                                        class="text-sm mb-0 text-capitalize font-weight-bold">
                                                                        New Clients</p>
                                                                    <h5 class="font-weight-bolder mb-0">
                                                                        วิธีคัดแยกขยะรีไซเคิล
                                                                        {{-- <span
                                                                            class="text-danger text-sm font-weight-bolder">-2%</span>
                                                                        --}}
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                            <div class="col-3 text-end">
                                                                <div
                                                                    class="icon icon-shape bg-primary shadow text-center border-radius-md">
                                                                    <i class="ni ni-paper-diploma text-lg opacity-10"
                                                                        aria-hidden="true"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item mb-3">
                                <h5 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button border-bottom font-weight-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                                        aria-controls="collapseTwo">
                                        <div style="position: absolute; top: 0; background-color: red;
                                        padding-left:8px;padding-right:8px; border-radius: 15px;left:5rem">
                                            {{ $notifies_pending_count }}
                                        </div>
                                        ประปา
                                        <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3"
                                            aria-hidden="true"></i>
                                        <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3"
                                            aria-hidden="true"></i>
                                    </button>
                                </h5>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                    data-bs-parent="#accordionRental">
                                    <div class="accordion-body text-sm opacity-8">
                                        <div class="row">
                                            <div class="col-lg-6 col-sm-6">
                                                <div class="card  mb-4">
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="col-8">
                                                                <a href="{{ route('twmanmobile.main') }}">
                                                                    <div class="numbers">
                                                                        <p
                                                                            class="text-sm mb-0 text-capitalize font-weight-bold">
                                                                            Today's Money</p>
                                                                        <h5 class="font-weight-bolder mb-0">
                                                                            จดมิเตอร์ประปา
                                                                            <span
                                                                                class="text-success text-sm font-weight-bolder">+55%</span>
                                                                        </h5>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                            <div class="col-4 text-end">
                                                                <div
                                                                    class="icon icon-shape bg-primary shadow text-center border-radius-md">
                                                                    <i class="ni ni-money-coins text-lg opacity-10"
                                                                        aria-hidden="true"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card ">
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="col-8">
                                                                <div class="numbers">
                                                                    <p
                                                                        class="text-sm mb-0 text-capitalize font-weight-bold">
                                                                        Today's Users</p>
                                                                    <h5 class="font-weight-bolder mb-0">
                                                                        ตัดมิเตอร์
                                                                        <span
                                                                            class="text-success text-sm font-weight-bolder">+3%</span>
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                            <div class="col-4 text-end">
                                                                <div
                                                                    class="icon icon-shape bg-primary shadow text-center border-radius-md">
                                                                    <i class="ni ni-world text-lg opacity-10"
                                                                        aria-hidden="true"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-6 mt-sm-0 mt-4">
                                                <div class="card  mb-4">
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="col-8">
                                                                <div class="numbers">
                                                                    <p
                                                                        class="text-sm mb-0 text-capitalize font-weight-bold">
                                                                        New Clients</p>
                                                                    <h5 class="font-weight-bolder mb-0">
                                                                        แจ้งซ่อมงานประปา
                                                                        <span class="text-danger text-sm font-weight-bolder">
                                                                            {{ $notifies_pending_count }}
                                                                        </span>
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                            <div class="col-4 text-end">
                                                                <div
                                                                    class="icon icon-shape bg-primary shadow text-center border-radius-md">
                                                                    <i class="ni ni-paper-diploma text-lg opacity-10"
                                                                        aria-hidden="true"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card ">
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="col-8">
                                                                <div class="numbers">
                                                                    <p
                                                                        class="text-sm mb-0 text-capitalize font-weight-bold">
                                                                        Sales</p>
                                                                    <h5 class="font-weight-bolder mb-0">
                                                                        $103,430
                                                                        <span
                                                                            class="text-success text-sm font-weight-bolder">+5%</span>
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                            <div class="col-4 text-end">
                                                                <div
                                                                    class="icon icon-shape bg-primary shadow text-center border-radius-md">
                                                                    <i class="ni ni-cart text-lg opacity-10"
                                                                        aria-hidden="true"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item mb-3">
                                <h5 class="accordion-header" id="headingThree">
                                    <button class="accordion-button border-bottom font-weight-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        ธนาคารขยะเปียก
                                        <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3"
                                            aria-hidden="true"></i>
                                        <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3"
                                            aria-hidden="true"></i>
                                    </button>
                                </h5>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                                    data-bs-parent="#accordionRental">
                                    <div class="accordion-body text-sm opacity-8">
                                        <div class="row">
                                            <div class="col-lg-6 col-sm-6">
                                                <div class="card  mb-4">
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            {{-- <div class="col-8">
                                                                <div class="numbers">
                                                                    <p
                                                                        class="text-sm mb-0 text-capitalize font-weight-bold">
                                                                        Today's Money</p>
                                                                    <h5 class="font-weight-bolder mb-0">
                                                                        จดมิเตอร์ประปา
                                                                        <span
                                                                            class="text-success text-sm font-weight-bolder">+55%</span>
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                            <div class="col-4 text-end">
                                                                <div
                                                                    class="icon icon-shape bg-primary shadow text-center border-radius-md">
                                                                    <i class="ni ni-money-coins text-lg opacity-10"
                                                                        aria-hidden="true"></i>
                                                                </div>
                                                            </div> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card ">
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="col-8">
                                                                <div class="numbers">
                                                                    <p
                                                                        class="text-sm mb-0 text-capitalize font-weight-bold">
                                                                        Today's Users</p>
                                                                    <h5 class="font-weight-bolder mb-0">
                                                                        ตัดมิเตอร์
                                                                        <span
                                                                            class="text-success text-sm font-weight-bolder">+3%</span>
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                            <div class="col-4 text-end">
                                                                <div
                                                                    class="icon icon-shape bg-primary shadow text-center border-radius-md">
                                                                    <i class="ni ni-world text-lg opacity-10"
                                                                        aria-hidden="true"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-6 mt-sm-0 mt-4">
                                                {{-- <div class="card  mb-4">
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="col-8">
                                                                <div class="numbers">
                                                                    <p
                                                                        class="text-sm mb-0 text-capitalize font-weight-bold">
                                                                        แจ้งเตือนจาก User</p>
                                                                    <h5 class="font-weight-bolder mb-0">
                                                                        +3,462
                                                                        <span
                                                                            class="text-danger text-sm font-weight-bolder">-2%</span>
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                            <div class="col-4 text-end">
                                                                <div
                                                                    class="icon icon-shape bg-primary shadow text-center border-radius-md">
                                                                    <i class="ni ni-paper-diploma text-lg opacity-10"
                                                                        aria-hidden="true"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card ">
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="col-8">
                                                                <div class="numbers">
                                                                    <p
                                                                        class="text-sm mb-0 text-capitalize font-weight-bold">
                                                                        Sales</p>
                                                                    <h5 class="font-weight-bolder mb-0">
                                                                        $103,430
                                                                        <span
                                                                            class="text-success text-sm font-weight-bolder">+5%</span>
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                            <div class="col-4 text-end">
                                                                <div
                                                                    class="icon icon-shape bg-primary shadow text-center border-radius-md">
                                                                    <i class="ni ni-cart text-lg opacity-10"
                                                                        aria-hidden="true"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="accordion-item mb-3">
                                <h5 class="accordion-header" id="headingFour">
                                    <button class="accordion-button border-bottom font-weight-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false"
                                        aria-controls="collapseFour">
                                        Can I resell the products?
                                        <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3"
                                            aria-hidden="true"></i>
                                        <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3"
                                            aria-hidden="true"></i>
                                    </button>
                                </h5>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                                    data-bs-parent="#accordionRental">
                                    <div class="accordion-body text-sm opacity-8">
                                        I always felt like I could do anything. That’s the main thing people are controlled
                                        by! Thoughts- their perception of themselves! They're slowed down by their
                                        perception of themselves. If you're taught you can’t do anything, you won’t do
                                        anything. I was taught I could do everything.
                                        <br><br>
                                        If everything I did failed - which it doesn't, it actually succeeds - just the fact
                                        that I'm willing to fail is an inspiration. People are so scared to lose that they
                                        don't even try. Like, one thing people can't say is that I'm not trying, and I'm not
                                        trying my hardest, and I'm not trying to do the best way I know how.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item mb-3">
                                <h5 class="accordion-header" id="headingFifth">
                                    <button class="accordion-button border-bottom font-weight-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseFifth" aria-expanded="false"
                                        aria-controls="collapseFifth">
                                        Where do I find the shipping details?
                                        <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3"
                                            aria-hidden="true"></i>
                                        <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3"
                                            aria-hidden="true"></i>
                                    </button>
                                </h5>
                                <div id="collapseFifth" class="accordion-collapse collapse" aria-labelledby="headingFifth"
                                    data-bs-parent="#accordionRental">
                                    <div class="accordion-body text-sm opacity-8">
                                        There’s nothing I really wanted to do in life that I wasn’t able to get good at.
                                        That’s my skill. I’m not really specifically talented at anything except for the
                                        ability to learn. That’s what I do. That’s what I’m here for. Don’t be afraid to be
                                        wrong because you can’t learn anything from a compliment.
                                        I always felt like I could do anything. That’s the main thing people are controlled
                                        by! Thoughts- their perception of themselves! They're slowed down by their
                                        perception of themselves. If you're taught you can’t do anything, you won’t do
                                        anything. I was taught I could do everything.
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>


@endsection