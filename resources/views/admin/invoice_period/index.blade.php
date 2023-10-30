@extends('layouts.admin1')

@section('style')
<script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="
crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/bootstrap-datepicker-thai.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/locales/bootstrap-datepicker.th.js') }}">
</script>
@endsection

@section('invoice_period')
    active
@endsection
@section('nav')
    <a href="{{ route('admin.invoice_period.create') }}"> สร้างรอบบิล</a>
@endsection
@section('content')
    <div class="card p-2">
        <div class="card-header text-right">
            <a href="{{ route('admin.invoice_period.create') }}" class="btn btn-primary">สร้างรอบบิล</a>
        </div>
        <div class="card-content">
            <table class="table example">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">รอบบิล</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ปีงบประมาณ</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">วันที่เริ่มรอบบิล</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">วันที่สิ้นสุดรอบบิล</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    @foreach ($invoice_periods as $invoice_period)
                        <tr>
                            <th>{{ $i++ }}</th>
                            <th>{{ $invoice_period->inv_p_name }}</th>
                            <th>{{ $invoice_periods[0]->budgetyear->budgetyear }}</th>
                            <th>{{ $invoice_period->startdate }}</th>
                            <th>{{ $invoice_period->enddate }}</th>
                            <th>
                                <span
                                    class="right badge {{ $invoice_period->status == 'active' ? 'badge-success' : 'badge-primary' }}">
                                    {{ $invoice_period->status == 'inactive' ? 'สิ้นสุดรอบบิล' : 'รอบบิลปัจจุบัน' }}
                                </span>
                            </th>
                            <td class="align-middle">
                                <div class="dropstart float-lg-end ms-auto pe-0">
                                    <a href="javascript:;" class="cursor-pointer" id="dropdownTable2"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-ellipsis-h text-secondary" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5"
                                        aria-labelledby="dropdownTable2" style="">
                                        <li><a class="dropdown-item" href="{{ route('admin.invoice_period.edit', $invoice_period->id) }}">แก้ไขข้อมูล</a></li>
                                        <li>
                                            <form class="dropdown-item" method="POST" action="{{ route('admin.invoice_period.destroy', $invoice_period->id) }}" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="border-0 bg-white hover:bg-gray-900 m-0" type="submit">ลบข้อมูล</button>
                                             </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            {{-- <th>

                                    <a href="{{ route('admin.invoice_period.edit', $invoice_period->id) }}"
                                        class="btn btn-warning">แก้ไขข้อมูล</a>
                                    <a href="javascript:void(0)" data-invoice_period={{ $invoice_period->id }}
                                        class="btn btn-danger delbtn">ลบ</a>
                            </th> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endsection

@section('script')
    <script>
        $('.delbtn').click(() => {
            let res = window.confirm('ต้องการลบข้อมูลใช่หรือไม่ ?')
            if (res === true) {
                //หาว่ามี invoice ผูกกับ รอบบิลนี้หรือเปล่า
                //ถ้ามี ไม่ให้ลบรอบบิลนี้ ต้องไปลบ invoice ที่ถูกให้หมดก่อน
                let inv_period_id = $('.delbtn').data('invoice_period')
                $.get('/api/invoice/checkInvoice/' + inv_period_id)
                    .done(function(data) {
                        if (data > 0) {
                            alert('ไม่สามารถลบข้อมูลได้ \n เนื่องจากมีใบแจ้งหนี้ผูกกับรอบบิลนี้อยู่')
                        } else {
                            window.location.href = './invoice_period/delete/' + inv_period_id
                        }
                    });
            }
        })
    </script>
@endsection
