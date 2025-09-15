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

  {{-- <link rel="stylesheet" href="admin-lte/css/adminlte.css" />
  <link rel="stylesheet" href="admin-lte/plugin/fontawesome-free/css/all.css" /> --}}

  {{-- <script src="js/jquery-3.6.0.min.js"></script> --}}
  <title>BTPrinter Plugin Demo</title>
  <style>
    body {
      background-color: #f45b69;
      font-family: "Asap", sans-serif;
    }

    .login {
      overflow: hidden;
      background-color: white;
      padding: 40px 30px 30px 30px;
      border-radius: 10px;
      position: absolute;
      top: 50%;
      left: 50%;
      width: 300px;
      -webkit-transform: translate(-50%, -50%);
      -moz-transform: translate(-50%, -50%);
      -ms-transform: translate(-50%, -50%);
      -o-transform: translate(-50%, -50%);
      transform: translate(-50%, -50%);
      -webkit-transition: -webkit-transform 300ms, box-shadow 300ms;
      -moz-transition: -moz-transform 300ms, box-shadow 300ms;
      transition: transform 300ms, box-shadow 300ms;
      box-shadow: 5px 10px 10px rgba(2, 128, 144, 0.2);
    }

    .login::before,
    .login::after {
      content: "";
      position: absolute;
      width: 600px;
      height: 600px;
      border-top-left-radius: 40%;
      border-top-right-radius: 45%;
      border-bottom-left-radius: 35%;
      border-bottom-right-radius: 40%;
      z-index: -1;
    }

    .login::before {
      left: 40%;
      bottom: -130%;
      background-color: rgba(69, 105, 144, 0.15);
      -webkit-animation: wawes 6s infinite linear;
      -moz-animation: wawes 6s infinite linear;
      animation: wawes 6s infinite linear;
    }

    .login::after {
      left: 20%;
      bottom: -120%;
      background-color: rgba(2, 128, 144, 0.2);
      -webkit-animation: wawes 7s infinite;
      -moz-animation: wawes 7s infinite;
      animation: wawes 7s infinite;
    }

    .login>input {
      font-family: "Asap", sans-serif;
      display: block;
      border-radius: 5px;
      font-size: 16px;
      background: white;
      width: 100%;
      border: 0;
      padding: 10px 10px;
      margin: 15px -10px;
    }

    .login>button {
      font-family: "Asap", sans-serif;
      cursor: pointer;
      color: #fff;
      font-size: 16px;
      text-transform: uppercase;
      width: 100%;
      border: 0;
      padding: 10px 0;
      margin-top: 10px;
      margin-left: -5px;
      border-radius: 5px;
      background-color: #f45b69;
      -webkit-transition: background-color 300ms;
      -moz-transition: background-color 300ms;
      transition: background-color 300ms;
    }

    .login>button:hover {
      background-color: #f24353;
    }

    @-webkit-keyframes wawes {
      from {
        -webkit-transform: rotate(0);
      }

      to {
        -webkit-transform: rotate(360deg);
      }
    }

    @-moz-keyframes wawes {
      from {
        -moz-transform: rotate(0);
      }

      to {
        -moz-transform: rotate(360deg);
      }
    }

    @keyframes wawes {
      from {
        -webkit-transform: rotate(0);
        -moz-transform: rotate(0);
        -ms-transform: rotate(0);
        -o-transform: rotate(0);
        transform: rotate(0);
      }

      to {
        -webkit-transform: rotate(360deg);
        -moz-transform: rotate(360deg);
        -ms-transform: rotate(360deg);
        -o-transform: rotate(360deg);
        transform: rotate(360deg);
      }
    }

    a {
      text-decoration: none;
      color: rgba(255, 255, 255, 0.6);
      position: absolute;
      right: 10px;
      bottom: 10px;
      font-size: 12px;
    }

    #topic {
      font-size: 1rem;
      font-weight: bold;
      margin-right: 1rem;

      /* left: 25%; */
      div {
        text-align: right;
      }
    }
  </style>
</head>

<body>
  <!-- <img src="img/logohz.png" id="logo" width="230"> -->


  <div class="login">
    <div id="topic">
      <div>งานประปา</div>
      <div id="err_message" class="org_name text-danger text-sm"></div>
    </div>
    <input type="text" name="username" id="username" value="twman1" placeholder="Username">
    <input type="password" name="password" id="password" value="tw1234" placeholder="Password">
    <button id="submit_btn">เข้าสู่ระบบ</button>
  </div>



  {{-- <script type="text/javascript" src="cordova.js"></script>
  <script type="text/javascript" src="js/index.js"></script> --}}
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
    $(document).on("click", "#submit_btn", async function () {

      window.localStorage.clear()
      window.localStorage.setItem("connect_status", 0);
      let params = {
        username: $("#username").val(),
        passwords: $("#password").val(),
        user_cate_id: 5,
      };
      let text = "";
      await $.post(`${API_URL}/api/users/authen`, params, function (response) {
        console.log('response', response)
        let res = JSON.parse(JSON.stringify(response));
        twman = res.data; 

        // window.localStorage.setItem("inv_period", JSON.stringify(res.inv_period))
        // window.localStorage.setItem("org", JSON.stringify(res.org))
        window.localStorage.setItem("twman", JSON.stringify(twman));


       // let logo = "";
        // if(res.org[0].id === 1){
        //  logo =  'logohz.png';
        // }else{
         // logo =  'khampom.png';
        // }
        // window.localStorage.setItem("logo", logo);
        if (res.code === 200 && res.data.rows > 0) {
         window.location.href = "twmanmobile/main";
        }else{
          $('#err_message').html('ชื่อผู้ใช้ หรือ รหัสผ่าน ไม่ถูกต้อง')
        }
      });
    }); //#login-button


    $(document).on('keyup', "#username", function(){
      $('#err_message').html("")
    })
    $(document).on('keyup', "#password", function(){
      $('#err_message').html("")
    })

    $(document).on("click", ".gotoRemainBtn", function () {
      window.location.href = "search_members.html";
    });

    $("#closebtn").click(function () {
      $("#modal").toggle();
    });

    $(document).ready( function () {
      $('#content').css('display', 'none')
    });



  </script>
</body>

</html>