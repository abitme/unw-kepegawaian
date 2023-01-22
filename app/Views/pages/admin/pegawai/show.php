<?= $this->extend('layouts/admin'); ?>

<?= $this->section('append-style'); ?>
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
      <li class="breadcrumb-item"><a href="<?= base_url('pegawai') ?>">Pegawai</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
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
        <h6 class="m-0 font-weight-bold text-primary"><?= $title ?></h6>
      </div>
      <div class="card-body table-responsive">
        <!-- Table content -->
        <div class="row mb-4">
          <div class="col-6">
            <table class="table table-borderless mb-0">
              <tr class="d-flex">
                <td class="col-5">Foto</td>
                <td>
                  <?php if (!empty($user->image) && file_exists("assets/img/users/{$user->image}")) : ?>
                    <img src="/assets/img/users/thumbnail/<?= $user->image ?>" alt="" class="img-thumb-list mr-2 mb-2 mb-md-0">
                  <?php else : ?>
                    <img src="/assets/img/users/default.jpg" alt="" class="img-thumb-list mr-2 mb-2 mb-md-0">
                  <?php endif ?>
                </td>
              </tr>
              <tr class="d-flex">
                <td class="col-5">NIK</td>
                <td>: &nbsp; <?= $pegawai->nik ?></td>
              </tr>
              <tr class="d-flex">
                <td class="col-5">Nama</td>
                <td>: &nbsp; <?= $pegawai->nama ?></td>
              </tr>
              <tr class="d-flex">
                <td class="col-5">Tempat Tanggal Lahir</td>
                <td>: &nbsp; <?= $pegawai->tempat_lahir . ', ' . date('d/m/Y', strtotime($pegawai->tanggal_lahir)) ?></td>
              </tr>
              <tr class="d-flex">
                <td class="col-5">Jenis Kelamin</td>
                <td>: &nbsp; <?= $pegawai->jenis_kelamin ?></td>
              </tr>
              <tr class="d-flex">
                <td class="col-5">Alamat</td>
                <td>: &nbsp; <?= $pegawai->alamat ?></td>
              </tr>
              <tr class="d-flex">
                <td class="col-5">Agama</td>
                <td>: &nbsp; <?= $pegawai->agama ?></td>
              </tr>
              <tr class="d-flex">
                <td class="col-5">Pendidikan</td>
                <td>: &nbsp; <?= $pegawai->pendidikan ?></td>
              </tr>
            </table>
          </div>
          <div class="col-6">
            <table class="table table-borderless mb-0">
              <tr class="d-flex">
                <td class="col-5">Username</td>
                <td>: &nbsp; <?= $user->username ?></td>
              </tr>
              <tr class="d-flex">
                <td class="col-5">Email</td>
                <td>: &nbsp; <?= $user->email ?></td>
              </tr>
            </table>

          </div>
        </div>
        <!-- datatable -->
        <div class="row mb-4">
          <div class="col-12">

            <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
              <!-- nav tab jabatan -->
              <li class="nav-item" role="presentation">
                <a class="nav-link active" id="jabatan-tab" data-toggle="tab" href="#jabatan" role="tab" aria-controls="jabatan" aria-selected="true">Jabatan</a>
              </li>
              <!-- nav tab jabatan-fungsional -->
              <li class="nav-item" role="presentation">
                <a class="nav-link" id="jabatan-fungsional-tab" data-toggle="tab" href="#jabatan-fungsional" role="tab" aria-controls="jabatan-fungsional" aria-selected="false">Jabatan Fungsional</a>
              </li>
              <!-- nav tab jabatan-struktural -->
              <li class="nav-item" role="presentation">
                <a class="nav-link" id="jabatan-struktural-tab" data-toggle="tab" href="#jabatan-struktural" role="tab" aria-controls="jabatan-struktural" aria-selected="false">Jabatan Struktural</a>
              </li>
              <!-- nav tab dokumen -->
              <li class="nav-item" role="presentation">
                <a class="nav-link" id="dokumen-tab" data-toggle="tab" href="#dokumen" role="tab" aria-controls="dokumen" aria-selected="false">Dokumen</a>
              </li>
            </ul>
            <div class="tab-content" id="myTabContent">
              <!-- tab jabatan -->
              <div class="tab-pane fade show active" id="jabatan" role="tabpanel" aria-labelledby="jabatan-tab">
                <div class="row mb-4">
                  <div class="col-12">

                    <!-- Create Button -->
                    <div class="row">
                      <div class="col-md">
                        <a href="javascript:void(0)" class="btn btn-primary btn-action shadow-sm mb-3" onclick="createDataJabatan()">
                          <i class=" fas fa-plus-circle"></i>
                          <span>Tambah Jabatan</span>
                        </a>
                      </div>
                    </div>

                    <table class="table table-borderless mb-0" id="table-jabatan">
                      <thead>
                        <tr>
                          <th scope="col">No</th>
                          <th scope="col">Unit</th>
                          <th scope="col">Jabatan</th>
                          <th scope="col">TMT</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>

                  </div>
                </div>
              </div>
              <!-- tab jabatan-fungsional -->
              <div class="tab-pane fade" id="jabatan-fungsional" role="tabpanel" aria-labelledby="jabatan-fungsional-tab">
                <div class="row mb-4">
                  <div class="col-12">

                    <!-- Create Button -->
                    <div class="row">
                      <div class="col-md">
                        <a href="javascript:void(0)" class="btn btn-primary btn-action shadow-sm mb-3" onclick="createDataJabatanFungsional()">
                          <i class=" fas fa-plus-circle"></i>
                          <span>Tambah Jabatan</span>
                        </a>
                      </div>
                    </div>

                    <table class="table table-borderless mb-0" id="table-jabatan-fungsional">
                      <thead>
                        <tr>
                          <th scope="col">No</th>
                          <th scope="col">Jabatan</th>
                          <th scope="col">Jabatan Fungsional</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>

                  </div>
                </div>
              </div>
              <!-- tab jabatan-struktural -->
              <div class="tab-pane fade" id="jabatan-struktural" role="tabpanel" aria-labelledby="jabatan-struktural-tab">
                <div class="row mb-4">
                  <div class="col-12">

                    <!-- Create Button -->
                    <div class="row">
                      <div class="col-md">
                        <a href="javascript:void(0)" class="btn btn-primary btn-action shadow-sm mb-3" onclick="createDataJabatanStruktural()">
                          <i class=" fas fa-plus-circle"></i>
                          <span>Tambah Jabatan</span>
                        </a>
                      </div>
                    </div>

                    <table class="table table-borderless mb-0" id="table-jabatan-struktural">
                      <thead>
                        <tr>
                          <th scope="col">No</th>
                          <th scope="col">Unit</th>
                          <th scope="col">Jabatan</th>
                          <th scope="col">Tanggal Mulai</th>
                          <th scope="col">Tanggal Selesai</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>

                  </div>
                </div>
              </div>
              <!-- tab dokumen -->
              <div class="tab-pane fade" id="dokumen" role="tabpanel" aria-labelledby="dokumen-tab">
                <div class="row mb-4">
                  <div class="col-12">

                    <!-- Create Button -->
                    <div class="row">
                      <div class="col-md">
                        <a href="javascript:void(0)" class="btn btn-primary btn-action shadow-sm mb-3" onclick="createDataDokumen()">
                          <i class=" fas fa-plus-circle"></i>
                          <span>Tambah Dokumen</span>
                        </a>
                      </div>
                    </div>

                    <table class="table table-borderless mb-0" id="table-pegawai-dokumen">
                      <thead>
                        <tr>
                          <th scope="col">No</th>
                          <th scope="col">Nama Dokumen</th>
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

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Jabatan -->
<div class="modal fade" id="aiModalJabatan" tabindex="-1" role="dialog" aria-labelledby="aiModalJabatanLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalJabatanLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="form-jabatan" action="" method="post" class="">
        <div class="modal-body form-row mx-0">
          <input type="hidden" name="id" id="id" value="" />
          <input type="hidden" name="id_pegawai" id="id_pegawai" value="<?= $pegawai->id ?>">
          <div class="form-group">
            <label for="id_jabatan_u">Jabatan</label>
            <?= form_dropdown('id_jabatan_u', getDropdownJabatan('jabatan_u_view', ['id', 'nama_unit', 'nama_jabatan'], '', '- Pilih Jabatan -', 'nama_jabatan', 'asc', ''), set_value('id_jabatan_u'), ['class' => 'form-control select2', 'id' => 'id_jabatan_u', 'style' => 'width:100%']) ?>
          </div>
          <div class="form-group">
            <label for="tmt">TMT</label>
            <input type="date" name="tmt" id="tmt" class="form-control">
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

