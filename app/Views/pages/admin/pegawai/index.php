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

<!-- Create Button -->
<?php if (is_allow('insert', $menu) && is_allow('update', $menu) && is_allow('delete', $menu)) : ?>
  <div class="row">
    <div class="col-md">
      <a href="<?= base_url('pegawai/new') ?>" class="btn btn-primary shadow-sm mb-3">
        <i class="fas fa-plus-circle"></i>
        <span>Tambah <?= $title ?></span>
      </a>
      <a href="<?= base_url('pegawai/import') ?>" class="btn btn-success shadow-sm mb-3">
        <i class="fas fa-plus-circle"></i>
        <span>Import Excel</span>
      </a>
      <form id="form-excel" class="d-inline" action="<?= base_url('pegawai/spreadsheet') ?>" method="GET">
        <input type="hidden" name="searchValue" value="">
        <button type="submit" class="btn btn-success shadow-sm  mb-3"><i class="fas fa-file-excel"></i> Export to Excel</button>
      </form>
    </div>
  </div>
<?php endif ?>
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
              <th scope="col">#</th>
              <th scope="col">Foto</th>
              <th scope="col">NIK</th>
              <th scope="col">Nama</th>
              <?php if (is_allow('insert', $menu) && is_allow('update', $menu) && is_allow('delete', $menu)) : ?>
                <th scope="col">TTL</th>
                <th scope="col">Jenis Kelamin</th>
                <th scope="col">Jabatan</th>
                <th scope="col">Action</th>
              <?php endif ?>
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
    let searchValue = $('#searchValue').val();
    table = $('#myTable').DataTable({
      processing: true,
      serverSide: true,
      order: [],
      ajax: {
        url: "<?= base_url('pegawai/ajax_list') ?>",
        type: "POST",
        data(d) {
          d.searchValue = searchValue
        },
      },

      //optional
      // lengthMenu: [
      //   [10, 50, 100],
      //   [10, 50, 100]
      // ],
      columnDefs: [{
        <?php if (is_allow('insert', $menu) && is_allow('update', $menu) && is_allow('delete', $menu)) : ?>
          targets: [0, 1, 7],
        <?php else : ?>
          targets: [0, 1],
        <?php endif ?>
        orderable: false,
      }, ],
      drawCallback: function() {
        $('[data-toggle="tooltip"]').tooltip()
      }
    });
    $('#myTable').on('search.dt', function() {
      var value = $('.dataTables_filter input').val();
      $('#form-excel input[name=searchValue]').val(value);
    });
  });
</script>
<?= $this->endSection(); ?>