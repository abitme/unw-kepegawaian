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
    <a href="javascript:void(0)" class="btn btn-primary shadow-sm mb-3" onclick="createData()">
      <i class=" fas fa-plus-circle"></i>
      <span>Tambah Jam Kerja</span>
    </a>
  </div>
</div>

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
              <th scope="col">Nama Jam Kerja</th>
              <th scope="col">Jam Masuk</th>
              <th scope="col">Jam Istirahat</th>
              <th scope="col">Jam Pulang</th>
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
            <label for="nama_jam_kerja">Nama Jam Kerja</label>
            <input type="text" class="form-control" id="nama_jam_kerja" name="nama_jam_kerja" />
          </div>
          <div class="form-group">
            <label for="jam_masuk">Jam Masuk</label>
            <input type="text" class="form-control" id="jam_masuk" name="jam_masuk" />
          </div>
          <div class="form-group">
            <label for="jam_istirahat_mulai">Jam Istirahat Mulai</label>
            <input type="text" class="form-control" id="jam_istirahat_mulai" name="jam_istirahat_mulai" />
          </div>
          <div class="form-group">
            <label for="jam_istirahat_selesai">Jam Istirahat Selesai</label>
            <input type="text" class="form-control" id="jam_istirahat_selesai" name="jam_istirahat_selesai" />
          </div>
          <div class="form-group">
            <label for="jam_pulang">Jam Pulang</label>
            <input type="text" class="form-control" id="jam_pulang" name="jam_pulang" />
          </div>
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
<!-- datatable -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.css">
<!-- timepicker -->
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/backend/libs/jonthornton-jquery-timepicker/jquery.timepicker.min.css" />

<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<!-- datatable -->
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js"></script>
<!-- timepicker -->
<script src="<?= base_url() ?>/assets/backend/libs/jonthornton-jquery-timepicker/jquery.timepicker.min.js"></script>
<script>
  $(document).ready(function() {
    // datatable
    table = $('#myTable').DataTable({
      processing: true,
      serverSide: true,
      order: [],
      ajax: {
        url: "<?= base_url('jam-kerja/ajax_list') ?>",
        type: "POST",
      },
      //optional
      lengthMenu: [
        [100, 150, 200],
        [100, 150, 200]
      ],
      columnDefs: [{
        targets: [0, 1, 2],
        orderable: false,
      }, ],
      // fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      //   if (aData[2].search("updateDataRelations") != -1) {
      //     $(nRow).find('td').eq(1).css('padding-left', '3rem');
      //   }
      // },
    });
    // timepicker
    $('#jam_masuk').timepicker({
      'timeFormat': 'H:i',
      'step': 15
    });
    $('#jam_istirahat_mulai').timepicker({
      'timeFormat': 'H:i',
      'step': 15
    });
    $('#jam_istirahat_selesai').timepicker({
      'timeFormat': 'H:i',
      'step': 15
    });
    $('#jam_pulang').timepicker({
      'timeFormat': 'H:i',
      'step': 15
    });
  });

  var url;

  function submitForm(method, id = null) {

    // remove form error message
    $('.form-text').remove();

    if (method == 'insert') {
      url = <?= json_encode(base_url('jam-kerja/create')) ?>;
    }
    if (method == 'update') {
      url = <?= json_encode(base_url()) ?> + `/jam-kerja/update/${id}`;
    }

  }

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
          if (error['nama_jam_kerja']) {
            $('input[name=nama_jam_kerja]').addClass('is-invalid');
            $('input[name=nama_jam_kerja]').after(`<div class="invalid-feedback">${error['nama_jam_kerja']}</div>`);
          }
          if (error['jam_masuk']) {
            $('input[name=jam_masuk]').addClass('is-invalid');
            $('input[name=jam_masuk]').after(`<div class="invalid-feedback">${error['jam_masuk']}</div>`);
          }
          if (error['jam_istirahat_mulai']) {
            $('input[name=jam_istirahat_mulai]').addClass('is-invalid');
            $('input[name=jam_istirahat_mulai]').after(`<div class="invalid-feedback">${error['jam_istirahat_mulai']}</div>`);
          }
          if (error['jam_istirahat_selesai']) {
            $('input[name=jam_istirahat_selesai]').addClass('is-invalid');
            $('input[name=jam_istirahat_selesai]').after(`<div class="invalid-feedback">${error['jam_istirahat_selesai']}</div>`);
          }
          if (error['jam_pulang']) {
            $('input[name=jam_pulang]').addClass('is-invalid');
            $('input[name=jam_pulang]').after(`<div class="invalid-feedback">${error['jam_pulang']}</div>`);
          }
        }

      },
    });

  });

  // ajax Create
  function createData() {
    removeValidation()
    $('#form')[0].reset();
    $('#id').val('');

    // change text
    $('#aiModalLabel').html('Tambah Jam Kerja');
    $('.modal-footer button[type=submit]').html('Create');

    $('#aiModal').modal('show');

    submitForm('insert');
  }

  // ajax Update
  function updateData(id) {
    removeValidation()
    $('#form')[0].reset();

    // change text
    $('#aiModalLabel').html('Edit Jam Kerja');
    $('.modal-footer button[type=submit]').html('Update');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/jam-kerja/update/${id}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);
        $('.modal-body input[name=id]').val(data.id);
        $('.modal-body input[name=nama_jam_kerja]').val(data.nama_jam_kerja);
        $('.modal-body input[name=jam_masuk]').val(data.jam_masuk);
        $('.modal-body input[name=jam_istirahat_mulai]').val(data.jam_istirahat_mulai);
        $('.modal-body input[name=jam_istirahat_selesai]').val(data.jam_istirahat_selesai);
        $('.modal-body input[name=jam_pulang]').val(data.jam_pulang);
        $('#aiModal').modal('show');
      },
      error: function(xhr, status, error) {
        alert(error);
      },
    });

    submitForm('update', id);
  }

  // ajax Delete
  function destroyData(id) {
    var x = confirm('Apakah anda yakin menghapus data?');
    if (x) {
      $.ajax({
        method: "POST",
        url: <?= json_encode(base_url()) ?> + `/jam-kerja/delete/${id}`,
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

  // set focus on shown modal 
  $('#aiModal').on('shown.bs.modal', function() {
    $('input[name=nama_jam_kerja]').trigger('focus')
  })

  function removeValidation() {
    $('form').find('.is-invalid').removeClass('is-invalid');
    $('form').find('.invalid-feedback').remove();
  }
</script>
<?= $this->endSection(); ?>