

                {{-- <table class="table" id="example">
                    <thead>
                        <tr>
                            <td colspan="11" style="text-align: center; background-color:aquamarine">
                                รายงานผู้ค้างชำระค่าน้ำประปา
                            </td>
                        </tr>
                        <tr>
                            <td colspan="11"></td>
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
                            <td>รวมเป็นเงิน(บาท)</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $name_first_row = true;

                        $index_row = 0;
                        ?>
                        @foreach ($owes as $owe)
                            <tr>
                                <td style="border:1px solid blue; color:white">{{ ++$index_row }}</td>
                                <td style="border:1px solid blue; color:white">{{ $owe['meter_id_fk'] }}</td>
                                <td style="border:1px solid blue; color:white">{{ $owe['name'] }}</td>
                                <td style="border:1px solid blue; color:white">{{ $owe['address'] }}</td>
                                <td style="border:1px solid blue; color:white">{{ $owe['zone'] }}</td>
                                <td style="border:1px solid blue; color:white">{{ $owe['subzone'] }}</td>
                                <td style="border:1px solid blue; color:white">{{ $owe['undertake_subzone'] }}</td>
                                <td style="border:1px solid blue; color:white"  class="text-end">{{ $owe['owe_count'] }}</td>
                                <td style="border:1px solid blue; color:white"  class="text-end">{{ $owe['paid'] }}</td>
                                <td style="border:1px solid blue; color:white"  class="text-end">{{ $owe['vat'] }}</td>
                                <td style="border:1px solid blue; color:white"  class="text-end">{{ $owe['totalpaid'] }}</td>

                            </tr>
                            <!--   แสดงรายละเอียดแต่ละรอบบิล ถ้า  $show_details == 'details' -->
                            @if ($show_details == 'details')
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($owe['owe_infos'] as $item)

                                    @if ($i == 1)
                                    <tr>
                                        <td colspan="2"></td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">เลขใบแจ้งหนี้</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">รอบบิล</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">มิเตอร์ก่อนจด</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">มิเตอร์หลังจด</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">ประเภท</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">ใช้น้ำจำนวน(หน่วย)</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">เป็นเงิน(บาท)</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">vat(บาท)</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">รวมทั้งสิ้น(บาท)</td>

                                    </tr>
                                    @endif
                                    @php
                                        $i++;
                                    @endphp
                                    <tr>
                                        <td colspan="2"></td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->inv_id}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->invoice_period->inv_p_name}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->lastmeter}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->currentmeter}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->inv_type == 'r' ? 'รักษามิเตอร์' : 'มีการใช้น้ำ'}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->water_used}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->paid}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->vat}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->totalpaid}}</td>

                                    </tr>
                                @endforeach
                            @endif

                        @endforeach
                    </tbody>
                </table> --}}



                <table class="table" id="example">
                    <thead>
                        <tr>
                            <td colspan="11" style="text-align: center; background-color:aquamarine">
                                รายงานผู้ค้างชำระค่าน้ำประปา
                            </td>
                        </tr>
                        <tr>
                            <td colspan="11"></td>
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
                            <td>รวมเป็นเงิน(บาท)</td>

                            <td style="border:1px solid black; background-color: yellow; color:blue">เลขใบแจ้งหนี้</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">รอบบิล</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">มิเตอร์ก่อนจด</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">มิเตอร์หลังจด</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">ประเภท</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">ใช้น้ำจำนวน(หน่วย)</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">เป็นเงิน(บาท)</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">vat(บาท)</td>
                            <td style="border:1px solid black; background-color: yellow; color:blue">รวมทั้งสิ้น(บาท)</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $name_first_row = true;

                        $index_row = 0;
                       
                        ?>
                        @foreach ($owes as $owe)
                        <?php $a = 0; ?>
                        @foreach ($owe['owe_infos'] as $item)
                            <tr>
                               
                                <td style="border:1px solid blue;">{{ ++$index_row }}</td>
                                <td style="border:1px solid blue;">{{ $owe['meter_id_fk'] }}</td>
                                <td style="border:1px solid blue; {{$a==1 ?'opacity:1' : 'opacity:0.5'}}">{{ $owe['name'] }}</td>
                                <td style="border:1px solid blue;">{{ $owe['address'] }}</td>
                                <td style="border:1px solid blue;">{{ $owe['zone'] }}</td>
                                <td style="border:1px solid blue;">{{ $owe['subzone'] }}</td>
                                <td style="border:1px solid blue;">{{ $owe['undertake_subzone'] }}</td>
                               
                                @if ($a++ == 0)
                                <td style="border:1px solid blue;"rowspan="{{collect($owe['owe_infos'])->count()}}"  class="text-end">{{ $owe['owe_count'] }}</td>
                                <td style="border:1px solid blue;"rowspan="{{collect($owe['owe_infos'])->count()}}"  class="text-end">{{ $owe['paid'] }}</td>
                                <td style="border:1px solid blue;" rowspan="{{collect($owe['owe_infos'])->count()}}" class="text-end">{{ $owe['vat'] }}</td>
                                <td style="border:1px solid blue;" rowspan="{{collect($owe['owe_infos'])->count()}}" class="text-end">{{ $owe['totalpaid'] }}</td>
                                @endif

                                <td style="border:1px solid black; background-color: yellow;">{{$item->inv_id}}</td>
                                <td style="border:1px solid black; background-color: yellow;">{{$item->invoice_period->inv_p_name}}</td>
                                <td style="border:1px solid black; background-color: yellow;">{{$item->lastmeter}}</td>
                                <td style="border:1px solid black; background-color: yellow;">{{$item->currentmeter}}</td>
                                <td style="border:1px solid black; background-color: yellow;">{{$item->inv_type == 'r' ? 'รักษามิเตอร์' : 'มีการใช้น้ำ'}}</td>
                                <td style="border:1px solid black; background-color: yellow;">{{$item->water_used}}</td>
                                <td style="border:1px solid black; background-color: yellow;">{{$item->paid}}</td>
                                <td style="border:1px solid black; background-color: yellow;">{{$item->vat}}</td>
                                <td style="border:1px solid black; background-color: yellow;">{{$item->totalpaid}}</td>
                            </tr>
                            @endforeach

                            <!--   แสดงรายละเอียดแต่ละรอบบิล ถ้า  $show_details == 'details' -->
                            {{-- @if ($show_details == 'details')
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($owe['owe_infos'] as $item)

                                    @if ($i == 1)
                                    <tr>
                                        <td colspan="2"></td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">เลขใบแจ้งหนี้</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">รอบบิล</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">มิเตอร์ก่อนจด</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">มิเตอร์หลังจด</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">ประเภท</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">ใช้น้ำจำนวน(หน่วย)</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">เป็นเงิน(บาท)</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">vat(บาท)</td>
                                        <td style="border:1px solid black; background-color: yellow; color:blue">รวมทั้งสิ้น(บาท)</td>

                                        
                                    </tr>
                                    @endif
                                    @php
                                        $i++;
                                    @endphp
                                    <tr>
                                        <td colspan="2"></td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->inv_id}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->invoice_period->inv_p_name}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->lastmeter}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->currentmeter}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->inv_type == 'r' ? 'รักษามิเตอร์' : 'มีการใช้น้ำ'}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->water_used}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->paid}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->vat}}</td>
                                        <td style="border:1px solid black; background-color: yellow;">{{$item->totalpaid}}</td>

                                    </tr>
                                @endforeach
                            @endif --}}

                        @endforeach
                    </tbody>
                </table>

