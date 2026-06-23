@extends('layouts.admin1')

@section('title_page')
    ข้อมูลผู้ใช้งานระบบ
@endsection

@section('url')
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">แอดมิน</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">ผู้ใช้งานระบบ</li>
@endsection
@section('style')
    <style>
        .hidden {
            display: none
        }

        .other_input {
            border: 1px solid red
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-5">
                        <div class="col-lg-3">
                            <div class="card position-sticky top-1">
                                <ul class="nav flex-column bg-white border-radius-lg p-3">
                                    <li class="nav-item">
                                        <a class="nav-link text-body" data-scroll="" href="#profile">
                                            <div class="icon me-2">
                                                <svg class="text-dark mb-1" width="16px" height="16px"
                                                    viewBox="0 0 40 40" version="1.1" xmlns="http://www.w3.org/2000/svg"
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
                                            <span class="text-sm">ข้อมูลผู้ใช้งาน</span>
                                        </a>
                                    </li>
                                    <li class="nav-item pt-2">
                                        <a class="nav-link text-body" data-scroll="" href="#owe">
                                            <div class="icon me-2">
                                                <svg class="text-dark mb-1" width="16px" height="16px"
                                                    viewBox="0 0 45 40" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink">
                                                    <title>shop </title>
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <g transform="translate(-1716.000000, -439.000000)" fill="#FFFFFF"
                                                            fill-rule="nonzero">
                                                            <g transform="translate(1716.000000, 291.000000)">
                                                                <g transform="translate(0.000000, 148.000000)">
                                                                    <path class="color-background"
                                                                        d="M46.7199583,10.7414583 L40.8449583,0.949791667 C40.4909749,0.360605034 39.8540131,0 39.1666667,0 L7.83333333,0 C7.1459869,0 6.50902508,0.360605034 6.15504167,0.949791667 L0.280041667,10.7414583 C0.0969176761,11.0460037 -1.23209662e-05,11.3946378 -1.23209662e-05,11.75 C-0.00758042603,16.0663731 3.48367543,19.5725301 7.80004167,19.5833333 L7.81570833,19.5833333 C9.75003686,19.5882688 11.6168794,18.8726691 13.0522917,17.5760417 C16.0171492,20.2556967 20.5292675,20.2556967 23.494125,17.5760417 C26.4604562,20.2616016 30.9794188,20.2616016 33.94575,17.5760417 C36.2421905,19.6477597 39.5441143,20.1708521 42.3684437,18.9103691 C45.1927731,17.649886 47.0084685,14.8428276 47.0000295,11.75 C47.0000295,11.3946378 46.9030823,11.0460037 46.7199583,10.7414583 Z"
                                                                        opacity="0.598981585"></path>
                                                                    <path class="color-foreground"
                                                                        d="M39.198,22.4912623 C37.3776246,22.4928106 35.5817531,22.0149171 33.951625,21.0951667 L33.92225,21.1107282 C31.1430221,22.6838032 27.9255001,22.9318916 24.9844167,21.7998837 C24.4750389,21.605469 23.9777983,21.3722567 23.4960833,21.1018359 L23.4745417,21.1129513 C20.6961809,22.6871153 17.4786145,22.9344611 14.5386667,21.7998837 C14.029926,21.6054643 13.533337,21.3722507 13.0522917,21.1018359 C11.4250962,22.0190609 9.63246555,22.4947009 7.81570833,22.4912623 C7.16510551,22.4842162 6.51607673,22.4173045 5.875,22.2911849 L5.875,44.7220845 C5.875,45.9498589 6.7517757,46.9451667 7.83333333,46.9451667 L19.5833333,46.9451667 L19.5833333,33.6066734 L27.4166667,33.6066734 L27.4166667,46.9451667 L39.1666667,46.9451667 C40.2482243,46.9451667 41.125,45.9498589 41.125,44.7220845 L41.125,22.2822926 C40.4887822,22.4116582 39.8442868,22.4815492 39.198,22.4912623 Z">
                                                                    </path>
                                                                </g>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </svg>
                                            </div>
                                            <span class="text-sm">สถานะการค้างจ่าย</span>
                                        </a>
                                    </li>
                                    @if ( $user[0]->status == 1 )
                                    <li class="nav-item pt-2">
                                        <a class="nav-link text-body" data-scroll="" href="#delete">
                                            <div class="icon me-2">
                                                <svg class="text-dark mb-1" width="16px" height="16px"
                                                    viewBox="0 0 45 40" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink">
                                                    <title>shop </title>
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <g transform="translate(-1716.000000, -439.000000)" fill="#FFFFFF"
                                                            fill-rule="nonzero">
                                                            <g transform="translate(1716.000000, 291.000000)">
                                                                <g transform="translate(0.000000, 148.000000)">
                                                                    <path class="color-background"
                                                                        d="M46.7199583,10.7414583 L40.8449583,0.949791667 C40.4909749,0.360605034 39.8540131,0 39.1666667,0 L7.83333333,0 C7.1459869,0 6.50902508,0.360605034 6.15504167,0.949791667 L0.280041667,10.7414583 C0.0969176761,11.0460037 -1.23209662e-05,11.3946378 -1.23209662e-05,11.75 C-0.00758042603,16.0663731 3.48367543,19.5725301 7.80004167,19.5833333 L7.81570833,19.5833333 C9.75003686,19.5882688 11.6168794,18.8726691 13.0522917,17.5760417 C16.0171492,20.2556967 20.5292675,20.2556967 23.494125,17.5760417 C26.4604562,20.2616016 30.9794188,20.2616016 33.94575,17.5760417 C36.2421905,19.6477597 39.5441143,20.1708521 42.3684437,18.9103691 C45.1927731,17.649886 47.0084685,14.8428276 47.0000295,11.75 C47.0000295,11.3946378 46.9030823,11.0460037 46.7199583,10.7414583 Z"
                                                                        opacity="0.598981585"></path>
                                                                    <path class="color-foreground"
                                                                        d="M39.198,22.4912623 C37.3776246,22.4928106 35.5817531,22.0149171 33.951625,21.0951667 L33.92225,21.1107282 C31.1430221,22.6838032 27.9255001,22.9318916 24.9844167,21.7998837 C24.4750389,21.605469 23.9777983,21.3722567 23.4960833,21.1018359 L23.4745417,21.1129513 C20.6961809,22.6871153 17.4786145,22.9344611 14.5386667,21.7998837 C14.029926,21.6054643 13.533337,21.3722507 13.0522917,21.1018359 C11.4250962,22.0190609 9.63246555,22.4947009 7.81570833,22.4912623 C7.16510551,22.4842162 6.51607673,22.4173045 5.875,22.2911849 L5.875,44.7220845 C5.875,45.9498589 6.7517757,46.9451667 7.83333333,46.9451667 L19.5833333,46.9451667 L19.5833333,33.6066734 L27.4166667,33.6066734 L27.4166667,46.9451667 L39.1666667,46.9451667 C40.2482243,46.9451667 41.125,45.9498589 41.125,44.7220845 L41.125,22.2822926 C40.4887822,22.4116582 39.8442868,22.4815492 39.198,22.4912623 Z">
                                                                    </path>
                                                                </g>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </svg>
                                            </div>
                                            <span class="text-sm">ลบผู้ใช้งานระบบ</span>
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-9 mt-lg-0 mt-4">
                            <div class="card card-body" id="profile">
                                    <h5>ข้อมูลผู้ใช้งาน</h5>
                                <div class="row mt-lg-4 mt-2">
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="card">
                                            <div class="card-body p-3">
                                                <div class="d-flex">

                                                    <div class="ms-3 my-auto">
                                                        <h6></h6>

                                                    </div>

                                                </div>
                                                <p class="mt-3 h-3">
                                                    {{ $user[0]->prefix . '' . $user[0]->firstname . ' ' . $user[0]->lastname }}
                                                </p>
                                                <hr class="horizontal dark">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h6 class="text-sm mb-0"></h6>
                                                        <p class="text-secondary text-sm font-weight-bold mb-0">
                                                        </p>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <h6 class="text-sm mb-0 {{$user[0]->status == 1 ? 'text-success' : 'text-danger'}}">
                                                            {{ $user[0]->status == 1 ? 'ใช้งานระบบ' : 'ยกเลิกการใช้งาน' }}</h6>
                                                        <p class="text-secondary text-sm font-weight-bold mb-0">
                                                            สถานะการใช้งาน
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-md-6 mb-4">
                                        <div class="card">
                                            <div class="card-header pb-0 p-3">
                                                <div class="d-flex align-items-center">
                                                    <h6 class="mb-0">ข้อมูลผู้ใช้งาน</h6>
                                                    <button type="button"
                                                        class="btn btn-icon-only btn-rounded btn-outline-secondary mb-0 ms-2 btn-sm d-flex align-items-center justify-content-center ms-auto"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        aria-label="See the consumption per room"
                                                        data-bs-original-title="See the consumption per room">
                                                        <i class="fas fa-info" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="table-responsive">
                                                            <table class="table align-items-center mb-0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <div class="d-flex px-2 py-0">
                                                                                <span
                                                                                    class="badge bg-gradient-primary me-3">
                                                                                </span>
                                                                                <div
                                                                                    class="d-flex flex-column justify-content-center">
                                                                                    <h6 class="mb-0 text-sm">ที่อยู่
                                                                                    </h6>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td class="align-middle text-center text-sm">
                                                                            <span class="text-xs font-weight-bold">
                                                                                {{ $user[0]->address . ' ' . $user[0]->user_zone->zone_name }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div class="d-flex px-2 py-0">
                                                                                <span
                                                                                    class="badge bg-gradient-secondary me-3">
                                                                                </span>
                                                                                <div
                                                                                    class="d-flex flex-column justify-content-center">
                                                                                    <h6 class="mb-0 text-sm">เบอร์โทรศัพท์
                                                                                    </h6>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td class="align-middle text-center text-sm">
                                                                            <span class="text-xs font-weight-bold">
                                                                                {{ $user[0]->phone }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div class="d-flex px-2 py-0">
                                                                                <span class="badge bg-gradient-info me-3">
                                                                                </span>
                                                                                <div
                                                                                    class="d-flex flex-column justify-content-center">
                                                                                    <h6 class="mb-0 text-sm">เลขบัตรประชาชน
                                                                                    </h6>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td class="align-middle text-center text-sm">
                                                                            <span class="text-xs font-weight-bold">
                                                                                {{ $user[0]->id_card }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <div class="d-flex px-2 py-0">
                                                                                <span
                                                                                    class="badge bg-gradient-success me-3">
                                                                                </span>
                                                                                <div
                                                                                    class="d-flex flex-column justify-content-center">
                                                                                    <h6 class="mb-0 text-sm">role_id</h6>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td class="align-middle text-center text-sm">
                                                                            <span class="text-xs font-weight-bold">
                                                                                {{ $user[0]->role_id }}
                                                                            </span>
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
                            <div class="card card-body  mt-4" id="owe">
                                    <h5>สถานะการค้างจ่าย</h5>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <h1 class="text-gradient text-primary"><span id="status1"
                                                        countto="{{collect($user[0]->usermeterinfos[0]->invoice)->count()}}">
                                                    {{collect($user[0]->usermeterinfos[0]->invoice)->count()}}
                                                    </span> <span class="text-lg ms-n2"> (รอบบิล)</span>
                                                </h1>
                                                <h6 class="mb-0 font-weight-bolder">ค้างจ่าย</h6>
                                                <p class="opacity-8 mb-0 text-sm">
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-md-0 mt-4">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <a href="" class="btn btn-info">ชำระค่าน้ำ</a>
                                                <a href="" class="btn btn-warning">ปริ้นใบแจ้งเตือนการชำระค่าน้ำ</a>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ( $user[0]->status == 1 )
                                <div class="card mt-4" id="delete">
                                    <div class="card-header">
                                        <h5>ลบผู้ใช้งานระบบ</h5>
                                        <p class="text-sm mb-0">
                                            ถ้ามียอดการค้างชำระค่าน้ำประปาอยู่ระบบจะยังคงแสดงข้อมูลการค้างชำระ แต่จะไม่สามารถสร้างรอบบิลได้
                                        </p>
                                    </div>
                                    <div class="card-body d-sm-flex pt-0">
                                        <div class="d-flex align-items-center mb-sm-0 mb-4">
                                            <div>
                                                <div class="form-check form-switch mb-0">

                                                </div>
                                            </div>
                                            <div class="ms-2">
                                                <span class="text-dark font-weight-bold d-block text-sm"></span>
                                                <span class="text-xs d-block"></span>
                                            </div>
                                        </div>
                                        <button class="btn btn-outline-secondary mb-0 ms-auto opacity-0" type="button"
                                            name="button"></button>
                                        <form action="{{ route('admin.users.destroy', $user[0]) }}" method="POST" onSubmit="return confirm2()">
                                            @csrf
                                            @method("DELETE")
                                            <input class="btn bg-gradient-danger mb-0 ms-2" type="submit" name="button" value="ยกเลิกการใช้งานระบบ">
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        function confirm2(){
            if (confirm("ท่านต้องการลบข้อมูลผู้ใช้งานระบบใช่หรือไม่!") == true) {
                return true
            } else {
                return false
            }
        }
        
    </script>
@endsection
