<?= $this->extend('layouts/app'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- alert -->
<?= $this->include('includes/_alertSweet') ?>

<!-- Main Content-->
<main class="relative bg-white border border-slate-300 md:w-2/3 lg:w-3/4 rounded-lg pb-3">

  <!-- Head -->
  <div class="flex items-center justify-between p-5">
    <h1 class="flex text-xl font-semibold">
      <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
        </path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
      </svg>
      Presensi
    </h1>
  </div>

  <!-- Catatan -->
  <!-- <div class="p-5">
    <div class="flex p-4 mb-3 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">
      <svg class="inline flex-shrink-0 mr-3 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
      </svg>
      <div>
        <span class="font-medium">Catatan :</span>
        <ol class="list-decimal">
          <li>Presensi/absen wajib menampilkan foto selfie saat melakukan presensi/absen</li>
          <li>Mohon untuk tidak lupa melakukan presensi/absen
            (Jika lupa presensi/absen, mohon untuk isi form lupa presensi/absen)</li>
        </ol>
      </div>
    </div>
  </div> -->
  <!-- End Catatan -->

  <!-- Scan Barcode -->
  <div class="container mx-auto">
    <div id="sourceSelectPanel" style="margin: 0 1.5rem; display:none">
      <div class="p-1"> <label for="sourceSelect" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Change video source</label>
        <select id="sourceSelect" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </select>
      </div>
      <div class="p-1">
        <label for="selectShift" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Pilih Shift (Untuk Pegawai yang memiliki shift seperti satpam dan dapur) </label>
        <select id="selectShift" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
          <option value="" selected>- Tidak Ada -</option>
          <?php foreach ($optionsShift as $row) : ?>
            <option value="<?= $row->id ?>"><?= "$row->nama_jam_kerja ($row->jam_masuk - $row->jam_pulang)" ?></option>
          <?php endforeach ?>
        </select>
      </div>
      <div id="scanBarcode" class="flex justify-center mt-4">
        <video id="video" width="500" height="400" style="border: 1px solid gray"></video>
      </div>
    </div>
  </div>
</main>
<!-- End Main Content -->

<!-- Modal toggle -->
<!-- <button class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button" data-modal="show">
  Toggle modal
</button> -->

<!-- Main modal -->
<div id="modalEl" tabindex="-1" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center hidden" aria-hidden="true">
  <div class="relative p-4 w-full max-w-2xl h-full md:h-auto" style="max-width:1535px">
    <!-- Modal content -->
    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
      <!-- Modal header -->
      <div class="flex justify-between items-start p-4 rounded-t border-b dark:border-gray-600">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
          Presensi
        </h3>
        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal="hide">
          <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
          </svg>
          <span class="sr-only">Close modal</span>
        </button>
      </div>
      <!-- Modal body -->
      <form id="form" action="<?= base_url('presensi/create-scan') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="photo" id="photo">
        <input type="hidden" name="alasan_pulang_cepat" id="alasan_pulang_cepat">
        <input type="hidden" name="alasan_pulang_cepat_lainnya" id="alasan_pulang_cepat_lainnya">
        <input type="hidden" name="shift" id="shift">
        <textarea hidden="" name="nik" id="result" readonly></textarea>
        <div class="flex justify-center items-center">
          <div class="p-5" id="camera">
            <div class="container flex w-full h-auto">
              <video id="webcam" autoplay playsinline style="width: 100%;height: auto;"></video>
            </div>
          </div>
          <div class="p-5" id="previewCam">
            <div class="container flex items-center justify-center w-full h-auto">
              <canvas id="canvas" class="hidden" style="width: 100%;height: auto;"></canvas>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- modalPulangCepat -->
<div id="modalPulangCepat" tabindex="-1" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center hidden" aria-hidden="true">
  <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
    <!-- Modal content -->
    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
      <!-- Modal header -->
      <div class="flex justify-between items-start p-4 rounded-t border-b dark:border-gray-600">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
          Durasi Kerja Kurang
        </h3>
        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal="hide">
          <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
          </svg>
          <span class="sr-only">Close modal</span>
        </button>
      </div>
      <!-- Modal body -->
      <form id="form-pulang-cepat" action="" method="post" class="p-5">
        <p></p>
        <div class="pt-4">
          <label for="selectAlasanPulangCepat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Alasan Pulang Cepat : </label>
          <select id="selectAlasanPulangCepat" name="selectAlasanPulangCepat" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="" selected>- Pilih Alasan -</option>
            <option value="Mengajar">Mengajar Kelas Reguler diluar Jam Kerja</option>
            <option value="Lainnya">Lainnya</option>
          </select>
        </div>
        <div class="pt-2 container_lainnya hidden">
          <label for="inputAlasanPulangCepatLainnya" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Lainnya :</label>
          <textarea id="inputAlasanPulangCepatLainnya" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder=""></textarea>
        </div>
      </form>
      <!-- Modal footer -->
      <div class="flex items-center p-6 space-x-2 rounded-b border-t border-gray-200 dark:border-gray-600">
        <button data-modal="hide" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Cancel</button>
        <button id="btnSubmit" onclick="submitPulangCepat(event)" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('append-style'); ?>
<style>
  @import url("https://fonts.googleapis.com/css?family=Montserrat:700");

  html {
    box-sizing: border-box;
  }

  *,
  *:before,
  *:after {
    box-sizing: inherit;
  }

  #countdown {
    height: 100%;
    background-color: #47ebb4;
    font-family: "Montserrat", sans-serif;
    -webkit-animation: 5s 0.875s cubic-bezier(0.9, 0, 0.1, 1) forwards background_color;
    animation: 5s 0.875s cubic-bezier(0.9, 0, 0.1, 1) forwards background_color;
  }

  @-webkit-keyframes background_color {
    20% {
      background-color: #eb47e0;
    }

    40% {
      background-color: #9eeb47;
    }

    60% {
      background-color: #475deb;
    }

    80% {
      background-color: #eb7347;
    }

    100% {
      background-color: #47ebb4;
    }

    120% {
      background-color: #eb47e0;
    }
  }

  @keyframes background_color {
    20% {
      background-color: #eb47e0;
    }

    40% {
      background-color: #9eeb47;
    }

    60% {
      background-color: #475deb;
    }

    80% {
      background-color: #eb7347;
    }

    100% {
      background-color: #47ebb4;
    }

    120% {
      background-color: #eb47e0;
    }
  }

  #countdown {
    width: 75vmin;
    height: 75vmin;
    box-shadow: 0 0 0 1.875vmin, inset 3.75vmin 3.75vmin 7.5vmin rgba(0, 0, 0, 0.125), 3.75vmin 3.75vmin 7.5vmin rgba(0, 0, 0, 0.125);
    font-size: 37.5vmin;
    text-shadow: 3.75vmin 3.75vmin 7.5vmin rgba(0, 0, 0, 0.125);
    position: relative;
    /* top: 50%;
    left: 50%; */
    /* transform: translate(-50%, -50%); */
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    color: white;
    border-radius: 50%;
    font-weight: 700;
  }

  @media (min-width: 600px) {
    #countdown {
      width: 20vmin;
      height: 20vmin;
      box-shadow: 0 0 0 1.25vmin, inset 2.5vmin 2.5vmin 5vmin rgba(0, 0, 0, 0.125), 2.5vmin 2.5vmin 5vmin rgba(0, 0, 0, 0.125);
      font-size: 10vmin;
      text-shadow: 2.5vmin 2.5vmin 5vmin rgba(0, 0, 0, 0.125);
    }
  }

  #countdown:before {
    content: "5";
    -webkit-animation: 5s 1s forwards timer_countdown, 1s 0.875s 5 timer_beat;
    animation: 5s 1s forwards timer_countdown, 1s 0.875s 5 timer_beat;
  }

  @-webkit-keyframes timer_beat {

    40%,
    80% {
      transform: none;
    }

    50% {
      transform: scale(1.125);
    }
  }

  @keyframes timer_beat {

    40%,
    80% {
      transform: none;
    }

    50% {
      transform: scale(1.125);
    }
  }

  @-webkit-keyframes timer_countdown {
    0% {
      content: "5";
    }

    20% {
      content: "4";
    }

    40% {
      content: "3";
    }

    60% {
      content: "2";
    }

    80% {
      content: "1";
    }

    100% {
      content: "0";
    }
  }

  @keyframes timer_countdown {
    0% {
      content: "5";
    }

    20% {
      content: "4";
    }

    40% {
      content: "3";
    }

    60% {
      content: "2";
    }

    80% {
      content: "1";
    }

    100% {
      content: "0";
    }
  }

  #countdown:after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    z-index: -100;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.125);
    -webkit-animation: 5s 1s linear forwards timer_indicator;
    animation: 5s 1s linear forwards timer_indicator;
  }

  @-webkit-keyframes timer_indicator {
    100% {
      transform: translateY(100%);
    }
  }

  @keyframes timer_indicator {
    100% {
      transform: translateY(100%);
    }
  }
