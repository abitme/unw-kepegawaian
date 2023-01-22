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

<!-- Button -->
<div class="row">
  <div class="col-md">
    <a href="<?= base_url("penilaian-rekap-recalculate/calc/$settingPenilaian->id") ?>" class="btn btn-success shadow-sm mb-3">
      <i class="fas fa-sync-alt"></i>
      <span>Recalculate</span>
    </a>
  </div>
</div>

<!-- filter button -->
<div class="row">
  <div class="col-md">
    <div class="card card-filter shadow mb-3">
      <div class="card-body">
        <form action="<?= base_url("penilaian-rekap/$settingPenilaian->id") ?>" method="GET">
          <div class="form-row align-items-end">
            <div class="form-group mb-0 col-md-5">
              <label for="unit">Unit</label>
              <?= form_dropdown('id_unit', getDropdownList('unit', ['id', 'nama_unit'], '', '- Semua Unit -', 'nama_unit', 'asc'), set_value('id_unit', $input->id_unit ?? ''), ['class' => 'form-control select2', 'id' => 'id_unit', 'style' => 'width:100%']) ?>
            </div>
            <div class="form-group mb-0 col-md-4">
              <?php if (isset($input->id_unit)) : ?>
                <a href="<?= base_url("penilaian-rekap/$settingPenilaian->id") ?>" class="btn btn-secondary shadow-sm">Hapus Filter</a>
              <?php endif ?>
              <button type="submit" class="btn btn-primary shadow-sm "><i class="fas fa-filter"></i> Filter</button>
            </div>
          </div>
        </form>
      </div>
    </div>
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
        <table class="table table-hover mb-5" id="myTable" style="overflow-x:auto;">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Nama Pegawai</th>
              <th scope="col">Jabatan</th>
              <th scope="col">Nilai</th>
              <th scope="col">Kategori</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $db = \Config\Database::connect();
            $no = 0;
            foreach ($pegawai as $row) :
              $no++;
              if ($settingPenilaian->jenis == 'Karyawan') {
                $pegawaiJabatan = $db->table('pegawai_jabatan_u_view')->select('nama_unit, nama_jabatan')->where('id_pegawai', $row->id_pegawai)->get()->getRow();
              } else {
                $pegawaiJabatan = $db->table('pegawai_jabatan_struktural_u_view')->select('nama_unit, nama_jabatan_struktural as nama_jabatan')->where('id_pegawai', $row->id_pegawai)->get()->getRow();
              }

              $namaJabatan = $pegawaiJabatan->nama_jabatan ?? '';
              $namaUnit = $pegawaiJabatan->nama_unit ?? '';
              $penilaianHasil = $db->table('penilaian_hasil')->where('id_pegawai', $row->id_pegawai)->where('jenis', $settingPenilaian->jenis)->get()->getRow();

              if ($penilaianHasil) {
                $kategori = getCategoryGrade($penilaianHasil->nilai_akhir);
              } else {
                $kategori = '-';
              }
            ?>
              <tr>
                <td><?= $no ?></td>
                <td><?= $row->nama ?></td>
                <td><?= "$namaJabatan - $namaUnit" ?></td>
                <td><?= $penilaianHasil->nilai_akhir ?? '-' ?></td>
                <td><?= $kategori ?? '-' ?></td>
                <td>
                  <a href="<?= base_url("penilaian-rekap/$settingPenilaian->id/$row->id_pegawai") ?>" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Detail">
                    <i class="fas fa-eye"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('append-style'); ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<!-- datatable js -->
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
<script>
  $(document).ready(function() {

    // datatable
    table = $('#myTable').DataTable({
      paging: false,
      order: [1, 'asc'],
      columnDefs: [{
        targets: [0, 5],
        orderable: false,
      }, ],
      dom: 'Bfrtip',
      buttons: [{
          extend: 'excelHtml5',
          exportOptions: {
            columns: [0, 1, 2, 3, 4]
          }
        },
        {
          extend: 'csvHtml5',
          exportOptions: {
            columns: [0, 1, 2, 3, 4]
          }
        },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          exportOptions: {
            columns: [0, 1, 2, 3, 4]
          }
        },
      ],
    });
    table.on('order.dt search.dt', function() {
      table.column(0, {
        search: 'applied',
        order: 'applied'
      }).nodes().each(function(cell, i) {
        cell.innerHTML = i + 1;
        // https://datatables.net/forums/discussion/32978/how-to-export-index-column-using-datatable-tabletool-plugin
        table.cell(cell).invalidate('dom');
      });
    }).draw();


    // select2
    $('.select2').select2();
  });
</script>
<?= $this->endSection(); ?>