<!-- Modal Jabatan Fungsional -->
<div class="modal fade" id="aiModalJabatanFungsional" tabindex="-1" role="dialog" aria-labelledby="aiModalJabatanFungsionalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalJabatanFungsionalLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="form-jabatan-fungsional" action="" method="post" class="">
        <div class="modal-body form-row mx-0">
          <input type="hidden" name="id" id="id" value="" />
          <input type="hidden" name="id_pegawai" id="id_pegawai" value="<?= $pegawai->id ?>">
          <div class="form-group">
            <label for="id_jabatan_fungsional">Jabatan</label>
            <?= form_dropdown('id_jabatan_fungsional', getDropdownJabatan('jabatan_fungsional_view', ['id', 'nama_jabatan', 'nama_jabatan_fungsional'], '', '- Pilih Jabatan -', 'nama_jabatan_fungsional', 'asc', ''), set_value('id_jabatan_fungsional'), ['class' => 'form-control select2', 'id' => 'id_jabatan_fungsional', 'style' => 'width:100%']) ?>
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

<!-- Modal Jabatan Struktural-->
<div class="modal fade" id="aiModalJabatanStruktural" tabindex="-1" role="dialog" aria-labelledby="aiModalJabatanStrukturalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalJabatanStrukturalLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="form-jabatan-struktural" action="" method="post" class="">
        <div class="modal-body form-row mx-0">
          <input type="hidden" name="id" id="id" value="" />
          <input type="hidden" name="id_pegawai" id="id_pegawai" value="<?= $pegawai->id ?>">
          <div class="form-group">
            <label for="id_jabatan_struktural_u">Jabatan</label>
            <?= form_dropdown('id_jabatan_struktural_u', getDropdownJabatan('jabatan_struktural_u_view', ['id', 'nama_unit', 'nama_jabatan_struktural'], '', '- Pilih Jabatan -', 'nama_jabatan_struktural', 'asc', ''), set_value('id_jabatan_struktural_u'), ['class' => 'form-control select2', 'id' => 'id_jabatan_struktural_u', 'style' => 'width:100%']) ?>
          </div>
          <div class="form-group">
            <label for="tanggal_mulai">Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control">
          </div>
          <div class="form-group">
            <label for="tanggal_selesai">Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control">
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

