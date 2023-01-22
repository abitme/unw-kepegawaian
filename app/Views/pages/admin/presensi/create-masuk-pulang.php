<?= $this->extend('layouts/admin'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
  <h1 class="h3 text-gray-800"><?= $title ?></h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('admin/artikel') ?>">Absen</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
    </ol>
  </nav>
</div>

<!-- Page Content -->
<div class="row">
  <div class="col-md-12 mb-card mx-auto">
    <div class="card shadow mb-3">

      <div class="card-body px-5 py-4">
        <form action="<?= $form_action ?>" method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="photo" id="photo">
          <input type="hidden" name="tipe" id="tipe">
          <input type="hidden" name="coord_latitude" id="coord_latitude">
          <input type="hidden" name="coord_longitude" id="coord_longitude">

          <div class="form-group">
            <div class="form-control webcam-start" id="webcam-control">
              <label class="form-switch">
                <input type="checkbox" id="webcam-switch">
                <i></i>
                <span id="webcam-caption">Click to Start Camera</span>
              </label>
            </div>
            <div id="errorMsg" class="col-12 col-md-6 alert-danger d-none">
              Fail to start camera, please allow permision to access camera. <br />
              If you are browsing through social media built in browsers, you would need to open the page in Sarafi (iPhone)/ Chrome (Android)
              <button id="closeError" class="btn btn-primary ml-3">OK</button>
            </div>
            <div class="md-effect-12">
              <div id="app-panel" class="app-panel md-content row p-0 m-0">
                <div id="webcam-container" class="webcam-container col-12 p-0 m-0">
                  <video id="webcam" autoplay playsinline width="360"></video>
                  <canvas id="canvas" style="object-fit: cover;"></canvas>
                  <div class="flash"></div>
                  <!-- <audio id="snapSound" src="audio/snap.wav" preload="auto"></audio> -->
                </div>
                <div id="cameraControls" class="cameraControls">
                  <a href="#" id="exit-app" title="Exit App" class="d-none"><i class="material-icons">exit_to_app</i></a>
                  <a href="#" id="take-photo" title="Take Photo" class="d-none"><i class="material-icons">camera_alt</i></a>
                  <a href="#" id="cameraFlip" title="Flip Camera" class="d-none"><i class="material-icons">flip_camera_ios</i></a>
                  <a href="#" id="download-photo" download="selfie.png" target="_blank" title="Save Photo" class="d-none"><i class="material-icons">file_download</i></a>
                  <a href="#" id="resume-camera" title="Resume Camera" class="d-none"><i class="material-icons">camera_front</i></a>
                </div>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary float-right  ml-2" style="width: 100px;">Simpan</button>
          <a href="<?= base_url('absen') ?>" class="btn btn-back float-right ml-2" style="width: 100px;">Kembali</a>
        </form>
      </div>

    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('append-style'); ?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<script src="<?= base_url() ?>/assets/backend/libs/webcam-easy/webcam-easy.min.js"></script>
<script>
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  document.getElementById('tipe').value = urlParams.get('tipe');

  // let formData = new FormData();

  // https://stackoverflow.com/questions/39854392/attach-file-to-input-type-file-with-javascript
  // $('form').submit(function(e) {

  //   e.preventDefault();
  //   // var request = new XMLHttpRequest();
  //   // request.open("POST", <?= json_encode(base_url("$form_action")) ?>);
  //   // request.send(formData);
  // });


  const webcamElement = document.getElementById('webcam');

  const canvasElement = document.getElementById('canvas');

  // const snapSoundElement = document.getElementById('snapSound');

  const webcam = new Webcam(webcamElement, 'user', canvasElement);

  // webcam.start()
  //   .then(result => {
  //     cameraStarted();
  //     console.log("webcam started");
  //   })
  //   .catch(err => {
  //     displayError();
  //     $("#webcam-switch").prop('checked', false);
  //   });

  $("#webcam-switch").change(function() {
    if (this.checked) {
      // $('.md-modal').addClass('md-show');
      webcam.start()
        .then(result => {
          cameraStarted();
          console.log("webcam started");
        })
        .catch(err => {
          displayError();
        });
    } else {
      cameraStopped();
      webcam.stop();
      console.log("webcam stopped");
    }
  });

  $('#cameraFlip').click(function() {
    webcam.flip();
    webcam.start();
  });

  $('#closeError').click(function() {
    $("#webcam-switch").prop('checked', false).change();
  });

  function displayError(err = '') {
    if (err != '') {
      $("#errorMsg").html(err);
    }
    $("#errorMsg").removeClass("d-none");
  }

  function cameraStarted() {
    $("#errorMsg").addClass("d-none");
    $('.flash').hide();
    $("#webcam-caption").html("on");
    $("#webcam-control").removeClass("webcam-off");
    $("#webcam-control").addClass("webcam-on");
    // $(".webcam-container").removeClass("d-none");
    $("#take-photo").removeClass("d-none");
    if (webcam.webcamList.length > 1) {
      $("#cameraFlip").removeClass('d-none');
    }
    $("#wpfront-scroll-top-container").addClass("d-none");
    window.scrollTo(0, 0);
    // $('body').css('overflow-y', 'hidden');
  }

  function cameraStopped() {
    $("#errorMsg").addClass("d-none");
    $("#wpfront-scroll-top-container").removeClass("d-none");
    $("#webcam-control").removeClass("webcam-on");
    $("#webcam-control").addClass("webcam-off");
    $("#cameraFlip").addClass('d-none');
    // $(".webcam-container").addClass("d-none");
    $("#take-photo").addClass("d-none");
    $("#webcam-caption").html("Click to Start Camera");
    // $('.md-modal').removeClass('md-show');
  }


  $("#take-photo").click(function() {
    beforeTakePhoto();
    let picture = webcam.snap();
    document.querySelector('#download-photo').href = picture;
    afterTakePhoto();
  });

  function beforeTakePhoto() {
    $('.flash')
      .show()
      .animate({
        opacity: 0.3
      }, 500)
      .fadeOut(500)
      .css({
        'opacity': 0.7
      });
    window.scrollTo(0, 0);
    // $('#webcam-control').addClass('d-none');
    // $('#cameraControls').addClass('d-none');
  }

  function afterTakePhoto() {
    // webcam.stop();
    // $('#canvas').removeClass('d-none');
    // $('#take-photo').addClass('d-none');
    // $('#exit-app').removeClass('d-none');
    // $('#download-photo').removeClass('d-none');
    // $('#resume-camera').removeClass('d-none');
    // $('#cameraControls').removeClass('d-none');

    // Base64 String
    imageBase64 = document.querySelector("#canvas").toDataURL().replace(/^data:image\/png;base64,/, "");
    document.getElementById('photo').value = imageBase64;

    // // PNG file
    // let file = null;
    // let blob = document.querySelector("#canvas").toBlob(function(blob) {
    //   file = new File([blob], 'test.png', {
    //     type: 'image/png'
    //   });;
    //   // append to formdata
    //   formData.append('file', file);
    // }, 'image/png');
  }

  function removeCapture() {
    $('#canvas').addClass('d-none');
    $('#webcam-control').removeClass('d-none');
    $('#cameraControls').removeClass('d-none');
    $('#take-photo').removeClass('d-none');
    $('#exit-app').addClass('d-none');
    $('#download-photo').addClass('d-none');
    $('#resume-camera').addClass('d-none');
  }

  $("#resume-camera").click(function() {
    webcam.stream()
      .then(facingMode => {
        removeCapture();
      });
  });

  $("#exit-app").click(function() {
    removeCapture();
    $("#webcam-switch").prop("checked", false).change();
  });

  function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
      alert("Geolocation is not supported by this browser.");
    }
  }

  function showPosition(position) {
    document.getElementById('coord_latitude').value = position.coords.latitude;
    document.getElementById('coord_longitude').value = position.coords.longitude;
  }

  function showError(error) {
    if (error.code == 1) {
      alert("Akses lokasi tidak diaktifkan. Mohon aktifkan akses lokasi agar dapat melakukan absen.");
      window.location.reload();
    } else if (error.code == 2) {
      alert("The network is down or the positioning service can't be reached.");
    } else if (error.code == 3) {
      alert("The attempt timed out before it could get the location data.");
    } else {
      alert("Geolocation failed due to unknown error.");
    }
  }

  getLocation();
</script>
<?= $this->endSection(); ?>