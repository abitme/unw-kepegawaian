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
      <span>Tambah Unit Piket</span>
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
              <th scope="col">Unit</th>
              <th scope="col">Hari Piket</th>
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

<!-- Modal Form -->
<div class="modal fade" id="aiModalForm" tabindex="-1" role="dialog" aria-labelledby="aiModalFormLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalFormLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form" action="" method="post">
          <input type="hidden" name="id" id="id">
          <div class="form-group">
            <label for="id_unit">Unit</label>
            <?= form_dropdown('id_unit', getDropdownList('unit', ['id', 'nama_unit'], '', '- Pilih Unit -', 'nama_unit', 'asc', ''), set_value('id_unit'), ['class' => 'form-control select2-unit', 'id' => 'id_unit', 'style' => 'width:100%']) ?>
          </div>
          <table class="table">
            <thead>
              <tr>
                <th>Hari</th>
                <th class="text-center">Piket</th>
              </tr>
            </thead>
            <tr>
              <td class="py-1">
                <label for="">Senin</label>
                <input type="hidden" name="day[1]" id="day" value="1">
              </td>
              <td class="text-center">
                <input type="checkbox" name="piket[1]" id="piket" class="" value="1">
              </td>
            </tr>
            <tr>
              <td class="py-1">
                <label for="">Selasa</label>
                <input type="hidden" name="day[2]" id="day" value="2">
              </td>
              <td class="text-center">
                <input type="checkbox" name="piket[2]" id="piket" class="" value="2">
              </td>
            </tr>
            <tr>
              <td class="py-1">
                <label for="">Rabu</label>
                <input type="hidden" name="day[3]" id="day" value="3">
              </td>
              <td class="text-center">
                <input type="checkbox" name="piket[3]" id="piket" class="" value="3">
              </td>
            </tr>
            <tr>
              <td class="py-1">
                <label for="">Kamis</label>
                <input type="hidden" name="day[4]" id="day" value="4">
              </td>
              <td class="text-center">
                <input type="checkbox" name="piket[4]" id="piket" class="" value="4">
              </td>
            </tr>
            <tr>
              <td class="py-1">
                <label for="">Jumat</label>
                <input type="hidden" name="day[5]" id="day" value="5">
              </td>
              <td class="text-center">
                <input type="checkbox" name="piket[5]" id="piket" class="" value="5">
              </td>
            </tr>
            <tr>
              <td class="py-1">
                <label for="">Sabtu</label>
                <input type="hidden" name="day[6]" id="day" value="6">
              </td>
              <td class="text-center">
                <input type="checkbox" name="piket[6]" id="piket" class="" value="6">
              </td>
            </tr>
            <tr>
              <td class="py-1">
                <label for="">Minggu</label>
                <input type="hidden" name="day[7]" id="day" value="7">
              </td>
              <td class="text-center">
                <input type="checkbox" name="piket[7]" id="piket" class="" value="7">
              </td>
            </tr>
          </table>
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
        url: "<?= base_url('unit-piket/ajax_list') ?>",
        type: "POST",
      },
      //optional
      lengthMenu: [
        [100, 150, 200],
        [100, 150, 200]
      ],
      columnDefs: [{
        targets: [0, 2, 3],
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
      url = <?= json_encode(base_url('unit-piket/create')) ?>;
    }
    if (method == 'update') {
      url = <?= json_encode(base_url()) ?> + `/unit-piket/update/${id}`;
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
          $('#aiModalForm').modal('hide')
          table.ajax.reload(null, false);
          tata.success('Success', obj.message)
        } else {
          tata.error('Error', obj.message)
          removeValidation()

          let error = obj.data.errors;
          if (error['id_unit']) {
            $('select[name=id_unit] + .select2 .select2-selection--single').addClass('is-invalid');
            $('select[name=id_unit] + .select2 .select2-selection--single').after(`<div class="invalid-feedback">${error['id_unit']}</div>`);
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
    $('span#select2-id_unit-container').html('-Pilih Unit-');

    // change text
    $('#aiModalFormLabel').html('Tambah Unit Piket');
    $('.modal-footer button[type=submit]').html('Create');

    $('#aiModalForm').modal('show');

    submitForm('insert');
  }

  // ajax Update
  function updateData(id) {
    removeValidation()
    $('#form')[0].reset();

    // change text
    $('#aiModalFormLabel').html('Edit Unit Piket');
    $('.modal-footer button[type=submit]').html('Update');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/unit-piket/update/${id}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);
        $('#aiModalForm input[name=id]').val(data.id);
        $('.modal-body select[name=id_unit]').val(data.id_unit).trigger('change');
        $('span#select2-id_unit-container').html(data.nama_unit);
        $.each(data.piket, function(i, v) {
          $(`#aiModalForm input[name='piket[${v}]']`).prop("checked", true)
        });
        $('#aiModalForm').modal('show');
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
        url: <?= json_encode(base_url()) ?> + `/unit-piket/delete/${id}`,
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
  $('#aiModalForm').on('shown.bs.modal', function() {
    $('input[name=id_unit]').trigger('focus')
  })

  function removeValidation() {
    $('.modal').find('.is-invalid').removeClass('is-invalid');
    $('.modal').find('.invalid-feedback').remove();
  }
</script>
<?= $this->endSection(); ?>