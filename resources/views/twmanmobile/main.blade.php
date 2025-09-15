<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <!-- <meta
      http-equiv="Content-Security-Policy"
      content="default-src *; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline' 'unsafe-eval'"
    /> -->
  <allow-intent href="*" />

  <meta name="format-detection" content="telephone=no" />
  <meta name="viewport"
    content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height" />

  <link rel="stylesheet" href="{{asset('/adminlte/dist/css/adminlte.min.css')}}" />
  
  <title>BTPrinter Plugin Demo</title>
  <style>
    .hidden {
      display: none;
    }
  </style>
</head>

<body class="bg-light h-100 sidebar-mini">
  <div id="wrapper">

    <div id="nav">
      @include('twmanmobile.nav')
    </div>
    <!-- Page Content -->
    <div id="page-content-wrapper" class="wrapper">
      <div class="container p-2">

        <div class="card card-widget widget-user">

          <div class="widget-user-header text-white" style="background: url('{{asset('/adminlte/dist/img/photo1.png')}}') center center;">
            <h3 class="widget-user-username text-right">งานประปา</h3>
            <h5 class="widget-user-desc text-right">หน้าหลัก</h5>
          </div>
          <div class="widget-user-image">
            <img class="img-circle " src="{{asset('/logo/khampom.png')}}" alt="User Avatar">
          </div>
          <br>


          <div class="direct-chat-msg mt-5">
            <div class="direct-chat-text bg-primary h5 inv_period text-center">

            </div>
          </div>
          <div class="card">
            <div class="card-body">
              <div class="user-block">
                <img class="img-circle img-bordered-sm" src="{{asset('/adminlte/dist/img/user1-128x128.jpg')}}" alt="user image">
                <span class="username">
                  <a href="#" class="twman_name"></a>
                </span>
                <span class="description">เจ้าหน้าที่จดมิเตอร์</span>
              </div>
            </div>
          </div>
          <div class="direct-chat-msg mb-4">

            <div class="direct-chat-text bg-warning h5">
              เส้นทางจดมิเตอร์ที่รับผิดชอบ
            </div>
          </div>
          <div class="row"></div>
          <div class="row p-4" id="subzone"></div>

        </div>
      </div>
    </div>



    <script type="text/javascript" src="{{asset('/soft-ui/assets/js/jquery-3.7.0.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/api_url.js')}}"></script>

    <script>
      let membersSubzone;
      let cur_meter = "";
      let water_used = 0.0;
      let paid_raw = 0.0;
      let vat = 0.0;
      let inv_id = 0.0;
      let totalpaid = 0.0;
      let twman;
      let membersInitStatusCount
      let membersInitStatus;
      let inv_period;
      $(document).ready(async function () {
        // $("#nav").load("nav.blade.php");


        twman = JSON.parse(window.localStorage.getItem('twman'));
        //inv_period = '08-69'// JSON.parse(window.localStorage.getItem("inv_period"))
      

       // let logo = window.localStorage.getItem("logo")
      //  $('.orglogo').prop('src','img/'+logo)
        $('.twman_name').html(`${twman.prefix}${twman.firstname} ${twman.lastname}`)
       window.localStorage.removeItem('membersLocalStorage')
        // let params = {
        //   username: twman.username,
        //   passwords: '123456',
        //   user_cate_id: 5,
        // };
        // await $.post(`${API_URL}/api/users/authen`, params, function (response) {
        //   let res = JSON.parse(JSON.stringify(response));
        //   console.log("res2", res);
        //   twman = res.data;
        //   window.localStorage.removeItem('twman')
        //   window.localStorage.setItem("twman", JSON.stringify(twman));
        // });

        // if (membersInitStatus !== null) {
        // } else {

        // }//else
        $('.inv_period').html("รอบบิลที่ " + twman.inv_period[0].inv_p_name)
        let text = ""
        let i = 0;
        twman.undertaker_subzone.forEach((element) => {
          if(element.memlists != undefined){
            // console.log(element.memlists)
          }else{
            console.log('unde')
          }
          // console.log('em', element)
          text += `
                  <div class="col-md-4 p-1">
                  <div class="card card-widget widget-user-2">
                      <div class="widget-user-header bg-info">
                      <div class="widget-user-image">
                        <img class="img-circle elevation-2" src="{{asset('/Applight/images/home_twater.png')}}" alt="User Avatar">
                      </div>
                      <h3 class="widget-user-desc">${element.subzone.zone.zone_name}</h3>
                      <h5 class="widget-user-username"> ${element.subzone.subzone_name}</h5>
                    </div>
                    <div class="card-footer p-0">
                    <ul class="nav flex-column">

                    <li class="nav-item">
                    <a href="#" class="nav-link h4 mt-4">
                    สมาชิก <span class="badge bg-info">${element.members}</span> คน
                    </a>
                    </li>

                  
                    <li class="nav-item p-2">
                      <div class="row">
                        <div class="col-12 h6">
                          <div>ชำระค่าน้ำประปาแล้ว</div> <div class=" badge bg-success">${element.members_status_paid} คน</div>
                        </div>
                        
                      </div>
                    </li>

                    <li class="nav-item p-2">
                      <div class="row">
                        <div class="col-7 h6">
                          <div>บันทึกเลขมิเตอร์แล้ว</div>
                           <div class=" badge bg-success">${element.members_status_invoice} คน</div>
                        </div>
                        <div class="col-5">
                          <a href="javascript:void(0)" data-subzone_id="${element.subzone_id}" data-undertaker_subzone_index="${i}" class="btn btn-success btn-block goto_edit_subzone_selected_btn 
                          ${element.members_status_invoice === 0 ? 'hidden' : ''}">แก้ไขข้อมูล</a>
                        </div>
                      </div>
                    </li>

                    
                    <li class="nav-item p-2">
                      <div class="row">
                        <div class="col-7 h6">
                          <div>ยังไม่บันทึกเลขมิเตอร์</div>
                          <div class="badge bg-info">${element.members_status_init} คน</div> 
                        </div>
                        <div class="col-5">
                          <a href="javascript:void(0)" data-subzone_id="${element.subzone_id}" 
                          class="btn btn-info btn-block goto_members_subzone_selected_btn  ${element.members_status_init === 0 ? 'hidden' : ''}"
                          data-undertaker_subzone_index="${i}">เพิ่มข้อมูล</a>
                        </div>
                      </div>
                    </div>
                    </li>
                    </ul>
                    </div>
                    </div>

                    </div>
            `;
          i++
        });
        $("#subzone").html(text);
      });

      $(document).on('click', '.goto_members_subzone_selected_btn', function () {
        let subzone_id_selected = $(this).data('subzone_id')
        let index = $(this).data('undertaker_subzone_index')

        window.localStorage.setItem('subzone_id_selected', JSON.stringify(subzone_id_selected))
        window.localStorage.setItem('subzone_index_selected', index)
        console.log('twman[index]', twman.undertaker_subzone[index])
        window.localStorage.setItem('subzone_selected', JSON.stringify(twman.undertaker_subzone[index]))

        window.location.href = 'members_subzone_selected.html'
      })

      $(document).on('click', '.goto_edit_subzone_selected_btn', function () {
        let subzone_id_selected = $(this).data('subzone_id')
        let index = $(this).data('undertaker_subzone_index')

        window.localStorage.setItem('subzone_id_selected', JSON.stringify(subzone_id_selected))
        window.localStorage.setItem('subzone_index_selected', index)
        console.log('twman[index]', twman.undertaker_subzone[index])
        window.localStorage.setItem('subzone_selected', JSON.stringify(twman.undertaker_subzone[index]))

        window.location.href = '/twmanmobile/edit_members_subzone_selected'
      })


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