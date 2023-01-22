<?= $this->extend('layouts/app'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- alert -->
<?= $this->include('includes/_alertSweet') ?>

<!-- Start Service Area -->
<!-- <section class="section">
  <div class="container">

  </div>
</section> -->

<section class="section" style="padding-top: 32px;">
  <div class="container">
    <div class="row">
      <div class="col-12 col-lg-8 offset-lg-2 col-12">
        <div class="section-title" style="margin-bottom: 22px;">
          <!-- <span class="wow fadeInDown" data-wow-delay=".2s">What We Offer You</span> -->
          <h2 class="wow fadeInUp" data-wow-delay=".4s">Presensi Online UNW</h2>
          <!-- <p class="wow fadeInUp" data-wow-delay=".6s">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form.</p> -->
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-lg-8 mx-auto">
        <div class="col-12 alert-danger px-4 py-2 mb-2 ">
          Catatan : <br />
          <ol type="1" style="list-style-type: auto">
            <li>Presensi/absen wajib menampilkan foto selfie saat melakukan presensi/absen</li>
            <li>Mohon untuk tidak lupa melakukan presensi/absen <br> (Jika lupa presensi/absen, mohon untuk isi form lupa presensi/absen) </li>
          </ol>
        </div>
        <div id="errorLocation" class="col-12 alert-danger px-4 py-2 mb-2 d-none ">
          Gagal Menemukan Lokasi <br />
          Mohon aktifkan akses lokasi dan refresh web ini agar dapat melakukan presensi.
        </div>
        <div id="errorLocation" class="col-12 alert-danger px-4 py-2 mb-2 d-none ">
          Gagal Menemukan Lokasi <br />
          Mohon aktifkan akses lokasi dan refresh web ini agar dapat melakukan presensi.
        </div>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#aiModal" style="width: 80px;">
          <i class="lni lni-book"></i> Tips
        </button>
        <form action="<?= base_url('presensi/create') ?>" method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="photo" id="photo">
          <input type="hidden" name="tipe" id="tipe">
          <input type="hidden" name="coord_latitude" id="coord_latitude">
          <input type="hidden" name="coord_longitude" id="coord_longitude">
          <div class="form-group mb-2">
            <label for="nik">NIK</label>
            <input type="number" class="form-control <?= ($validation->hasError('nik')) ? 'is-invalid' : '' ?>" id="nik" name="nik" value="<?= set_value('nik') ?>" placeholder="Masukkan minimal 3 karakter NIK pegawai tanpa titik (.)" required>
            <div class="invalid-feedback"><?= $validation->getError('nik') ?></div>
            <small id="nama" class="text-primary d-none">Nama Pegawai : </small>
          </div>

          <div class="form-group mb-2">
            <div class="md-effect-12">
              <div id="app-panel" class="app-panel md-content row">
                <div id="webcam-container" class="webcam-container col-12 col-lg-6 text-center">
                  <video id="webcam" autoplay playsinline style="width: 100%;height: auto;"></video>
                  <div class="flash"></div>
                  <!-- <audio id="snapSound" src="audio/snap.wav" preload="auto"></audio> -->
                  <div id="cameraControls" class="cameraControls text-center">
                    <a href="#!" id="exit-app" title="Exit App" class="d-none" onclick="event.preventDefault()"><i class="material-icons" style="font-size: 44px;">exit_to_app</i></a>
                    <a href="#!" id="take-photo" title="Take Photo" class="d-none" onclick="event.preventDefault()"><i class="material-icons" style="font-size: 44px;">camera_alt</i></a>
                    <!-- <a href="#!" id="cameraFlip" title="Flip Camera" class="d-none" onclick="event.preventDefault()"><i class="material-icons" style="font-size: 44px;">flip_camera_ios</i></a> -->
                    <a href="#!" id="download-photo" download="selfie.png" target="_blank" title="Save Photo" class="d-none" onclick="event.preventDefault()"><i class="material-icons" style="font-size: 44px;">file_download</i></a>
                    <a href="#!" id="resume-camera" title="Resume Camera" class="d-none" onclick="event.preventDefault()"><i class="material-icons" style="font-size: 44px;">camera_front</i></a>
                  </div>
                </div>
                <div class="col-12 col-lg-6 text-center">
                  <canvas id="canvas" style="width: 100%;height: auto;"></canvas>
                </div>
              </div>
            </div>
            <small class="text-danger"><?= $validation->getError('photo') ?></small>
          </div>

          <div id="button-submit" class="text-center">
            <button type="submit" name="tipe" value="Masuk" class="btn btn-primary">Masuk</button>
            <button type="submit" name="tipe" value="Pulang" class="btn btn-success">Pulang</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<section class="services section" style="padding-top: 32px;">
  <div class="container">
    <div class="row">
      <div class="col-12 col-lg-8 offset-lg-2 col-12">
        <div class="section-title" style="margin-bottom: 22px;">
          <!-- <span class="wow fadeInDown" data-wow-delay=".2s">What We Offer You</span> -->
          <h2 class="wow fadeInUp" data-wow-delay=".4s">Daftar Hadir</h2>
          <!-- <p class="wow fadeInUp" data-wow-delay=".6s">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form.</p> -->
        </div>
      </div>
    </div>
    <div class="row table-responsive">
      <div class="col">
        <table id="myTable" class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Pegawai</th>
              <th>Foto</th>
              <th>Tipe</th>
              <th>Waktu</th>
              <th>Status</th>
              <th>Lokasi</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
<!-- /End Services Area -->

<!-- Modal -->
<div class="modal fade" id="aiModal" tabindex="-1" aria-labelledby="aiModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalLabel">Tips</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6>Perangkat terdeteksi diluar UNW</h6>
        <p>Jika presensi gagal karena perangkat terdeteksi diluar UNW buka terlebih dahulu maps lalu pastikan titik lokasi sudah berada di UNW</p>
        <img src="<?= base_url() ?>/assets/img/tips1.png" alt="">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('append-style'); ?>
<!-- datatable -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<!-- icon google font -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<!-- datatable -->
<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
<!-- webcam -->
<script src="<?= base_url() ?>/assets/backend/libs/webcam-easy/webcam-easy.min.js"></script>
<script>
  $(document).ready(function() {
    // datatable
    table = $('#myTable').DataTable({
      processing: true,
      serverSide: true,
      order: [],
      ajax: {
        url: "<?= base_url('presensi/ajax_list') ?>",
        type: "POST"
      },
      //optional
      // lengthMenu: [
      //   [10, 50, 100],
      //   [10, 50, 100]
      // ],
      columnDefs: [{
        targets: [0, 5, 6],
        orderable: false,
      }, ],
      fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        if (aData[7] == 'Tidak Valid') {
          $(nRow).addClass('alert alert-danger');
        }
      },
    });

    // autocomplete
    // autocomplete id
    $("#nik").autocomplete({
        source: '<?= base_url('presensi/search-nik') ?>',
        select: function(event, ui) {
          $("#nik").val(ui.item.nik);
          $('#nama').removeClass('d-none');
          $("#nama").html('Nama Pegawai : ' + ui.item.nama);
          return false;
        }
      })
      .autocomplete("instance")._renderItem = function(ul, item) {
        $('#nama').addClass('d-none');
        return $("<li>")
          .append("<div>" + "(" + item.nik + ") " + item.nama + "</div>")
          .appendTo(ul);
      };
  });

  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  document.getElementById('tipe').value = urlParams.get('tipe');

  const webcamElement = document.getElementById('webcam');

  const canvasElement = document.getElementById('canvas');

  // const snapSoundElement = document.getElementById('snapSound');

  const webcam = new Webcam(webcamElement, 'user', canvasElement);

  // webcam.stop();

  webcam.start()
    .then(result => {
      cameraStarted();
      console.log("webcam started");
    })
    .catch(err => {
      displayError();
      $("#webcam-switch").prop('checked', false);
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
    $('#button-submit').removeClass('d-none');
    $('#button-submit').addClass('d-none');
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
    // window.scrollTo(0, 0);
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
    // window.scrollTo(0, 0);
  }

  function afterTakePhoto() {
    // Base64 String
    imageBase64 = document.querySelector("#canvas").toDataURL().replace(/^data:image\/png;base64,/, "");
    document.getElementById('photo').value = imageBase64;
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
      $('#errorLocation').removeClass('d-none');
      $('#button-submit').removeClass('d-none');
      $('#button-submit').addClass('d-none');
      // window.location.reload();
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