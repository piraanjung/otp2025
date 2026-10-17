<?php
use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\SubzoneController;
$fnc = new FunctionsController();
$zoneApi = new ZoneController();
$subzoneApi = new SubzoneController();
?>

<style>
    .textbetweenKrut{
        padding-top:60pt !important
    }
    .tesabanAddr{
        padding-left: 60pt
    }
    .date{
        padding-left: 45pt
    }
</style>
<?php
$headText = '';
//  dd($cutmeteriInfos['cutmeter_status']);
if ($cutmeteriInfos['cutmeter_status'] == 'cutmeter'){
    $headText = 'ตัดมิเตอร์น้ำ';
}else {
    $headText = 'ดำเนินการติดตั้งมิเตอร์น้ำ';
}
?>

<table class="table mt-4" style="width:220mm; margin-left:5rem">
    <tr>
        <td style="padding-left: 4rem;padding-right: 1rem;">
            <div class="row">
                    <div class="col-12 h4 text-center">ใบงาน <?php echo $headText; ?></div>
            </div>

            <div class="row mt-2">
                <div class="col-5"></div>
                <div class="col-4 date">
                    <?php
                        $currentYear = date('Y') + 543;
                        $monthThai =  $fnc->fullThaiMonth(date('m'));
                    ?>
                    วันที่ {{date('d')." ".$monthThai." ".$currentYear}}
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-12">
                    <div style="letter-spacing: 1.2px;">
                        เลขที่ผู้ใช้น้ำ
                        <b>{{$user[0]['user_meter_infos']['meternumber']}}</b>
                    </div>ในนามของ
                    <b>{{$user[0]['name']}}</b>
                    บ้านเลขที่
                    {{$user[0]['address']}}
                    <?php
                        $zone = json_decode($zoneApi->getZone($user[0]['zone_id'])->content(), true);
                        $subzone = json_decode($subzoneApi->subzone($user[0]['subzone_id'])->content(), true);
                        echo " ".$zone[0]['zone_name']." เส้นทางจดมิเตอร์ ". $subzone[0]['subzone_name']." ";
                    ?>
                   @if ($cutmeteriInfos['cutmeter_status'] == 'cutmeter')
                        ซึ่งมีหนี้ค้างชำระค่าน้ำประปาเกิน 3 รอบบิล ดังนี้
                   @else
                        ได้ทำการชำระค่าน้ำประปาเรียบร้อยแล้ว <br>เจ้าหน้าที่จึงได้ทำการติดตั้งมิเตอร์น้ำ ให้กับผู้ใช้น้ำเรียบร้อยแล้ว
                   @endif
                </div>
                    @if ($cutmeteriInfos['cutmeter_status'] == 'cutmeter')

                    <div class="col-12">
                        <table class="table mt-1 text-center table-bordered">
                            <tr>
                                <th class="waterUsedHisHead td_history text-center">เลขใบแจ้งหนี้</th>
                                <th class="waterUsedHisHead td_history text-center">ประจำเดือน</th>
                                <th class="waterUsedHisHead td_history text-center">สถานะ</th>
                                <th class="waterUsedHisHead td_history text-center">หลังจด</th>
                                <th class="waterUsedHisHead td_history text-center">ก่อนจด</th>
                                <th class="waterUsedHisHead td_history text-center">ใช้น้ำ(หน่วย)</th>
                                <th class="text-center td_history">รวม (บาท)</th>
                            </tr>
                            <tr>
                                <?php $oweSum = 0;  ?>
                                <?php $count_history = 0; ?>

                                @foreach ($invoiceOweAndIvoiceStatus as $history)
                                    <tr>
                                        <?php
                                            $status = $history['status'] == 'owe' ? 'ค้างชำระ' : 'ออกใบแจ้งหนี้';
                                            if($history['status'] == 'paid'){
                                                $status = "ชำระแล้ว";
                                            }
                                        ?>
                                        <td>{{ $history['id'] }}</td>
                                        <td>{{ $history['invoice_period']['inv_period_name'] }}</td>
                                        <td>{{ $status }}</td>
                                        <td>{{ $history['currentmeter'] }}</td>
                                        <td>{{ $history['lastmeter'] }}</td>
                                        <?php
                                            $history_diff = $history['currentmeter'] - $history['lastmeter'];
                                            $history_diff_plus = $history_diff == 0 ? 10 : $history_diff*8;
                                            $history_status = $history['status'] =='owe' ? '(ค้าง)' : '';
                                            $oweSum += $history_diff * 8;
                                            if($history_diff == 0){
                                                $oweSum += 10;
                                                $history_diff_plus = 10;
                                            }
                                        ?>
                                        <td>{{ $history_diff }}</td>
                                        <td class="text-center td_history">
                                            {{ $history_diff_plus }}
                                        </td>
                                    </tr>
                                @endforeach
                            <tr>
                                <td colspan="6" class="h5">รวมทั้งสิ้น (บาท)</td>
                                <td class="text-center td_history">
                                    <?php
                                        echo number_format($oweSum,2);
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    @endif
                </div>
        </td>
    </tr>
    <tr>
        <td class="text-center" style="border-top:none">
            {{-- วันที่  {{ $cutmeteriInfos[0]['operate_date']}}  เวลา {{ $cutmeteriInfos[0]['operate_time']}}
             ได้ดำเนินการ {{ $headText }} --}}
        </td>

    </tr>
    <tr>
        <td>
            <table border="0">
                <tr>
                    <th style="border-top:none">ผู้รับผิดชอบ {{$headText}}</th>

                </tr>
                <tr>
                    {{-- @foreach ($cutmeteriInfos[0]['twmans'] as $item)
                        <td style="border-top:none" class="text-center">
                            <div>______________________</div>
                            <div><h6 class="mt-2"> {{$item['name']}}</h6></div>
                            <div>ผู้รับผิดชอบ {{$headText}}</div>
                        </td>
                    @endforeach --}}
                 <td style="border-top:none" class="text-center">
                    {{-- <div>______________________</div>
                    <div><h6 class="mt-2">{{$recorder[0]->name}}</h6></div>
                    <div>ผู้บันทึกข้อมูล </div> --}}

                </td>
                 <td style="border-top:none" class="text-center">
                    {{-- <div>______________________</div>
                    <div><h6 class="mt-2">{{ $headTwman[0]->name }}</h6></div>
                    <div>หัวหน้างานประปา </div> --}}

                </td>
                </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td class="text-center mt-3" style="width: 100%; border:1px solid black">
            @if ($status == 'complete')
                <h3>ชำระเงินแล้ว</h3>
            @else
                <h3>รอการชำระเงิน</h3>

            @endif
        </td>

    </tr>

</table>
