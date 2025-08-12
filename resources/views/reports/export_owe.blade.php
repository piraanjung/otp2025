
                <table class="table" id="example">
                    <thead>
                        <tr>
                            <td colspan="12" style="text-align: center; background-color:aquamarine">
                                รายงานผู้ค้างชำระค่าน้ำประปา       ปีงบประมาณ 
                                @php
                                  echo App\Models\BudgetYear::where('id', $budgetyears_selected)->get('budgetyear_name')[0]->budgetyear_name;
                                @endphp
                               
                            </td>
                        </tr>
                        <tr>
                            <td colspan="12" style="text-align: center; background-color:aquamarine">
                                รอบบิล
                                @php
                                if ($selected_inv_periods[0] == "all"){
                                    echo "ทั้งหมด";
                                }else{
                                    foreach($selected_inv_periods as $invp){
                                        echo $invp->inv_p_name.", ";
                                    }
                                }
                               
                                @endphp
                               
                            </td>
                        </tr>
                        <tr>
                            <td colspan="12" style="text-align: center; background-color:aquamarine">
                                
                                @if ($zone_selected[0] == "all")
                                  @php
                                  $zone_selected =  App\Models\Zone::get('zone_name');
                                  foreach ($zone_selected as $zone){
                                        echo $zone->zone_name.", ";
                                  }
                                  $zone_selected=['all'];
                                  @endphp  
                                @else
                                    @foreach ($zone_selected as $zone)
                                        @php
                                        $zone =App\Models\Zone::where('id', $zone)->get('zone_name');
                                        echo $zone[0]->zone_name.", ";
                                        @endphp
                                    @endforeach
                                @endif
                               
                            </td>
                        </tr>
                        <tr>
                            <td colspan="12"></td>
                        </tr>
                        <tr>
                            <td>#</td>
                            <td>เลขมิเตอร์</td>
                            <td>ชื่อ-สกุล</td>
                            <td>บ้านเลขที่</td>
                            <td>หมู่ที่</td>
                            <td>ซอย</td>
                            <td>เส้นทางจดมิเตอร์</td>
                            <td>ค้างชำระ(รอบบิล) </td>
                            <td>เป็นเงิน (บาท) </td>
                            <td>Vat 7%(บาท)</td>
                            <td>ค่ารักษามิเตอร์(บาท)</td>
                            <td>รวมเป็นเงิน(บาท)</td>
                            @if ($show_details)

                            <td style="border:1px solid black; background-color: yellow; color:blue">เลขใบแจ้งหนี้</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">รอบบิล</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">มิเตอร์ก่อนจด</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">มิเตอร์หลังจด</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">ประเภท</td>
                         

                            <td style="border:1px solid black; background-color: yellow; color:blue">ใช้น้ำจำนวน(หน่วย)</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">เป็นเงิน(บาท)</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">vat(บาท)</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">ค่ารักษามิเตอร์(บาท)</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">รวมทั้งสิ้น(บาท)</td>
                            @endif

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $name_first_row = true;

                        $index_row = 0;
                        ?>
                        {{-- @foreach ($owes as $owe) --}}
                        <?php $a = 0;  $sumwater_used=0; $sumpaid=0; $sumvat= 0; $sumreserve_meter=0; $sumtotalpaid = 0;?>
                         @foreach ($owes as $owe)
                         @php
                             $userInfos = App\Models\UserMerterInfo::with('user')
                            ->where('meter_id', $owe['meter_id_fk'])->get();
                            
                            $sumpaid += str_replace( ',', '', $owe['paid'] ) ;;
                            $sumvat  += $owe['vat'];
                            $sumreserve_meter += $owe['reserve_meter'];
                            $sumtotalpaid +=  str_replace( ',', '', $owe['totalpaid'] ) ;
                         @endphp
                            @if (!$show_details)
                                <tr>
                                    <td style="border:1px solid blue;">{{ ++$index_row }}</td>
                                    <td style="border:1px solid blue;">{{ $owe['meter_id_fk'] }}</td>
                                    <td style="border:1px solid blue;" >
                                       {{ $userInfos[0]->user->prefix."".$userInfos[0]->user->firstname.
                                        " ".$userInfos[0]->user->lastname}}
                    
                                    </td>
                
                                    <td style="border:1px solid blue;">{{ $userInfos[0]->user->address }}</td>
                                    <td style="border:1px solid blue;">{{ $userInfos[0]->user->user_zone->zone_name }}</td>
                                    <td style="border:1px solid blue;">{{ $userInfos[0]->undertake_subzone_id == 13 ? 'เส้นหมู่13' :  $userInfos[0]->undertake_subzone->subzone_name }}</td>
                                    <td style="border:1px solid blue;">{{ $userInfos[0]->undertake_subzone_id == 13 ? 'เส้นหมู่13' :  $userInfos[0]->undertake_subzone->subzone_name }}</td>
                                
                                
                                    <td style="border:1px solid blue;" class="text-end">{{ $owe['owe_count'] }}</td>
                                    <td style="border:1px solid blue;" class="text-end">{{ $owe['paid'] }}</td>
                                    <td style="border:1px solid blue;" class="text-end">{{ $owe['vat'] }}</td>
                                    <td style="border:1px solid blue;" class="text-end">{{ $owe['reserve_meter'] }}</td>
                                    <td style="border:1px solid blue;" class="text-end">{{ str_replace( ',', '', $owe['totalpaid'] )  }}</td>
                                </tr>
                            @else
                                {{-- $show_details == true --}}
                                @foreach ($owe['owe_infos'] as $infos)
                                    
                                    <?php $rowspanNum =  $show_details == true ? collect($owe['owe_infos'])->count() : 1  ; ?>

                                    <tr>
                                        @if ($a++ == 0)
                                        <td style="border:1px solid blue;" rowspan="{{$rowspanNum}}">{{ ++$index_row }}</td>
                                        <td style="border:1px solid blue;" rowspan="{{$rowspanNum}}">{{ $owe['meter_id_fk'] }}</td>
                                        <td style="border:1px solid blue;" rowspan="{{$rowspanNum}}" >
                                            {{ $userInfos[0]->user->prefix."".$userInfos[0]->user->firstname.
                                        " ".$userInfos[0]->user->lastname}}
                                        </td>
                    
                                        <td style="border:1px solid blue;" rowspan="{{$rowspanNum}}">{{  $userInfos[0]->user->address }}</td>
                                        <td style="border:1px solid blue;" rowspan="{{$rowspanNum}}">{{ $userInfos[0]->user->user_zone->zone_name }}</td>
                                        <td style="border:1px solid blue;" rowspan="{{$rowspanNum}}">{{  $userInfos[0]->undertake_subzone_id == 13 ? 'เส้นหมู่13' :  $userInfos[0]->undertake_subzone->subzone_name }}</td>
                                        <td style="border:1px solid blue;" rowspan="{{$rowspanNum}}">{{  $userInfos[0]->undertake_subzone_id == 13 ? 'เส้นหมู่13' :  $userInfos[0]->undertake_subzone->subzone_name }}</td>
                                    
                                    
                                        <td style="border:1px solid blue;" rowspan="{{$rowspanNum}}" class="text-end">{{ $owe['owe_count'] }}</td>
                                        <td style="border:1px solid blue;" rowspan="{{$rowspanNum}}" class="text-end">{{ str_replace( ',', '', $owe['paid'] )  }}</td>
                                        <td style="border:1px solid blue;" rowspan="{{$rowspanNum}}" class="text-end">{{ $owe['vat'] }}</td>
                                        <td style="border:1px solid blue;" rowspan="{{$rowspanNum}}" class="text-end">{{ $owe['reserve_meter'] }}</td>
                                        <td style="border:1px solid blue;" rowspan="{{$rowspanNum}}" class="text-end">{{ str_replace( ',', '', $owe['totalpaid'] )  }}</td>
                                        @endif
                                        @if ($show_details)
                                        <td style="border:1px solid black; background-color: yellow;">{{$infos->inv_id}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$infos->invoice_period->inv_p_name}}</td>

                                        <td style="border:1px solid black; background-color: yellow;">{{$infos->lastmeter}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$infos->currentmeter}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$infos->inv_type == 'r' ? 'รักษามิเตอร์' : 'มีการใช้น้ำ'}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$infos->water_used}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{str_replace( ',', '', $infos->paid )}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$infos->vat}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$infos->reserve_meter}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{str_replace( ',', '', $infos->totalpaid )}}</td>

                                        @endif

                                    </tr>
                                @endforeach

                                @php
                                $a=0;
                                @endphp
                            @endif
                        @endforeach
                        <tr>
                            <th style="border:1px solid black; background-color:aqua;  color: black; text-align: center;" colspan="8">ผลรวม</th>
                            <th style="border:1px solid black; background-color:aqua;  color: black;">{{ $sumpaid }}</th>
                            <th style="border:1px solid black; background-color:aqua;  color: black;">{{ $sumvat }}</th>
                            <th style="border:1px solid black; background-color:aqua;  color: black;">{{ $sumreserve_meter }}</th>
                            <th style="border:1px solid black; background-color:black;  color:white;">{{ $sumtotalpaid }}</th>
                        </tr>

                    </tbody>
                </table>
