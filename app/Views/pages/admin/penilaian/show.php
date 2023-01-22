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
      <li class="breadcrumb-item"><a href="<?= base_url('penilaian-setting') ?>">Pengaturan Penilaian</a></li>
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
        <h6 class="m-0 font-weight-bold text-primary">Penilaian Periode <?= date('F Y', strtotime($settingPenilaian->periode_penilaian_awal)) ?> - <?= date('F Y', strtotime($settingPenilaian->periode_penilaian_akhir)) ?></h6>
      </div>
      <div class="card-body table-responsive">

        <form action="<?= base_url("penilaian/$pegawaiDinilai->id/$jenis/menilai") ?>" method="POST">
          <input type="hidden" name="id_pegawai_dinilai" value="<?= $pegawaiDinilai->id ?>">
          <?= csrf_field() ?>

          <div class="row mb-2">
            <div class="col-6">
              <p class="mb-2">Dengan ini mengajukan penilaian kerja karyawan sebagai berikut :</p>
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
                  foreach ($settingPenilaianDetail as $row) : $no++; ?>
                    <?php if ($kategori == '') : $kategori = $row->kategori ?>
                      <input type="hidden" class="form-control" name="kategori[<?= $kategori ?>]" value="">
                      <tr>
                        <td colspan="4"><?= $kategori ?></td>
                      </tr>
                      <tr>
                        <td colspan="4">* Keterangan Nilai :
                          <?php foreach (explode(';', $row->range_desc) as $explode) : ?>
                            <?php $desc = explode('/', $explode) ?>
                            <?= "$desc[0] = $desc[1], " ?>
                          <?php endforeach ?>
                        </td>
                      </tr>
                    <?php elseif ($kategori != '' && $row->kategori != $kategori) : $kategori = $row->kategori;
                      $no = 1 ?>
                      <input type="hidden" class="form-control" name="kategori[<?= $kategori ?>]" value="">
                      <tr>
                        <td colspan="4"><?= $kategori ?></td>
                      </tr>
                      <tr>
                        <td colspan="4">* Keterangan Nilai :
                          <?php foreach (explode(';', $row->range_desc) as $explode) : ?>
                            <?php $desc = explode('/', $explode) ?>
                            <?= "$desc[0] = $desc[1], " ?>
                          <?php endforeach ?>
                        </td>
                      </tr>
                    <?php endif ?>
                    <input type="hidden" class="form-control" name="kategori[<?= $kategori ?>][<?= $no ?>]" value="<?= $row->id_pertanyaan ?>">
                    <tr>
                      <td><?= $no ?></td>
                      <td><?= $row->pertanyaan ?></td>
                      <td>
                        <?php $nameNilai = 'nilai' . $row->id_pertanyaan ?>
                        <div class="form-group">
                          <input type="hidden" class="form-control" id="id_pertanyaan" name="id_pertanyaan[]" value="<?= $row->id_pertanyaan ?>">
                          <?php for ($i = $row->nilai_min; $i <= $row->nilai_max; $i++) : ?>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="<?= $nameNilai ?>" id="<?= $nameNilai ?><?= $i ?>" value="<?= $i ?>" <?= old($nameNilai) == $i  ? 'checked' : '' ?>>
                              <label class="form-check-label" for="<?= $nameNilai ?><?= $i ?>"><?= $i ?></label>
                            </div>
                            <?php if ($i == $row->nilai_max) : ?>
                              <small class="form-text text-danger"><?= $validation->getError($nameNilai) ?></small>
                            <?php endif ?>
                          <?php endfor ?>
                        </div>
                      </td>
                      <td>
                        <?php $nameKeterangan = 'keterangan' . $row->id_pertanyaan ?>
                        <textarea name="<?= $nameKeterangan ?>" id="" cols="30" rows="4" class="form-control"></textarea>
                      </td>
                    </tr>
                  <?php endforeach ?>
                </tbody>
              </table>
            </div>
          </div>
          <!-- <div class="row mb-4">
            <div class="col-md">
              <p>III. Lain-Lain (Apabila ada penilaian / catatan khusus yang belum tertulis dari indikator penilaian hasil kerja diatas, dapat ditulis pada point III)</p>
              <textarea name="nilai_lain" id="" cols="30" rows="6" class="form-control"></textarea>
            </div>
          </div> -->
          <button type="submit" class="btn btn-md-inline shadow-sm mb-3 btn-primary" style="width: 140px;">
            <i class="fas fa-save"></i>
            <span>Simpan</span>
          </button>

        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<script>
  $('#myTable').DataTable();
</script>
<?= $this->endSection(); ?>