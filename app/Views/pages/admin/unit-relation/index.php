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
    <a href="javascript:void(0)" class="btn btn-primary shadow-sm mb-3" onclick="createDataUnitRelations()">
      <i class=" fas fa-plus-circle"></i>
      <span>Tambah Relasi</span>
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
              <th scope="col">Unit Parent</th>
              <th scope="col">Unit Child</th>
              <th scope="col">Depth</th>
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

<!-- Modal Unit Relations -->
<div class="modal fade" id="aiModalUnitRelations" tabindex="-1" role="dialog" aria-labelledby="aiModalUnitRelationsLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalUnitRelationsLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form-unit-realtions" action="" method="post">
          <input type="hidden" name="id" id="id">
          <div class="form-group">
            <label for="parent">Parent</label>
            <?= form_dropdown('parent', getDropdownList('unit', ['id', 'nama_unit'], '', '- Pilih Unit -', 'nama_unit', 'asc', ''), set_value('parent'), ['class' => 'form-control select2-unit', 'id' => 'parent', 'style' => 'width:100%']) ?>
          </div>
          <div class="form-group">
            <label for="child">Child</label>
            <?= form_dropdown('child', getDropdownList('unit', ['id', 'nama_unit'], '', '- Pilih Unit -', 'nama_unit', 'asc', ''), set_value('child'), ['class' => 'form-control select2-unit', 'id' => 'child', 'style' => 'width:100%']) ?>
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
        url: "<?= base_url('unit-relation/ajax_list') ?>",
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
      //   if (aData[2].search("updateDataUnitRelations") != -1) {
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

  // Unit Relations - set url form
  var url;

  function submitFormUnitRelations(method, id = null) {

    // remove form error message
    $('.form-text').remove();

    if (method == 'insert') {
      url = <?= json_encode(base_url('unit-relation/create')) ?>;
    }
    if (method == 'update') {
      url = <?= json_encode(base_url()) ?> + `/unit-relation/update/${id}`;
    }

  }

  // Unit Relations - handle submit form
  $('#form-unit-realtions').submit(function(e) {

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
          $('#aiModalUnitRelations').modal('hide')
          table.ajax.reload(null, false);
          tata.success('Success', obj.message)
        } else {
          tata.error('Error', obj.message)
          removeValidation()

          let error = obj.data.errors;
          if (error['parent']) {
            $('select[name=parent] + .select2 .select2-selection--single').addClass('is-invalid');
            $('select[name=parent] + .select2 .select2-selection--single').after(`<div class="invalid-feedback">${error['parent']}</div>`);
          }
          if (error['child']) {
            $('select[name=child] + .select2 .select2-selection--single').addClass('is-invalid');
            $('select[name=child] + .select2 .select2-selection--single').after(`<div class="invalid-feedback">${error['child']}</div>`);
          }
        }

      },
    });

  });

  // Unit Relations - ajax Create
  function createDataUnitRelations() {
    removeValidation()
    $('#form-unit-realtions')[0].reset();
    $('#id').val('');
    $('span#select2-parent-container').html('-Pilih Unit-');
    $('span#select2-child-container').html('-Pilih Unit-');

    // change text
    $('#aiModalUnitRelationsLabel').html('Tambah Relasi');
    $('.modal-footer button[type=submit]').html('Create');

    $('#aiModalUnitRelations').modal('show');

    submitFormUnitRelations('insert');
  }

  // Unit Relations - ajax Update
  function updateDataUnitRelations(id) {
    removeValidation()
    $('#form-unit-realtions')[0].reset();

    // change text
    $('#aiModalUnitRelationsLabel').html('Edit Relasi');
    $('.modal-footer button[type=submit]').html('Update');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/unit-relation/update/${id}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);
        $('.modal-body input[name=id]').val(data.id);
        $('.modal-body select[name=parent]').val(data.parent).trigger('change');
        $('span#select2-parent-container').html(data.nama_unit);
        $('.modal-body select[name=child]').val(data.child).trigger('change');
        $('span#select2-child-container').html(data.nama_unit);
        $('#aiModalUnitRelations').modal('show');
      },
      error: function(xhr, status, error) {
        alert(error);
      },
    });

    submitFormUnitRelations('update', id);
  }

  // Unit Relations - ajax Delete
  function destroyDataUnitRelations(id) {
    var x = confirm('Apakah anda yakin menghapus data?');
    if (x) {
      $.ajax({
        method: "POST",
        url: <?= json_encode(base_url()) ?> + `/unit-relation/delete/${id}`,
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

  function removeValidation() {
    $('form').find('.is-invalid').removeClass('is-invalid');
    $('form').find('.invalid-feedback').remove();
  }
</script>
<?= $this->endSection(); ?>