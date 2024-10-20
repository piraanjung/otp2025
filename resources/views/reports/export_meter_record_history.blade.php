

                <table id="oweTable" class="table  table-bordered table-striped">
                    <thead>
                        <tr>
                            <th colspan="10" style="text-align: center"><h4>ตารางสมุดจดเลขอ่านมาตรวัดน้ำ(ป.31)</h4></th>
                        </tr>
                        <tr>
                            <th colspan="10"></th>
                        </tr>
                        <tr>
                            <th style="background-color: #FFD3B6">#</th>
                            <th style="background-color: #FFD3B6">รหัสผู้ใช้น้ำ</th>
                            <th style="background-color: #FFD3B6">ชื่อผู้ใช้น้ำ</th>
                            <th style="background-color: #FFD3B6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ที่อยู่&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th style="background-color: #FFD3B6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;หมู่ที่&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th style="background-color: #FFD3B6">เส้นทางจดมิเตอร์</th>
                            <th style="background-color: #FFD3B6">ยอดยกมา</th>
                            @foreach ($inv_period_list as $inv_period)
                                <th colspan="3" style="text-align: center; background-color: #FFD3B6">รอบบิล {{ $inv_period->inv_p_name }}</th>
                            @endforeach

                        </tr>
                        <tr>
                            <th style="background-color: #FFD3B6"></th>
                            <th style="background-color: #FFD3B6"></th>
                            <th style="background-color: #FFD3B6"></th>
                            <th style="background-color: #FFD3B6"></th>
                            <th style="background-color: #FFD3B6"></th>
                            <th style="background-color: #FFD3B6"></th>
                            <th style="background-color: #FFD3B6"></th>
                            @foreach ($inv_period_list as $inv_period)
                                <th style="background-color: #FFD3B6">วันที่อ่านมิเตอร์</th>
                                <th style="background-color: #FFD3B6">เลขอ่านมิเตอร์</th>
                                <th style="background-color: #FFD3B6">จำนวนที่ใช้</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        @foreach ($usermeterinfos as $user)
                        @php
                            $aa = 0;
                            $subzone = 0;

                            if(isset( $user->user->user_subzone->subzone_name)){
                                $subzone =  $user->user->user_subzone->subzone_name;
                            }
                        @endphp

                            <tr>
                                <td>{{$i++ }}</td>
                                <th>{{ $user['meter_id'] }}</th>
                                <th>

                                    {{  $user->user->prefix. '' . $user->user->firstname . ' ' . $user->user->lastname }}
                                </th>
                                <th style="text-align:right">{{ $user->user->address }}</th>
                                <th style="text-align:center">{{ $user->user->user_zone->zone_name }}</th>
                                <th style="text-align:center">{{ $subzone}}</th>

                                <th style="background-color:#F7E7DC; text-align:right"> {{ number_format($user['bringForward'])}}</th>
                                @foreach ($user['infos'] as $inv_period)
                                    <th style="text-align:right">
                                        {{ number_format($inv_period['lastmeter']) }}
                                    </th>
                                    <th style="text-align:right">
                                        {{ number_format($inv_period['currentmeter']) }}
                                    </th>
                                    <th style="background-color:#DEF9C4; text-align:right">
                                        {{ number_format($inv_period['water_used']) }}
                                    </th>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
