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
      <li class="breadcrumb-item active" aria-current="page">Penilaian</li>
    </ol>
  </nav>
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
        <p class="font-weight-bold">Penilaian Sebagai Karyawan</p>
        <table class="table table-hover mb-5" id="myTable" style="overflow-x:auto;">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Nama Pegawai</th>
              <th scope="col">Jabatan</th>
              <th scope="col">Status Penilaian</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 0;
            foreach ($userDinilaiKaryawan as $row) : $no++; ?>
              <?php
              $db         = \Config\Database::connect();
              $penilaian = $db->table('penilaian')->where('id_pegawai_penilai', $userPenilai->id_pegawai)->where('id_pegawai_dinilai', $row->id)->where('jenis', 'Karyawan')->get()->getRow();
              ?>
              <tr>
                <td><?= $no ?></td>
                <td><?= $row->nama ?></td>
                <td><?= $row->jabatan ?></td>
                <td><?= $penilaian ? 'Sudah Dinilai' : 'Belum Dinilai' ?></td>
                <td>
                  <?php if (!$penilaian) : ?>
                    <a href="<?= base_url("penilaian/$row->id/karyawan") ?>" class="btn btn-primary">
                      Beri Nilai
                    </a>
                  <?php endif ?>
                </td>

              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
        <p class="font-weight-bold ">Penilaian Sebagai Pejabat Struktural</p>
        <table class="table table-hover" id="myTable" style="overflow-x:auto;">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Nama Pegawai</th>
              <th scope="col">Jabatan</th>
              <th scope="col">Status Penilaian</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 0;
            foreach ($userDinilaiPejabatStruktural as $row) : $no++; ?>
              <?php
              $db         = \Config\Database::connect();
              $penilaian = $db->table('penilaian')->where('id_pegawai_penilai', $userPenilai->id_pegawai)->where('id_pegawai_dinilai', $row->id)->where('jenis', 'Pejabat Struktural')->get()->getRow();
              ?>
              <tr>
                <td><?= $no ?></td>
                <td><?= $row->nama ?></td>
                <td><?= $row->jabatan ?></td>
                <td><?= $penilaian ? 'Sudah Dinilai' : 'Belum Dinilai' ?></td>
                <td>
                  <?php if (!$penilaian) : ?>
                    <a href="<?= base_url("penilaian/$row->id/pejabat-struktural") ?>" class="btn btn-primary">
                      Beri Nilai 
                    </a>
                  <?php endif ?>
                </td>

              </tr>
            <?php endforeach ?>
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
      <form id="form" action="" method="post">
        <div class="modal-body">
          <input type="hidden" name="id" id="id">
          <div class="form-group">
            <label for="pertanyaan">Pertanyaan</label>
            <input type="text" class="form-control" id="pertanyaan" name="pertanyaan" autocomplete="off">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
          <button type="submit" id="submit" class="btn btn-primary">Create</button>
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
  // $(document).ready(function() {
  //   table = $('#myTable').DataTable();
  // });

  // set url form
  var url;

  function submit_form(method, id = null) {

    // remove form error message
    $('.form-text').remove();

    if (method == 'insert') {
      url = <?= json_encode(base_url('pertanyaan/create')) ?>;
    }
    if (method == 'update') {
      url = <?= json_encode(base_url()) ?> + `/pertanyaan/update/${id}`;
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
        let obj = $.parseJSON(data);
        if (obj.status) {
          $('#aiModal').modal('hide')
          table.ajax.reload(null, false);

          $('.toast-body').text(obj.message);
          setTimeout(function() {
            $('.toast').toast({
              // autohide: false
              delay: 3000
            });
            $('.toast').toast('show');
          }, 400);
        } else {
          removeValidation()

          let error = obj.data;
          if (error['pertanyaan']) {
            $('input[name=pertanyaan]').addClass('is-invalid');
            $('input[name=pertanyaan]').after(`<div class="invalid-feedback">${error['pertanyaan']}</div>`);
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
    $('#aiModalLabel').html('Tambah Pertanyaan');
    $('.modal-footer button[type=submit]').html('Create');

    $('#aiModal').modal('show');

    submit_form('insert');
  }

  // Ajax Update
  function updateData(id) {
    removeValidation()
    $('#form')[0].reset();

    // change text
    $('#aiModalLabel').html('Edit Pertanyaan');
    $('.modal-footer button[type=submit]').html('Update');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/pertanyaan/update/${id}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);
        $('.modal-body input[name=id]').val(data.id);
        $('.modal-body input[name=pertanyaan]').val(data.pertanyaan);
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
        url: <?= json_encode(base_url()) ?> + `/pertanyaan/delete/${id}`,
        data: {
          id: id
        },
        cache: false,
        success: function(data) {
          let obj = $.parseJSON(data);
          table.ajax.reload(null, false);

          $('.toast-body').text(obj.message);
          setTimeout(function() {
            $('.toast').toast({
              // autohide: false
              delay: 3000
            });
            $('.toast').toast('show');
          }, 400);
        },
        error: function(xhr, status, error) {
          alert(error);
        },
      });
    }
  }

  $('#aiModal').on('shown.bs.modal', function() {
    $('input[name=pertanyaan]').trigger('focus')
  })

  function removeValidation() {
    $('.form-group > *').removeClass('is-invalid');
    $('.form-group > *.invalid-feedback').remove();
  }
</script>
<?= $this->endSection(); ?>