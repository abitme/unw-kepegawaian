<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title><?= $this->renderSection('title'); ?></title>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="<?= base_url() ?>/assets/frontend/css/bootstrap-4.6.min.css">

  <!-- Custom styles for this template-->
  <link href="<?= base_url() ?>/assets/frontend/css/ai.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @media print {
      @page {
        size: A4;
        margin: 40px 40px 60px 40px;
        overflow: hidden;
      }

      .pagebreak {
        page-break-before: always;
      }
    }
  </style>
</head>

<body>

  <div class="container-fluid">
    <!-- Header Logo -->
    <div id="header-logo">
      <div class="row d-flex align-items-center">
        <div class="col-1">
          <?php if (file_exists("assets/img/konfigurasi/logo.png")) : ?>
            <img src="<?= base_url("assets/img/konfigurasi/logo.png") ?>" alt="logo" style="width: 90px; height: auto;">
          <?php else : ?>
            <img src="<?= base_url("assets/img/konfigurasi/default.png") ?>" alt="logo" style="width: 90px; height: auto;">
          <?php endif ?>
        </div>
        <div class="col-11 pl-5">
          <div>
            <span class="h5">UNIVERSITAS NGUDI WALUYO</span>
            <br>
            <span class="h5">FORMULIR PENILAIAN KERJA <?= $penilaian->jenis == 'Karyawan' ? 'KARYAWAN' : 'ATASAN' ?> (PERFOMANCE APPRAISAL)</span>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="row">
      <div class="col-md-12">
        <div class="card" style="border: none;">
          <div class="card-body pb-0 table-responsive">
            <p class="m-0">Dengan ini kami mengajukan Penilaian Kerja Karyawan sebagai berikut:</p>
            <table class="table table-dir table-borderless mb-2">
              <tr class="d-flex">
                <td class="p-0 col-2">Nama</td>
                <td class="p-0">: &nbsp; <?= $pegawaiDinilai->nama ?></td>
              </tr>
              <tr class="d-flex">
                <td class="p-0 col-2">Departemen</td>
                <td class="p-0">: &nbsp; <?= $jabatanPegawaiDinilai->nama_unit ?></td>
              </tr>
              <tr class="d-flex">
                <td class="p-0 col-2">NIK</td>
                <td class="p-0">: &nbsp; <?= $pegawaiDinilai->nik ?></td>
              </tr>
              <tr class="d-flex">
                <td class="p-0 col-2">Jabatan</td>
                <td class="p-0">: &nbsp; <?= $jabatanPegawaiDinilai->nama_jabatan ?></td>
              </tr>
              <tr class="d-flex">
                <td class="p-0 col-2">Periode Penilaian</td>
                <?php setlocale(LC_ALL, 'IND') ?>
                <td class="p-0">: &nbsp; <?= strftime('%B %Y', strtotime($settingPenilaian->periode_penilaian_awal)) ?> - <?= strftime('%B %Y', strtotime($settingPenilaian->periode_penilaian_akhir)) ?></td>
              </tr>
              <tr class="d-flex">
                <td class="p-0 col-2">Penilai</td>
                <td class="p-0">: &nbsp; <?= $penilaian->nama_penilai ?></td>
              </tr>
            </table>
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
                <?php
                $i = 1;
                $sizeKategori = 0;
                foreach ($penilaianKategori as $row1) :
                  $i++;  ?>
                  <p><?= integerToRoman($i) . ". " . $row1->kategori ?></p>
                  <table class="table table-bordered table-input mb-4" id="table">
                    <thead>
                      <tr>
                        <th class="text-center align-middle" scope="col" rowspan="2">No</th>
                        <th class="text-center align-middle" scope="col" rowspan="2">Aspek</th>
                        <th class="text-center align-middle" scope="col" colspan="<?= $row1->nilai_max ?>">Nilai</th>
                        <th class="text-center align-middle" scope="col" rowspan="2">Keterangan</th>
                      </tr>
                      <tr>
                        <?php foreach (explode(';', $row1->range_desc) as $explode) : ?>
                          <?php $desc = explode('/', $explode) ?>
                          <th class="text-center align-middle"> <?= $desc[0] ?><br><?= $desc[1] ?></th>
                        <?php endforeach ?>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $j = 0;
                      $jumlahPertanyaan = 0;
                      foreach ($penilaian->penilaianDetail as $index => $value) :  ?>
                        <?php if ($row1->kategori == $value->kategori) :
                          $j++;
                          $jumlahPertanyaan++;
                        ?>
                          <tr>
                            <td><?= $j ?></td>
                            <td><?= $value->pertanyaan ?></td>
                            <?php foreach (explode(';', $row1->range_desc) as $explode) : ?>
                              <?php $desc = explode('/', $explode) ?>
                              <?php if ($desc[0] == $value->nilai) : ?>
                                <td class="text-center">
                                  <i class="fa-solid fa-check"></i> <?php else : ?>
                                <td class="text-center">
                                </td>
                              <?php endif ?>
                            <?php endforeach ?>
                            <td>
                              <?= $value->keterangan ?>
                            </td>
                          </tr>
                        <?php endif ?>
                      <?php endforeach ?>
                      <!-- <tr>
                        <td></td>
                        <td>Nilai = Nilai Total / <?= $row1->nilai_max * $jumlahPertanyaan ?> x 100</td>
                        <?php foreach (explode(';', $row1->range_desc) as $explode) : ?>
                          <td></td>
                        <?php endforeach ?>
                        <td></td>
                      </tr> -->
                    </tbody>
                  </table>
                  <?php if (sizeof($penilaianKategori) != ++$sizeKategori) : ?>
                    <div class="pagebreak"> </div>
                  <?php endif ?>
                <?php endforeach ?>
                <?php if ($penilaian->jenis == 'Pejabat Struktural') : ?>
                  <div class="pagebreak"> </div>
                <?php endif ?>
                <p class="m-0 font-weight-bold">Hasil Penilaian:</p>
                <table class="table table-dir table-borderless mb-2">
                  <tr class="d-flex">
                    <td class="p-1 col-2">90 - 100</td>
                    <td class="p-1">: &nbsp; Baik Sekali</td>
                  </tr>
                  <tr class="d-flex">
                    <td class="p-1 col-2">80 - 89</td>
                    <td class="p-1">: &nbsp; Baik</td>
                  </tr>
                  <tr class="d-flex">
                    <td class="p-1 col-2">70 - 79</td>
                    <td class="p-1">: &nbsp; Cukup</td>
                  </tr>
                  <tr class="d-flex">
                    <td class="p-1 col-2">&lt;70</td>
                    <td class="p-1">: &nbsp; Kurang</td>
                  </tr>
                </table>
                <?php if (sizeof($semuaPenilai) > 1) : ?>
                  <p class="font-weight-bold">Nilai : <?= $penilaian->nilai_hasil ?></p>
                  <table class="table table-dir table-borderless mb-0 d-flex justify-content-start ">
                    <tbody class="mr-5">
                      <tr>
                        <td class="text-center pb-0" style="font-size: 16px;">Yang memberi nilai,</td>
                      </tr>
                      <tr>
                        <td class="text-center" style="font-size: 16px;">&nbsp;</td>
                      </tr>
                      <tr>
                        <td></td>
                      </tr>
                      <tr>
                        <td></td>
                      </tr>
                      <tr>
                        <td></td>
                      </tr>
                      <tr>
                        <td></td>
                      </tr>
                      <tr>
                        <td></td>
                      </tr>
                      <tr>
                        <td class="text-center" style="font-size: 16px;"><?= $penilaian->nama_penilai ?></td>
                      </tr>
                    </tbody>
                  </table>
                <?php endif ?>
                <?php if ($penilaianHasil) : ?>
                  <?php if (sizeof($semuaPenilai) > 1) : ?>
                    <div class="pagebreak"> </div>
                    <p class="m-0">Nilai Akhir:</p>
                    <table class="table table-bordered mb-2" style="border: none">
                      <tr class="d-flex">
                        <td class="p-1 col-4">Penilai</td>
                        <td class="p-1 col-3 font-weight">Nilai</td>
                      </tr>
                      <tr class="d-flex">
                        <td class="p-1 col-4"><?= $penilaian->nama_penilai ?></td>
                        <td class="p-1 col-3"><?= $penilaian->nilai_hasil ?></td>
                      </tr>
                      <?php
                      $total = $penilaian->nilai_hasil;
                      foreach ($semuaPenilai as $row) :  ?>
                        <?php if ($row->id_pegawai_penilai != $penilaian->id_pegawai_penilai) : $total += $row->nilai_hasil; ?>
                          <tr class="d-flex">
                            <td class="p-1 col-4"><?= $row->nama_penilai ?></td>
                            <td class="p-1 col-3"><?= $row->nilai_hasil ?></td>
                          </tr>
                        <?php endif ?>
                      <?php endforeach ?>
                      <tr class="d-flex">
                        <td class="p-1 col-4">Total</td>
                        <td class="p-1 col-3"><?= $total ?></td>
                      </tr>
                    </table>
                  <?php endif ?>
                  <p class="font-weight-bold">Nilai Akhir : <?= $penilaianHasil->nilai_akhir ?> (<?= getCategoryGrade($penilaianHasil->nilai_akhir) ?>)</p>
                <?php else : ?>
                  <p>-</p>
                <?php endif ?>
              </div>
            </div>
            <?php if (sizeof($semuaPenilai) > 1) : ?>
              <table class="table table-dir table-borderless mb-0 d-flex justify-content-end ">
                <tbody class="mr-5">
                  <tr>
                    <td class="text-center pb-0" style="font-size: 16px;">Menyetujui,</td>
                  </tr>
                  <tr>
                    <td class="text-center" style="font-size: 16px;">WR Bidang Umum dan Kepegawaian</td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td class="text-center" style="font-size: 16px;">Rosalina, S.Kp., M.Kes</td>
                  </tr>
                </tbody>
              </table>
            <?php else : ?>
              <table class="table table-dir table-borderless mb-0 d-flex justify-content-between ">
                <tbody class="mr-5">
                  <tr>
                    <td class="text-center pb-0" style="font-size: 16px;">Yang memberi nilai,</td>
                  </tr>
                  <tr>
                    <td class="text-center" style="font-size: 16px;">&nbsp;</td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td class="text-center" style="font-size: 16px;"><?= $penilaian->nama_penilai ?></td>
                  </tr>
                </tbody>
                <tbody class="mr-5">
                  <tr>
                    <td class="text-center pb-0" style="font-size: 16px;">Menyetujui,</td>
                  </tr>
                  <tr>
                    <td class="text-center" style="font-size: 16px;">WR Bidang Umum dan Kepegawaian</td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td class="text-center" style="font-size: 16px;">Rosalina, S.Kp., M.Kes</td>
                  </tr>
                </tbody>
              </table>
            <?php endif ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    window.onload = function() {
      window.print();
    }
  </script>

</body>

</html>