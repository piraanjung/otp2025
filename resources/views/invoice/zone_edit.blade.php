@extends('layouts.adminlte')

@section('mainheader')
แก้ไขข้อมูลประปา <span id="undertake_zone"></span> [รอบบิล <span id="inv_period_name"></span>]
@endsection
@section('invoice')
    active
@endsection
@section('nav')
<a href="{{url('invoice/index')}}">ออกใบแจ้งหนี้</a>
@endsection


@section('content')
@if ($message = Session::get('massage'))
<div class="alert alert-{{Session::get('color')}} alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
	<strong>{{ $message }}</strong>
</div>
@endif

<div class="card">
    <div class="card-body table-responsive">
        <form action="{{url('invoice/zone_update/'.$subzone_id)}}" method="POST">
            @csrf
            @method("PUT")
                <input type="submit" class="btn btn-primary mb-3 col-2" id="print_multi_inv"
                    value="บันทึกการแก้ไข">
                <table id="oweTable" class="table text-nowrap" width="100%"></table>
        </form>
    </div>
    <div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>

</div>
@endsection


@section('script')
<script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script src="{{asset('/js/my_script.js')}}"></script>
<script>
    let i = 0;
    let table;
    let cloneThead = true
    let col_index = -1

     //getข้อมูลจาก api มาแสดงใน datatable
     $(document).ready(function () {
        getOweInfos()
    })

    function getOweInfos() {
            
        $.get(`../../api/invoice/zone_edit/<?php echo $subzone_id;?>`).done(function (data) {
            console.log('data',data)
            $('#inv_period_name').html(data.presentInvoicePeriod)
            $('#undertake_zone').html(data.zoneInfo)
            
            if (data.length === 0) {
                $('.res').html('<div class="card-body h3 text-center">ไม่พบข้อมูล</div>')
            } else {
                table = $('#oweTable').DataTable({
                    responsive: true,
                    // order: false,
                    // searching:false,
                    "pagingType": "listbox",
                    "lengthMenu": [
                        [10, 25, 50, 150, -1],
                        [10, 25, 50, 150, "ทั้งหมด"]
                    ],
                    "language": {
                        "search": "ค้นหา:",
                        "lengthMenu": "แสดง _MENU_ แถว",
                        "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว",
                        "infoEmpty": "แสดง 0 ถึง 0 จาก 0 แถว",
                        "paginate": {
                            "info": "แสดง _MENU_ แถว",
                        },

                    },
                    data: data.memberHasInvoice,
                    select:false,
                    columns: [
                        {
                            'title': 'เลขใบ<br>แจ้งหนี้',
                            data: function(data){
                                return `${data.id}<input type="hidden" value="${data.id}" name="zone[${data.id}][iv_id]" data-id="${data.id}" 
                                    id="iv_id${data.id}" class="form-control text-right iv_id">`
                            },
                            'className': 'text-center '

                        }, {
                            'title': '&nbsp;&nbsp;เลขที่&nbsp;&nbsp;&nbsp;&nbsp;',
                            data: function(data){
                                return `<center>${data.meternumber}&nbsp;&nbsp;</center><input type="hidden" value="${data.meternumber}" name="zone[${data.id}][meter_id]">`
                                },
                        },
                        {
                            'title': 'ชื่อ-สกุล',
                            data: 'name',
                            'className': 'text-center '
                        },
                        {
                            'title': 'บ้านเลขที่',
                            data: 'address',
                            'className': 'text-center'
                        },
                        {
                            'title': 'ยกยอดมา',
                            data: function(data){
                                return `<input type="text" value="${data.lastmeter}" name="zone[${data.id}][lastmeter]" data-id="${data.id}" 
                                    id="lastmeter${data.id}" class="form-control text-right lastmeter">`
                                    
                            },
                            'className': 'text-right'
                        },

                        {
                            'title': 'มิเตอร์<br>ปัจจุบัน',
                            data: function(data){
                                return `<input type="text" value="${data.currentmeter}" name="zone[${data.id}][currentmeter]" data-id="${data.id}" id="currentmeter${data.id}"
                                            class="form-control text-right currentmeter">
                                        <input type="hidden" value="0" name="zone[${data.id}][changevalue]" data-id="${data.id}" id="changevalue${data.id}">`
                                
                            },
                            'className': 'text-right'
                        },
                        {
                            'title': 'ใช้น้ำ<br>(หน่วย)',
                            data: function(data){
                                return `<input type="text" readonly class="form-control text-right water_used_net"  id="water_used_net${data.id}" value="${data.meter_net.toLocaleString()}">`
                            }
                        },
                        {
                            'title': 'เป็นเงิน<br>(บาท)',
                            data: function(data){
                                return ` <input type="text" readonly class="form-control text-right total" id="total${data.id}" value="${data.total.toLocaleString()}">`
                            }
                        },
                        {
                            'title': '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;สถานะ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                            data: function(data){
                                let init_selected = data.status === 'init' ? 'selected' : ''
                                let invoice_selected = data.status === 'invoice' ? 'selected' : ''
                                return ` <select class="form-control" name="zone[${data.id}][status]">
                                        <option value="init" ${init_selected}>เริ่มต้น</option> 
                                        <option value="invoice" ${invoice_selected}>ออกใบแจ้งหนี้</option> 
                                        <option value="delete">ลบ</option> 
                                    </select>`
                            }
                        },
                        {
                            'title': 'หมายเหตุ',
                            data: function(data){
                                let comment = data.comment === null ? "" : data.comment
                                return `<input type="text" name="zone[${data.id}][comment]"  value="${comment}"
                                        data-id="${data.id}" id="comment${data.id}" class="form-control  comment">`
                            }
                        },
                        // {
                        //     'title': '',
                        //     data: function(data){
                        //         return `<a href="javascript:void(0)" class="btn btn-danger delBtn" data-del_invoice_id="${data.id}">ลบ</a>`
                        //     }
                        // }

                    ],
                    
                }) //table
                // ทำการ clone thead แล้วสร้าง input text
                if(cloneThead){
                    $('#oweTable thead tr').clone().appendTo('#oweTable thead');
                    cloneThead= false
                }
                $('#oweTable thead tr:eq(1) th').each( function (index) {
                    var title = $(this).text();
                    $(this).removeClass('sorting')
                    $(this).removeClass('sorting_asc')
                    if(index < 4){
                        $(this).html( `<input type="text" data-id="${index}" class="col-md-12" style="font-size:14px" id="search_col_${index}" placeholder="ค้นหา" />` );
                    }else{
                        $(this).html('')
                    }
                } );
            } //else
            $('.overlay').remove()
            $('#oweTable_filter').remove()

            //custom การค้นหา
            
            $('#oweTable thead input[type="text"]').keyup(function(){
                let that = $(this)
                var col = parseInt(that.data('id'))

                if(col !== col_index && col_index !== -1){
                    $('#search_col_'+col_index).val('') 
                    table.column(col_index)
                    .search('')
                    .draw();
                }
                setTimeout(function(){ 
                    
                    let _val = that.val()
                    if(col === 0  || col===3){
                        var val = $.fn.dataTable.util.escapeRegex(
                            _val
                        );
                        table.column(col)
                        .search( val ? '^'+val+'.*$' : '', true, false )
                        .draw();
                    }
                    else{
                        table.column(col)
                        .search( _val )
                        .draw();
                    }
                 }, 300);
     
                col_index = col

            })

        }) //.get

    }//function getOweInfos


    //คำนวนเงินค่าใช้น้ำ
    $(document).on('keyup', '.currentmeter', function(){
        let inv_id = $(this).data('id')
        let currentmeter = $(this).val()
        let lastmeter = $(`#lastmeter${inv_id}`).val()
        let net = currentmeter - lastmeter
        let total = net === 0 ? 10 : net * 8;
        $('#water_used_net'+inv_id).val(net)
        $('#total'+inv_id).val(total);
        $('#changevalue'+inv_id).val(1)
    });

    $(document).on('keyup', '.lastmeter', function(){
        let inv_id = $(this).data('id')
        let lastmeter = $(this).val()
        let currentmeter = $(`#currentmeter${inv_id}`).val()
        let net = currentmeter - lastmeter
        let total = net === 0 ? 10 : net * 8;
        $('#water_used_net'+inv_id).val(net)
        $('#total'+inv_id).val(total);
        $('#changevalue'+inv_id).val(1)
    });

    $('.dataTable').DataTable({
        "pagingType": "listbox",
        "lengthMenu": [[10, 25, 50, 150, -1], [10, 25, 50, 150, "ทั้งหมด"]],
        "language": {
            "search": "ค้นหา:",
            "lengthMenu": "แสดง _MENU_ แถว", 
            "info":       "แสดง _START_ ถึง _END_ จาก _TOTAL_ แถว", 
            "infoEmpty":  "แสดง 0 ถึง 0 จาก 0 แถว",
            "paginate": {
                "info": "แสดง _MENU_ แถว",
            },
        }
    })
    $(document).ready(function(){
        $('.paginate_page').text('หน้า')
        let val = $('.paginate_of').text()
        $('.paginate_of').text(val.replace('of', 'จาก')); 

        setTimeout(()=>{
            $('.alert').toggle('slow')
        },2000)
        
    })

    $(document).on('click','.delBtn', function(){
        let res = window.confirm('ต้องการลบข้อมูลใช่หรือไม่ !!!')
          if(res === true){
            let inv_id = $(this).data('del_invoice_id');
            let comment = $(`#comment${inv_id}`).val()
            window.location.href = `/invoice/delete/${inv_id}/${comment}`
          }else{
            return false;
          } 
      });

</script>
@endsection


