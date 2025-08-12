@extends('layouts.print')



@section('content')
<?php
use App\Http\Controllers\Api\FunctionsController;

?>

<head>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('adminlte/dist/css/adminlte.min.css')}}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        body{
            /* background: gray */
            /* color: red */
        }
         .a {
            font-size: 11pt
        }
        .col-5 {
            -ms-flex: 0 0 41.4% !important;
            flex: 0 0 41.4% !important;
            max-width: 41.4% !important;
            position: relative !important;
            width: 100% !important;
            padding-right: 0px !important;
            padding-left: 0px !important;
        }

        td {
            height: 17pt;
            font-size: 0.85rem;
            padding-top:3px;
            padding-bottom: 3px;
        }

        .formleft {
            /* border-right: 1px red dotted; */
        }

        .pagebreak {
            clear: both;
            page-break-after: always;
        }
       
        .aa {
            /* color: red; */
        }
        .border-bottom-none{
            /* border-bottom: 1px solid #ffffff; */
        }
       

        .text-top-left {
            vertical-align: top;
            text-align: left;
        }
        .invoice-text{
               text-align: center;
               vertical-align:middle
            } 
       img.barcodemain{
            height:22px
       }
       .tdbarcode{
           /* border-top: 1px solid #ffffff; */
       }
       .invoicehead{
           font-size: 1.3rem;
           font-weight: bold
       }
       .invoice_totaltext{
            font-weight: bold
        }
        .head{
            height: 1.8cm;
        }
        .table2{
            margin-left:-10px !important;
        }
        .c2{
            margin-top: -10px;
        }
       
        
        @media print { 
            @page {
                margin: 0cm;
                /* size: 595pt 150pt ;  */
                /* border:1px solid red; */
            }

            .page{
                margin: 0cm;
                /* size: 595pt 150pt ;  */
                /* border:1px solid red; */
            }
            td{
                height: 16pt;
                font-size:10pt;
            }
            .invoice-text{
               text-align: center;
               font-size: 12pt;
               vertical-align:middle
            } 
            img.barcodemain{
                height:10px
            }
            .pagebreak {
                /* clear: both; */
                page-break-after: always;
            }
            .invoicehead{
                font-size: 18pt;
                font-weight: bold
            }
            .invoicehead2{
                font-size: 20pt;
                font-weight: bold
            }
            .totaltext{
                font-size: .8rem;
            }
            .invoice_totaltext{
                font-size: 0.8rem;
                font-weight: bold
            }
            .a{
                font-size:11pt;
            }
            .sign td{
                margin-top: -15px;
                font-size: 0.8rem
            }
            .col-5{
                flex: 0 0 40.55% !important;
                max-width: 40.55% !important;
                position: relative;
                width: 100%;
                padding-right: 7.5px;
                padding-left: 0px;
                /* color: red */
            }
            .col-2{
                flex: 0 0 18.45% !important;
                max-width: 18.45% !important;
                margin: 0px !important;
                position: relative;
                width: 100%;
                padding-right: 7.5px;
                padding-left: 0px;
                /* color: red */
            }
            .table2{
                margin-left:-10px !important;
            
            } 
            .c2{
                margin-top: -10px;
            }
            
          
           
        }
    </style>
</head>

<?php $index =  0; ?>
<input type="hidden" id="subzone_id"  value="{{$subzone_id}}">
<?php 
    $year = date('Y')+543;
    $year2 = date('y')+43;
    $a =1;
    $c2 = 1;
    $invoiceNumber = FunctionsController::invoice_last_record()->id + 1;
