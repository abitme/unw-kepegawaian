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
      Presensi Acara
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
      <label for="sourceSelect" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Change video source</label>
      <select id="sourceSelect" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
      </select>
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
  <div class="relative p-4 w-full max-w-lg h-full md:h-auto" style="max-width:32rem">
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
      <form id="form" action="#" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <textarea hidden="" name="result" id="result" readonly></textarea>
        <input type="hidden" name="barcode">
        <div class="p-5">
          <label for="nik" class="block mb-2 text-sm font-medium text-gray-900 ">NIK Pegawai</label>
          <div class="ui-widget relative flex">
            <input type="tel" name="nik" value="<?= $nik ?? set_value('nik') ?>" id="nik" class="block p-4 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-indigo-500" placeholder="NIK Pegawai" onfocus="this.setSelectionRange(this.value.length, this.value.length);"  required>
            <svg class="w-6 h-6 absolute bottom-1/4 right-3 mr-3 stroke-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
              </path>
            </svg>
          </div>
          <p class="text-xs mt-2 text-indigo-700">[Tulis NIK Tanpa Titik Minimal 3 Angka]</p>
          <p id="nama" class="text-xs mt-2 text-indigo-700 <?= !isset($nama) ? 'hidden' : '' ?> ">[Nama Pegawai :] <?= $nama ?? '' ?></p>
          <small class="text-red-600"><?= $validation->getError('nik') ?></small>
        </div>
        <div id="button-submit" class="flex justify-center lg:justify-start">
          <button id="btnSubmit" onclick="submitData()" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center  mt-3 mr-2 mb-2">
            <svg class="w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"></path>
            </svg>
            Submit
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('append-style'); ?>
<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<!-- webcam -->
<!-- <script src="<?= base_url() ?>/assets/backend/libs/webcam-easy/webcam-easy.min.js"></script> -->
<script src="<?= base_url() ?>/assets/frontend/vendor/zxing/zxing.min.js"></script>
<script>
  const modal = new Modal(document.getElementById('modalEl'));
  $('button[data-modal="show"]').on('click', function() {
    modal.show();
    document.getElementById("nik").focus()
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

  $('#form').submit(function(e) {
    console.log('submited');
    e.preventDefault();
    let formData = new FormData(this);
    // for (var p of formData) {
    //   let name = p[0];
    //   let value = p[1];

    //   console.log(name, value)
    // }
    $.ajax({
      method: "POST",
      url: <?= json_encode(base_url('presensi-acara/create')) ?>,
      data: formData,
      cache: false,
      processData: false,
      contentType: false,
      success: function(data) {
        $('#btnSubmit').html('Submit')
        let obj = $.parseJSON(data);
        const status = obj.status == 200 ? 'Success' : 'Error';
        if (obj.status == 400) {
          Swal.fire({
            icon: status.toLowerCase(),
            title: status,
            text: obj.message,
          });
          // modal.hide();
          setTimeout(() => {
            Swal.close()
          }, 4000);
        } else if (obj.status == 200) {
          $('input[name=nik]').val('');
          modal.hide();
          Swal.fire({
            icon: status.toLowerCase(),
            title: status,
            text: obj.message,
          });
          setTimeout(() => {
            Swal.close()
          }, 5000);
        } else {
          window.location.href = window.location.href;
        }

        loadQRCode()
      },
    });
  });

  // ajax Create
  function submitData(tipe) {
    wrapper = '#btnSubmit';
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
  }

  // load QRcode
  function loadQRCode() {
    let selectedDeviceId;
    let audio = new Audio("/assets/audio/beep.mp3");
    const codeReader = new ZXing.BrowserQRCodeReader()

    function decodeInput(selectedDeviceId) {
      codeReader.decodeFromInputVideoDevice(selectedDeviceId, 'video').then((result) => {
        document.getElementById('result').textContent = result.text
        if (result) {
          modal.show();
          document.getElementById("nik").focus()
        }
        if (result != null) {
          audio.play();
        }

        document.getElementById('result').textContent = result.text
        $('input[name=barcode]').val(result.text);

      }).catch((err) => {
        console.error(err)
        document.getElementById('result').textContent = err
      })
    }

    console.log('ZXing code reader initialized')
    codeReader.getVideoInputDevices()
      .then((videoInputDevices) => {
        const sourceSelect = document.getElementById('sourceSelect')
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

  window.addEventListener('load', function() {
    loadQRCode()
  })
</script>
<?= $this->endSection(); ?>