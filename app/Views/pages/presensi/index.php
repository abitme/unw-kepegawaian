<?= $this->extend('layouts/app'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- Main Content-->
<main class="relative bg-white border border-slate-300 md:w-2/3 lg:w-3/4 rounded-lg pb-3">
  <div class="flex items-center justify-between p-5">
    <div>
      <h1 class="flex text-xl font-semibold">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
          </path>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
        Presensi
      </h1>
    </div>
    <div>
      <!-- Modal Button -->
      <button type="button" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-1 text-center inline-flex items-center mr-2 " data-modal-toggle="small-modal">
        <svg class=" w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Tips
      </button>
      <!-- End Modal Button -->
    </div>
  </div>
  <hr class="pt-5">

  <div class="p-5">
    <!-- Catatan -->
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
    <!-- End Catatan -->

    <!-- Error GPS/Camera -->
    <div id="errorLocation" class="flex p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800 hidden" role="alert">
      <svg class="inline flex-shrink-0 mr-3 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
      </svg>
      <div>
        <span class="font-medium">Error!</span> Gagal Menemukan Lokasi
        Mohon aktifkan akses lokasi dan refresh web ini agar dapat melakukan presensi.
      </div>
    </div>
    <!-- End Error GPS/Camera -->
  </div>

  <div class="container mx-auto">

    <!-- Form Attendance -->
    <form id="form" action="<?= base_url('presensi/create') ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="photo" id="photo">
      <input type="hidden" name="tipe" id="tipe">
      <input type="hidden" name="coord_latitude" id="coord_latitude">
      <input type="hidden" name="coord_longitude" id="coord_longitude">
      <input type="hidden" name="alasan_pulang_cepat" id="alasan_pulang_cepat">
      <input type="hidden" name="alasan_pulang_cepat_lainnya" id="alasan_pulang_cepat_lainnya">

      <div class="grid grid-cols-1 -space-y-2 lg:grid-cols-2 lg:space-y-0">
        <div class="p-5">
          <label for="location" class="block mb-2 text-sm font-medium text-gray-900 ">Lokasi</label>
          <div class="relative flex">
            <input type="text" id="location" class="block p-4 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-indigo-300 focus:outline-none" value="" disabled />
            <svg class="w-6 h-6 absolute bottom-1/4 right-3 mr-3 stroke-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
              </path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
              </path>
            </svg>
          </div>
          <p class="text-xs mt-2 text-indigo-700">[Jika Diluar UNW Open Maps dan pastikan titik sudah berada di UNW]</p>
          <a href="https://maps.google.com/" target="_blank" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-4 py-2 text-center inline-flex items-center  mt-3 mr-2 mb-2">
            <svg class="w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Open Maps
          </a>
        </div>
        <div class="p-5">
          <label for="nik" class="block mb-2 text-sm font-medium text-gray-900 ">NIK Pegawai</label>
          <div class="ui-widget relative flex">
            <input type="number" name="nik" value="<?= set_value('nik') ?>" id="nik" class="block p-4 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-indigo-500" placeholder="NIK Pegawai" required>
            <svg class="w-6 h-6 absolute bottom-1/4 right-3 mr-3 stroke-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
              </path>
            </svg>
          </div>
          <p class="text-xs mt-2 text-indigo-700">[Tulis NIK Tanpa Titik Minimal 3 Angka]</p>
          <p id="nama" class="text-xs mt-2 text-indigo-700 hidden">[Nama Pegawai :]</p>
          <small class="text-red-600"><?= $validation->getError('nik') ?></small>
        </div>
        <div class="p-5">
          <label for="shift" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Pilih Shift (Untuk Pegawai yang memiliki shift seperti satpam dan dapur) </label>
          <select id="shift" name="shift" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="" selected>- Tidak Ada -</option>
            <?php foreach ($optionsShift as $row) : ?>
              <option value="<?= $row->id ?>"><?= "$row->nama_jam_kerja ($row->jam_masuk - $row->jam_pulang)" ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <div class="">
        </div>
        <div class="p-5">
          <label for="photo" class="block mb-2 text-sm font-medium text-gray-900 ">Kamera</label>
          <div class="container flex w-full h-auto">
            <video id="webcam" autoplay playsinline style="width: 100%;height: auto;"></video>
          </div>
          <div class="flex justify-center lg:justify-start">
            <button onclick="event.preventDefault()" type="button" id="take-photo" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center  mt-3 mr-2 mb-2" onClick="take_snapshot()">
              <svg class=" w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                </path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              Capture
            </button>
            <br>
          </div>
          <small class="text-red-600"><?= $validation->getError('photo') ?></small>
        </div>
        <div class="p-5">
          <label for="preview" class="block mb-2 text-sm font-medium text-gray-900 ">Preview</label>
          <div class="container flex w-full h-auto">
            <canvas id="canvas" style="width: 100%;height: auto;"></canvas>
          </div>
          <div id="button-submit" class="flex justify-center lg:justify-start">
            <button id="btnMasuk" onclick="submitData('Masuk')" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center  mt-3 mr-2 mb-2">
              <svg class="w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"></path>
              </svg>
              Masuk
            </button>
            <?php if (isset($jabatanUser) && $jabatanUser && $jabatanUser->nama_jabatan == 'Dosen') : ?>
              <!-- tidak menampilkan button pulang -->
            <?php else : ?>
              <button id="btnPulang" onclick="submitData('Pulang')" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none dark:focus:ring-green-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center  mt-3 mr-2 mb-2">
                <svg class="w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"></path>
                </svg>
                Pulang
              </button>
            <?php endif ?>
          </div>
        </div>
      </div>
    </form>
    <!-- End Form Attendance -->

    <!-- Modal alasan pulang cepat -->
    <div id="modalEl" tabindex="-1" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full justify-center items-center hidden" aria-hidden="true">
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
              <label for="select_alasan_pulang_cepat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Alasan Pulang Cepat : </label>
              <select id="select_alasan_pulang_cepat" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="" selected>- Pilih Alasan -</option>
                <option value="Mengajar">Mengajar Kelas Reguler diluar Jam Kerja</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>
            <div class="pt-2 container_lainnya hidden">
              <label for="input_alasan_pulang_cepat_lainnya" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Lainnya :</label>
              <textarea id="input_alasan_pulang_cepat_lainnya" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder=""></textarea>
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

    <!-- Modal info -->
    <div id="modalInfo" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden rounded-lg fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal">
      <div class="relative p-4 w-full max-w-xl h-modal">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
          <!-- Modal header -->
          <div class="flex justify-between items-start p-4 rounded-t border-b dark:border-gray-600">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
              Pemberitahuan
            </h3>
            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal="hideModalInfo">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd">
                </path>
              </svg>
            </button>
          </div>
          <!-- Modal body -->
          <div class="p-5 space-y-6 rounded-lg">
            <!-- <h5 class="font-semibold text-lg"></h5> -->
            <ul class="list-disc list-inside ">
              <li class="mb-2">
                Anda telah dialihkan (redirect) ke presensi.unw.ac.id
              </li>
              <li class="mb-2">
                Link presensi.unw.<span style="font-weight: bold;">my</span>.id sudah tidak digunakan lagi dan berubah menjadi presensi.unw.<span style="font-weight: bold;">ac</span>.id
              </li>
              <li class="mb-2">
                Redirect presensi.unw.my.id ke presensi.unw.ac.id akan berlangsung sampai masa aktif presensi.unw.my.id berakhir yaitu 14 Februari 2022
              </li>
              <li class="mb-2">
                Jika sebelumnya telah menambahkan shortcut di handphone maka disarankan untuk membuat shortcut baru lagi karena shortcut yang lama tidak akan bisa diakses lagi jika sudah tidak ada redirect
              </li>
            </ul>
          </div>
          <!-- Modal footer -->
          <div class="flex items-center p-4 rounded-b border-t border-gray-200 dark:border-gray-600">
            <button data-modal="hideModalInfo" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<!-- End Main Content -->

<?= $this->endSection(); ?>

<?= $this->section('append-style'); ?>
<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<!-- tata -->
<script src="<?= base_url() ?>/assets/backend/libs/tata/dist/tata.js"></script>
<!-- webcam -->
<script src="<?= base_url() ?>/assets/backend/libs/webcam-easy/webcam-easy.min.js"></script>
<script>
  const modalInfo = new Modal(document.getElementById('modalInfo'));
  modalInfo.show();
  $('button[data-modal="hideModalInfo"]').on('click', function() {
    modalInfo.hide();
  });

  const modal = new Modal(document.getElementById('modalEl'));
  $('button[data-modal="show"]').on('click', function() {
    modal.show();
  });
  $('button[data-modal="hide"]').on('click', function() {
    modal.hide();
  });

  // autocomplete
  $("#nik").autocomplete({
      source: '<?= base_url('pegawai/search-nik') ?>',
      select: function(event, ui) {
        $("#nik").val(ui.item.nik);
        $('#nama').removeClass('hidden');
        $("#nama").html('[Nama Pegawai : ' + ui.item.nama + ']');
        return false;
      }
    })
    .autocomplete("instance")._renderItem = function(ul, item) {
      $('#nama').addClass('hidden');
      return $("<li>")
        .append("<div>" + "(" + item.nik + ") " + item.nama + "</div>")
        .appendTo(ul);
    };

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
    // $(".webcam-container").removeClass("hidden");
    $("#take-photo").removeClass("hidden");
    if (webcam.webcamList.length > 1) {
      $("#cameraFlip").removeClass('hidden');
    }
    $("#wpfront-scroll-top-container").addClass("hidden");
    // window.scrollTo(0, 0);
    // $('body').css('overflow-y', 'hidden');
  }

  function cameraStopped() {
    $("#wpfront-scroll-top-container").removeClass("hidden");
    $("#webcam-control").removeClass("webcam-on");
    $("#webcam-control").addClass("webcam-off");
    $("#cameraFlip").addClass('hidden');
    // $(".webcam-container").addClass("hidden");
    $("#take-photo").addClass("hidden");
    $("#webcam-caption").html("Click to Start Camera");
    // $('.md-modal').removeClass('md-show');
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

  function removeCapture() {
    $('#canvas').addClass('hidden');
    $('#webcam-control').removeClass('hidden');
    $('#cameraControls').removeClass('hidden');
    $('#take-photo').removeClass('hidden');
    $('#exit-app').addClass('hidden');
    $('#download-photo').addClass('hidden');
    $('#resume-camera').addClass('hidden');
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

  function getDistance(latitude1, longitude1, latitude2, longitude2) {
    earth_radius = 6371;

    dLat = deg2rad(latitude2 - latitude1);
    dLon = deg2rad(longitude2 - longitude1);

    a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(deg2rad(latitude1)) * Math.cos(deg2rad(latitude2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
    c = 2 * Math.asin(Math.sqrt(a));
    d = earth_radius * c;

    return d;
  }

  // https://www.w3resource.com/javascript-exercises/javascript-math-exercise-33.php
  function deg2rad(degrees) {
    return degrees * (Math.PI / 180);
  }

  function showPosition(position) {
    document.getElementById('coord_latitude').value = position.coords.latitude;
    document.getElementById('coord_longitude').value = position.coords.longitude;
    distance1 = getDistance(-7.15111, 110.40805, position.coords.latitude, position.coords.longitude);
    distance2 = getDistance(-7.15173, 110.40726, position.coords.latitude, position.coords.longitude);
    distance3 = getDistance(-7.15263, 110.40709, position.coords.latitude, position.coords.longitude);
    distance4 = getDistance(-7.154620, 110.407700, position.coords.latitude, position.coords.longitude); //farmasi
    if ((distance1 > 0.13 && distance2 > 0.14 && distance3 > 0.076) && distance4 > 0.1) {
      $('input#location').val("Di luar Universitas Ngudi Waluyo");
    } else {
      $('input#location').val("Universitas Ngudi Waluyo");
    }
  }

  function showError(error) {
    if (error.code == 1) {
      $('#errorLocation').removeClass('hidden');
      $('#button-submit').removeClass('hidden');
      $('#button-submit').addClass('hidden');
      // window.location.reload();
    } else if (error.code == 2) {
      alert("The network is down or the positioning service can't be reached.");
    } else if (error.code == 3) {
      alert("The attempt timed out before it could get the location data.");
    } else {
      alert("Geolocation failed due to unknown error.");
    }
  }

  var url;

  function submitForm() {

    // remove form error message
    $('.form-text').remove();

    url = <?= json_encode(base_url('presensi/create')) ?>;
  }

  $('#form').submit(function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    // for (var p of formData) {
    // let name = p[0];
    // let value = p[1];

    // console.log(name, value)
    // }
    $.ajax({
      method: "POST",
      url: url,
      data: formData,
      cache: false,
      processData: false,
      contentType: false,
      success: function(data) {
        $('#btnSubmit').html('Submit')
        $('#btnMasuk').html(
          `              <svg class="w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"></path>
              </svg>
              Masuk`
        )
        $('#btnPulang').html(
          `              <svg class="w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"></path>
              </svg>
              Pulang`
        )
        let obj = $.parseJSON(data);
        const status = obj.status == 200 ? 'Success' : 'Error';
        if (obj.status == 300) {
          $('#form-pulang-cepat p').html(`Durasi kerja kurang ${obj.durasiKurang}`);
          modal.show()
          tata.error('Error', obj.message)
        } else if (obj.status == 400) {
          Swal.fire({
            icon: status.toLowerCase(),
            title: status,
            text: obj.message,
          });
        } else if (obj.status == 200) {
          $('input[name=nik]').val('');
          $('input[name=photo]').val('');
          $('select[name=shift]').val('');
          $('select[name=alasan_pulang_cepat]').val('');
          let canvas = document.querySelector("#canvas");
          let context = canvas.getContext('2d');
          context.clearRect(0, 0, canvas.width, canvas.height);
          modal.hide();
          Swal.fire({
            icon: status.toLowerCase(),
            title: status,
            text: obj.message,
          });

        } else {
          window.location.href = window.location.href;
        }
      },
    });

  });

  // ajax Create
  function submitData(tipe) {
    if (tipe == 'Masuk') {
      wrapper = '#btnMasuk';
    } else {
      wrapper = '#btnPulang';
    }
    if (document.forms['form'].reportValidity()) {
      $(wrapper).html(
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
    }

    document.getElementById('tipe').value = tipe;
    document.getElementById('alasan_pulang_cepat').value = document.getElementById('alasan_pulang_cepat').value;
    submitForm();
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
    document.getElementById('tipe').value = 'Pulang';
    document.getElementById('alasan_pulang_cepat').value = document.getElementById('select_alasan_pulang_cepat').value;
    document.getElementById('alasan_pulang_cepat_lainnya').value = document.getElementById('input_alasan_pulang_cepat_lainnya').value;
    submitForm();
    $('#form').submit()
  }

  $('#select_alasan_pulang_cepat').on('change', function() {
    if ($('#select_alasan_pulang_cepat').val() == 'Lainnya') {
      $('.container_lainnya').removeClass('hidden');
    } else {
      $('.container_lainnya').addClass('hidden');
    }
  });

  getLocation();
</script>
<?= $this->endSection(); ?>