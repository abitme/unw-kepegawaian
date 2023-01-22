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
      <span>Tambah <?= $title ?></span>
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
              <th scope="col">Nama Pegawai</th>
              <th scope="col">Lumsum</th>
              <th scope="col">Keterangan</th>
              <th scope="col">Tanggal Awal</th>
              <th scope="col">Tanggal Akhir</th>
              <th scope="col">User</th>
              <th scope="col">Tanggal Input</th>
              <th scope="col" style="width: 90px;">Action</th>
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
  <div class="modal-dialog modal-lg" role="document">
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
          <input type="hidden" name="kode_tugas_dinas" id="kode_tugas_dinas">
          <div class="form-group">
            <label for="id_pegawai">Pegawai</label>
            <?= form_dropdown('id_pegawai', getDropdownList('pegawai', ['id', 'nama'], '', '- Pilih Pegawai -', 'nama', 'asc', ''), set_value('id_pegawai'), ['class' => 'form-control select2-pegawai', 'id' => 'id_pegawai', 'style' => 'width:100%']) ?>
          </div>
          <div class="form-group">
            <label for="keterangan">Keterangan (Nomor Surat Tugas)</label>
            <input type="text" class="form-control" id="keterangan" name="keterangan" autocomplete="off">
          </div>
          <div class="form-group">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="lumsum" value="1" name="lumsum">
              <label class="form-check-label lumsum" for="lumsum">
                Dapat Lumsum (Uang Makan & Transport)?
              </label>
            </div>
          </div>
          <div class="form-group">
            <label for="tanggal_awal">Tanggal_awal</label>
            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="tanggal_akhir">Tanggal Akhir</label>
            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" autocomplete="off">
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
        url: "<?= base_url('tugas-dinas/ajax_list') ?>",
        type: "POST",
      },
      //optional
      lengthMenu: [
        [10, 50, 100],
        [10, 50, 100]
      ],
      columnDefs: [{
        targets: [0],
        orderable: false,
      }, ],
      drawCallback: function() {
        $('[data-toggle="tooltip"]').tooltip()
      }
    });
    // select2 pegawai
    $('.select2-pegawai').select2({
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
      url = <?= json_encode(base_url('tugas-dinas/create')) ?>;
    }
    if (method == 'update') {
      url = <?= json_encode(base_url()) ?> + `/tugas-dinas/update/${id}`;
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
    $('span#select2-id_pegawai-container').html('-Pilih Pegawai-');

    // change text
    $('#aiModalLabel').html('Tambah Tugas Dinas');
    $('.modal-footer button[type=submit]').html('Create');

    $('#aiModal').modal('show');

    submitForm('insert');
  }

  // ajax Update
  function updateData(kode_tugas_dinas) {
    removeValidation()
    $('#form')[0].reset();

    // change text
    $('#aiModalLabel').html('Edit Tugas Dinas');
    $('.modal-footer button[type=submit]').html('Update');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/tugas-dinas/update/${kode_tugas_dinas}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);
        $('.modal-body input[name=id]').val(data.id);
        $('.modal-body input[name=kode_tugas_dinas]').val(data.kode_tugas_dinas);
        $('.modal-body select[name=id_pegawai]').val(data.id_pegawai).trigger('change');
        if (data.lumsum == true) {
          $('.modal-body input[name=lumsum]').prop("checked", true);
        } else {
          $('.modal-body input[name=lumsum]').prop("checked", false);
        }
        $('.modal-body input[name=keterangan]').val(data.keterangan);
        $('.modal-body input[name=tanggal_awal]').val(data.tanggal_awal);
        $('.modal-body input[name=tanggal_akhir]').val(data.tanggal_akhir);
        $('span#select2-id_pegawai-container').html(data.nama);

        $('#aiModal').modal('show');
      },
      error: function(xhr, status, error) {
        alert(error);
      },
    });

    submitForm('update', kode_tugas_dinas);
  }

  // ajax Delete
  function destroyData(kode_tugas_dinas) {
    var x = confirm('Apakah anda yakin menghapus data?');
    if (x) {
      $.ajax({
        method: "POST",
        url: <?= json_encode(base_url()) ?> + `/tugas-dinas/delete/${kode_tugas_dinas}`,
        data: {
          kode_tugas_dinas: kode_tugas_dinas
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
    $('input[name=id_unit]').trigger('focus')
  })

  function removeValidation() {
    $('form').find('.is-invalid').removeClass('is-invalid');
    $('form').find('.invalid-feedback').remove();
  }
</script>
<?= $this->endSection(); ?>