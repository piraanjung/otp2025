
            <table class="table" id="example">

                <thead>
                    <tr>
                        <th colspan="18" style="text-align: center">ตารางรายงานการรับชำระค่าน้ำประจำวันที่ {{$fromdateThTopic}} - {{$todateThTopic}}</th>
                    </tr>
                    <tr>
                        <th colspan="3">
                              ปีงบประมาณ : {{ $request_selected['budgeryear'][0] }}
                        </th>
                        <th colspan="3">
                             รอบบิลที่ : {{ $request_selected['inv_period'][0] }}
                        </th>
                        <th colspan="3">
                             หมู่ที่  : {{ $request_selected['zone'][0] }}
                        </th>
                        <th colspan="3">
                            เส้นทางจด : {{ $request_selected['subzone'][0] }}
                        </th>
                        <th colspan="3">
                             ผู้รับเงิน : {{ $request_selected['cashier'][0]['firstname']." ".$request_selected['cashier'][0]['lastname'] }}
                        </th>
                    </tr>
                    <tr><th colspan="18"></th></tr>
                    <tr>
                        <th style="background-color: #07e2f6; border:1px solid black">#</th>
                        <th style="background-color: #07e2f6; border:1px solid black">เลขผู้ใช้น้ำ</th>
                        <th style="background-color: #07e2f6; border:1px solid black">ชื่อ-สกุล</th>
                        <th style="background-color: #07e2f6; border:1px solid black">บ้านเลขที่</th>
                        <th style="background-color: #07e2f6; border:1px solid black">หมู่ที่</th>
                        <th style="background-color: #07e2f6; border:1px solid black">เส้นทางจดมิเตอร์</th>
                        <th style="background-color: #07e2f6; border:1px solid black">ผู้รับเงิน</th>
                        <th style="background-color: #07e2f6; border:1px solid black">วันที่รับเงิน</th>
                        <th style="background-color: #07e2f6; border:1px solid black">เลขใบแจ้งหนี้</th>
                        <th style="background-color: #07e2f6; border:1px solid black">รอบบิล</th>
                        <th style="background-color: #07e2f6; border:1px solid black">ก่อนจด<div><sup>หน่วย</sup></div>
                        </th>
                        <th style="background-color: #07e2f6; border:1px solid black">หลังจด <div><sup>หน่วย</sup></div>
                        </th>
                        <th style="background-color: #07e2f6; border:1px solid black">ใช้น้ำ <div><sup>หน่วย</sup></div>
                        </th>
                        <th style="background-color: #07e2f6; border:1px solid black">เป็นเงิน <div><sup>บาท</sup></div>
                        </th>
                        <th style="background-color: #07e2f6; border:1px solid black">รักษามิเตอร์ <div><sup>บาท</sup></div>
                        </th>
                        <th style="background-color: #07e2f6; border:1px solid black">Vat 7% <div><sup>บาท</sup></div>
                        </th>
                        <th style="background-color: #07e2f6; border:1px solid black">รวมเป็นเงิน <div><sup>บาท</sup></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; $water_used =0; $paid = 0; $vat = 0; $reserve = 0; $totalpaid = 0; ?>
                    @foreach ($paidInfos as $key => $infos)
                        <?php $firstRow = 1; ?>
                        @foreach ($infos as $owe)

                            <tr>
                                @if ($firstRow == 1)
                                    <td>{{ $i }}</td>
                                    <td class="text-right">{{ $owe->usermeterinfos->meternumber }}</td>

                                    <td class="text-start">
                                        {{ $owe->usermeterinfos->user->prefix . '' . $owe->usermeterinfos->user->firstname . ' ' . $owe->usermeterinfos->user->lastname }}
                                    </td>
                                    <td class="text-right">{{ $owe->usermeterinfos->user->address }}</td>
                                    <td class="text-right">
                                        {{ $owe->usermeterinfos->user->user_zone->zone_name }}</td>
                                    <td class="text-right">
                                        {{ collect($owe->usermeterinfos->user->user_subzone)->isEmpty() ? '-' : $owe->usermeterinfos->user->user_subzone->subzone_name }}
                                    </td>

                                    <td class="text-end">
                                        {{ $owe->acc_transactions->cashier_info->prefix . '' . $owe->acc_transactions->cashier_info->firstname . ' ' . $owe->acc_transactions->cashier_info->lastname }}
                                    </td>

                                    <td class="text-center">{{ $owe->updated_at }}</td>
                                    <?php $firstRow = 0; ?>
                                @else
                                    <td class="info_blur">{{ $i }}</td>

                                    <td class="info_blur text-right">
                                        {{ $owe->usermeterinfos->meternumber }}</td>

                                    <td class="info_blur text-start">
                                        {{ $owe->usermeterinfos->user->prefix . '' . $owe->usermeterinfos->user->firstname . ' ' . $owe->usermeterinfos->user->lastname }}
                                    </td>
                                    <td class="info_blur text-end">
                                        {{ $owe->usermeterinfos->user->address }}</td>
                                    <td class="info_blur text-center">
                                        {{ $owe->usermeterinfos->user->user_zone->zone_name }}</td>
                                    <td class="info_blur text-center">
                                        {{ collect($owe->usermeterinfos->user->user_subzone)->isEmpty() ? '-' : $owe->usermeterinfos->user->user_subzone->subzone_name }}
                                    </td>
                                    <td class="info_blur text-end">

                                        {{ $owe->acc_transactions->cashier_info->prefix . '' . $owe->acc_transactions->cashier_info->firstname . ' ' . $owe->acc_transactions->cashier_info->lastname }}
                                    </td>
                                    <td class="info_blur text-center">{{ $owe->updated_at }}</td>
                                @endif
                                <td class="text-end">{{ $owe->inv_id }}</td>
                                <td class="text-end">{{ $owe->invoice_period->inv_p_name }}</td>
                                <td class="text-end">{{ $owe->lastmeter }}</td>
                                <td class="text-end">{{ $owe->currentmeter }}</td>
                                <td class="text-end">{{ $owe->water_used }}</td>
                                <td class="text-end">{{ $owe->water_used == 0 ? 0 : $owe->paid }}</td>
                                <td class="text-end">{{ $owe->water_used == 0 ? 10 : 0 }}</td>
                                <td class="text-end">{{ $owe->vat }}</td>
                                <td class="text-end">{{ $owe->totalpaid }}</td>

                                @php
                                    $water_used += $owe->water_used;
                                    $paid += $owe->water_used == 0 ? 0 : $owe->paid;
                                    $reserve += $owe->water_used == 0 ? 10 : 0;
                                    $vat += $owe->vat;
                                    $totalpaid += $owe->totalpaid;
                                @endphp
                            </tr>
                        @endforeach
                        <?php $i++; ?>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="12"></th>
                        <th style="background-color: #07e2f6; border:1px solid black">{{$water_used}}</th>
                        <th style="background-color: #07e2f6; border:1px solid black">{{$paid}}</th>
                        <th style="background-color: #07e2f6; border:1px solid black">{{$reserve}}</th>
                        <th style="background-color: #07e2f6; border:1px solid black">{{$vat}}</th>
                        <th style="background-color: #07e2f6; border:1px solid black">{{$totalpaid}}</th>
                    </tr>
                </tfoot>
            </table>