?>


    
    @for ($i = 0; $i < collect($invoiceArray)->count(); $i++)
        <?php $invoiceNumber = $invoiceNumber + $i;?>
        <div class=" " >
            <div class="padding-top:0.5rem; padding-left:0.5rem">
               
                <div class="row {{$c2  >1 ? 'c2' : ''}}">
                    {{-- @for ($j = 0; $j < 2; $j++) --}}
                        @if ($index==collect($invoiceArray)->count())
                            <?php return; ?>
                        @endif
                        <?php 
                            $year = date('Y')+543;
                            $year2 = date('y')+43;
                        ?>
                        
                        <div  class="col-5">
                           
                           
                           

                            <table style="width:100%" border="0" > 
                                @if ($c2 == 2)
                                <tr><td colspan="12">&nbsp;</td></tr>
                                <tr><td colspan="12">&nbsp;</td></tr>
                                <tr><td colspan="12" style="height: 28px !important">&nbsp;</td></tr>

                                @endif
                                @if ($c2 == 3)
                                <tr><td colspan="12">&nbsp;</td></tr>
                                <tr><td colspan="12" style="height: 41px !important">&nbsp;</td></tr>

                                @endif
                                <tr>
                                    <th colspan="7" rowspan="3"  class="invoice-text">
                                        {{--  ใบเสร็จรับเงินค่าน้ำประปา/ใบกำกับภาษี --}}&nbsp;
                                    </th>
                                    <th colspan="5">
                                        {{-- <h6>No. 029386</h6> --}}&nbsp;
                                    </th>
                                    
                                </tr>
                            
                                <tr><td colspan="5">&nbsp;</td></tr>

                                <tr><td colspan="5">&nbsp;</td></tr>

                                <tr>
                                    <td colspan="5" >
                                        {{-- เลขที่ใบเสร็จ --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <?php
                                            // echo substr(FunctionsController::createInvoiceNumberString($invoiceArray[$index]->id),2);
                                            echo $invoiceNumber;
                                        ?>
                                        {{-- {{FunctionsController::createInvoiceNumberString($invoiceArray[$index]->id)}} --}}
                                    </td>
                                    <td colspan="7">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="7" >
                                        {{-- ค่าน้ำประจำเดือน  --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?php
                                            $month = explode('-',$invoiceArray[$index]['invoice_period']['inv_period_name']);
                                            echo FunctionsController::shortThaiMonth($month[0]);
                                        ?>
                                    </td>
                                    <td colspan="5" >
                                        {{-- เลขที่ผู้ใช้น้ำ --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        {{$invoiceArray[$index]['usermeterinfos']['meternumber']}}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7"  >
                                        {{-- ชื่อ --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp; 
                                        {{$invoiceArray[$index]['user_profile']['name']}}</td>
                                    <td colspan="5" >
                                        {{-- หมายเลขมาตร --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        -
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="a">
                                        <?php    
                                            $address = $invoiceArray[$index]['user_profile']['address']; 
                                            $address .= $invoiceArray[$index]['usermeterinfos']['zone']['zone_name'];
                                            $address .= " ต.ห้องแซง อ.เลิงนกทา ยส.";//$invoiceArray[$index]['usermeterinfos']['zone']['location'];
                                            
                                        ?>
                                        {{-- ที่อยู่ --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;
                                         {{$address}}
                                    </td>
                                    <td colspan="5" >
                                        {{-- เส้นทาง --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?php
                                            $way = explode(' ', $invoiceArray[$index]['usermeterinfos']['zone']['zone_name']);
                                            echo $way[1];
                                        ?>
                                        {{-- {{$invoiceArray[$index]['usermeterinfos']['zone']['zone_name']}}</td> --}}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">&nbsp;</td>
                                    <td colspan="2">
                                        {{-- วันที่จด --}} 
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                    <td colspan="2">
                                        {{-- เลขที่จด --}} 
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                    <td colspan="2">
                                        {{-- หน่วยที่ใช้ --}} 
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                    <td colspan="3">
                                        {{-- ค่าน้ำประปา --}} 
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        {{-- จดครั้งก่อน --}} 
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                    <td colspan="2" class="pt-2">
                                        {{-- 10-01-64 --}}
                                        {{$invoiceArray[$index]['invoice_period']['th_startdate']}}

                                        {{-- {{date('d-m-Y')}} --}}
                                    </td>
                                    <td colspan="2" class="text-left pt-2">{{$invoiceArray[$index]['lastmeter']}}</td>
                                    <td colspan="2" rowspan="2" class="text-left  h6 ">
                                        <?php  $remain = $invoiceArray[$index]['currentmeter'] - $invoiceArray[$index]['lastmeter']; ?>
                                        &nbsp;
                                        {{$remain}}
                                        {{-- <div style="font-size:0.8rem" class="mt-3"> (8บาท:หน่วย)</div> --}}
                                    </td>

                                    <td rowspan="2" colspan="3" class="text-left pt-2 " style="padding-left:0px !important;font-size: 1.2em">
                                        <h5 style="">{{$remain*8}}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        {{-- จดครั้งหลัง --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                    <td colspan="2">
                                        {{-- 10-02-64 --}}
                                        {{$invoiceArray[$index]['invoice_period']['th_enddate']}}
                                    </td>
                                    <td colspan="2" class="text-left">{{$invoiceArray[$index]['currentmeter']}}</td>

                                </tr>
                            
                                <tr>
                                    <td colspan="7">
                                        <table width="100%">
                                            <tr>
                                                <td>
                                                    {{-- ค่ารักษามาตร --}}&nbsp;
                                                </td>
                                                <td class="text-center"> {{$remain == 0 ? '10' : ''}}</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td colspan="5">
                                        {{-- รวมเป็นเงินที่ต้องชำระทั้งสิ้น (บาท) --}}&nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <table width="100%">
                                            <tr>
                                                <td>
                                                    {{-- ภาษีมูลค่าเพิ่ม --}}&nbsp;
                                                </td>
                                                <td style="text-align:center">
                                                    {{-- &nbsp; &nbsp; บาท &nbsp;&nbsp; --}}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <th colspan="5" rowspan="2" class="text-center">
                                        <h5 style="font-size: 1.2em">{{$remain == 0 ? $remain*8 + 10 : $remain*8}}</h5>
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="7"> &nbsp;</td>
                                </tr>

                            </table>
                            
                            <table width="100%" class="sign">
                               
                                <tr class="text-center">

                                    <td>

<br>                                        <div>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        นส.พัชรี ทองคุณ
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{-- ............................................. --}}&nbsp;
                                        </div>
                                      
                                    </td>
                                </tr>
                            </table>

                        </div>
                        <div  class="col-5 table2" >
                         {{-- ช่/อง2 --}}
                            <table style="width:100%" border="0" > 
                                @if ($c2 == 2)
                                <tr><td colspan="12">&nbsp;</td></tr>
                                <tr><td colspan="12">&nbsp;</td></tr>
                                <tr><td colspan="12" style="height: 28px !important">&nbsp;</td></tr>

                                @endif
                                @if ($c2 == 3)
                                <tr><td colspan="12">&nbsp;</td></tr>
                                <tr><td colspan="12" style="height: 41px !important">&nbsp;</td></tr>

                                @endif
                                <tr>
                                    <th colspan="7" rowspan="3"  class="invoice-text">
                                        {{--  ใบเสร็จรับเงินค่าน้ำประปา/ใบกำกับภาษี --}}&nbsp;
                                    </th>
                                    <th colspan="5">
                                        {{-- <h6>No. 029386</h6> --}}&nbsp;
                                    </th>
                                    
                                </tr>
                            
                                <tr><td colspan="5">&nbsp;</td></tr>

                                <tr><td colspan="5">&nbsp;</td></tr>

                                <tr>
                                    <td colspan="5" >
                                        {{-- เลขที่ใบเสร็จ --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <?php
                                            // echo substr(FunctionsController::createInvoiceNumberString($invoiceArray[$index]->id),2);
                                            echo $invoiceNumber;
                                        ?>
                                        {{-- {{FunctionsController::createInvoiceNumberString($invoiceArray[$index]->id)}} --}}
                                    </td>
                                    <td colspan="7">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="7" >
                                        {{-- ค่าน้ำประจำเดือน  --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?php
                                            $month = explode('-',$invoiceArray[$index]['invoice_period']['inv_period_name']);
                                            echo FunctionsController::shortThaiMonth($month[0]);
                                        ?>
                                    </td>
                                    <td colspan="5" >
                                        {{-- เลขที่ผู้ใช้น้ำ --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        {{$invoiceArray[$index]['usermeterinfos']['meternumber']}}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7"  >
                                        {{-- ชื่อ --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;
                                        {{$invoiceArray[$index]['user_profile']['name']}}</td>
                                    <td colspan="5" >
                                        {{-- หมายเลขมาตร --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        -
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="a">
                                        <?php   
                                            $address = $invoiceArray[$index]['user_profile']['address']; 
                                            $address .= $invoiceArray[$index]['usermeterinfos']['zone']['zone_name'];
                                            $address .= " ต.ห้องแซง อ.เลิงนกทา ยส.";//$invoiceArray[$index]['usermeterinfos']['zone']['location;
                                            
                                        ?>
                                        {{-- ที่อยู่ --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;
                                         {{$address}}
                                    </td>
                                    <td colspan="5" >
                                        {{-- เส้นทาง --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?php
                                            $way = explode(' ', $invoiceArray[$index]['usermeterinfos']['zone']['zone_name']);
                                            echo $way[1];
                                        ?>
                                        {{-- {{$invoiceArray[$index]['usermeterinfos']['zone']['zone_name}}</td> --}}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">&nbsp;</td>
                                    <td colspan="2">
                                        {{-- วันที่จด --}} 
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                    <td colspan="2">
                                        {{-- เลขที่จด --}} 
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                    <td colspan="2">
                                        {{-- หน่วยที่ใช้ --}} 
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                    <td colspan="3">
                                        {{-- ค่าน้ำประปา --}} 
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        {{-- จดครั้งก่อน --}} 
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                    <td colspan="2" class="pt-2">
                                        {{-- 10-01-64 --}}
                                        {{$invoiceArray[$index]['invoice_period']['th_startdate']}}

                                        {{-- {{date('d-m-Y')}} --}}
                                    </td>
                                    <td colspan="2" class="text-left pt-2">{{$invoiceArray[$index]['lastmeter']}}</td>
                                    <td colspan="2" rowspan="2" class="text-left  h6 ">
                                        <?php  $remain = $invoiceArray[$index]['currentmeter'] - $invoiceArray[$index]['lastmeter']; ?>
                                        &nbsp;
                                        {{$remain}}
                                        {{-- <div style="font-size:0.8rem" class="mt-3"> (8บาท:หน่วย)</div> --}}
                                    </td>

                                    <td rowspan="2" colspan="3" class="text-left pt-2 " style="padding-left:0px !important;font-size: 1.2em">
                                        <h5 style="">{{$remain*8}}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        {{-- จดครั้งหลัง --}}
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
                                    <td colspan="2">
                                        {{-- 10-02-64 --}}
                                        {{$invoiceArray[$index]['invoice_period']['th_enddate']}}
                                    </td>
                                    <td colspan="2" class="text-left">{{$invoiceArray[$index]['currentmeter']}}</td>

                                </tr>
                            
                                <tr>
                                    <td colspan="7">
                                        <table width="100%">
                                            <tr>
                                                <td>
                                                    {{-- ค่ารักษามาตร --}}&nbsp;
                                                </td>
                                                <td class="text-center"> {{$remain == 0 ? '10' : ''}}</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td colspan="5">
                                        {{-- รวมเป็นเงินที่ต้องชำระทั้งสิ้น (บาท) --}}&nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <table width="100%">
                                            <tr>
                                                <td>
                                                    {{-- ภาษีมูลค่าเพิ่ม --}}&nbsp;
                                                </td>
                                                <td style="text-align:center">
                                                    {{-- &nbsp; &nbsp; บาท &nbsp;&nbsp; --}}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <th colspan="5" rowspan="2" class="text-center">
                                        <h5 style="font-size: 1.2em">{{$remain == 0 ? $remain*8 + 10 : $remain*8}}</h5>
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="7"> &nbsp;</td>
                                </tr>

                            </table>
                            
                            <table width="100%" class="sign">
            
                              
                                <tr>
                                    <td >
                                        <br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                        นส.พัชรี ทองคุณ
                                        {{-- หัวหน้าหน่วยงานคลัง --}}&nbsp;
                                    </td>
                                    <td>
                                        <div>
                                            {{-- ............................................. --}}&nbsp;
                                        </div>
                                        {{-- ผู้เก็บเงิน --}}&nbsp;
                                        
                                    </td>
                                </tr>
                            </table>

                        </div>
                      
                        <div class="col-2">
                            <table border="0" style="margin-left:1.2rem">
                                <tr class="text-center">
                                    <td colspan="2">
                                        &nbsp;
                                        {{-- <div class="invoicehead">ใบแจ้งหนี้ค่าน้ำประปา</div>
                                        <h6 class="invoicehead2">(ไม่ใช่ใบเสร็จรับเงิน)</h6> --}}
                                    </td>
                                </tr>
                                <tr><td>&nbsp;</td></tr>
                                <tr><td>&nbsp;</td></tr>
                                <tr>
                                    <td>
                                        &nbsp;
                                        {{-- ค่าน้ำประจำเดือน {{FunctionsController::shortThaiMonth('01')}} --}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        &nbsp;
                                        {{-- เลขที่ผู้ใช้น้ำ {{$invoiceArray[$index]['usermeterinfos']['meternumber}} --}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        &nbsp;
                                        {{-- เลขที่ใบแจ้งหนี้ 0001 --}}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">ชื่อ
                                        &nbsp;&nbsp;&nbsp;&nbsp; {{$invoiceArray[$index]['user_profile']['name']}}
                                
                                    
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                                <?php   
                                                    // $address = $invoiceArray[$index]['user_profile']['address; 
                                                    $address = ' '.$invoiceArray[$index]['usermeterinfos']['zone']['zone_name'];
                                                    // $address .= "ต.ห้องแซง อ.เลิงนกทา ยส. อ.เลิงนกทา ยส.";//$invoiceArray[$index]['usermeterinfos']['zone']['location;
                                                    
                                                ?> 
                                               <span class="a"> &nbsp;&nbsp;&nbsp;&nbsp;  {{$address}} </span>
                                    </td>
                                </tr>
                                <tr><td></td></tr>

                                <tr>
                                    <td class="text-center invoice_totaltext">
                                        &nbsp; 
                                        {{-- รวมเป็นเงินที่ต้องชำระทั้งสิ้น --}}
                                    </td>
                                </tr>
                                @if ($c2 == 2)
                                <tr><td>&nbsp;</td></tr>
                                <tr><td>&nbsp;</td></tr>
                                <tr><td>&nbsp;</td></tr>
                                <tr><td>&nbsp;</td></tr>
                                @endif

                                @if ($c2 == 3)
                                <tr><td>&nbsp;</td></tr>

                                @endif
                                
                                <tr>
                                    <td class="text-center pt-2 pb-3" >
                                            <h3>{{$remain == 0 ? $remain*8 + 10 : $remain*8}}</h3>
                                    </td>
                                </tr>
                               
                            </table>
                            <br>
                        
                            <table width="100%" class="sign">
                               
                                <tr>
                                    <td colspan="2" class="text-center">
                                        {{-- โปรดชำระให้เสร็จสิ้น --}}
                                    </td>
                                </tr>
                                <tr class="text-center">

                                    <td>
                                        {{-- ภายในวันที่ {{date('d')}} {{FunctionsController::shortThaiMonth('01')}} {{date('Y')+543}}  --}}
                                    </td>
                                
                                </tr>
                            </table>
                        </div>
                        <?php $index++;?>
                    {{-- @endfor --}}
                </div>

            </div>
        </div>

        @if ($c2%3 == 0)
            {{-- <div style="height: 150px">xxx</div> --}}
            <div class="pagebreak"></div> 
            <?php $c2 = 0; ?>
        @endif
        <?php $a++; $c2++;?>
    @endfor

    @endsection

    @section('script')
    <script>
        $(document).ready(function () {
            // $('.btnprint').click(function(){
            $('.btnprint').hide();
            var css = '@page {  }',
    
    
        head = document.head || document.getElementsByTagName('head')[0],
    
    
        style = document.createElement('style');
    
    
        style.type = 'text/css';
    
    
        style.media = 'print';
    
    
    
    
    
        if (style.styleSheet){
    
    
        style.styleSheet.cssText = css;
    
    
        } else {
    
    
        style.appendChild(document.createTextNode(css));
    
    
        }
    
    
    
    
    
        head.appendChild(style);
    
        style.type = 'text/css';
        style.media = 'print';
    
        if (style.styleSheet){
        style.styleSheet.cssText = css;
        } else {
        style.appendChild(document.createTextNode(css));
        }
    
        head.appendChild(style);
    
        window.print();
        setTimeout(function(){ window.location.href = '../invoice'; }, 200);
 
        // window.onafterprint = function(){
        //     window.history.back();
        //     window.location.href = `../../invoice/zone_info/${$('#subzone_id').val()}`;
        // }
    });
    
    </script>
    @endsection
   


      {{-- <tr>
                                <th colspan="4" class="text-center tdbarcode">
                                    <php
                                        echo $barcode = $year2.'-'.$invoiceArray[$index]['usermeterinfos']['meternumber.'-'.FunctionsController::createInvoiceNumberString($invoiceArray[$index]->id);
                                        echo ' <img src="data:image/png;base64,' . DNS1D::getBarcodePNG($barcode, "C128") . 
                                        '" alt="barcode" class="barcodemain"/>';
                                    ?>

                                </th>
                            </tr> --}}