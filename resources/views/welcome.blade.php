<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>OPT-ConnecT</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.css">
  <link rel="stylesheet" href="{{ asset('Applight/css/animate.css')}}">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
    integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="{{ asset('Applight/style.css')}}" />

<link href="https://fonts.googleapis.com/css2?family=Bruno+Ace+SC&family=Sarabun:ital,wght@0,500;0,700;1,400;1,500&display=swap" rel="stylesheet">

<style>
  .aa{
    background-image: url("{{asset('imgs/iotrash1.png')}}");
    background-repeat: no-repeat;
    background-size: 100% 100%;
  }
  .disabled-section{
    display: none
  }
</style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
        
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"> <span
          class="fas fa-bars"></span> </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item"> <a class="nav-link" href="" data-scroll-nav="0">Home</a> </li>
          <li class="nav-item"> <a class="nav-link" href="#" data-scroll-nav="1">งานประปา</a> </li>
          <li class="nav-item"> <a class="nav-link" href="#" data-scroll-nav="2">ธนาคารขยะรีไซเคิล</a> </li>
          <li class="nav-item"> <a class="nav-link" href="#" data-scroll-nav="3">ธนาคารขยะเปียก</a> </li>
          <li class="nav-item"> <a class="nav-link" href="#" data-scroll-nav="4">ธนาคารชุมชนออมทรัพย์</a> </li>
          <li class="nav-item"> <a class="nav-link" href="#" data-scroll-nav="5">จัดเก็บค่าถังขยะรายปี</a> </li>
          {{-- <li class="nav-item"> <a class="nav-link" href="#" data-scroll-nav="5">Faq</a> </li> --}}
          <li class="nav-item"> <a class="nav-link" href="#" data-scroll-nav="7">ติดต่อเรา</a> </li>
          <li class="nav-item"> <a class="nav-link" href="{{route('login')}}">Login</a> </li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- End Navbar -->
  <!-------Banner Start------->
  
  <div id="otp-connect">
            <div class="icon-box wow fadeInUp" data-wow-delay="0.2s">
              OPT-CONECT
              <hr style="margin-bottom: 3px;margin-top: 3px;">
              <div id="org_addr">พัฒนาชุมชน เชื่อมใจ ให้ใกล้กัน</div>
            </div>
          </div>
   <div id="org">
            <div class="icon-box wow fadeInUp" data-wow-delay="0.6s">
          
              <div>ระบบบริหารจัดการ</div>
              <div>องค์การบริหารส่วนท้องถิ่น</div>
            
            </div>
          </div>
  <section class="banner" data-scroll-index='0'>
    <div class="banner-overlay">
      <div class="container">
        <div class="main-container centralized ">
         
          <div class="main-circle">
            <div class="inner centralized">
              ระบบบริหารจัดการ
            </div>
          </div>
          <div class="bubble-container centralized blue-dark">
            <a href="#">
              <div class="bubble centralized">
                <div class="inner centralized">
                  งานประปา
                </div>
              </div>
            </a>
          </div>
           <div class="bubble-container centralized blue-light">

            <div class="bubble centralized">
              <div class="inner centralized">
                ค่าจัดการ<br>ถังขยะรายปี
              </div>
            </div>
          </div>
          <div class="bubble-container centralized green">
            <a href="">
              <div class="bubble centralized">
                <div class="inner centralized">
                  ธนาคาร<br>ขยะรีไซเคิล
                </div>
              </div>
            </a>
          </div>
          <div class="bubble-container centralized orange">
            <a href="">
              <div class="bubble centralized">
                <div class="inner centralized">
                  จัดเก็บ<br>ถังขยะรายปี
                </div>
              </div>
            </a>
          </div>
          <div class="bubble-container centralized red">

            <div class="bubble centralized">
              <div class="inner centralized">
                ถังหมัก<br>เศษอาหาร
              </div>
            </div>
          </div>
          <div class="bubble-container centralized black">

            <div class="bubble centralized">
              <div class="inner centralized">
                ธนาคาร<br>ชุมชน<br>ออมทรัพย์
              </div>
            </div>
          </div>
          {{-- <div class="bubble-container centralized blue-light">

            <div class="bubble centralized">
              <div class="inner centralized">
                ผู้ดูแลระบบ
              </div>
            </div>
          </div> --}}
        </div>
      </div>
    </div>
  </section>

  <!-------Banner End------->

  <!-------About End------->

  <section class="about section-padding prelative" data-scroll-index='1'>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="sectioner-header text-center">
            <h3>จดมิเตอร์ประปา</h3>
            {{-- <span class="line"></span>
            <p>Sed quis nisi nisi. Proin consectetur porttitor dui sit amet viverra. Fusce sit amet lorem faucibus,
              vestibulum ante in, pharetra ante.</p> --}}
          </div>
          <div class="text-center">
            <div class="row">
              <div class="col-md-12">
                <div class="icon-box wow fadeInUp" data-wow-delay="0.2s">
                  <img src="{{asset('imgs/tabwater.png')}}" width="100%" height="680px" alt="">

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-------About End------->

  <!-------Video Start------->
  {{-- <section class="video-section prelative text-center white">
    <div class="section-padding video-overlay">
      <div class="container">
        <h3>Watch Now</h3>
        <i class="fa fa-play" id="video-icon" aria-hidden="true"></i>
        <div class="video-popup">
          <div class="video-src">
            <div class="iframe-src">
             <iframe width="560" height="315" src="https://www.youtube.com/embed/L051YSpEEYU?si=0TcEcmtDgVcdmu6f" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>          </div>
        </div>
      </div>
    </div>
  </section> --}}
  <!-------Video End------->

  <!-------Features Start------->
  <section class="feature section-padding" data-scroll-index='2'>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="sectioner-header text-center">
            <h3>ธนาคารขยะรีไซเคิล</h3>
            <span class="line"></span>
            <p>Sed quis nisi nisi. Proin consectetur porttitor dui sit amet viverra. Fusce sit amet lorem faucibus,
              vestibulum ante in, pharetra ante.</p>
          </div>
          <div class="section-content text-center">
            <div class="row">
              <div class="col-md-12">
            <img src="{{ asset('imgs/recycle.png') }}" width="100%" alt="">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-------Features End------->

  <!-------Team Start------->
  <section class="team section-padding" data-scroll-index='3'>
    <div class="container">
      <div class="row aa" >
        <div class="col-md-12">
          <div class="sectioner-header text-center">
            <h3>ธนาคารขยะเปียก</h3>
            <span class="line"></span>
            <p><span style="font-size: 2rem; font-weigth:bold;  color: black;">SmartWaste</span> ถังหมัก AIroTrash
              และระบบธนาคารขยะเปียกครบวงจร</p>
          </div>
          <div class="section-content text-center">
            <div class="row">
              <div class="col-md-4">
                <div class="icon-box wow fadeInUp" data-wow-delay="0.2s">
                  <img src="{{asset('imgs/iotrash.png')}}"width="350px" height="450px" alt="">
                  {{-- <h5>Support</h5> --}}
                </div>
              </div>
              <div class="col-md-4">
                <div class="icon-box wow fadeInUp" data-wow-delay="0.4s">
                  <img src="{{asset('imgs/iot.png')}}"width="350px" height="450px" alt="">

                  {{-- <h5>Cross Platform</h5> --}}

                </div>
              </div>
              <div class="col-md-4">
                <div class="icon-box wow fadeInUp" data-wow-delay="0.6s">
                  <img src="{{asset('imgs/iot_web.png')}}" width="350px" height="450px" alt="">
                  {{-- <h5>Fast</h5> --}}

                </div>
              </div>
            </div>
           
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-------Team End------->

  <!-------Testimonial Start------->
  <section class="testimonial section-padding" data-scroll-index='4'>
    <div class="container">
    
            <div class="row" >
              <div class="col-md-12">
                <div class="sectioner-header text-center">
                  <h3>ธนาคารชุมชนออมทรัพย์</h3>
                  <span class="line"></span>
                  <p><span style="font-size: 1.5rem; font-weigth:bold;  color: black;">สร้างรายได้  ใช้จ่ายภายในชุมชน
                    </span></p>
                </div>
                <div class="section-content text-center">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="icon-box wow fadeInUp" data-wow-delay="0.2s">
                        <img src="{{asset('imgs/bookbank.png')}}"width="100%" height="100%" alt="">
                        {{-- <h5>Support</h5> --}}
                      </div>
                    </div>
                    <div class="col-md-8">
                      <div class="icon-box wow fadeInUp" data-wow-delay="0.4s">
                        <img src="{{asset('imgs/buystore.png')}}"width="100%" height="100%" alt="">

                        {{-- <h5>Cross Platform</h5> --}}

                      </div>
                    </div>
                    {{-- <div class="col-md-4">
                      <div class="icon-box wow fadeInUp" data-wow-delay="0.6s">
                        <img src="{{asset('imgs/iot_web.png')}}" width="350px" height="450px" alt="">
                        {{-- <h5>Fast</h5> --}}

                      </div>
                    </div> 
                  </div>
                
                </div>
              </div>
            </div>
         
    </div>
  </section>

  <!-------Testimonial End------->

  <!-------FAQ Start------->
  <section class="faq section-padding prelative" data-scroll-index='5'>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="sectioner-header text-center">
            <h3>ค่าจัดการถังขยะรายปี</h3>
            <span class="line"></span>
            <p>Sed quis nisi nisi. Proin consectetur porttitor dui sit amet viverra. Fusce sit amet lorem faucibus,
              vestibulum ante in, pharetra ante.</p>
          </div>
          <div class="section-content">
            <img src="{{asset('imgs/map.png')}}" width="100%" alt="">
           
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-------FAQ End------->

 

  <section class="contact section-padding" data-scroll-index='7'>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="sectioner-header text-center">
            <h3>ติดต่อเรา</h3>
            <span class="line"></span>
            <p>Sed quis nisi nisi. Proin consectetur porttitor dui sit amet viverra. Fusce sit amet lorem faucibus,
              vestibulum ante in, pharetra ante.</p>
          </div>
          <div class="section-content">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-8">
                <form id="contact_form" name="aa" action="{{ route('login') }}" method="POST">
                  @csrf
                  <div class="row">
                    <div class="col">
                      <input type="text" id="your_name" class="form-input w-100" value=""
                        name="usernae" placeholder="Username" required>
                    </div>
                    <div class="col">
                      <input type="password" id="password" class="form-input w-100" value="" name="password"
                        placeholder="password" required>
                    </div>
                  </div>
                   <button class="btn-grad w-100 text-uppercase" type="submit" name="buttond">submit</button>
                </form>
              </div>
              <div class="col-sm-12 col-md-12 col-lg-4">
                <div class="contact-info white">
                  <div class="contact-item media"> <i class="fa fa-map-marker-alt media-left media-right-margin"></i>
                    <div class="media-body">
                      <p class="text-uppercase">Address</p>
                      <p class="text-uppercase">New Delhi, India</p>
                    </div>
                  </div>
                  <div class="contact-item media"> <i class="fa fa-mobile media-left media-right-margin"></i>
                    <div class="media-body">
                      <p class="text-uppercase">Phone</p>
                      <p class="text-uppercase"><a class="text-white" href="tel:+15173977100">009900990099</a> </p>
                    </div>
                  </div>
                  <div class="contact-item media"> <i class="fa fa-envelope media-left media-right-margin"></i>
                    <div class="media-body">
                      <p class="text-uppercase">E-mail</p>
                      <p class="text-uppercase"><a class="text-white"
                          href="mailto:abcdefg@gmail.com">yogeshsingh.now@gmail.com</a> </p>
                    </div>
                  </div>
                  <div class="contact-item media"> <i class="fa fa-clock media-left media-right-margin"></i>
                    <div class="media-body">
                      <p class="text-uppercase">Working Hours</p>
                      <p class="text-uppercase">Mon-Fri 9.00 AM to 5.00PM.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-------Download End------->
  {{-- <section class="download section-padding">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="sectioner-header text-center white">
            <h3>Download Our App</h3>
            <span class="line"></span>
            <p class="white">Sed quis nisi nisi. Proin consectetur porttitor dui sit amet viverra. Fusce sit amet lorem
              faucibus, vestibulum ante in, pharetra ante.</p>
          </div>
        </div>
        <div class="col-md-12">
          <div class="section-content text-center">
            <ul>
              <li><a href="#"><img src="{{ asset('Applight/images/appstore.png')}}" class="wow fadeInUp"
                    data-wow-delay="0.4s" /></a></li>
              <li><a href="#"><img src="{{ asset('Applight/images/playstore.png')}}" class="wow fadeInUp"
                    data-wow-delay="0.7s" /></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section> --}}

  <!-------Download End------->

  <footer class="footer-copy">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <p>2018 &copy; Applight. Website Designed by <a href="http://w3Template.com" target="_blank" rel="dofollow">W3
              Template</a></p>
        </div>
      </div>
    </div>
  </footer>


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"
    integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script>
  <!-- scrollIt js -->
  <script src="{{ asset('Applight/js/scrollIt.min.js')}}"></script>
  <script src="{{ asset('Applight/js/wow.min.js')}}"></script>
  <script>
    wow = new WOW();
    wow.init();
    $(document).ready(function (e) {

      $('#video-icon').on('click', function (e) {
        e.preventDefault();
        $('.video-popup').css('display', 'flex');
        $('.iframe-src').slideDown();
      });
      $('.video-popup').on('click', function (e) {
        var $target = e.target.nodeName;
        var video_src = $(this).find('iframe').attr('src');
        if ($target != 'IFRAME') {
          $('.video-popup').fadeOut();
          $('.iframe-src').slideUp();
          $('.video-popup iframe').attr('src', " ");
          $('.video-popup iframe').attr('src', video_src);
        }
      });

      $('.slider').bxSlider({
        pager: false
      });
    });

    $(window).on("scroll", function () {

      var bodyScroll = $(window).scrollTop(),
        navbar = $(".navbar");

      if (bodyScroll > 50) {
        $('.navbar-logo img').attr('src', 'images/logo-black.png');
        navbar.addClass("nav-scroll");

      } else {
        $('.navbar-logo img').attr('src', 'images/logo.png');
        navbar.removeClass("nav-scroll");
      }

    });
    $(window).on("load", function () {
      var bodyScroll = $(window).scrollTop(),
        navbar = $(".navbar");

      if (bodyScroll > 50) {
        $('.navbar-logo img').attr('src', 'images/logo-black.png');
        navbar.addClass("nav-scroll");
      } else {
        $('.navbar-logo img').attr('src', 'images/logo-white.png');
        navbar.removeClass("nav-scroll");
      }

      $.scrollIt({

        easing: 'swing',      // the easing function for animation
        scrollTime: 900,       // how long (in ms) the animation takes
        activeClass: 'active', // class given to the active nav element
        onPageChange: null,    // function(pageIndex) that is called when page is changed
        topOffset: -63
      });
    });

  </script>
  <script>
    $(document).ready(function () {
      var bubbleList = $('.bubble-container');
      const bubbleCount = bubbleList.length;
      const degStep = 180 / (bubbleCount - 1);

      $('.bubble-container').each((index) => {
        const deg = index * degStep;
        const invertDeg = deg * -1;

        $(bubbleList[index]).css('transform', `rotate(${deg}deg)`);
        $(bubbleList[index]).css('opacity', `1`);
        $(bubbleList[index]).find('.bubble').css('transform', `rotate(${invertDeg}deg)`);
      })
    })
  </script>
</body>

</html>