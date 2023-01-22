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
      <li class="breadcrumb-item"><a href="<?= base_url('penilaian-rekap') ?>">Rekap Penilaian</a></li>
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
        <h6 class="m-0 font-weight-bold text-primary">Daftar <?= $title ?></h6>
      </div>
      <div class="card-body table-responsive">
        <div class="row mb-2">
          <div class="col-6">
            <p class="mb-2">Data Karyawan :</p>
            <table class="table table-borderless mb-0">
              <tr class="d-flex">
                <td class="col-5">Nama</td>
                <td><?= $pegawaiDinilai->nama ?></td>
              </tr>
              <tr class="d-flex">
                <td class="col-5">Departemen</td>
                <td><?= $jabatanPegawaiDinilai->nama_unit ?></td>
              </tr>
              <tr class="d-flex">
                <td class="col-5">Jabatan</td>
                <td><?= $jabatanPegawaiDinilai->nama_jabatan ?></td>
              </tr>
              <tr class="d-flex">
                <td class="col-5">Periode Penilaian</td>
                <td><?= date('F Y', strtotime($settingPenilaian->periode_penilaian_awal)) ?> - <?= date('F Y', strtotime($settingPenilaian->periode_penilaian_akhir)) ?></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-md">
            <p>I. Presensi</p>
            <ol>
              <li>
                <span>Sebab dan Jumlah Hari Absen</span>
                <ol type="a" class="pl-3">
                  <li>Cuti : <?= $pegawaiDinilaiPresensi->cuti ?? 0 ?> Kali Selama 1 Tahun</li>
                  <li>Alpha : <?= $pegawaiDinilaiPresensi->alpha ?? 0 ?> Kali Selama 1 Tahun</li>
                  <li>Total Cuti : <?= $pegawaiDinilaiPresensi->total_cuti ?? 0 ?> Kali Selama 1 Tahun</li>
                </ol>
              </li>
              <li>Keterlambatan : <?= $pegawaiDinilaiPresensi->terlambat ?? 0 ?> Kali Selama 1 Tahun</li>
            </ol>
          </div>
        </div>
        <div class="row">
          <div class="col-md">
            <p>II. Penilaian Hasil Kerja</p>
            <?php foreach ($penilaian as $row) : ?>
              <p class="font-weight-bold">Nama Penilai : <?= $row->nama_penilai ?></p>
              <a href="<?= base_url("penilaian-rekap/print/$row->id") ?>" class="btn btn-info shadow-sm ml-2" target="_blank"><i class="fas fa-print"></i> Print</a>

              <table class="table table-input mb-4" id="table">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Aspek</th>
                    <th scope="col">Nilai</th>
                    <th scope="col">Keterangan</th>
                  </tr>
                </thead>
                <tbody class="container-ai">
                  <tr class="row-input-ai">
                  </tr>
                  <?php
                  $no = 0;
                  $kategori = '';
                  foreach ($row->penilaianDetail as $index => $value) : $no++; ?>
                    <?php if ($kategori == '') : $kategori = $value->kategori ?>
                      <input type="hidden" class="form-control" name="kategori[<?= $kategori ?>]" value="">
                      <tr>
                        <td colspan="4"><?= $kategori ?></td>
                      </tr>
                      <tr>
                        <td colspan="4">* Keterangan Nilai :
                          <?php foreach (explode(';', $value->range_desc) as $explode) : ?>
                            <?php $desc = explode('/', $explode) ?>
                            <?= "$desc[0] = $desc[1], " ?>
                          <?php endforeach ?>
                        </td>
                      </tr>
                    <?php elseif ($kategori != '' && $value->kategori != $kategori) : $kategori = $value->kategori;
                      $no = 1 ?>
                      <input type="hidden" class="form-control" name="kategori[<?= $kategori ?>]" value="">
                      <tr>
                        <td colspan="4"><?= $kategori ?></td>
                      </tr>
                      <tr>
                        <td colspan="4">* Keterangan Nilai :
                          <?php foreach (explode(';', $value->range_desc) as $explode) : ?>
                            <?php $desc = explode('/', $explode) ?>
                            <?= "$desc[0] = $desc[1], " ?>
                          <?php endforeach ?>
                        </td>
                      </tr>
                    <?php endif ?>
                    <input type="hidden" class="form-control" name="kategori[<?= $kategori ?>][<?= $no ?>]" value="<?= $value->id ?>">
                    <tr>
                      <td><?= $no ?></td>
                      <td><?= $value->pertanyaan ?></td>
                      <td>
                        <?= $value->nilai ?>
                      </td>
                      <td>
                        <?= $value->keterangan ?>
                      </td>
                    </tr>
                    <?php if (sizeof($row->penilaianDetail) == ++$index) : ?>
                      <tr>
                        <td>Hasil Nilai</td>
                        <td></td>
                        <td><?= $row->nilai_hasil ?></td>
                        <td></td>
                      </tr>
                    <?php endif ?>
                  <?php endforeach ?>
                </tbody>
              </table>
            <?php endforeach ?>
            <?php if ($penilaianHasil) : ?>
              <p class="font-weight-bold">Nilai Akhir : <?= $penilaianHasil->nilai_akhir ?> (<?= getCategoryGrade($penilaianHasil->nilai_akhir) ?>)</p>
            <?php else : ?>
              <p>-</p>
            <?php endif ?>
          </div>
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
  $(document).ready(function() {

  });
</script>
<?= $this->endSection(); ?>