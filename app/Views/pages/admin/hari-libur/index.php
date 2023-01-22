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
      <span>Tambah Hari Libur</span>
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
              <th scope="col">Keterangan</th>
              <th scope="col">Tanggal</th>
              <th scope="col">Unit Piket</th>
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
            <label for="keterangan">Keterangan</label>
            <input type="text" class="form-control" id="keterangan" name="keterangan" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="tanggal_awal">Tanggal_awal</label>
            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="tanggal_akhir">Tanggal Akhir</label>
            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="id_unit_piket">Unit Piket</label>
            <select name="id_unit_piket[]" id="id_unit_piket" class="form-control select2-unit" multiple="multiple" style="width:100%"></select>
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
        url: "<?= base_url('hari-libur/ajax_list') ?>",
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
    // select2 unit
    $('.select2-unit').select2({
      ajax: {
        url: "<?= base_url('unit-relation/ajax_select2') ?>",
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

  var url;

  function submitForm(method, id = null) {

    // remove form error message
    $('.form-text').remove();

    if (method == 'insert') {
      url = <?= json_encode(base_url('hari-libur/create')) ?>;
    }
    if (method == 'update') {
      url = <?= json_encode(base_url()) ?> + `/hari-libur/update/${id}`;
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
          if (error['tanggal']) {
            $('input[name=tanggal]').addClass('is-invalid');
            $('input[name=tanggal]').after(`<div class="invalid-feedback">${error['tanggal']}</div>`);
          }
          if (error['keterangan']) {
            $('input[name=keterangan]').addClass('is-invalid');
            $('input[name=keterangan]').after(`<div class="invalid-feedback">${error['keterangan']}</div>`);
          }
          if (error['tanggal_awal']) {
            $('input[name=tanggal_awal]').addClass('is-invalid');
            $('input[name=tanggal_awal]').after(`<div class="invalid-feedback">${error['tanggal_awal']}</div>`);
          }
          if (error['tanggal_akhir']) {
            $('input[name=tanggal_akhir]').addClass('is-invalid');
            $('input[name=tanggal_akhir]').after(`<div class="invalid-feedback">${error['tanggal_akhir']}</div>`);
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
    $(`#id_unit_piket`).html('');

    // change text
    $('#aiModalLabel').html('Tambah Hari Libur');
    $('.modal-footer button[type=submit]').html('Create');

    $('#aiModal').modal('show');

    submitForm('insert');
  }

  // ajax Update
  function updateData(kode_unik) {
    removeValidation()
    $('#form')[0].reset();
    $(`#id_unit_piket`).html('');

    // change text
    $('#aiModalLabel').html('Edit Hari Libur');
    $('.modal-footer button[type=submit]').html('Update');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/hari-libur/update/${kode_unik}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);

        $(`#id_unit_piket`).html('');
        $.each(data.id_unit_piket, function(i, v) {
          $(`#id_unit_piket`).append(`<option value="${v.id}" selected="selected">${v.nama_unit}</option>`);
        });
        $('.modal-body input[name=id]').val(data.id);
        $('.modal-body input[name=keterangan]').val(data.keterangan);
        $('.modal-body input[name=tanggal_awal]').val(data.tanggal_awal);
        $('.modal-body input[name=tanggal_akhir]').val(data.tanggal_akhir);
        $('#aiModal').modal('show');
      },
      error: function(xhr, status, error) {
        alert(error);
      },
    });

    submitForm('update', kode_unik);
  }

  // ajax Delete
  function destroyData(kode_unik) {
    var x = confirm('Apakah anda yakin menghapus data?');
    if (x) {
      $.ajax({
        method: "POST",
        url: <?= json_encode(base_url()) ?> + `/hari-libur/delete/${kode_unik}`,
        data: {
          kode_unik: kode_unik
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
    $('input[name=tanggal]').trigger('focus')
  })

  function removeValidation() {
    $('form').find('.is-invalid').removeClass('is-invalid');
    $('form').find('.invalid-feedback').remove();
  }
</script>
<?= $this->endSection(); ?>