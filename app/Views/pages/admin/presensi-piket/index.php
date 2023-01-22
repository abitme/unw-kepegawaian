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
      <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
    </ol>
  </nav>
</div>

<!-- alert -->
<?= $this->include('includes/_alert') ?>

<!-- Page Content -->
<div class="row">
  <div class="col-md-12">
    <div class="card shadow mb-3">
      <div class="card-header text-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar <?= $title ?></h6>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-hover" id="myTable" style="overflow-x:auto;">
          <thead>
            <tr>
              <th>#</th>
              <th>Pegawai</th>
              <th>Foto</th>
              <th>Tipe</th>
              <th>Waktu</th>
              <th>Lokasi</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('append-style'); ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.css">
<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<!-- datatable js -->
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script>
  $(document).ready(function() {
    // datatable
    table = $('#myTable').DataTable({
      processing: true,
      serverSide: true,
      order: [],
      ajax: {
        url: "<?= base_url('admin/presensi-piket/ajax_list') ?>",
        type: "POST"
      },
      //optional
      // lengthMenu: [
      //   [10, 50, 100],
      //   [10, 50, 100]
      // ],
      columnDefs: [{
        targets: [0, 5],
        orderable: false,
      }, ],
      drawCallback: function() {
        $('[data-toggle="tooltip"]').tooltip()
      },
    });
  });
</script>
<?= $this->endSection(); ?>