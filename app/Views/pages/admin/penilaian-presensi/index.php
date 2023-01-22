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
      <li class="breadcrumb-item"><a href="#">Master Data</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
    </ol>
  </nav>
</div>

<!-- Create Button -->
<div class="row">
  <div class="col-md">
    <a href="<?= base_url('penilaian-presensi/import') ?>" class="btn btn-success shadow-sm mb-3">
      <i class="fas fa-plus-circle"></i>
      <span>Import Excel</span>
    </a>
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
              <th scope="col">#</th>
              <th scope="col">Nama</th>
              <th scope="col">Cuti</th>
              <th scope="col">Alpha</th>
              <th scope="col">Total Cuti</th>
              <th scope="col">Terlambat</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="aiModal" tabindex="-1" role="dialog" aria-labelledby="aiModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form" action="" method="post">
          <input type="hidden" name="id" id="id">
          <div class="form-group">
            <label for="id_pegawai">Pegawai</label>
            <?= form_dropdown('id_pegawai', getDropdownList('pegawai', ['id', 'nama'], '', '- Pilih Pegawai -', 'nama', 'asc', ''), set_value('id_pegawai'), ['class' => 'form-control select2-pegawai', 'id' => 'id_pegawai', 'readonly' => 'true', 'style' => 'width:100%']) ?>
          </div>
          <div class="form-group">
            <label for="cuti">Cuti</label>
            <input type="number" class="form-control" id="cuti" name="cuti" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="alpha">Alpha</label>
            <input type="number" class="form-control" id="alpha" name="alpha" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="total_cuti">Total Cuti</label>
            <input type="number" class="form-control" id="total_cuti" name="total_cuti" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="terlambat">Terlambat</label>
            <input type="number" class="form-control" id="terlambat" name="terlambat" autocomplete="off">
          </div>
          <!-- <div class="form-group">
            <label for="pulang_cepat">Pulang Cepat</label>
            <input type="number" class="form-control" id="pulang_cepat" name="pulang_cepat" autocomplete="off">
          </div> -->
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            <button type="submit" id="submit" class="btn btn-primary">Create</button>
          </div>
        </form>
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
        url: "<?= base_url('penilaian-presensi/ajax_list') ?>",
        type: "POST",
      },
      //optional
      lengthMenu: [
        [100, 150, 200],
        [100, 150, 200]
      ],
      columnDefs: [{
        targets: [0, 2, 3, 4, 5, 6],
        orderable: false,
      }, ],
    });
    // select2 pegawai
    $('.select2-pegawai').select2({
      disabled: true,
      ajax: {
        url: "<?= base_url('pegawai/ajax_select2') ?>",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function(params) {
          return {
            searchTerm: params.term // search term
          };
        },
        processResults: function(response) {
          return {
            results: response
          };
        },
        cache: true
      }
    });
  });

  // set url form
  var url;

  function submitForm(method, id = null) {

    // remove form error message
    $('.form-text').remove();

    if (method == 'insert') {
      url = <?= json_encode(base_url('penilaian-presensi/create')) ?>;
    }
    if (method == 'update') {
      url = <?= json_encode(base_url()) ?> + `/penilaian-presensi/update/${id}`;
    }

  }

  // handle submit form
  $('#form').submit(function(e) {

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
          $('#aiModal').modal('hide')
          table.ajax.reload(null, false);
          tata.success('Success', obj.message)
        } else {
          tata.error('Error', obj.message)
          removeValidation()

          let error = obj.data.errors;
          if (error['id_pegawai']) {
            $('select[name=id_pegawai] + .select2 .select2-selection--single').addClass('is-invalid');
            $('select[name=id_pegawai] + .select2 .select2-selection--single').after(`<div class="invalid-feedback">${error['id_pegawai']}</div>`);
          }
          if (error['cuti']) {
            $('input[name=cuti]').addClass('is-invalid');
            $('input[name=cuti]').after(`<div class="invalid-feedback">${error['cuti']}</div>`);
          }
          if (error['alpha']) {
            $('input[name=alpha]').addClass('is-invalid');
            $('input[name=alpha]').after(`<div class="invalid-feedback">${error['alpha']}</div>`);
          }
          if (error['total_cuti']) {
            $('input[name=total_cuti]').addClass('is-invalid');
            $('input[name=total_cuti]').after(`<div class="invalid-feedback">${error['total_cuti']}</div>`);
          }
          if (error['terlambat']) {
            $('input[name=terlambat]').addClass('is-invalid');
            $('input[name=terlambat]').after(`<div class="invalid-feedback">${error['terlambat']}</div>`);
          }
        }

      },
    });

  });

  // ajax Update
  function updateData(id) {
    removeValidation()
    $('#form')[0].reset();

    // change text
    $('#aiModalLabel').html('Edit Data Presensi');
    $('.modal-footer button[type=submit]').html('Update');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/penilaian-presensi/update/${id}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);
        if (data) {
          $('.modal-body input[name=id]').val(data.id);
          $('.modal-body select[name=id_pegawai]').val(data.id_pegawai).trigger('change');
          $('span#select2-id_pegawai-container').html(data.nama);
          $('.modal-body input[name=cuti]').val(data.cuti);
          $('.modal-body input[name=alpha]').val(data.alpha);
          $('.modal-body input[name=total_cuti]').val(data.total_cuti);
          $('.modal-body input[name=terlambat]').val(data.terlambat);
        }

        $('#aiModal').modal('show');
      },
      error: function(xhr, status, error) {
        alert(error);
      },
    });

    submitForm('update', id);
  }

  // set focus on shown modal 
  $('#aiModal').on('shown.bs.modal', function() {
    $('input[name=cuti]').trigger('focus')
  })

  function removeValidation() {
    $('form').find('.is-invalid').removeClass('is-invalid');
    $('form').find('.invalid-feedback').remove();
  }
</script>
<?= $this->endSection(); ?>