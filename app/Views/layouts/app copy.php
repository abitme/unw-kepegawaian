<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="x-ua-compatible" content="ie=edge" />
  <title><?= $this->renderSection('title'); ?></title>
  <meta name="description" content="" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="shortcut icon" type="image/x-icon" href="<?= base_url() ?>/assets/img/logo-unw-favicon.png" />
  <!-- Place favicon.ico in the root directory -->

  <!-- Web Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- ========================= CSS here ========================= -->
  <link rel="stylesheet" href="<?= base_url() ?>/assets/frontend/bizfinity/css/bootstrap.min.css" />
  <link rel="stylesheet" href="<?= base_url() ?>/assets/frontend/bizfinity/css/LineIcons.2.0.css" />
  <link rel="stylesheet" href="<?= base_url() ?>/assets/frontend/bizfinity/css/animate.css" />
  <link rel="stylesheet" href="<?= base_url() ?>/assets/frontend/bizfinity/css/tiny-slider.css" />
  <link rel="stylesheet" href="<?= base_url() ?>/assets/frontend/bizfinity/css/glightbox.min.css" />
  <link rel="stylesheet" href="<?= base_url() ?>/assets/frontend/bizfinity/css/main.css" />
  <link rel="stylesheet" href="<?= base_url() ?>/assets/frontend/bizfinity/css/reset.css" />
  <link rel="stylesheet" href="<?= base_url() ?>/assets/frontend/bizfinity/css/responsive.css" />

  <!-- <link href="<?= base_url() ?>/assets/frontend/libs/sbadmin2/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css"> -->

  <!-- Jquery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="<?= base_url() ?>/assets/frontend/vendor/jquery/jquery-ui.css" rel="stylesheet">
  <script src="<?= base_url() ?>/assets/frontend/vendor/jquery/jquery-ui.min.js"></script>

  <?= $this->renderSection('append-style'); ?>

  <!-- Ai css-->
  <link href="<?= base_url() ?>/assets/frontend/css/ai.css?version=0.1" rel="stylesheet">
</head>

<body>
  <!--[if lte IE 9]>
      <p class="browserupgrade">
        You are using an <strong>outdated</strong> browser. Please
        <a href="https://browsehappy.com/">upgrade your browser</a> to improve
        your experience and security.
      </p>
    <![endif]-->

  <!-- Preloader -->
  <div class="preloader">
    <div class="preloader-inner">
      <div class="preloader-icon">
        <span></span>
        <span></span>
      </div>
    </div>
  </div>
  <!-- /End Preloader -->

  <!-- ========================= header start ========================= -->
  <header class="header navbar-area">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-12">
          <div class="nav-inner">
            <nav class="navbar navbar-expand-lg">
              <a class="navbar-brand" href="index.html">
                <img src="<?= base_url() ?>/assets/img/logo.svg" alt="Logo" class="py-2">
              </a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="toggler-icon"></span>
                <span class="toggler-icon"></span>
                <span class="toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                <ul id="nav" class="navbar-nav ms-auto">
                  <!-- <li class="nav-item" style="visibility: hidden;">
                    <a class="page-scroll active" href="index.html">Home</a>
                  </li> -->
                  <!-- <li class="nav-item">
                    <a class="page-scroll" href="about-us.html">About Us</a>
                  </li>
                  <li class="nav-item">
                    <a class="page-scroll" href="javascript:void(0)">Services</a>
                  </li>
                  <li class="nav-item">
                    <a class="page-scroll" href="javascript:void(0)">Portfolio</a>
                  </li>
                  <li class="nav-item">
                    <a class="page-scroll dd-menu collapsed" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#submenu-1-1" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">Pages</a>
                    <ul class="sub-menu collapse" id="submenu-1-1">
                      <li class="nav-item"><a href="about-us.html">About Us</a></li>
                    </ul>
                  </li>
                  <li class="nav-item">
                    <a class="page-scroll" href="javascript:void(0)">Blog</a>
                  </li>
                  <li class="nav-item">
                    <a class="page-scroll" href="javascript:void(0)">Contact</a>
                  </li> -->
                </ul>
              </div> <!-- navbar collapse -->
              <!-- <div class="button">
                <a href="javascript:void(0)" class="btn white-bg mouse-dir">Get a Quote <span class="dir-part"></span></a>
              </div> -->
            </nav> <!-- navbar -->
          </div>
        </div>
      </div> <!-- row -->
    </div> <!-- container -->

  </header>
  <!-- ========================= header end ========================= -->

  <!-- Content -->
  <?= $this->renderSection('content'); ?>

  <!-- Start Footer Area -->
  <footer class="footer">
    <!-- Start Footer Bottom -->
    <div class="footer-bottom">
      <div class="container">
        <div class="inner">
          <div class="row">
            <div class="col-lg-6 col-md-6 col-12">
              <div class="left">
                <p>Universitas Ngudi Waluyo</a></p>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-12">
              <div class="right">
                <p>All Right Reserved</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Footer Middle -->
  </footer>
  <!--/ End Footer Area -->

  <!-- ========================= scroll-top ========================= -->
  <a href="#" class="scroll-top btn-hover">
    <i class="lni lni-chevron-up"></i>
  </a>

  <!-- ========================= JS here ========================= -->
  <?= $this->renderSection('prepend-script'); ?>

  <script src="<?= base_url() ?>/assets/frontend/bizfinity/js/bootstrap.min.js"></script>
  <script src="<?= base_url() ?>/assets/frontend/bizfinity/js/main.js"></script>
  <!-- Sweet ALert -->
  <script src="<?= base_url() ?>/assets/frontend/vendor/sweetalert/sweetalert2.all.min.js"></script>

  <?= $this->renderSection('append-script'); ?>

  <!-- Ai Js -->
  <script src="<?= base_url() ?>/assets/frontend/js/ai.js"></script>
</body>

</html>