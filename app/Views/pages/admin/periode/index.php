<?= $this->extend('layouts/admin'); ?>

<?= $this->section('append-style'); ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/backend/libs/datepicker/datepicker.min.css" rel="stylesheet">
<?= $this->endSection(); ?>

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
      <li class="breadcrumb-item active" aria-current="page">Periode</li>
    </ol>
  </nav>
</div>

<!-- Create Button -->
<?php if (is_allow('insert', $menu)) : ?>
  <div class="row">
    <div class="col-md">
      <a href="javascript:void(0)" class="btn btn-primary shadow-sm mb-3" onclick="createData()">
        <i class=" fas fa-plus-circle"></i>
        <span>Tambah <?= $title ?></span>
      </a>
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
              <th scope="col">No.</th>
              <th scope="col">Tahun</th>
              <th scope="col">Semester</th>
              <th scope="col">Tanggal Awal</th>
              <th scope="col">Tanggal Akhir</th>
              <th scope="col" class="text-center">Aktif</th>
              <th scope="col" class="text-center" style="width: 180px;">Action</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php if (is_allow('insert', $menu) || is_allow('update', $menu)) : ?>
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
        <form id="form" action="" method="post">
          <div class="modal-body">
            <input type="hidden" name="id">
            <div class="form-group">
              <label for="tahun">Tahun</label>
              <input type="text" class="form-control" id="tahun" name="tahun" autocomplete="off">
            </div>
            <div class="form-group">
              <label for="">Semester</label>
              <br>
              <div class="custom-control custom-radio">
                <input class="custom-control-input" type="radio" name="semester" id="Gasal" value="Gasal">
                <label class="custom-control-label" for="Gasal">
                  Gasal
                </label>
              </div>
              <div class="custom-control custom-radio">
                <input class="custom-control-input" type="radio" name="semester" id="Genap" value="Genap">
                <label class="custom-control-label" for="Genap">
                  Genap
                </label>
              </div>
            </div>
            <div class="form-group">
              <label for="tanggal_awal">Tanggal Awal</label>
              <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" autocomplete="off">
            </div>
            <div class="form-group">
              <label for="tanggal_akhir">Tanggal Akhir</label>
              <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" autocomplete="off">
            </div>
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="is_active" value="1" name="is_active">
              <label class="form-check-label is_active" for="is_active">
                Aktif?
              </label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
            <button type="submit" id="submit" class="btn btn-primary">Create</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endif ?>

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
        url: "<?= base_url('periode/ajax_list') ?>",
        type: "POST",
      },
      //optional
      lengthMenu: [
        [100, 150, 200],
        [100, 150, 200]
      ],
      columnDefs: [{
        targets: [1, 2, 3, 4],
        orderable: false,
      }, ],
      fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        $(nRow).find('td').eq(5).addClass('text-center');
      },
    });

    $('#tanggal_awal').on('change', function() {
      const tanggal_akhir = new Date($('#tanggal_awal').val());
      tanggal_akhir.setFullYear(tanggal_akhir.getFullYear() + 1);
      tanggal_akhir.setDate(tanggal_akhir.getDate() - 1);
      $('#tanggal_akhir').val(formatDate(tanggal_akhir));
      // console.log(formatDate(tanggal_akhir));
      // console.log(formatDate(tanggal_akhir));
    });
  });

  // set url form
  let url;

  function submit_form(method, id = null) {

    // remove form error message
    $('.form-text').remove();

    if (method == 'insert') {
      url = <?= json_encode(base_url('periode/create')) ?>;
    }
    if (method == 'update') {
      url = <?= json_encode(base_url()) ?> + `/periode/update/${id}`;
    }

  }

  // handle submit form
  $('form').submit(function(e) {

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
        // show form error message if exist
        let obj = $.parseJSON(data);

        if (obj.status == 200) {
          $('#aiModal').modal('hide')
          table.ajax.reload(null, false);
          tata.success('Success', obj.message)
        } else {
          tata.error('Error', obj.message)
          removeValidation()

          let error = obj.data.errors;
          if (error['tahun']) {
            $('input[name=tahun]').addClass('is-invalid');
            $('input[name=tahun]').after(`<div class="invalid-feedback">${error['tahun']}</div>`);
          }
          if (error['semester']) {
            $('input[name=semester]').addClass('is-invalid');
            $('label[for=genap]').after(`<div class="invalid-feedback">${error['semester']}</div>`);
          }
          if (error['tanggal_awal']) {
            $('input[name=tanggal_awal]').addClass('is-invalid');
            $('input[name=tanggal_awal]').after(`<div class="invalid-feedback">${error['tanggal_awal']}</div>`);
          }
          if (error['tanggal_akhir']) {
            $('input[name=tanggal_akhir]').addClass('is-invalid');
            $('input[name=tanggal_akhir]').after(`<div class="invalid-feedback">${error['tanggal_akhir']}</div>`);
          }
          if (error['is_active']) {
            $('input[name=is_active]').addClass('is-invalid');
            $('.form-check-label.is_active').after(`<div class="invalid-feedback">${error['is_active']}</div>`);
          }
        }
      },
    });

  });

  // Ajax Create
  function createData() {
    removeValidation()
    $('#form')[0].reset();
    $('#id').val('');

    // change text
    $('#aiModalLabel').html('Tambah Periode');
    $('.modal-footer button[type=submit]').html('Create');

    $('#aiModal').modal('show');

    submit_form('insert');
  }

  // Ajax Update
  function updateData(id) {
    removeValidation()
    $('#form')[0].reset();

    // change text
    $('#aiModalLabel').html('Edit Periode');
    $('.modal-footer button[type=submit]').html('Update');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/periode/update/${id}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);

        $('.modal-body input[name=id]').val(data.id);
        $('.modal-body input[name=tahun]').val(data.tahun);
        $('.modal-body').find(`:radio[name=semester][value="${data.semester}"]`).prop('checked', true);
        $('.modal-body input[name=tanggal_awal]').val(data.tanggal_awal);
        $('.modal-body input[name=tanggal_akhir]').val(data.tanggal_akhir);
        if (data.is_active == true) {
          $('.modal-body input[name=is_active]').prop("checked", true);
        } else {
          $('.modal-body input[name=is_active]').prop("checked", false);
        }
        $('#aiModal').modal('show');
      },
      error: function(xhr, status, error) {
        alert(error);
      },
    });

    submit_form('update', id);
  }

  // Ajax Delete
  function destroyData(id) {
    var x = confirm('Apakah anda yakin menghapus data?');
    if (x) {
      $.ajax({
        method: "POST",
        url: <?= json_encode(base_url()) ?> + `/periode/delete/${id}`,
        data: {
          id: id
        },
        cache: false,
        success: function(data) {
          let obj = $.parseJSON(data);
          table.ajax.reload(null, false);
          tata.success('Success', obj.message)
        },
        error: function(xhr, status, error) {
          alert(error);
        },
      });
    }
  }

  $('#aiModal').on('shown.bs.modal', function() {
    $('input[name=tahun]').trigger('focus')
  })

  function removeValidation() {
    $('form').find('.is-invalid').removeClass('is-invalid');
    $('form').find('.invalid-feedback').remove();
  }
</script>
<?= $this->endSection(); ?>