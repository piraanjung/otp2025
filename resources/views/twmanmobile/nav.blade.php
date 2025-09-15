<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .widget-user .widget-user-image {
    left: 50%;
    margin-left: -45px;
    position: absolute;
    top: 100px !important;
}
</style>
<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fa fa-bars"></i></a>
      </li>
    </ul>
  </nav>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <nav>
      <div class="card card-widget widget-user">
        <div class="widget-user-image">
            <img class="img-circle elevation-2 orglogo" src="{{asset('/logo/khampom.png')}}" alt="User Avatar">
          </div>
        <div class="widget-user-header bg-info">
          <h3 class="widget-user-username">ประปา</h3>
          <h5 class="widget-user-desc org_type">อบต.</h5>
          <h5 class="widget-user-desc org_name">ขามป้อม</h5>
        </div>
        

      </div>
      <ul class="nav sidebar-nav">
        <div class="sidebar-header">
          <div class="sidebar-brand">

          </div>
        </div>
        <li style="list-style-type:none">&nbsp;</li>
        <li style="list-style-type:none">&nbsp;</li>
        <li style="list-style-type:none">&nbsp;</li>


      </ul>
      <div class="row p-3">
        <div class="col-12"><a href="main.html" class="btn btn-info btn-block mt-3">หน้าหลัก</a></div>
        <div class="col-12"><a href="cutmeter.html" class="btn btn-warning btn-block mt-3">ตัดมิเตอร์</a></div>
        <div class="col-12"><a href="installmeter.html" class="btn btn-success btn-block mt-3">ติดตั้งมิเตอร์</a>
        </div>
        <div class="col-12"><a href="index.html" class="btn btn-danger btn-block mt-3">ออกจากระบบ</a></div>
      </div>
    </nav>
  </aside>


      <script type="text/javascript" src="{{asset('/soft-ui/assets/js/jquery-3.7.0.js')}}"></script>

  <script src="{{asset('/js/my.js')}}"></script>
  <script>

    $(document).ready(function () {
        let org = JSON.parse(window.localStorage.getItem('org'));
        console.log(org)
        set_org_infos(org)
    })
  </script>