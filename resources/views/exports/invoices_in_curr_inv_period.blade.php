
{{-- @dd($umfs) --}}
<table class="table">
    <thead>
        <tr>
            <th colspan="9" style="text-align: center; font-size: 12px;">
                {{-- ตารางจดน้ำประปา ขามป้อม {{$umfs[0]->undertake_zone->zone_name}}  --}}
                ประจำปีงบประมาณ  2568
            </th>
        </tr>
        <tr>
            <th colspan="9" style="text-align: center; font-size: 12px;">
                ประจำเดือน  {{ $umfs[0]->invoice_currrent_inv_period[0]->invoice_period->inv_p_name }} 
                  &nbsp;&nbsp;&nbsp;&nbsp;  ( 
                    {{ $umfs[0]->undertake_subzone->undertaker_subzone->twman_info->prefix.$umfs[0]->undertake_subzone->undertaker_subzone->twman_info->firstname." ".$umfs[0]->undertake_subzone->undertaker_subzone->twman_info->lastname 
                }} ผู้จดมิเตอร์)
            </th>
        </tr>
       
        <tr>
            <th rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">#</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">ชื่อ-สกุล</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">บ้าน<br/>เลขที่</th>
            <th rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">หมู่ที่</th>
            <th rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">เส้นทาง</th>
            <th rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">รหัสผู้ใช้น้ำ</th>
            <th rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">รหัสสมาชิก</th>
            <th rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">เลขผู้ใช้น้ำ</th>
            
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">เลขจาก<br/>โรงงาน</th>
            <th  colspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">เลขอ่านของมาตรวัดน้ำ</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">จำนวน<br/>หน่วย</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">อัตรา<br/>ค่าน้ำ</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">คิด<br/>เป็นเงิน</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">ค่าธรรม<br/>เนียม</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">รวมเป็น<br/>เงินบาท</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">ใบเสร็จ<br/>เลขที่</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">หมายเหตุ</th>
            
        </tr>
        <tr>
            <th style="border: 1px solid white;background-color: black;color:white; text-align: center;">จาก</th>
            <th style="border: 1px solid white;background-color: black;color:white; text-align: center;">ถึง</th>
        </tr>
    </thead>
    <tbody>
        @php
            $i = 1;
        @endphp
         @php
            $sum_water_used =0; $sum_paid = 0; $sum_totalpaid = 0;
        @endphp
        @foreach ($umfs as $umf)
        @if (collect($umf->invoice_currrent_inv_period)->isEmpty())
            @dd($umf)
        @endif
            @php
                 $sum_water_used +=$umf->invoice_currrent_inv_period[0]->water_used; 
                 $sum_paid += $umf->invoice_currrent_inv_period[0]->paid; 
                 $sum_totalpaid += $umf->invoice_currrent_inv_period[0]->totalpaid;
            @endphp
        <tr>
            <td style="border: 1px solid black;">{{ $i++ }}</td>
             <td style="border: 1px solid black;">
                {{ $umf->user->prefix."".$umf->user->firstname." ".$umf->user->lastname }}
                {{ $umf->submeter_name }}
            </td>
            <td style="border: 1px solid black;text-align: right;">{{ $umf->meter_address}}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $umf->undertake_zone->zone_name}}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $umf->undertake_subzone->subzone_name}}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $umf->meter_id}}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $umf->user_id}}</td>
            <td style="border: 1px solid black;text-align: center;">{{ $umf->meternumber}}</td>
           
            
            <td style="border: 1px solid black; text-align: right;width:60px">{{ $umf->factory_no }}</td>
            <td style="border: 1px solid black; font-weight: bold;width:80px; text-align: right;">{{ $umf->invoice_currrent_inv_period[0]->lastmeter }}</td>
            <td style="border: 1px solid black;width:80px; text-align: right;">{{ $umf->invoice_currrent_inv_period[0]->currentmeter }}</td>
            <td style="border: 1px solid black; color:black;  text-align: right;">{{$umf->invoice_currrent_inv_period[0]->water_used}}</td>
            <td style="border: 1px solid black; color:black;  text-align: right;">6.00</td>
            <td style="border: 1px solid black; color:black;  text-align: right;">{{$umf->invoice_currrent_inv_period[0]->paid}}</td>
            <td style="border: 1px solid black; color:black;  text-align: right;">10.00</td>
            <td style="border: 1px solid black; color:black;  text-align: right;">{{$umf->invoice_currrent_inv_period[0]->totalpaid}}</td>
            <td style="border: 1px solid black; color:black;  text-align: center;">{{$umf->invoice_currrent_inv_period[0]->inv_no}}</td>
            <td style="border: 1px solid black; color:black; text-align: center;"></td>
        </tr>
        @endforeach
        <tr>
            <th colspan="9" style="text-align: center">รวม</th>
            <th>{{ number_format($sum_water_used,2) }}</th>
            <th></th>
            <th style="text-align: right">{{number_format($sum_paid,2)}}</th>
            <th style="text-align: right">{{number_format(collect($umfs)->count()*10,2)}}</th>
            <th style="text-align: right">{{number_format($sum_totalpaid,2)}}</th>
            <td style="text-align: right"></td>
            <td style="text-align: right"></td>
    </tbody>
    <tfoot>
        <tr>
            <th rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">#</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">ชื่อ-สกุล</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">บ้าน<br/>เลขที่</th>
            <th rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">หมู่ที่</th>
            <th rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">เส้นทาง</th>
            <th rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">เลขผู้ใช้น้ำ</th>
            
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">เลขจาก<br/>โรงงาน</th>
            <th  colspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">เลขอ่านของมาตรวัดน้ำ</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">จำนวน<br/>หน่วย</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">อัตรา<br/>ค่าน้ำ</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">คิด<br/>เป็นเงิน</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">ค่าธรรม<br/>เนียม</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">รวมเป็น<br/>เงินบาท</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">ใบเสร็จ<br/>เลขที่</th>
            <th  rowspan="2" style="border: 1px solid white;background-color: black;color:white; text-align: center;">หมายเหตุ</th>
            
        </tr>
         <tr>
            <th style="border: 1px solid white;background-color: black;color:white; text-align: center;">จาก</th>
            <th style="border: 1px solid white;background-color: black;color:white; text-align: center;">ถึง</th>
        </tr>
    </tfoot>
</table>


    
