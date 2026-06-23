<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  {{-- <meta http-equiv="Content-Security-Policy"
    content="default-src *; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline' 'unsafe-eval'" />
  <allow-intent href="*" /> --}}

  <meta name="format-detection" content="telephone=no" />
  <meta name="viewport"
    content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height" />
  <link rel="stylesheet" href="{{asset('/adminlte/dist/css/adminlte.min.css')}}" />
  <title>BTPrinter Plugin Demo</title>
  <style>
    .hidden {
      display: none;
    }
    .spinner-wrapper{
      width: 100%;
      height: 100%;
      z-index: 50;
      position: fixed;
      top:0;
      left: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: antiquewhite;
    }
  </style>
</head>

<body class="bg-light h-100">
  <div class="spinner-wrapper">
    <div class="spinner-grow text-primary" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <div class="spinner-grow text-secondary" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <div class="spinner-grow text-success" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <div class="spinner-grow text-danger" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <div class="spinner-grow text-warning" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <div class="spinner-grow text-info" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
 <div id="nav">
        @include('twmanmobile.nav')
 </div>
  <div id="wrapper" class="wrapper">
    <!-- Page Content -->
    <div id="header">
      <div class="card card-widget widget-user">

          <div class="widget-user-header text-white" style="background: url('{{asset('/adminlte/dist/img/photo1.png')}}') center center;">
          <h3 class="widget-user-username text-right">งานประปา</h3>
          <h5 class="widget-user-desc text-right">แก้ไขข้อมูลมิเตอร์ <span class="zoneAndSubzone"></span></h5>
        </div>
        <div class="widget-user-image">
            <img class="img-circle " src="{{asset('/logo/khampom.png')}}" alt="User Avatar">
        </div>

      </div>


      <span class="info-box-icon h1" style="position: absolute;left: 10px;top: 90px;z-index: 10;">
        <a href="main.html">
          <i class="fa fa-chevron-circle-left"></i>
        </a>
      </span>
    </div>
    <!--header-->
    <br>    <br>

    <div class="card">
      <div class="card-body">
        <div class="small-box bg-warning">
          <div class="inner">
          <h5 class="zone_name"></h5>
          <h6 class="subzone_name"></h6>
          </div>
          <div class="icon">
          <i class="ion ion-person-add"></i>
          </div>
          
          </div>
        <div class="input-group mb-1 row">
          <input type="text" id="myInput" size="20" class="form-control col-10" placeholder="ค้นหา..."
            aria-label="Recipient's username" aria-describedby="button-addon2" />
          <div class="text-right col-2">
            <i class="fa fa-qrcode h1" id="barcode"></i>
          </div>
        </div>

        <table  style="width: 100%;">
          <thead>
            <tr>
              <th>
                แก้ไขข้อมูลมิเตอร์ <span id="member_init_status"></span> คน
              </th>
            </tr>
          </thead>
          <tbody id="myTable"></tbody>
        </table>
      </div>
    </div>

    <div class="modal fade" id="modal" style="display: none" aria-modal="true" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">ฟอร์มแก้ไขข้อมูลการใช้น้ำ</h4>
            <button type="button" class="close" id="closebtn" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="card card-warning">
              <div class="card-header" id="member_info"></div>

              <div class="card-body">
                <div class="form-group row">
                  <label for="inputEmail3" class="col-6 col-form-label">เลขมิเตอร์ยกมา</label>
                  <div class="col-5">
                    <input type="text" class="form-control" style="font-size: 1.5rem !important" id="lastmeter"
                      placeholder="" readonly />
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputEmail3" class="col-6 col-form-label">เลขมิเตอร์ปัจจุบัน</label>
                  <div class="col-5">
                    <input type="number" class="form-control" style="font-size: 1.5rem !important" id="currentmeter"
                      placeholder="" />
                  </div>
                </div>

              </div>

              <div class="card-footer">
                <button class="btn btn-warning float-right" id="savebtn">
                  บันทึกข้อมูล
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript" src="{{asset('/soft-ui/assets/js/jquery-3.7.0.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/api_url.js')}}"></script>
<script src="{{asset('/js/my.js')}}"></script>
    <script>
      let membersSubzone;
      let cur_meter = "";
      let water_used = 0.0;
      let paid_raw = 0.0;
      let vat7 = 0.0;
      let inv_id = 0.0;
      let totalpaid = 0.0;
      let zone_id_selected;
      let twman
      let currentmember;
      let membersInitStatus;
      let subzone_selected;
      let org
      let subzone_index_selected
      $(document).ready(function () {
        localStorage.removeItem('edit_page')
        // $("#nav").load("nav.html");

        zone_id_selected = JSON.parse(window.localStorage.getItem('subzone_id_selected'));
        subzone_selected = JSON.parse(window.localStorage.getItem('subzone_selected'));
        subzone_index_selected = JSON.parse(window.localStorage.getItem('subzone_index_selected'));

        $('.zone_name').html(subzone_selected.subzone.zone.zone_name)
        $('.subzone_name').html(subzone_selected.subzone.subzone_name)
        twman = JSON.parse(window.localStorage.getItem('twman'));
     
        $('.zone_name').html(subzone_selected.zone_name)
        $('.subzone_name').html(subzone_selected.subzone_name)
        window.localStorage.removeItem('data_from_serve')

        getMemberInSubzone(zone_id_selected);

        $('.spinner-wrapper').css('opacity', 0)
        setTimeout(() => {
          $('.spinner-wrapper').css('display','none')
        }, 200);
      });



      async function getMemberInSubzone(subzone_id) {
        membersSubzone = await $.get(
          API_URL +
          "/api/subzone/get_members_last_inactive_invperiod/" +subzone_id,
          function (data) {
            console.log('data',data)
            return data;
          }
        );

        await membersLists(membersSubzone);
      }

      $("#myInput").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function () {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
      });

      async function membersLists(membersSubzone) {
        let txt = "";
        membersInitStatus = membersSubzone.members.filter(function (v) {
          return v.status === "invoice";
        });

        membersInitStatus.forEach((element) => {
          txt += `
				<tr>
					<td>
					<div class="info-box" id="member${element.meter_id}" data-lastmeter="${element.init_meter}" data-inv_od="${element.inv_id}">
						<span class="info-box-icon bg-success" style="width:40px !important"><i class="fa fa-user"></i></span>

						<div class="info-box-content">
							<span class="info-box-text" style="white-space:initial !important">${element.prefix + "" + element.firstname + " " + element.lastname}</span>
							<span class="info-box-number text-primary"><b>${element.meternumber}</b> [ บ้านเลขที่ ${element.address} ]</span>
						</div>
						<!-- /.info-box-content -->
												<span class="info-box-icon" style="width:25px !important">
							<a href="javascript:void(0)" onclick="goto_add_meter_info_of_user('${element.meter_id}')" class="float-right" style="margin-top:-2rem" >
								<i class="fa fa-plus-circle " style="font-size:75px"></i>
							</a>
						</span>

					</div>
					</td>
				</tr>`;
        });

        $('#member_init_status').html(Object.keys(membersInitStatus).length)
        $("#myTable").html(txt);
      }
      function goto_add_meter_info_of_user(meter_id) {
        currentmember = "";
        currentmember =membersSubzone.members.filter(function (v) {
          return v.meter_id == meter_id;
        });

        window.localStorage.setItem('selected_member', JSON.stringify(currentmember))
        inv_id = currentmember[0].inv_id;
        $("#member_info").html(`
          <div>เลขมิเตอร์ ${currentmember[0].meternumber}</div>
          <div>${currentmember[0].prefix +
          "" +
          currentmember[0].firstname +
          " " +
          currentmember[0].lastname
          }</div>
          <div>ที่อยู่บ้านเลขที่ ${currentmember[0].address}</div>

        `); //          <div>ที่อยู่ ${currentmember[0].address + " " + currentmember[0].user_zonename}</div>

        $(`#modal #lastmeter`).val(currentmember[0].init_meter);
        cur_meter = currentmember[0].currentmeter;
        $(`#modal #currentmeter`).val(currentmember[0].currentmeter);
        $("#modal").addClass("show");
        $("#modal").prop("style", "display:block");

        $('#currentmeter').trigger('keyup');
      }


      $("#closebtn").click(function () {
        $("#modal").toggle();
      });

      $(document).on("keyup", "#currentmeter", function () {

        cur_meter = parseFloat($("#currentmeter").val());
        let lastmeter = parseFloat($('#lastmeter').val())
        if (cur_meter >= lastmeter) {
          $('#savebtn').removeClass('hidden')
        } else {
          $('#savebtn').addClass('hidden')
        }

        if (cur_meter === "") {
          water_used  = 0.0;
          paid_raw    = 0.0;
          vat7         = 0.0;
          inv_id      = 0.0;
          totalpaid   = 0.0;
        }
        water_used =
          cur_meter - parseFloat(currentmember[0].init_meter).toFixed(2);

        // paid_raw = water_used === 0 ? 10 : water_used * 8;

        // vat7 = water_used === 0 ? parseFloat(0.7) : paid_raw * parseFloat(0.07);

        // totalpaid = parseFloat(paid_raw + vat7).toFixed(2);
        // console.log('totalpaid', totalpaid)
      });

      $(document).on("click", "#savebtn", async function () {
        //get เอาราคาเก็บต่อหน่วยมิเตอร์;
        window.localStorage.setItem('data_for_print', JSON.stringify({
          inv_id: inv_id,
          metertype_id: currentmember[0].metertype_id,
          currentmeter: cur_meter,
          water_used: water_used,
          paid: paid_raw,
          vat: vat7,
          totalpaid: totalpaid,
          recorder_id: twman.id,
          lastmeter: $('#lastmeter').val(),
        }));
        membersSubzone.init = 1;
        membersSubzone.invoice_status =72
        //update status invoice ใน local Table
        membersSubzone.init =  membersSubzone.init - 1;
        membersSubzone.invoice_status = membersSubzone.invoice_status+1;
        let member_selected = membersSubzone.members.find((v)=>{
          return v.inv_id === inv_id
        });
        member_selected.status = 'invoice'
        
        window.localStorage.removeItem('member_init_remain')

        window.localStorage.setItem('membersSubzone', JSON.stringify(membersSubzone))
        window.localStorage.setItem('edit_page', JSON.stringify('edit_member'))
        setTimeout(function () {
          window.location.href = 'printbill.html'
        }, 500)


      
      });

      $(document).on('click', '.nav-link', function () {
        $('body').addClass('layout-fixed sidebar-open')
      })
      $(document).on('click', '.wrapper', function () {
        $('body').removeClass('layout-fixed sidebar-open')

        $('body').addClass('sidebar-closed sidebar-collapse')
      })
    

    </script>
</body>

</html>