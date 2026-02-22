<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">

    <title>PpP</title>

    <!-- CSS FILES -->
    <link href="{{ asset('templatemo/css/bootstrap.min.css') }}" rel="stylesheet">

    <link href="{{ asset('templatemo/css/bootstrap-icons.css') }}" rel="stylesheet">

    <link href="{{ asset('templatemo/css/templatemo-kind-heart-charity.css') }}" rel="stylesheet">
    <style>
        .carousel-inner {
            position: relative;
            width: 100%;
            overflow: hidden;
            height: 600px;
        }

        #hero-slide .carousel-item {
            height: 600px;
            min-height: 600px;
        }

        .hero-section-full-height {
            height: 580px;
            min-height: 580px;
            position: relative;
        }

        .section-padding {
            padding-top: 60px;
            padding-bottom: 50px;
        }

        .carousel-caption {
            margin-top: -3rem
        }

        .cus-btn {
            margin-top: -2.5rem;
            /* width: 50% */
        }

        .inactive-menu {
            opacity: 0.3;
        }

        .inactive-menu .align-items-center a:hover {
            cursor: none !important;
        }
    </style>

</head>

<body id="section_1">

    <nav class="navbar navbar-expand-lg bg-light shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('/logo/' . $orgInfos->org_logo_img) }}" class="logo img-fluid">
                <span>
                    {{$orgInfos->org_type_name . "" . $orgInfos->org_name}}
                    <small>อำเภอ{{$orgInfos->districts->district_name}}
                        จังหวัด{{$orgInfos->provinces->province_name}}</small>
                </span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

        </div>
    </nav>

    <main>



        <section class="section-padding">
            {{-- <div class="col-lg-10 col-12 text-center mx-auto">
                <h2 class="">{{$orgInfos->org_type_name . "" . $orgInfos->org_name}}</h2>
            </div> --}}
            <div class="container">
                <div class="row">




                    <div class="col-lg-3 cus-btn col-md-6 col-12 mb-4 mb-lg-0
                        {{-- {{ Auth::user()->hasPermission('access-tabwater-menu') ? '' : " inactive-menu" }} --}} ">
                        <div class=" featured-block d-flex justify-content-center align-items-center">
                            <a href="{{'dashboard'}}" class="d-block">
                                <img src="{{ asset('soft-ui/assets/img/water.png') }}"
                                    class="featured-block-image img-fluid" style="height:220px" alt="">

                                {{-- <p class="featured-block-text"> <strong>ระบบ</strong></p> --}}
                                <p class="featured-block-text">จดมิเตอร์ประปา</p>

                            </a>
                        </div>
                    </div>


                    <div class="col-lg-3 cus-btn col-md-6 col-12 mb-4 mb-lg-0 mb-md-4
                    {{ Auth::user()->can('access waste bank') ? '' : "inactive-menu" }}
                    ">
                        <div class="featured-block d-flex justify-content-center align-items-center">


                            <button type="submit" class="btn btn-outline-white">
                                <a href="{{Auth::user()->can('access waste bank') ? route('keptkayas.dashboard') : '#'}}"
                                    class="d-block">
                                    <img src="{{ asset('imgs/recycle_sys.jpg') }}"
                                        style="width: 60%"
                                        class="featured-block-image img-fluid" alt="">

                                    {{-- <p class="featured-block-text"><strong>ระบบ</strong> </p> --}}
                                    <p class="featured-block-text">ธนาคารขยะชุมชน</p>
                                </a>

                            </button>

                        </div>
                    </div>

                    <div class="col-lg-3 cus-btn col-md-6 col-12 mb-4 mb-lg-0 mb-md-4
                    {{ Auth::user()->can('access waste bank') ? '' : "inactive-menu" }}
                    ">
                        <div class="featured-block d-flex justify-content-center align-items-center">


                            <button type="submit" class="btn btn-outline-white">
                                <a href="{{Auth::user()->can('access waste bank') ? route('keptkayas.dashboard') : '#'}}"
                                    class="d-block">
                                    <img src="{{ asset('imgs/bin_empty.png') }}"
                                        style="width: 67%"
                                        class="featured-block-image img-fluid" alt="">

                                    {{-- <p class="featured-block-text"><strong>ระบบ</strong> </p> --}}
                                    <p class="featured-block-text">จัดเก็บค่าจัดการขยะรายปี</p>
                                </a>

                            </button>

                        </div>
                    </div>

                    <div class="col-lg-3 cus-btn col-md-6 col-12 mb-4 mb-lg-0 mb-md-4
                    {{ Auth::user()->can('access waste bank') ? '' : "inactive-menu" }}
                    ">
                        <div class="featured-block d-flex justify-content-center align-items-center">
                            <a href="{{Auth::user()->can('access waste bank') ? route('keptkayas.dashboard') : '#'}}"
                                class="d-block">

                                <img src="{{ asset('templatemo/images/icons/receive.png') }}"
                                    class="featured-block-image img-fluid" alt="">

                                <p class="featured-block-text"> <strong>ระบบ</strong></p>
                                <p class="featured-block-text">ธนาคารขยะเปียก</p>

                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 cus-btn col-md-6 col-12 mb-4 mb-lg-0 
                    {{-- {{ Auth::user()->hasPermission('access-local-bank-menu') ? '' : " inactive-menu" }} --}} ">
                        <div class=" featured-block d-flex justify-content-center align-items-center">
                            <a href="#" class="d-block">
                                <img src="{{ asset('templatemo/images/icons/scholarship.png') }}"
                                    class="featured-block-image img-fluid" alt="">

                                <p class="featured-block-text"><strong>ระบบ</strong> </p>
                                <p class="featured-block-text">ธนาคารชุมชนออมทรัพย์</p>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>


    </main>



    <!-- JAVASCRIPT FILES -->
    <script src="{{ asset('templatemo/js/jquery.min.js') }}"></script>
    <script src="{{ asset('templatemo/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('templatemo/js/jquery.sticky.js') }}"></script>
    <script src="{{ asset('templatemo/js/click-scroll.js') }}"></script>
    <script src="{{ asset('templatemo/js/counter.js') }}"></script>
    <script src="{{ asset('templatemo/js/custom.js') }}"></script>

</body>

</html>