<?= $this->extend('layouts/app'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- alert -->
<?= $this->include('includes/_alertSweet') ?>

<!-- Main Content-->
<main class="relative bg-white border border-slate-300 md:w-2/3 lg:w-3/4 rounded-lg pb-3">
  <div class="flex items-center justify-between p-5">
    <div>
      <h1 class="flex text-xl font-semibold">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
          </path>
        </svg>
        Data Presensi Pegawai
      </h1>
    </div>
  </div>
  <hr class="pt-5">

  <div class="container mx-auto">
    <!-- Table Attendance -->
    <div class="relative overflow-x-auto sm:rounded-lg p-5">
      <table id="myTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" class="px-6 py-3">
              #
            </th>
            <th scope="col" class="px-6 py-3">
              Pegawai
            </th>
            <th scope="col" class="px-6 py-3">
              Foto
            </th>
            <th scope="col" class="px-6 py-3">
              Tipe
            </th>
            <th scope="col" class="px-6 py-3">
              Waktu
            </th>
            <th scope="col" class="px-6 py-3">
              Status
            </th>
            <th scope="col" class="px-6 py-3">
              Lokasi
            </th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <!-- End Table Attendance -->
  </div>
</main>
<!-- End Main Content -->
</div>

<?= $this->endSection(); ?>

<?= $this->section('append-style'); ?>
<link href="<?= base_url() ?>/assets/frontend/vendor/datatable/datatables.min.css" rel="stylesheet">
<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<script src="<?= base_url() ?>/assets/frontend/vendor/datatable/datatables.min.js"></script>
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
  });
</script>
<?= $this->endSection(); ?>