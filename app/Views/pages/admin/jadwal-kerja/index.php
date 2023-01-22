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
      <span>Tambah Jadwal Kerja</span>
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
              <th scope="col">Jadwal Kerja</th>
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

<!-- Modal View -->
<div class="modal fade" id="aiModalView" tabindex="-1" role="dialog" aria-labelledby="aiModalViewLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalViewLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless mb-0">
          <tr class="d-flex">
            <td class="col-3">Nama Jadwal Kerja</td>
            <td id="nama-jadwal-kerja">: &nbsp; </td>
          </tr>
        </table>
        <table class="table">
          <thead>
            <tr>
              <th>Hari</th>
              <th class="text-center">Libur</th>
              <th>Jam Kerja</th>
            </tr>
          </thead>
          <tr>
            <td class="py-1">
              <label for="">Senin</label>
            </td>
            <td class="text-center">
              <input type="checkbox" name="libur[0]" id="libur" class="" value="1" disabled>
            </td>
            <td class="py-1">
              <?= form_dropdown('id_jam_kerja[0]', getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'nama_jam_kerja', 'asc', ''), set_value('id_jam_kerja[]'), ['class' => 'form-control select2', 'id' => 'id_jam_kerja', 'style' => 'width:100%', 'disabled' => true]) ?>
            </td>
          </tr>
          <tr>
            <td class="py-1">
              <label for="">Selasa</label>
            </td>
            <td class="text-center">
              <input type="checkbox" name="libur[1]" id="libur" class="" value="1" disabled>
            </td>
            <td class="py-1">
              <?= form_dropdown('id_jam_kerja[1]', getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'nama_jam_kerja', 'asc', ''), set_value('id_jam_kerja[]'), ['class' => 'form-control select2', 'id' => 'id_jam_kerja', 'style' => 'width:100%', 'disabled' => true]) ?>
            </td>
          </tr>
          <tr>
            <td class="py-1">
              <label for="">Rabu</label>
            </td>
            <td class="text-center">
              <input type="checkbox" name="libur[2]" id="libur" class="" value="1" disabled>
            </td>
            <td class="py-1">
              <?= form_dropdown('id_jam_kerja[2]', getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'nama_jam_kerja', 'asc', ''), set_value('id_jam_kerja[]'), ['class' => 'form-control select2', 'id' => 'id_jam_kerja', 'style' => 'width:100%', 'disabled' => true]) ?>
            </td>
          </tr>
          <tr>
            <td class="py-1">
              <label for="">Kamis</label>
            </td>
            <td class="text-center">
              <input type="checkbox" name="libur[3]" id="libur" class="" value="1" disabled>
            </td>
            <td class="py-1">
              <?= form_dropdown('id_jam_kerja[3]', getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'nama_jam_kerja', 'asc', ''), set_value('id_jam_kerja[]'), ['class' => 'form-control select2', 'id' => 'id_jam_kerja', 'style' => 'width:100%', 'disabled' => true]) ?>
            </td>
          </tr>
          <tr>
            <td class="py-1">
              <label for="">Jumat</label>
            </td>
            <td class="text-center">
              <input type="checkbox" name="libur[4]" id="libur" class="" value="1" disabled>
            </td>
            <td class="py-1">
              <?= form_dropdown('id_jam_kerja[4]', getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'nama_jam_kerja', 'asc', ''), set_value('id_jam_kerja[]'), ['class' => 'form-control select2', 'id' => 'id_jam_kerja', 'style' => 'width:100%', 'disabled' => true]) ?>
            </td>
          </tr>
          <tr>
            <td class="py-1">
              <label for="">Sabtu</label>
            </td>
            <td class="text-center">
              <input type="checkbox" name="libur[5]" id="libur" class="" value="1" disabled>
            </td>
            <td class="py-1">
              <?= form_dropdown('id_jam_kerja[5]', getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'nama_jam_kerja', 'asc', ''), set_value('id_jam_kerja[]'), ['class' => 'form-control select2', 'id' => 'id_jam_kerja', 'style' => 'width:100%', 'disabled' => true]) ?>
            </td>
          </tr>
          <tr>
            <td class="py-1">
              <label for="">Minggu</label>
            </td>
            <td class="text-center">
              <input type="checkbox" name="libur[6]" id="libur" class="" value="1" disabled>
            </td>
            <td class="py-1">
              <?= form_dropdown('id_jam_kerja[6]', getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'nama_jam_kerja', 'asc', ''), set_value('id_jam_kerja[]'), ['class' => 'form-control select2', 'id' => 'id_jam_kerja', 'style' => 'width:100%', 'disabled' => true]) ?>
            </td>
          </tr>
        </table>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
        </div>
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
            <label for="nama_jadwal_kerja">Nama Jadwal Kerja</label>
            <input type="text" class="form-control" id="nama_jadwal_kerja" name="nama_jadwal_kerja" autocomplete="off">
          </div>
          <table class="table">
            <thead>
              <tr>
                <th>Hari</th>
                <th class="text-center">Libur</th>
                <th>Jam Kerja</th>
              </tr>
            </thead>
            <tr>
              <td class="py-1">
                <label for="">Senin</label>
                <input type="hidden" name="id_jadwal_kerja_detail[0]" id="id_jadwal_kerja_detail">
                <input type="hidden" name="day[0]" id="day" value="1">
              </td>
              <td class="text-center">
                <input type="checkbox" name="libur[0]" id="libur" class="" value="1">
              </td>
              <td class="py-1">
                <?= form_dropdown('id_jam_kerja[0]', getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'nama_jam_kerja', 'asc', ''), set_value('id_jam_kerja[]'), ['class' => 'form-control select2', 'id' => 'id_jam_kerja', 'style' => 'width:100%']) ?>
              </td>
            </tr>
            <tr>
              <td class="py-1">
                <label for="">Selasa</label>
                <input type="hidden" name="id_jadwal_kerja_detail[1]" id="id_jadwal_kerja_detail">
                <input type="hidden" name="day[1]" id="day" value="2">
              </td>
              <td class="text-center">
                <input type="checkbox" name="libur[1]" id="libur" class="" value="1">
              </td>
              <td class="py-1">
                <?= form_dropdown('id_jam_kerja[1]', getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'nama_jam_kerja', 'asc', ''), set_value('id_jam_kerja[]'), ['class' => 'form-control select2', 'id' => 'id_jam_kerja', 'style' => 'width:100%']) ?>
              </td>
            </tr>
            <tr>
              <td class="py-1">
                <label for="">Rabu</label>
                <input type="hidden" name="id_jadwal_kerja_detail[2]" id="id_jadwal_kerja_detail">
                <input type="hidden" name="day[2]" id="day" value="3">
              </td>
              <td class="text-center">
                <input type="checkbox" name="libur[2]" id="libur" class="" value="1">
              </td>
              <td class="py-1">
                <?= form_dropdown('id_jam_kerja[2]', getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'nama_jam_kerja', 'asc', ''), set_value('id_jam_kerja[]'), ['class' => 'form-control select2', 'id' => 'id_jam_kerja', 'style' => 'width:100%']) ?>
              </td>
            </tr>
            <tr>
              <td class="py-1">
                <label for="">Kamis</label>
                <input type="hidden" name="id_jadwal_kerja_detail[3]" id="id_jadwal_kerja_detail">
                <input type="hidden" name="day[3]" id="day" value="4">
              </td>
              <td class="text-center">
                <input type="checkbox" name="libur[3]" id="libur" class="" value="1">
              </td>
              <td class="py-1">
                <?= form_dropdown('id_jam_kerja[3]', getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'nama_jam_kerja', 'asc', ''), set_value('id_jam_kerja[]'), ['class' => 'form-control select2', 'id' => 'id_jam_kerja', 'style' => 'width:100%']) ?>
              </td>
            </tr>
            <tr>
              <td class="py-1">
                <label for="">Jumat</label>
                <input type="hidden" name="id_jadwal_kerja_detail[4]" id="id_jadwal_kerja_detail">
                <input type="hidden" name="day[4]" id="day" value="5">
              </td>
              <td class="text-center">
                <input type="checkbox" name="libur[4]" id="libur" class="" value="1">
              </td>
              <td class="py-1">
                <?= form_dropdown('id_jam_kerja[4]', getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'nama_jam_kerja', 'asc', ''), set_value('id_jam_kerja[]'), ['class' => 'form-control select2', 'id' => 'id_jam_kerja', 'style' => 'width:100%']) ?>
              </td>
            </tr>
            <tr>
              <td class="py-1">
                <label for="">Sabtu</label>
                <input type="hidden" name="id_jadwal_kerja_detail[5]" id="id_jadwal_kerja_detail">
                <input type="hidden" name="day[5]" id="day" value="6">
              </td>
              <td class="text-center">
                <input type="checkbox" name="libur[5]" id="libur" class="" value="1">
              </td>
              <td class="py-1">
                <?= form_dropdown('id_jam_kerja[5]', getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'nama_jam_kerja', 'asc', ''), set_value('id_jam_kerja[]'), ['class' => 'form-control select2', 'id' => 'id_jam_kerja', 'style' => 'width:100%']) ?>
              </td>
            </tr>
            <tr>
              <td class="py-1">
                <label for="">Minggu</label>
                <input type="hidden" name="id_jadwal_kerja_detail[6]" id="id_jadwal_kerja_detail">
                <input type="hidden" name="day[6]" id="day" value="7">
              </td>
              <td class="text-center">
                <input type="checkbox" name="libur[6]" id="libur" class="" value="1">
              </td>
              <td class="py-1">
                <?= form_dropdown('id_jam_kerja[6]', getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'nama_jam_kerja', 'asc', ''), set_value('id_jam_kerja[]'), ['class' => 'form-control select2', 'id' => 'id_jam_kerja', 'style' => 'width:100%']) ?>
              </td>
            </tr>
          </table>
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_default" value="1" name="is_default">
            <label class="form-check-label is_default" for="is_default">
              Jadwal Default?
            </label>
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
        url: "<?= base_url('jadwal-kerja/ajax_list') ?>",
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
    // // select2
    // $('.select2').select2();
  });

  // ajax view
  function viewData(id) {
    removeValidation()

    // change text
    $('#aiModalViewLabel').html('Detail Jadwal Kerja');

    $.ajax({
      method: 'GET',
      url: <?= json_encode(base_url()) ?> + `/jadwal-kerja/${id}`,
      cache: false,
      success: function(data) {
        obj = JSON.parse(data);
        data = obj.data;
        $('#aiModalView #nama-jadwal-kerja').html(`: &nbsp; ${data.nama_jadwal_kerja}`);
        for (let i = 0; i <= 6; i++) {
          data[i].libur == true ? $(`#aiModalView input[name='libur[${i}]']`).prop("checked", true) : $(`#aiModalView input[name='libur[${i}]']`).prop("checked", false);
          $(`#aiModalView select[name='id_jam_kerja[${i}]']`).val(data[i].id_jam_kerja);
        }
        $('#aiModalView').modal('show');
      },
      error: function(xhr, status, error) {
        alert(error);
      },
    });
  }

  var url;

  function submitForm(method, id = null) {

    // remove form error message
    $('.form-text').remove();

    if (method == 'insert') {
      url = <?= json_encode(base_url('jadwal-kerja/create')) ?>;
    }
    if (method == 'update') {
      url = <?= json_encode(base_url()) ?> + `/jadwal-kerja/update/${id}`;
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
          if (error['nama_jadwal_kerja']) {
            $('input[name=nama_jadwal_kerja]').addClass('is-invalid');
            $('input[name=nama_jadwal_kerja]').after(`<div class="invalid-feedback">${error['nama_jadwal_kerja']}</div>`);
          }
          for (let index = 0; index <= 6; index++) {
            if (error[`id_jam_kerja.${index}`]) {
              $(`select[name="id_jam_kerja[${index}]"`).addClass(`is-invalid`);
              $(`select[name="id_jam_kerja[${index}]"`).after(`<div class="invalid-feedback">${error[`id_jam_kerja.${index}`]}</div>`);
            }
          }
          if (error['is_default']) {
            $('input[name=is_default]').addClass('is-invalid');
            $('.form-check-label.is_default').after(`<div class="invalid-feedback">${error['is_default']}</div>`);
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
    for (let i = 0; i <= 6; i++) {
      $(`#aiModalForm input[name='id_jadwal_kerja_detail[${i}]']`).val('')
    }

    // change text
    $('#aiModalFormLabel').html('Tambah Jadwal Kerja');
    $('.modal-footer button[type=submit]').html('Create');

    $('#aiModalForm').modal('show');

    submitForm('insert');
  }

  // ajax Update
  function updateData(id) {
    removeValidation()
    $('#form')[0].reset();

    // change text
    $('#aiModalFormLabel').html('Edit Jadwal Kerja');
    $('.modal-footer button[type=submit]').html('Update');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/jadwal-kerja/update/${id}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);
        $('#aiModalForm input[name=id]').val(data.id);
        $('#aiModalForm input[name=nama_jadwal_kerja]').val(data.nama_jadwal_kerja);
        for (let i = 0; i <= 6; i++) {
          $(`#aiModalForm input[name='id_jadwal_kerja_detail[${i}]']`).val(data[i].id);
          data[i].libur == true ? $(`#aiModalForm input[name='libur[${i}]']`).prop("checked", true) : $(`#aiModalForm input[name='libur[${i}]']`).prop("checked", false);
          $(`#aiModalForm select[name='id_jam_kerja[${i}]']`).val(data[i].id_jam_kerja);
        }
        if (data.is_default == true) {
          $('.modal-body input[name=is_default]').prop("checked", true);
        } else {
          $('.modal-body input[name=is_default]').prop("checked", false);
        }
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
        url: <?= json_encode(base_url()) ?> + `/jadwal-kerja/delete/${id}`,
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
    $('input[name=nama_jadwal_kerja]').trigger('focus')
  })

  function removeValidation() {
    $('.modal').find('.is-invalid').removeClass('is-invalid');
    $('.modal').find('.invalid-feedback').remove();
  }
</script>
<?= $this->endSection(); ?>