</style>
<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<!-- tata -->
<script src="<?= base_url() ?>/assets/backend/libs/tata/dist/tata.js"></script>
<!-- webcam -->
<script src="<?= base_url() ?>/assets/backend/libs/webcam-easy/webcam-easy.min.js"></script>
<script src="<?= base_url() ?>/assets/frontend/vendor/zxing/zxing.min.js"></script>
<script>
  const modal = new Modal(document.getElementById('modalEl'));
  $('button[data-modal="show"]').on('click', function() {
    modal.show();
  });
  $('button[data-modal="hide"]').on('click', function() {
    loadQRCode();
    modal.hide();
  });
  const modalPulangCepat = new Modal(document.getElementById('modalPulangCepat'));
  $('button[data-modal="show"]').on('click', function() {
    modalPulangCepat.show();
  });
  $('button[data-modal="hide"]').on('click', function() {
    modalPulangCepat.hide();
  });

  const webcamElement = document.getElementById('webcam');
  const canvasElement = document.getElementById('canvas');
  const webcam = new Webcam(webcamElement, 'user', canvasElement);

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
    if (err != '') {}
    $('#button-submit').removeClass('hidden');
    $('#button-submit').addClass('hidden');
  }

  function cameraStarted() {
    $('.flash').hide();
    $("#webcam-caption").html("on");
    $("#webcam-control").removeClass("webcam-off");
    $("#webcam-control").addClass("webcam-on");
    $("#take-photo").removeClass("hidden");
    if (webcam.webcamList.length > 1) {
      $("#cameraFlip").removeClass('hidden');
    }
    $("#wpfront-scroll-top-container").addClass("hidden");
  }

  $("#take-photo").click(function() {
    beforeTakePhoto();
    let picture = webcam.snap();
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

  $('#form').submit(function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    // for (var p of formData) {
    //   let name = p[0];
    //   let value = p[1];

    //   console.log(name, value)
    // }
    $.ajax({
      method: "POST",
      url: <?= json_encode(base_url('presensi/create-scan')) ?>,
      data: formData,
      cache: false,
      processData: false,
      contentType: false,
      success: function(data) {
        $('#btnSubmit').html('Submit')
        let obj = $.parseJSON(data);
        const status = obj.status == 200 ? 'Success' : 'Error';
        if (obj.status == 300) {
          $('#form-pulang-cepat p').html(`Durasi kerja kurang ${obj.durasiKurang}`);
          modalPulangCepat.show()
          tata.error('Error', obj.message)
        } else if (obj.status == 400) {
          Swal.fire({
            icon: status.toLowerCase(),
            title: status,
            text: obj.message,
          });
          modal.hide();
          setTimeout(() => {
            Swal.close()
          }, 4000);

          clearForm()
        } else if (obj.status == 200) {
          modalPulangCepat.hide();
          modal.hide();
          Swal.fire({
            icon: status.toLowerCase(),
            title: status,
            text: obj.message,
          });
          setTimeout(() => {
            Swal.close()
          }, 5000);

          clearForm()
        } else {
          window.location.href = window.location.href;
        }
        loadQRCode();
      },
    });
  });

  // load QRcode
  let codeReader = new ZXing.BrowserQRCodeReader()

  function loadQRCode() {
    let selectedDeviceId;
    let audio = new Audio("/assets/audio/beep.mp3");
    codeReader.reset();
    codeReader = new ZXing.BrowserQRCodeReader();

    function decodeInput(selectedDeviceId) {
      codeReader.decodeFromInputVideoDevice(selectedDeviceId, 'video').then((result) => {
        document.getElementById('result').textContent = result.text
        if (result) {
          modal.show();
          let canvas = document.querySelector("#canvas");
          let context = canvas.getContext('2d');
          context.clearRect(0, 0, canvas.width, canvas.height);

          $('#canvas').addClass('hidden');
          $('#camera').removeClass('hidden');
        }
        if (result != null) {
          audio.play();
        }

        // $('#scanBarcode #video').addClass('hidden');
        $('#countdown').remove();
        $('#previewCam .container').append('<div id="countdown"></div>');
        setTimeout(() => {
          $('#canvas').removeClass('hidden');
          $('#countdown').remove();
          let picture = webcam.snap();
          console.log(picture);
          afterTakePhoto();
          $('#camera').addClass('hidden');
          $('#form').submit()
        }, 5000);

      }).catch((err) => {
        console.error(err)
        document.getElementById('result').textContent = err
      })
    }

    console.log('ZXing code reader initialized')
    codeReader.getVideoInputDevices()
      .then((videoInputDevices) => {
        const sourceSelect = document.getElementById('sourceSelect')
        sourceSelect.innerHTML = '';
        selectedDeviceId = videoInputDevices[0].deviceId
        if (videoInputDevices.length >= 1) {
          selectedDeviceId = window.localStorage.getItem('selectedDeviceId');
          videoInputDevices.forEach((element) => {
            const sourceOption = document.createElement('option')
            sourceOption.text = element.label
            sourceOption.value = element.deviceId
            selectedDeviceId == element.deviceId ? sourceOption.selected = true : ''
            sourceSelect.appendChild(sourceOption)
          })
          sourceSelect.onchange = () => {
            console.log(sourceSelect.value);
            selectedDeviceId = sourceSelect.value;
            window.localStorage.setItem('selectedDeviceId', selectedDeviceId);
            decodeInput(selectedDeviceId)
          };
          const sourceSelectPanel = document.getElementById('sourceSelectPanel')
          sourceSelectPanel.style.display = 'block'
        }
        decodeInput(selectedDeviceId)
        console.log(`Started continous decode from camera with id ${selectedDeviceId}`)
      })
      .catch((err) => {
        console.error(err)
      })
  }

  function clearForm() {
    $('input[name=nik]').val('');
    $('input[name=photo]').val('');
    $('input[name=shift]').val('');
    $('#selectShift').val('');
    $('#inputAlasanPulangCepatLainnya').val('');
    $('input[name=alasan_pulang_cepat]').val('');
    $('input[name=alasan_pulang_cepat_lainnya]').val('');
    $('select[name=selectAlasanPulangCepat]').val('');
    $('.container_lainnya').addClass('hidden');
  }

  function submitPulangCepat(e) {
    $('#btnSubmit').html(
      `<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
        width="20px" height="20px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
      <path fill="#fff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
        <animateTransform attributeType="xml"
          attributeName="transform"
          type="rotate"
          from="0 25 25"
          to="360 25 25"
          dur="0.6s"
          repeatCount="indefinite"/>
        </path>
      </svg>`
    );
    document.getElementById('alasan_pulang_cepat').value = document.getElementById('selectAlasanPulangCepat').value;
    document.getElementById('alasan_pulang_cepat_lainnya').value = document.getElementById('inputAlasanPulangCepatLainnya').value;
    $('#form').submit()
  }

  window.addEventListener('load', function() {
    loadQRCode()
  })

  $('#selectAlasanPulangCepat').on('change', function() {
    if ($('#selectAlasanPulangCepat').val() == 'Lainnya') {
      $('.container_lainnya').removeClass('hidden');
    } else {
      $('.container_lainnya').addClass('hidden');
    }
  });

  $('#selectShift').on('change', function() {
    $('#shift').val(this.value);
  });
</script>
<?= $this->endSection(); ?>