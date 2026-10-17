<style>
    /* table{
        width: 210mm;
        height: 148mm;
        border:1px solid red
    } */
    /* @page { margin: 50px } */
</style>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<script>
    $(document).ready(function(){
        $('.btnprint').click(function(){
            $('.btnprint').hide();
            let css = '@page{ size: a5; .pagebreak {clear: both;page-break-after: always;}';
            let head = document.head || document.getElementsByTagName('head')[0];
            let style = document.createElement('style');
            style.type = 'text/css';
            style.media = 'print';
            if(style.styleSheet){
                style.styleSheet.cssText = css;
            }else{
                style.appendChild(document.createTextNode(css));
            }
            head.appendChild(style);
            window.print();

        })
    });
</script>
<div class="row">
    <div class="col-lg-3 col-6">
      <!-- small box -->
      <div class="small-box bg-info">
        <div class="inner">
          <h3>150</h3>

          <p>New Orders</p>
        </div>
        <div class="icon">
          <i class="ion ion-bag"></i>
        </div>
        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
      <!-- small box -->
      <div class="small-box bg-success">
        <div class="inner">
          <h3>53<sup style="font-size: 20px">%</sup></h3>

          <p>Bounce Rate</p>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
      <!-- small box -->
      <div class="small-box bg-warning">
        <div class="inner">
          <h3>44</h3>

          <p>User Registrations</p>
        </div>
        <div class="icon">
          <i class="ion ion-person-add"></i>
        </div>
        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
      <!-- small box -->
      <div class="small-box bg-danger">
        <div class="inner">
          <h3>65</h3>

          <p>Unique Visitors</p>
        </div>
        <div class="icon">
          <i class="ion ion-pie-graph"></i>
        </div>
        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
  </div>
<button class="btn btn-danger btnprint">print</button>


{{-- <table >

    <tr>
        <td colspan="4">ใบแจ้งหนี้น้ำประปา</td>
    </tr>
    <tr>
        <td colspan="4">
            <php
                echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG("OE1906-04", "C128") . 
                '" alt="barcode" style="width:200px; height:30px"/>';
            ?>
        </td>
    </tr>
    <tr>
        <td id='owner' colspan="4">
            <div class="inner">
                <div class="title">เทศบาลตำบลห้องแซง</div> 
                <div class="address">ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร 35120</div>
                <div class="phone">โทรศัพท์ 045-777116 </div>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <div class="inv_period">
                รอบบิลที่: 
                {{$item->invoice_period->inv_period_name}}   
                <span class="date_period">
                        [{{$item->invoice_period->startdate}} ถึง
                        {{$item->invoice_period->enddate}} ]
                </span>
            </div>
        </td>
    </tr>
    <tr>
        <td id="owner" colspan="4">
            <div class="inner">
                <div>
                    <span class="title text-left">รหัสผู้ใช้น้ำ</span>
                    <span class="address">:{{$item->users->username}}</span> 
                </div>
                <div>
                    <span class="title">เลขมิเตอร์</span>
                    <span class="address">:
                       :Hz0{{$item->users->usermeter_info->id}}0{{$item->users->usermeter_info->metertype}}
                    </span> 
                </div>
        </div>
        </td>
    </tr>
    <tr id="owner">
        <td ></td>
        <th >วันที่</th>
        <th >หน่วย</th>
        <td ></td>
    </tr>
    <tr>
        <td>ยอดยกมา</td>
        <td>12/22/2563</td>
        <td>{{$item->lastmeter}}</td>
        <td></td>
    </tr>
    <tr>
        <td>ยอดปัจจุบัน</td>
        <td>12/22/3322</td>
        <td>{{$item->currentmeter}}</td>
        <td></td>
    </tr>
    <tr>
        <td colspan="2">จำนวนมิเตอร์</td>
        <td>{{$item->used_water_net}}</td>
        <td></td>
    </tr>
    <tr>
        <td colspan="3">
            <div>คิดเป็นเงิน {{$item->used_water_net}} x {{$item->users->usermeter_info->counter_unit}} บาท:หน่วย </div>
        </td>
        <tdfont-weight-bold bg-warning text-black">  {{$item->must_paid}} บาท</td>
    </tr>
</table> --}}
{{-- <div id="printpage">
    <div class="row">
        <div class="col-md-6 invoice_title">
                ใบแจ้งหนี้น้ำประปา
                <br>
                <php
                    echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG("OE1906-04", "C128") . 
                    '" alt="barcode" style="width:200px; height:30px"/>';
                ?>
        </div>
        <div class="col-md-6" id="owner">
            <div class="inner">
                <div class="title">เทศบาลตำบลห้องแซง</div> 
                <div class="address">ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร 35120</div>
                <div class="phone">โทรศัพท์ 045-777116 </div>
            </div>
          
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12 inv_period">
            รอบบิลที่: 
            {{$item->invoice_period->inv_period_name}}   
            <span class="date_period">
                    [{{$item->invoice_period->startdate}} ถึง
                    {{$item->invoice_period->enddate}} ]
            </span>
        </div>
    </div>


    <div class="row mt-2">
            <div class="col-md-4" id="owner">
                <div class="inner">
                        <div>
                            <span class="title text-left">รหัสผู้ใช้น้ำ</span>
                            <span class="address">:{{$item->users->username}}</span> 
                        </div>
                        <div>
                            <span class="title">เลขมิเตอร์</span>
                            <span class="address">:
                               :Hz0{{$item->users->usermeter_info->id}}0{{$item->users->usermeter_info->metertype}}
                            </span> 
                        </div>
                </div>
                    
            </div>
            <div class="col-md-8" id="owner">
                <div class="inner">
                    <div class="title">{{$item->users->user_profile->name}}</div> 
                    <div class="address">
                        {{$item->users->usermeter_info->zone->zone_name}} 
                         {{$item->users->usermeter_info->zone->location}}
                    </div>
                    <div class="phone">โทรศัพท์ {{$item->users->user_profile->phone}} </div>
                </div>
              
            </div>
        </div>


<table class="table mt-2" >
    <tr style="border: 1px solid red">
        <td></td>
        <th>วันที่</th>
        <th>หน่วย</th>
        <td></td>
    </tr>
    <tr>
        <td>ยอดยกมา</td>
        <td>12/22/2563</td>
        <td>{{$item->lastmeter}}</td>
        <td></td>
    </tr>
    <tr>
        <td>ยอดปัจจุบัน</td>
        <td>12/22/3322</td>
        <td>{{$item->currentmeter}}</td>
        <td></td>
    </tr>
    <tr>
        <td colspan="2">จำนวนมิเตอร์</td>
        <td>{{$item->used_water_net}}</td>
        <td></td>
    </tr>
    <tr>
        <td colspan="3">
            <div>คิดเป็นเงิน {{$item->used_water_net}} x {{$item->users->usermeter_info->counter_unit}} บาท:หน่วย </div>
        </td>
        <tdfont-weight-bold bg-warning text-black">  {{$item->must_paid}} บาท</td>
    </tr>
</table> --}}

{{-- <div class="row mt-3">
        <div class="col-md-12 inv_period">
                ชำระก่อนวันที่ {{$item->created_at}}
        </div>
    </div>
    
 <hr>
<div class="text-right mt-2">พนักงานจดมิเตอร์ : {{$item->recorder->username}}</div> --}}
</div>
<div class="page_break"></div>
{{-- {{dd($invoiceArray)}} --}}
