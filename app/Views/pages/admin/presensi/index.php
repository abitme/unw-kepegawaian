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
<div class="row">
  <div class="col-md">
    <!-- <a href="<?= base_url('presensi/new-masuk-pulang?tipe=Masuk') ?>" class="btn btn-primary shadow-sm mb-3">
      <span>Absen Masuk</span>
    </a>
    <a href="<?= base_url('presensi/new-masuk-pulang?tipe=Pulang') ?>" class="btn btn-success shadow-sm mb-3">
      <span>Absen Pulang</span>
    </a> -->
    <!-- <a href="<?= base_url('presensi/new-izin') ?>" class="btn btn-info shadow-sm mb-3">
      <span>Izin Pegawai</span>
    </a> -->
  </div>
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
              <th>Status</th>
              <th>Lokasi</th>
              <th>Alasan pulang cepat</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal status-->
<div class="modal fade" id="aiModalStatus" tabindex="-1" role="dialog" aria-labelledby="aiModalStatusLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalStatusLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="form-status" action="" method="post">
        <div class="modal-body">
          <input type="hidden" name="id" id="id">
          <div class="form-group">
            <label for="validitas">Validitas</label>
            <select class="form-control" name="validitas" id="validitas">
              <option value="">- Pilih Validitas -</option>
              <option value="Valid">Valid</option>
              <option value="Tidak Valid">Tidak Valid</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
          <button type="submit" id="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
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
        url: "<?= base_url('admin/presensi/ajax_list') ?>",
        type: "POST"
      },
      //optional
      // lengthMenu: [
      //   [10, 50, 100],
      //   [10, 50, 100]
      // ],
      columnDefs: [{
        targets: [0],
        orderable: false,
      }, ],
      drawCallback: function() {
        $('[data-toggle="tooltip"]').tooltip()
      },
      fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        if (aData[7] == 'Tidak Valid') {
          $(nRow).addClass('alert alert-danger');
        }
      },
    });
  });

  function submitForm(method, id = null) {

    // remove form error message
    $('.form-text').remove();
    if (method == 'validate') {
      url = <?= json_encode(base_url()) ?> + `/admin/presensi/validate-data/${id}`;
    }

  }

  // update status kegiatan ke pengajuan
  $('#form-status').submit(function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
      method: "POST",
      url: url,
      data: formData,
      cache: false,
      processData: false,
      contentType: false,
      success: function(data) {
        let obj = $.parseJSON(data);
        if (obj.status == 200) {
          table.ajax.reload(null, false);
          tata.success('Success', obj.message)
          $('#aiModalStatus').modal('hide');
        } else {
          tata.error('Error', obj.message)
          removeValidation()

          let error = obj.data.errors;
          if (error['validitas']) {
            $('select[name=validitas]').addClass('is-invalid');
            $('select[name=validitas]').after(`<div class="invalid-feedback">${error['validitas']}</div>`);
          }
        }
      },
    });
  });

  // Ajax Validate
  function validateData(id) {
    removeValidation()
    $('#form-status')[0].reset();

    // change text
    $('#aiModalStatusLabel').html('Validasi Data');
    $('.modal-footer button[type=submit]').html('Simpan');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/admin/presensi/validate-data/${id}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);
        $('.modal-body input[name=id]').val(data.id);
        $('.modal-body select[name=validitas]').val(data.validitas);
        $('#aiModalStatus').modal('show');
      },
      error: function(xhr, status, error) {
        alert(error);
      },
    });

    submitForm('validate', id);
  }

  function removeValidation() {
    $('form').find('.is-invalid').removeClass('is-invalid');
    $('form').find('.invalid-feedback').remove();
  }
</script>
<?= $this->endSection(); ?>