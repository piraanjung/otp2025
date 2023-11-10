@extends('layouts.admin1')

@section('nav-invoice')
    active
@endsection
@section('nav-header')
จัดการใบแจ้งหนี้
@endsection
@section('nav-main')
<a href="{{route('invoice.index')}}"> ออกใบแจ้งหนี้</a>
@endsection
@section('nav-current')
ข้อมูลใบแจ้งหนี้แยกตามเส้นทางจัดเก็บ
@endsection
@section('page-topic')
ข้อมูลใบแจ้งหนี้แยกตามเส้นทางจัดเก็บ
@endsection

@section('content')
    <div class="container-fluid my-3 py-3">
        <div class="row mb-5">
            <div class="col-lg-4">
                <div class="card position-sticky top-1">
                    <ul class="nav bg-white border-radius-lg p-3 row">
                        <?php $i = 0; ?>
                        <li class="col-12"><h5>เส้นทางจดมิเตอร์</h5></li>
                        @foreach ($zones as $key => $zone)
                            <li class="nav-item pt-2 col-12 col-lg-6">
                                <a class="nav-link text-body" data-scroll="" href="#b{{ $i++ }}">
                                    <div class="icon me-2">
                                        <svg class="text-dark mb-1" width="16px" height="16px" viewBox="0 0 40 44"
                                            version="1.1" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <title>document</title>
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <g transform="translate(-1870.000000, -591.000000)" fill="#FFFFFF"
                                                    fill-rule="nonzero">
                                                    <g transform="translate(1716.000000, 291.000000)">
                                                        <g transform="translate(154.000000, 300.000000)">
                                                            <path class="color-background"
                                                                d="M40,40 L36.3636364,40 L36.3636364,3.63636364 L5.45454545,3.63636364 L5.45454545,0 L38.1818182,0 C39.1854545,0 40,0.814545455 40,1.81818182 L40,40 Z"
                                                                opacity="0.603585379"></path>
                                                            <path class="color-background"
                                                                d="M30.9090909,7.27272727 L1.81818182,7.27272727 C0.814545455,7.27272727 0,8.08727273 0,9.09090909 L0,41.8181818 C0,42.8218182 0.814545455,43.6363636 1.81818182,43.6363636 L30.9090909,43.6363636 C31.9127273,43.6363636 32.7272727,42.8218182 32.7272727,41.8181818 L32.7272727,9.09090909 C32.7272727,8.08727273 31.9127273,7.27272727 30.9090909,7.27272727 Z M18.1818182,34.5454545 L7.27272727,34.5454545 L7.27272727,30.9090909 L18.1818182,30.9090909 L18.1818182,34.5454545 Z M25.4545455,27.2727273 L7.27272727,27.2727273 L7.27272727,23.6363636 L25.4545455,23.6363636 L25.4545455,27.2727273 Z M25.4545455,20 L7.27272727,20 L7.27272727,16.3636364 L25.4545455,16.3636364 L25.4545455,20 Z">
                                                            </path>
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>
                                    </div>
                                    <span class="text-sm">เส้น:: {{ $zone['zone_info']['undertake_subzone']->subzone_name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-lg-8 mt-lg-0 mt-4">
                <?php $i = 0; ?>
                @foreach ($zones as $zone)
                    @if ($i == 0)
                        <div class="card" id="b{{ $i++ }}">
                    @else
                    <div class="card mt-4" id="b{{ $i++ }}">
                    @endif
                            <div class="card-header col-6">
                                <div class="card">
                                    <span class="mask bg-gradient-dark opacity-9 border-radius-xl"></span>
                                    <div class="card-body p-3 position-relative">
                                        <div class="row">
                                            <div class="col-8 text-start">

                                                <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                                    {{ $zone['zone_info']['undertake_zone']->zone_name }}
                                                </h5>
                                                <span class="text-white text-sm">เส้นทาง :
                                                    {{ $zone['zone_info']['undertake_subzone']->subzone_name }}</span>
                                            </div>
                                            <div class="col-4">

                                                <p class="text-white text-sm text-end font-weight-bolder mt-auto mb-0">สมาชิก {{ $zone['members_count'] }} คน</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <p class="my-auto h6 col-12 col-md-3">ยังไม่บันทึกข้อมูลมิเตอร์</p>
                                    <p class="text-secondary h5 ms-auto my-auto me-3 col-12 col-md-2  text-xl-end">

                                        {{ $zone['initTotalCount'] }} <sup> คน</sup>


                                    </p>
                                    <div class="col-12 col-md-6">
                                        <a href="{{ route('invoice.zone_create' ,
                                        ['zone_id' => $zone['zone_info']->undertake_subzone_id,'curr_inv_prd' =>$current_inv_period->id])
                                    }}"
                                            class="foatright btn btn-sm btn-outline-dark mb-0  {{ $zone['initTotalCount'] == 0 ? 'disabled' : '' }}">เพิ่มข้อมูล
                                        </a>
                                    </div>
                                </div>

                                <hr class="horizontal dark">

                                <div class="row">
                                    <p class="my-auto h6 col-12 col-md-3">ค้างชำระเกิน 3 รอบบิล</p>
                                    <p class="text-secondary h5 ms-auto my-auto me-3 col-12 col-md-2  text-xl-end">

                                        {{ $zone['owe_over3'] }} <sup> คน</sup>
                                    </p>
                                    <div class="col-12 col-md-6">
                                        <a href="{{ url('cutmeter/index/' . $zone['zone_info']->undertake_subzone_id) }}"
                                            class="foatright btn btn-sm btn-outline-dark mb-0 {{ $zone['owe_over3'] == 0 ? 'disabled' : '' }}">ดูข้อมูล
                                        </a>
                                    </div>
                                </div>
                                <hr class="horizontal dark">

                                <div class="row">
                                    <p class="my-auto h6 col-12 col-md-3">บันทึกข้อมูลแล้ว</p>
                                    <p class="text-secondary h5 ms-auto my-auto me-3 col-12 col-md-2  text-xl-end">
                                        {{ $zone['invoiceTotalCount'] }} <sup> คน</sup>
                                    </p>
                                    <div class="col-12 col-md-6">
                                        <a href="{{ route('invoice.invoiced_lists' , $zone['zone_info']->undertake_subzone_id) }}"
                                            class="foatright btn btn-sm btn-outline-dark mb-0 {{ $zone['invoiceTotalCount'] == 0 ? 'disabled' : '' }}">
                                            ปริ้นใบแจ้งหนี้
                                        </a>
                                        <a style="margin-right: 5px"
                                            href="{{ route('invoice.zone_edit' , ['subzone_id' =>$zone['zone_info']->undertake_subzone_id,
                                            'curr_inv_prd' => $current_inv_period]) }}"
                                            class="foatright btn btn-sm btn-outline-dark mb-0  btn-sm {{ $zone['invoiceTotalCount'] == 0 ? 'disabled' : '' }}">
                                            แก้ไขข้อมูล
                                        </a>
                                    </div>
                                </div>
                                <hr class="horizontal dark">
                                <div class="row">
                                    <p class="my-auto h6 col-12 col-md-3">ชำระเงินแล้ว</p>
                                    <p class="text-secondary h5 ms-auto my-auto me-3 col-12 col-md-2  text-xl-end">
                                        {{ $zone['paidTotalCount'] }} <sup> คน</sup>
                                    </p>
                                    <div class="col-12 col-md-6">
                                        <a href="{{ url('payment/paymenthistory/' . $current_inv_period->id . '/' . $zone['zone_info']->undertake_subzone_id) }}"
                                            class="foatright btn btn-sm btn-outline-dark mb-0 {{ $zone['paidTotalCount'] == 0 ? 'disabled' : '' }} ">
                                            ดูข้อมูล
                                        </a>
                                    </div>
                                </div>

                                <hr class="horizontal dark">
                                <div class="row">
                                    <p class="my-auto h6 col-12 col-md-4">เพิ่มผู้ใช้น้ำระหว่างรอบบิล</p>
                                    <p class="text-secondary h5 ms-auto my-auto me-3 col-12 col-md-1  text-xl-end">
                                        {{-- {{ $zone['new_user'] }} <sup> คน</sup> --}}
                                    </p>
                                    <div class="col-12 col-md-6">
                                        <a href="{{ url('/users') }}"
                                            class="foatright btn btn-sm btn-outline-dark mb-0">เพิ่มข้อมูล </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let i = 0;
        //ค้นหาโดยเลขมิเตอร์
        $('#meternumber').keyup(function() {
            let meternumber = $('#meternumber').val();

            $.get("invoice/search_from_meternumber/" + meternumber).done(function(data) {
                console.log('data', data)
                if (data.usermeterInfos === null) {
                    $('.addBtn').addClass('hidden');
                    $('.empty_user').text('ไม่พบผู้ใช้งานเลขมิเตอร์นี้')
                } else {

                    let address =
                        `${data.usermeterInfos.user.usermeter_info.zone.zone_name}  ${data.usermeterInfos.user.usermeter_info.zone.location}`;
                    $('#feFirstName').val(data.usermeterInfos.user.user_profile.name);
                    $('#feInputAddress').val(address);
                    $('.empty_user').text('')
                    //ถ้า invoice = 0
                    if (data.invoice === null) {
                        $('#lastmeter').val(0);
                        $('#last_invoice').val(-1);
                    } else {
                        $('#lastmeter').val(data.invoice.currentmeter);
                        $('#last_invoice').val(data.invoice.id);
                    }
                    $('#user_id').val(data.usermeterInfos.user.id);
                    if ($('.addBtn').hasClass('hidden')) {
                        $('.addBtn').removeClass('hidden');
                    }
                }
            });
        })

        //คำนวนเงินค่าใช้น้ำ
        $('#currentmeter').keyup(function() {
            let lastmeter = $('#lastmeter').val();
            let currentmeter = $(this).val();
            let total = (currentmeter - lastmeter) * 8;
            $('#cashtotal').val(total);
        });

        //เพิ่มข้อมูลลงตาราง lists
        $('.addBtn').click(function() {
            let newtr = `
        <tr>
            <td width="1%"></td>
            <td width="24%">
                <input type="text" value="${$('#feFirstName').val()}" class="form-control" name="" readonly>
                <input type="hidden" value="${$('#last_invoice').val()}" name="data[${i}][last_invoice]">
                <input type="hidden" value="${$('#user_id').val()}" name="data[${i}][user_id]">
            </td>
            <td width="40%"><input type="text" value="${$('#feInputAddress').val()}" class="form-control" name="" readonly></td>
            <td width="10%"><input type="text" value="${$('#lastmeter').val()}" class="form-control" name="data[${i}][lastmeter]" readonly></td>
            <td width="10%"><input type="text" value="${$('#currentmeter').val()}" class="form-control" name="data[${i}][currentmeter]"></td>
            <td width="10%"><input type="text" value="${$('#cashtotal').val()}" class="form-control" name="data[${i}][cashtotal]" readonly></td>
            <td width="5%"><button type="button" class="btn btn-danger aa">
                              <span><i class="fa fa-trash"></i></span> </button></td>
        </tr>
        `;
            $('#lists').append(newtr);
            i++;
        });

        $("body").on("click", ".aa", function() {
            $(this).parent().parent().remove();
        });
        $(document).ready(() => {
            setTimeout(() => {
                $('.alert').toggle('slow')
            }, 2000)
        })
    </script>
@endsection
