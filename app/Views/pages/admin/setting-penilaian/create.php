<?= $this->extend('layouts/admin'); ?>

<?= $this->section('append-style'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
<?= $this->endSection(); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- Toast -->
<div aria-live="polite" aria-atomic="true" style="position: fixed; top: 1em; right: 1.5em; z-index: 999; width:250px">
  <div class="toast">
    <div class="toast-header">
      <i class="fas fa-check-circle text-success mr-2"></i>
      <strong class="mr-auto">Success</strong>
      <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="toast-body">
      Message
    </div>
  </div>
</div>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3">
  <h1 class="h3 text-gray-800"><?= $title ?></h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('setting-penilaian') ?>">Pengaturan Penilaian</a></li>
      <li class="breadcrumb-item active" aria-current="page">Atur Penilaian</li>
    </ol>
  </nav>
</div>

<!-- Edit Button -->


<!-- alert -->
<?= $this->include('includes/_alert') ?>

<!-- Page content -->
<div class="row">
  <div class="col-md-12">
    <div class="card shadow mb-3">
      <div class="card-header text-center">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Penilaian</h6>
      </div>
      <div class="card-body table-responsive">

        <form action="<?= base_url($form_action) ?>" method="post">
          <?= csrf_field() ?>

          <div class="row">
            <div class="col-6">
              <table class="table table-borderless mb-0">
                <tr class="d-flex">
                  <td class="col-5">Jenis</td>
                  <td class="col-6">
                    <select class="form-control" name="jenis" id="jenis">
                      <option value="">- Pilih Jenis -</option>
                      <option value="Karyawan" <?= set_select('jenis', 'Karyawan') ?>>Karyawan</option>
                      <option value="Pejabat Struktural" <?= set_select('jenis', 'Pejabat Struktural') ?>>Pejabat Struktural</option>
                    </select>
                    <small class="text-danger"><?= $validation->getError('jenis') ?></small>
                  </td>
                </tr>
                <tr class="d-flex">
                  <td class="col-5">Periode Penilaian Awal</td>
                  <td class="col-6"><input type="date" name="periode_penilaian_awal" class="form-control" value="<?= set_value('periode_penilaian_awal', $penilaian_setting->periode_penilaian_awal ?? '') ?>"></td>
                </tr>
                <tr class="d-flex">
                  <td class="col-5">Periode Penilaian Akhir</td>
                  <td class="col-6"><input type="date" name="periode_penilaian_akhir" class="form-control" value="<?= set_value('periode_penilaian_akhir', $penilaian_setting->periode_penilaian_akhir ?? '') ?>"></td>
                </tr>
              </table>
            </div>
            <div class="col-6">
              <table class="table table-borderless mb-0">
                <tr class="d-flex">
                  <td class="col-5">Tanggal Mulai Penilaian</td>
                  <td class="col-6"><input type="date" name="tanggal_mulai" class="form-control" value="<?= set_value('tanggal_mulai', $penilaian_setting->tanggal_mulai ?? '') ?>"></td>
                </tr>
                <tr class="d-flex">
                  <td class="col-5">Tanggal Selesai Penilaian</td>
                  <td class="col-6"><input type="date" name="tanggal_selesai" class="form-control" value="<?= set_value('tanggal_selesai', $penilaian_setting->tanggal_selesai ?? '') ?>"></td>
                </tr>
              </table>
            </div>
          </div>
          <div class="row">
            <div class="col-md">
              <a href="javascript:void(0)" class="btn btn-primary shadow-sm mb-3 ml-2 " data-toggle="modal" data-target="#aiModal">
                <i class=" fas fa-plus-circle"></i>
                <span>Pilih Pertanyaan</span>
              </a>
              <table class="table table-input mb-4" id="table">
                <thead>
                  <tr>
                    <th scope="col">Pertanyaan</th>
                    <th scope="col">Nomor Urut</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody class="container-ai">
                  <tr class="row-input-ai">
                  </tr>
                  <?php if (!empty(old('id_pertanyaan'))) : ?>
                    <?php $i = -1 ?>
                    <?php foreach (old('id_pertanyaan') as $row) : $i++ ?>
                      <tr class="row-input-ai">
                        <input class="form-control" type="hidden" name="id_pertanyaan[]" value="<?= set_value("id_pertanyaan.$i") ?>">
                        <td>
                          <input class="form-control" type="hidden" name="id_pertanyaan[]" value="<?= set_value("id_pertanyaan.$i") ?>">
                          <input class="form-control" type="hidden" name="pertanyaan[]" value="<?= set_value("pertanyaan.$i") ?>">
                          <?= set_value("pertanyaan.$i") ?>
                        </td>
                        <td>
                          <input class="form-control currency no_urut <?= ($validation->hasError("no_urut.$i")) ? 'is-invalid' : '' ?>" type="text" name="no_urut[]" value="<?= set_value("no_urut.$i") ?>" autocomplete="off">
                          <div class="invalid-feedback"><?= $validation->getError("no_urut.$i") ?></div>
                        </td>
                        <td>
                          <a href="#" class="delete-input">Hapus</a>
                        </td>
                      </tr>
                    <?php endforeach ?>
                  <?php endif ?>
                </tbody>
              </table>
            </div>
          </div>
          <button type="submit" class="btn btn-md-inline shadow-sm mb-3 btn-primary  ml-2" style="width: 140px;">
            <i class="fas fa-save"></i>
            <span>Simpan</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="aiModal" tabindex="-1" role="dialog" aria-labelledby="aiModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 50%!important;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalLabel">Pilih Pertanyaan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="card">
        <div class="card-body">
          <table class="table table-hover " id="myTable" style="overflow-x:auto;">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Pertanyaan</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 0;
              foreach ($pertanyaan as $row) : $no++; ?>
                <tr>
                  <td><?= $no ?></td>
                  <td><?= $row->pertanyaan ?></td>
                  <td>
                    <button class="btn btn-sm btn-info" data-id="<?= $row->id ?>" data-pertanyaan="<?= $row->pertanyaan ?>" onclick="pilihPertanyaan(this)">
                      Pilih
                    </button>
                  </td>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
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
  $('#myTable').DataTable();

  let max_fields = 1000;
  let wrapper = $(".container-ai");
  let x = $('.row-input-ai').length;

  function pilihPertanyaan(e) {
    wrapper = $(".row-input-ai").last();
    const id = $(e).data('id');
    const pertanyaan = $(e).data('pertanyaan');

    // add row
    if (x < max_fields) {
      x++;
      $(wrapper).after(
        `
        <tr class="row-input-ai">
          <input class="form-control" type="hidden" name="id_pertanyaan[]" value="${id}">
          <td>
            <input class="form-control" type="hidden" name="pertanyaan[]" value="${pertanyaan}">
           ${pertanyaan}
          </td>
          <td>
            <input class="form-control currency${x} no_urut" type="text" name="no_urut[]" value="" autocomplete = "off" style="width:100px">
          </td>
          <td>
            <a href="#" class="delete-input">Hapus</a>
          </td>
        </tr>
        `
      ); //add input box
    } else {
      alert('You Reached the limits')
    }

    if ($('.row-input-ai').length > 1) {
      $('.table-input').removeClass('d-none');
    } else {
      $('.table-input').addClass('d-none');
    }

    // $('#aiModal').modal('hide')
  };

  // delete input
  $(wrapper).on('click', '.delete-input', function(e) {
    e.preventDefault();
    if (x > 1) {
      $(this).parents('tr').remove();
      x--;
    }
  })
</script>
<?= $this->endSection(); ?>