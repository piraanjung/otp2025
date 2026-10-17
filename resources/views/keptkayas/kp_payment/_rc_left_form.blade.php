<table class="a" style="width: 100%">
    <tbody>
        <tr>
            <td class="text-center bg-primary" style="width: 10%">
                <img src="{{asset('/logo/hs_logo.jpg')}}" width="70">
            </td>
            <td class="org bg-primary">
                <div>องค์การบริหารส่วนตำบลห้องแซง</div>
                <div>22 หมู่ 12 ต.ห้องแซง</div>
                <div>อ.เลิงนกทา จ.ยโสธร 35120</div>
            </td>
            <td class="text-right">
                <div class="reciept_topic">ใบเสร็จรับเงิน (ต้นขั้ว)</div>
                <div class="reciept_topic">การจัดเก็บขยะรายปี</div>
                <div style="border-top: 1px solid blue"> เลขที่  <span style="font-size: 1.1rem; font-weight: bold;" class="text-danger pl-3"> 00001</span></div>
                
            </td>
        </tr>
    </tbody>
</table>

<table class="mt-1" style="width: 100%">
    <tbody>
        <tr>
            <td colspan="2" class="p-1">
                <div><span class="username">ชื่อสมาชิก</span>  นายพิพัฒน์พงษ์ ห้องแซง</div>
                <div class="pl-5 ml-3">294 หมู่ 12 ต.ห้องแซง อ.เลิงนกทา จ.ยโสธร 35120</div>
            </td>
           
        </tr>
        <tr>
            <td>
                <span class="username">เลขสมาชิก</span> <span class="ml-5">{{explode('-',$invoces_model[0]->kp_bins->bincode)[0]}}</span>
            </td>
            <td>
                <span class="username"> รหัสถังขยะ</span> <span class="ml-5"> {{$invoces_model[0]->kp_bins->bincode}}</span>
               
            </td>
        </tr>
    </tbody>
</table>
<?php 
    $current_invoice_num = $invoces_model[0]->kp_bins->next_invoice_num - 1;
    $paid_count = 0; $sumpaid =0; $sumvat= 0; $sumtotalpaid = 0; 
?>

<table class="aa mt-1" style="width: 100%">
<thead>
    <tr>
        <th colspan="2"><span class="username">ข้อมูลการจัดเก็บขยะรายปี</span></th>
    </tr>
</thead>
<tr>
    <td>
        <div class="row">
            @foreach ($invoces_model as $invoice)
            <div class="col-6">
                <ul class="list-group list-group-unbordered mb-3" >
                    <li class="list-group-item" style="border-bottom-color: white; ">
                      <b>
                        
                     <?php 
                        $badge =  substr($invoice->inv_no,-1) == $current_invoice_num ? 'info' : 'secondary';
                        if($invoice->status == 'init'){
                            $badge = 'warning';
                        }
                        echo '<span class="badge badge-'.$badge.'">'.$invoice->periods->period_name."</span> - "?>   
                     {{$invoice->status == 'be_tbank' ? 'ธนาคารขยะ' : 'ค่าถังขยะ'}}
                    </b> <a class="float-right">
                        @if ($invoice->status != 'init')
                            <span style="font-size: 0.6rem">เลขใบแจ้งหนี้:</span> <span class="text-{{$badge}}"> {{$invoice->inv_no}}</span>
                        @endif
                    </a>
                    </li>
                    <li class="list-group-item" >
                      <b>
                        @php
                        if($invoice->status == 'paid'){
                            if(substr($invoice->inv_no,-1) == $current_invoice_num){
                                echo '<i class="fa fa-check-circle text-'.$badge.'"></i> <span class="text-'.$badge.'"> ชำระแล้ว</span> ';
                                $paid_count++;
                                $sumpaid += $invoice->paid; 
                                $sumvat += $invoice->vat; 
                                $sumtotalpaid += $invoice->totalpaid; 
                            }else{
                                echo '<i class="fa fa-check-circle text-secondary"></i>   ชำระแล้ว ';
                            }
                            echo " ( ".$invoice->totalpaid.'บาท )';
                        }else if($invoice->status == 'be_tbank'){
                            echo '<i class="fa fa-minus-circle text-secondary"></i>  ยกเว้น ';
                        }else if($invoice->status == 'init'){
                            echo '<i class="fas fa-stop-circle text-warning"></i> <span class="text-'.$badge.'"> ค้างชำระ</span> '; 
                        }
                       
                        @endphp     
                    </b> <a class="float-right text-{{$badge}}">
                        @if ($invoice->status != 'init')
                            <span style="font-size: 0.6rem">วันที่ชำระเงิน:</span> 12/08/2568</a>
                        @endif
                       
                    </li>
                 
                </ul>
               
            </div>
   
@endforeach
</div>
</td>
</tr>


</table>
<table style="width: 100%" class="mt-1">
    <tbody>
        <tr>
            <td class="p-3 border-l-t-b-none" width="50%">
               
                <div>กองงานสิ่งแวดล้อม</div>
                <div>องค์การบริหารส่วนตำบลห้องแซง</div>
                <div>โทร. 0451232322</div>
            </td>
            <td class="p-1" width="50%">
                <ul class="list-group list-group-unbordered mb-3" >
                    <li class="list-group-item">
                      <b>จำนวนเดือนที่ชำระ</b> 
                        <a class="float-right">
                            {{$paid_count}} เดือน
                        </a>
                    </li>
                    <li class="list-group-item">
                        <b>เป็นเงิน</b> 
                          <a class="float-right">
                            {{ number_format($sumpaid,2)}}  บาท
                          </a>
                      </li>
                      <li class="list-group-item">
                        <b>vat 7%</b> 
                          <a class="float-right">
                            {{ number_format($sumvat,2)}}  บาท
                          </a>
                      </li>
                      <li class="list-group-item">
                        <b>รวมทั้งสิ้น</b> 
                          <a class="float-right">
                              {{ number_format($sumtotalpaid,2)}}  บาท
                          </a>
                      </li>
                      <li class="list-group-item text-right">
                                หกสิบสี่บาทสี่สิบสตางค์ 
                      </li>
                    
                </ul>
            </td>
        </tr>
    </tbody>
</table>
<table style="width: 100%" class="mt-1" border="0">
    <tbody>
        <tr>
            <td width="50%" class="text-center text-md td-border-none">
                <div><img src="{{asset('/sign/sign.png')}}" alt="" width="80" height="25"></div>
                <div class="text-center" style="font-size: 0.8rem;">(นายพิพัฒน์พงษ์ ห้องแซง)</div>
                <div class="text-center" style="font-size: 0.8rem">ผู้รับเงิน</div>
            </td>
            <td width="50%" class="text-center text-md td-border-none">
                <div><img src="{{asset('/sign/sign.png')}}" alt="" width="80" height="25"></div>
                <div class="text-center" style="font-size: 0.8rem;">(นายพิพัฒน์พงษ์ ห้องแซง)</div>
                <div class="text-center" style="font-size: 0.8rem">หัวหน้ากองคลัง</div>
            </td>
        </tr>
    </tbody>
</table>

