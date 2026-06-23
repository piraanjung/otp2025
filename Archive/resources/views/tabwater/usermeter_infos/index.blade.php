@extends('layouts.admin1')

@section('content')
    <div class="card">
        <div class="card-header">
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>รอบบิล</th>
                        <th>ก่อนจด<div>(หน่วย)</div></th>
                        <th>หลังจด<div>(หน่วย)</div></th>
                        <th>ใช้น้ำ<div>(หน่วย)</div></th>
                        <th>ต้องชำระ<div>(บาท)</div></th>
                        <th>ค่ารักษา<div>มิเตอร์(บาท)</div></th>
                        <th>vat 7%<div>(บาท)</div></th>
                        <th>ต้องชำระ<div>ทั้งสิ้น(บาท)</div></th>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;สถานะ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 0;
                    @endphp
                    <form action="{{ route('usermeter_infos.store_edited_invoice') }}" method="post">
                        <input type="submit" class="btn btn-info" value="บันทึกข้อมูล">
                        @csrf
                        @foreach ($usermeter_infos->invoice as $invoice)
                            <tr>
                                <td>
                                    {{ $invoice->invoice_period->inv_p_name }}
                                    <input type="hidden" name="data[{{$i}}][inv_id]" value="{{ $invoice->inv_id }}">
                                    <input type="hidden" name="data[{{$i}}][acc_trans_id_fk]" value="{{ $invoice->acc_trans_id_fk }}">

                                </td>
                                <td>
                                    <input type="text" name="data[{{$i}}][lastmeter]" class="form-control" value="{{ $invoice->lastmeter }}">
                                </td>
                                <td>
                                    <input type="text" name="data[{{$i}}][currentmeter]" class="form-control" value="{{ $invoice->currentmeter }}">
                                </td>
                                <td>
                                    <input type="text" name="data[{{$i}}][water_used]" class="form-control" value="{{ $invoice->water_used }}">
                                </td>
                                <td>
                                    <input type="text" name="data[{{$i}}][paid]" class="form-control" value="{{ $invoice->paid }}">

                                </td>
                                <td>
                                    <input type="text" name="data[{{$i}}][reserve_meter]" class="form-control" value="{{ $invoice->reserve_meter }}">
                                </td>
                                <td>
                                    <input type="text" name="data[{{$i}}][vat]" class="form-control" value="{{ $invoice->vat }}">

                                </td>
                                <td>
                                    <input type="text" name="data[{{$i}}][totalpaid]" class="form-control" value="{{ $invoice->totalpaid }}">

                                </td>
                                <td>
                                    <select name="data[{{$i}}][status]" id="satus" class="form-control">
                                        <option value="" {{ $invoice->status == 'init' ? 'selected' : "" }}>เริ่มต้น</option>
                                        <option value="" {{ $invoice->status == 'invoice' ? 'selected' : "" }}>บันทึกข้อมูลแล้ว</option>
                                        <option value="" {{ $invoice->status == 'paid' ? 'selected' : "" }}>ชำระแล้ว</option>
                                        <option value="" {{ $invoice->status == 'deleted' ? 'selected' : "" }}>ลบ</option>
                                    
                                    </select>

                                </td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                        @endforeach
                    </form>
                </tbody>
            </table>
        </div>
    </div>
@endsection