<!-- Modal Dokumen-->
<div class="modal fade" id="aiModalDokumen" tabindex="-1" role="dialog" aria-labelledby="aiModalDokumenLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalDokumenLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="form-dokumen" action="" method="post" class="">
        <div class="modal-body form-row mx-0">
          <input type="hidden" name="id" id="id" value="" />
          <input type="hidden" name="id_pegawai" id="id_pegawai" value="<?= $pegawai->id ?>">
          <div class="form-group">
            <label for="file">File</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="dokumen" name="dokumen" onchange="labelFileNoRemove()">
              <label class="custom-file-label" for="file">Choose file</label>
              <div class="invalid-feedback"></div>
            </div>
          </div>
          <div class="form-group">
            <label for="nama_dokumen">Nama Dokumen</label>
            <input type="text" name="nama_dokumen" id="nama_dokumen" class="form-control">
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
  $(document).ready(function() {

    // datatable
    tableJabatan = $('#table-jabatan').DataTable({
      processing: true,
      serverSide: true,
      order: [],
      ajax: {
        url: "<?= base_url("pegawai-jabatan/ajax_list/$pegawai->id") ?>",
        type: "POST"
      },
      //optional
      // lengthMenu: [
      //   [10, 50, 100],
      //   [10, 50, 100]
      // ],
      columnDefs: [{
        targets: [0, 3, 4],
        orderable: false,
      }, ],
      drawCallback: function() {
        $('[data-toggle="tooltip"]').tooltip()
      }
    });
    tableJabatanFungsional = undefined;
    tableJabatanStruktural = undefined;
    tableDokumen = undefined;

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
      if (e.target.hash == '#jabatan-fungsional' && tableJabatanFungsional == undefined) {
        tableJabatanFungsional = $('#table-jabatan-fungsional').DataTable({
          processing: true,
          serverSide: true,
          order: [],
          ajax: {
            url: "<?= base_url("pegawai-jabatan-fungsional/ajax_list/$pegawai->id") ?>",
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
          }
        });
      }
      if (e.target.hash == '#jabatan-struktural' && tableJabatanStruktural == undefined) {
        tableJabatanStruktural = $('#table-jabatan-struktural').DataTable({
          processing: true,
          serverSide: true,
          order: [],
          ajax: {
            url: "<?= base_url("pegawai-jabatan-struktural-unit/ajax_list/$pegawai->id") ?>",
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
          }
        });
      }
      if (e.target.hash == '#dokumen' && tableDokumen == undefined) {
        tableDokumen = $('#table-pegawai-dokumen').DataTable({
          processing: true,
          serverSide: true,
          order: [],
          ajax: {
            url: "<?= base_url("pegawai-dokumen/ajax_list/$pegawai->id") ?>",
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
          }
        });
      }
    });

    // select2
    $('.select2').select2();
  });

  // ======================= Form Jabatan ===============================================

  // set url form
  var url;

  function submitFormJabatan(method, id = null) {

    // remove form error message
    $('.form-text').remove();

    if (method == 'insert') {
      url = <?= json_encode(base_url('pegawai-jabatan/create')) ?>;
    }
    if (method == 'update') {
      url = <?= json_encode(base_url()) ?> + `/pegawai-jabatan/update/${id}`;
    }

  }

  // handle submit form
  $('#form-jabatan').submit(function(e) {

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
          $('#aiModalJabatan').modal('hide')
          tableJabatan.ajax.reload(null, false);
          tata.success('Success', obj.message)
        } else {
          tata.error('Error', obj.message)
          removeValidation()
          let error = obj.data.errors;

          if (error['id_jabatan_u']) {
            $('.select2-selection--single').addClass('is-invalid');
            $('.select2-selection--single').after(`<div class="invalid-feedback">${error['id_jabatan_u']}</div>`);
          }
          if (error['tmt']) {
            $('input[name=tmt]').addClass('is-invalid');
            $('input[name=tmt]').after(`<div class="invalid-feedback">${error['tmt']}</div>`);
          }
        }
      },
    });

  });

  // Ajax Create
  function createDataJabatan() {
    removeValidation()
    $('#form-jabatan')[0].reset();
    $('#id').val('');
    $('span#select2-id_jabatan_u-container').html('- Pilih Jabatan -');

    // change text
    $('#aiModalJabatanLabel').html('Tambah Jabatan Pegawai');
    $('.modal-footer button[type=submit]').html('Create');

    $('#aiModalJabatan').modal('show');

    submitFormJabatan('insert');
  }

  // Ajax Update
  function updateDataJabatan(id) {
    removeValidation()
    $('#form-jabatan')[0].reset();

    // change text
    $('#aiModalJabatanLabel').html('Edit Jabatan Pegawai');
    $('.modal-footer button[type=submit]').html('Update');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/pegawai-jabatan/update/${id}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);
        $('.modal-body input[name=id]').val(data.id);
        $(".modal-body select[name=id_jabatan_u]").val(data.id_jabatan_u).trigger('change');
        $('.modal-body input[name=tmt]').val(data.tmt);
        $('#aiModalJabatan').modal('show');
      },
      error: function(xhr, status, error) {
        alert(error);
      },
    });

    submitFormJabatan('update', id);
  }

  // Ajax Delete
  function destroyDataJabatan(id) {
    var x = confirm('Apakah anda yakin menghapus data?');
    if (x) {
      $.ajax({
        method: "POST",
        url: <?= json_encode(base_url()) ?> + `/pegawai-jabatan/delete/${id}`,
        data: {
          id: id
        },
        cache: false,
        success: function(data) {
          let obj = $.parseJSON(data);
          tableJabatan.ajax.reload(null, false);
          tata.success('Success', obj.message)
        },
        error: function(xhr, status, error) {
          alert(error);
        },
      });
    }
  }

  $('#aiModalJabatan').on('shown.bs.modal', function() {
    $('input[name=id_jabatan]').trigger('focus')
  })

  // ======================= Form Jabatan Fungsional ===============================================

  // set url form
  var url;

  function submitFormJabatanFungsional(method, id = null) {

    // remove form error message
    $('.form-text').remove();

    if (method == 'insert') {
      url = <?= json_encode(base_url('pegawai-jabatan-fungsional/create')) ?>;
    }
    if (method == 'update') {
      url = <?= json_encode(base_url()) ?> + `/pegawai-jabatan-fungsional/update/${id}`;
    }

  }

  // handle submit form
  $('#form-jabatan-fungsional').submit(function(e) {

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
          $('#aiModalJabatanFungsional').modal('hide')
          tableJabatanFungsional.ajax.reload(null, false);
          tata.success('Success', obj.message)
        } else {
          tata.error('Error', obj.message)
          removeValidation()
          let error = obj.data.errors;

          if (error['id_jabatan_fungsional']) {
            $('.select2-selection--single').addClass('is-invalid');
            $('.select2-selection--single').after(`<div class="invalid-feedback">${error['id_jabatan_fungsional']}</div>`);
          }
        }
      },
    });

  });

  // Ajax Create
  function createDataJabatanFungsional() {
    removeValidation()
    $('#form-jabatan-fungsional')[0].reset();
    $('#id').val('');
    $('span#select2-id_jabatan_fungsional-container').html('- Pilih Jabatan -');

    // change text
    $('#aiModalJabatanFungsionalLabel').html('Tambah Jabatan Pegawai');
    $('.modal-footer button[type=submit]').html('Create');

    $('#aiModalJabatanFungsional').modal('show');

    submitFormJabatanFungsional('insert');
  }

  // Ajax Update
  function updateDataJabatanFungsional(id) {
    removeValidation()
    $('#form-jabatan-fungsional')[0].reset();

    // change text
    $('#aiModalJabatanFungsionalLabel').html('Edit Jabatan Pegawai');
    $('.modal-footer button[type=submit]').html('Update');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/pegawai-jabatan-fungsional/update/${id}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);
        $('.modal-body input[name=id]').val(data.id);
        $(".modal-body select[name=id_jabatan_fungsional]").val(data.id_jabatan_fungsional).trigger('change');
        $('#aiModalJabatanFungsional').modal('show');
      },
      error: function(xhr, status, error) {
        alert(error);
      },
    });

    submitFormJabatanFungsional('update', id);
  }

  // Ajax Delete
  function destroyDataJabatanFungsional(id) {
    var x = confirm('Apakah anda yakin menghapus data?');
    if (x) {
      $.ajax({
        method: "POST",
        url: <?= json_encode(base_url()) ?> + `/pegawai-jabatan-fungsional/delete/${id}`,
        data: {
          id: id
        },
        cache: false,
        success: function(data) {
          let obj = $.parseJSON(data);
          tableJabatanFungsional.ajax.reload(null, false);
          tata.success('Success', obj.message)
        },
        error: function(xhr, status, error) {
          alert(error);
        },
      });
    }
  }

  $('#aiModalJabatanFungsional').on('shown.bs.modal', function() {
    $('input[name=id_jabatan_fungsional]').trigger('focus')
  })

  // ======================= Form Jabatan Struktural ===============================================

  // set url form
  var url;

  function submitFormJabatanStruktural(method, id = null) {

    // remove form error message
    $('.form-text').remove();

    if (method == 'insert') {
      url = <?= json_encode(base_url('pegawai-jabatan-struktural-unit/create')) ?>;
    }
    if (method == 'update') {
      url = <?= json_encode(base_url()) ?> + `/pegawai-jabatan-struktural-unit/update/${id}`;
    }

  }

  // handle submit form
  $('#form-jabatan-struktural').submit(function(e) {

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
          $('#aiModalJabatanStruktural').modal('hide')
          tableJabatanStruktural.ajax.reload(null, false);
          tata.success('Success', obj.message)
        } else {
          tata.error('Error', obj.message)
          removeValidation()
          let error = obj.data.errors;

          if (error['id_jabatan_struktural_u']) {
            $('.select2-selection--single').addClass('is-invalid');
            $('.select2-selection--single').after(`<div class="invalid-feedback">${error['id_jabatan_struktural_u']}</div>`);
          }
          if (error['tanggal_mulai']) {
            $('input[name=tanggal_mulai]').addClass('is-invalid');
            $('input[name=tanggal_mulai]').after(`<div class="invalid-feedback">${error['tanggal_mulai']}</div>`);
          }
          if (error['tanggal_selesai']) {
            $('input[name=tanggal_selesai]').addClass('is-invalid');
            $('input[name=tanggal_selesai]').after(`<div class="invalid-feedback">${error['tanggal_selesai']}</div>`);
          }
        }
      },
    });

  });

  // Ajax Create
  function createDataJabatanStruktural() {
    removeValidation()
    $('#form-jabatan-struktural')[0].reset();
    $('#id').val('');
    $('span#select2-id_jabatan_struktural_u-container').html('- Pilih Jabatan -');

    // change text
    $('#aiModalJabatanStrukturalLabel').html('Tambah Jabatan Pegawai');
    $('.modal-footer button[type=submit]').html('Create');

    $('#aiModalJabatanStruktural').modal('show');

    submitFormJabatanStruktural('insert');
  }

  // Ajax Update
  function updateDataJabatanStruktural(id) {
    removeValidation()
    $('#form-jabatan-struktural')[0].reset();

    // change text
    $('#aiModalJabatanStrukturalLabel').html('Edit Jabatan Pegawai');
    $('.modal-footer button[type=submit]').html('Update');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/pegawai-jabatan-struktural-unit/update/${id}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);
        $('.modal-body input[name=id]').val(data.id);
        $(".modal-body select[name=id_jabatan_struktural_u]").val(data.id_jabatan_struktural_u).trigger('change');
        $('.modal-body input[name=tanggal_mulai]').val(data.tanggal_mulai);
        $('.modal-body input[name=tanggal_selesai]').val(data.tanggal_selesai);
        $('#aiModalJabatanStruktural').modal('show');
      },
      error: function(xhr, status, error) {
        alert(error);
      },
    });

    submitFormJabatanStruktural('update', id);
  }

  // Ajax Delete
  function destroyDataJabatanStruktural(id) {
    var x = confirm('Apakah anda yakin menghapus data?');
    if (x) {
      $.ajax({
        method: "POST",
        url: <?= json_encode(base_url()) ?> + `/pegawai-jabatan-struktural-unit/delete/${id}`,
        data: {
          id: id
        },
        cache: false,
        success: function(data) {
          let obj = $.parseJSON(data);
          tableJabatanStruktural.ajax.reload(null, false);
          tata.success('Success', obj.message)
        },
        error: function(xhr, status, error) {
          alert(error);
        },
      });
    }
  }

  $('#aiModalJabatanStruktural').on('shown.bs.modal', function() {
    $('input[name=id_jabatan_struktural_u]').trigger('focus')
  })

  // ======================= Form Dokumen ===============================================

  // set url form
  var url;

  function submitFormDokumen(method, id = null) {

    // remove form error message
    $('.form-text').remove();

    if (method == 'insert') {
      url = <?= json_encode(base_url('pegawai-dokumen/create')) ?>;
    }
    if (method == 'update') {
      url = <?= json_encode(base_url()) ?> + `/pegawai-dokumen/update/${id}`;
    }

  }

  // handle submit form
  $('#form-dokumen').submit(function(e) {

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
          $('#aiModalDokumen').modal('hide')
          tableDokumen.ajax.reload(null, false);
          tata.success('Success', obj.message)
        } else {
          tata.error('Error', obj.message)
          removeValidation()
          let error = obj.data.errors;

          if (error['dokumen']) {
            $('input[name=dokumen]').addClass('is-invalid');
            $('.custom-file-label').after(`<div class="invalid-feedback">${error['dokumen']}</div>`);
          }
          if (error['nama_dokumen']) {
            $('input[name=nama_dokumen]').addClass('is-invalid');
            $('input[name=nama_dokumen]').after(`<div class="invalid-feedback">${error['nama_dokumen']}</div>`);
          }
        }
      },
    });

  });

  // Ajax Create
  function createDataDokumen() {
    removeValidation()
    $('#form-dokumen')[0].reset();
    $('#id').val('');
    $('.custom-file-label').html('');

    // change text
    $('#aiModalDokumenLabel').html('Tambah Dokumen');
    $('.modal-footer button[type=submit]').html('Create');

    $('#aiModalDokumen').modal('show');

    submitFormDokumen('insert');
  }

  // Ajax Update
  function updateDataDokumen(id) {
    removeValidation()
    $('#form-dokumen')[0].reset();
    $('.custom-file-label').html('');
    
    // change text
    $('#aiModalDokumenLabel').html('Edit Dokumen');
    $('.modal-footer button[type=submit]').html('Update');

    $.ajax({
      method: 'POST',
      url: <?= json_encode(base_url()) ?> + `/pegawai-dokumen/update/${id}`,
      cache: false,
      success: function(data) {
        data = JSON.parse(data);
        $('.modal-body input[name=id]').val(data.id);
        $('.modal-body input[name=nama_dokumen]').val(data.nama_dokumen);
        $('#aiModalDokumen').modal('show');
      },
      error: function(xhr, status, error) {
        alert(error);
      },
    });

    submitFormDokumen('update', id);
  }

  // Ajax Delete
  function destroyDataDokumen(id) {
    var x = confirm('Apakah anda yakin menghapus data?');
    if (x) {
      $.ajax({
        method: "POST",
        url: <?= json_encode(base_url()) ?> + `/pegawai-dokumen/delete/${id}`,
        data: {
          id: id
        },
        cache: false,
        success: function(data) {
          let obj = $.parseJSON(data);
          tableDokumen.ajax.reload(null, false);
          tata.success('Success', obj.message)
        },
        error: function(xhr, status, error) {
          alert(error);
        },
      });
    }
  }

  $('#aiModalDokumen').on('shown.bs.modal', function() {
    $('input[name=dokumen]').trigger('focus')
  })

  function removeValidation() {
    $('form').find('.is-invalid').removeClass('is-invalid');
    $('form').find('.invalid-feedback').remove();
  }
</script>
<?= $this->endSection(); ?>