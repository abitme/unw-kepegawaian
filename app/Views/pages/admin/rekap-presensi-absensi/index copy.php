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
      <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
    </ol>
  </nav>
</div>

<!-- alert -->
<?= $this->include('includes/_alert') ?>

<!-- filter button -->
<div class="row">
  <div class="col-md">
    <div class="card card-filter shadow mb-3">
      <div class="card-body">
        <form action="<?= base_url('rekap-presensi-absensi') ?>" method="GET">
          <div class="form-row align-items-end">
            <div class="form-group mb-0 col-md-3">
              <label for="dates">Tanggal</label>
              <input type="text" class="form-control" id="dates" name="dates" value="<?= $input->dates ?? '' ?>">
            </div>
            <div class="form-group mb-0 col-md-5">
              <label for="unit">Unit</label>
              <?= form_dropdown('id_unit', getDropdownList('unit', ['id', 'nama_unit'], '', '- Semua Unit -', 'nama_unit', 'asc'), set_value('id_unit', $input->id_unit ?? ''), ['class' => 'form-control select2', 'id' => 'id_unit', 'style' => 'width:100%']) ?>
            </div>
            <div class="form-group mb-0 col-md-4">
              <?php if (isset($input->dates)) : ?>
                <a href="<?= base_url('rekap-presensi-absensi') ?>" class="btn btn-secondary shadow-sm">Hapus Filter</a>
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
        <div class="row">
          <!-- Card -->
          <div class="col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Terlambat</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800 card-total-terlambat"></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-users fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Card -->
          <div class="col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pulang Cepat</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800 card-total-pulang-cepat"></div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-users fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="chart">
        </div>
        <table class="table table-hover" id="myTable" style="overflow-x:auto;">
          <thead>
            <tr>
              <th>#</th>
              <th>Pegawai</th>
              <th>Total Hari Kerja</th>
              <th>Total Jam Kerja</th>
              <th>Hadir</th>
              <th>Dinas dengan Lumsum</th>
              <th>Dinas tanpa Lumsum</th>
              <th>Izin</th>
              <th>Sakit</th>
              <th>Cuti</th>
              <th>Alpha</th>
              <th>Terlambat</th>
              <th>Pulang Cepat</th>
              <th>Akumulasi Jam Kerja Pegawai</th>
              <th>Durasi Kerja (Hari)</th>
              <th>Bonus Kehadiran</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $db = \Config\Database::connect();
            $no = 0;

            if (isset($input->dates)) {
              $date = explode(" - ", $input->dates);
              $date[0] = date('Y-m-d', strtotime($date[0]));
              $date[1] = date('Y-m-d', strtotime($date[1]));

              $tanggal_awal = $date[0];
              $tanggal_akhir = $date[1];
            } else {
              $tanggal_awal = date('Y-m-d');
              $tanggal_akhir = date('Y-m-d');
            }
            $selectHolidays = $db->query("SELECT tanggal FROM hari_libur WHERE tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5)")->getResultArray();
            $implodeHolidays = implode("', '", array_column($selectHolidays, 'tanggal'));
            $notInHolidays = !empty($selectHolidays) ? "AND tanggal NOT IN ('$implodeHolidays')" : '';
            $totalHoliday = $db->query("SELECT COUNT(*) as libur FROM hari_libur WHERE tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5)")->getRow()->libur;
            $totalWorkingDays = getWorkingDays($tanggal_awal, $tanggal_akhir, [1, 2, 3, 4, 5]);
            $totalWorkingDays -= $totalHoliday;
            // $totalHolidayDays = getHolidayDays($tanggal_awal, $tanggal_akhir, [6, 7]);
            $totalAlpha = [];
            if (isset($input->unit)) {
              foreach ($input->unit as $row) {
                $totalAlpha[$row->id] = 0;
                $totalTerlambat[$row->id] = 0;
                $totaPulangCepat[$row->id] = 0;
              }
            }

            foreach ($pegawai as $p) : $no++ ?>
              <?php
              $selectAbsen = $db->query("SELECT tanggal FROM absensi WHERE id_pegawai = $p->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getResultArray();
              $implodeAbsen = implode("', '", array_column($selectAbsen, 'tanggal'));
              $notInAbsen = !empty($selectAbsen) ? "AND tanggal NOT IN ('$implodeAbsen')" : '';
              $selectLumsum0 = $db->query("SELECT tanggal FROM tugas_dinas WHERE id_pegawai = $p->id AND lumsum = 0 AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays $notInAbsen")->getResultArray();
              $implodeLumsum0 = implode("', '", array_column($selectLumsum0, 'tanggal'));
              $notInLumsum0 = !empty($selectLumsum0) ? "AND tanggal NOT IN ('$implodeLumsum0')" : '';
              $hadir = $db->query("SELECT COUNT(*) as hadir FROM view_rekap_presensi WHERE id_pegawai = $p->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' $notInHolidays $notInAbsen $notInLumsum0")->getRow()->hadir;
              $izin = $db->query("SELECT COUNT(*) as izin FROM absensi WHERE id_pegawai = $p->id AND jenis_absensi = 'Izin' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->izin;
              $sakit = $db->query("SELECT COUNT(*) as sakit FROM absensi WHERE id_pegawai = $p->id AND jenis_absensi = 'Sakit' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->sakit;
              $cuti = $db->query("SELECT COUNT(*) as cuti_umum FROM absensi WHERE id_pegawai = $p->id AND jenis_absensi = 'Cuti Umum' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->cuti_umum + $db->query("SELECT COUNT(*) as cuti_sosial FROM absensi WHERE id_pegawai = $p->id AND jenis_absensi = 'Cuti Sosial' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->cuti_sosial;
              $terlambat = $db->query("SELECT COUNT(*) as terlambat FROM view_rekap_presensi WHERE id_pegawai = $p->id AND SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1) > ADDTIME(SUBSTRING_INDEX(SUBSTRING_INDEX(jadwal_jam_masuk_pulang, ' - ', 1), ' - ', -1), '00:05:00') AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->getRow()->terlambat;
              $pulangCepat = $db->query("SELECT COUNT(*) as pulang_cepat FROM view_rekap_presensi WHERE id_pegawai = $p->id AND jam_timediff != '00:00:00.000000' AND jam_timediff < jadwal_timediff AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->getRow()->pulang_cepat;

              $tugasDinas = $db->query("SELECT * FROM tugas_dinas WHERE id_pegawai = $p->id AND lumsum = false AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->getResult();
              $tugas_dinas_lumsum1 = $db->query("SELECT COUNT(*) as tugas_dinas_lumsum1 FROM tugas_dinas WHERE id_pegawai = $p->id AND lumsum = 1 AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->tugas_dinas_lumsum1;
              $tugas_dinas_lumsum0 = $db->query("SELECT COUNT(*) as tugas_dinas_lumsum0 FROM tugas_dinas WHERE id_pegawai = $p->id AND lumsum = 0 AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->tugas_dinas_lumsum0;
              if ($tugasDinas) {
                $durasiJamTugas = 0;
                foreach ($tugasDinas as $t) {
                  $durasiJamTugas += getWorkingHoursSimple($t->tanggal, $p->id);
                }
              }
              if (isset($p->id_unit)) {
                $totalAlpha[$p->id_unit] += $alpha;
                $totalTerlambat[$p->id_unit] += $terlambat;
                $totaPulangCepat[$p->id_unit] += $pulangCepat;
              }
              $hadir += $tugas_dinas_lumsum0;
              $alpha = $totalWorkingDays - $hadir - $izin - $sakit - $cuti - $tugas_dinas_lumsum0 - $tugas_dinas_lumsum1;
              //   if($p->id == 169) {
              //     var_dump($totalWorkingDays);
              //     var_dump($hadir);
              //     var_dump($tugas_dinas_lumsum1);
              //     var_dump($tugas_dinas_lumsum0);
              //     var_dump($izin);
              //     var_dump($sakit);
              //     var_dump($cuti);
              //     var_dump($totalWorkingDays - $hadir - $tugas_dinas_lumsum1 - $tugas_dinas_lumsum0 - $izin - $sakit - $cuti);
              //   }

              $durasiJamKerja = $db->query("SELECT SUM(TIME_TO_SEC(jam_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->getRow()->total_hours ?? '0';
              $durasiJamKerjaPlus1 = $db->query("SELECT SUM(TIME_TO_SEC('01:00:00.000000')/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND jam_timediff = '00:00:00.000000' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->getRow()->total_hours ?? '0';
              $durasiJamKerja = $durasiJamKerja + $durasiJamKerjaPlus1;
              $jadwalDurasiJamKerja = getWorkingHours($tanggal_awal, $tanggal_akhir, $p->id);
              if ($durasiJamKerja < $jadwalDurasiJamKerja) {
                $durasiKerjaHari = $jadwalDurasiJamKerja - $durasiJamKerja;
                $pembagi = $jadwalDurasiJamKerja / $totalWorkingDays;
                $durasiKerjaHari = floor($durasiKerjaHari / $pembagi);
                if ($terlambat == 0 && $pulangCepat == 0) {
                  $durasiKerjaHari = $hadir;
                } else if ($durasiKerjaHari <= $hadir) {
                  $durasiKerjaHari = $hadir - $durasiKerjaHari;
                  $hasil = $durasiJamKerja / $pembagi;
                  $durasiKerjaHari = ceil($hasil);
                } else {
                  $durasiKerjaHari = 0;
                }
              } else {
                $durasiKerjaHari = $totalWorkingDays;
              }
              $percentageKehadiran = floor((90 / 100) * $totalWorkingDays);
              $tepatWaktu = $hadir - $terlambat;
              if ($tepatWaktu >= $percentageKehadiran) {
                $bonusKehadiran = '<i class="fas fa-check-circle text-success"></i> <span class="d-none">Dapat</span>';
              } else {
                $bonusKehadiran = '<i class="fas fa-times-circle text-danger"></i> <span class="d-none">Tidak Dapat</span>';
              }
              ?>
              <tr>
                <td></td>
                <td><?= $p->nama ?></td>
                <td><?= $totalWorkingDays ?></td>
                <td><?= $jadwalDurasiJamKerja ?></td>
                <td><?= $hadir + $tugas_dinas_lumsum0 + $tugas_dinas_lumsum1 ?></td>
                <td><?= $tugas_dinas_lumsum1 ?></td>
                <td><?= $tugas_dinas_lumsum0 ?></td>
                <td><?= $izin ?></td>
                <td><?= $sakit ?></td>
                <td><?= $cuti ?></td>
                <td><?= $alpha < 1 ? 0 : $alpha ?></td>
                <td class="td-terlambat"><?= $terlambat ?></td>
                <td class="td-pulang-cepat"><?= $pulangCepat ?></td>
                <td><?= number_format($durasiJamKerja, 2) ?></td>
                <td><?= $durasiKerjaHari ?></td>
                <td><?= $bonusKehadiran ?></td>
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
<!-- daterange -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<!-- datatable css -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<!-- daterange -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

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
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  $(document).ready(function() {
    <?php if (isset($input->unit)) : ?>
      unit = <?= json_encode($input->unit) ?>;
      let namaUnit = unit.slice(0, 10).map((obj, index) => {
        let rObj = {}
        rObj = obj.nama_unit
        return rObj
      })

      let alpha = [];
      let terlambat = [];
      let pulangCepat = [];
      <?php if (isset($input->unit)) :  ?>
        <?php foreach ($input->unit as $row) : ?>
          alpha.push(<?= $totalAlpha[$row->id] ?>);
          terlambat.push(<?= $totalTerlambat[$row->id] ?>);
          pulangCepat.push(<?= $totaPulangCepat[$row->id] ?>);
        <?php endforeach ?>
      <?php endif ?>
      // alpha = alpha.filter(function(value) {
      //   return value > 0;
      // });

      var options = {
        series: [{
          name: 'Alpha',
          data: alpha
        }, {
          name: 'Terlambat',
          data: terlambat
        }, {
          name: 'Pulang Cepat',
          data: pulangCepat
        }],
        chart: {
          type: 'bar',
          height: 350
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
        xaxis: {
          categories: namaUnit,
        },
        yaxis: {
          title: {
            text: ''
          }
        },
        fill: {
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val
            }
          }
        }
      };

    <?php endif ?>
    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();

    // https://stackoverflow.com/questions/35232127/how-to-sum-a-single-table-column-using-javascript
    var cls = document.getElementsByTagName("td");
    var sumTerlambat = 0;
    var sumPulangCepat = 0;
    for (var i = 0; i < cls.length; i++) {
      if (cls[i].className == "td-terlambat") {
        sumTerlambat += isNaN(cls[i].innerHTML) ? 0 : parseInt(cls[i].innerHTML);
      }
      if (cls[i].className == "td-pulang-cepat") {
        sumPulangCepat += isNaN(cls[i].innerHTML) ? 0 : parseInt(cls[i].innerHTML);
      }
    }
    $('.card-total-terlambat').html(sumTerlambat);
    $('.card-total-pulang-cepat').html(sumPulangCepat);
    // datatable
    table = $('#myTable').DataTable({
      paging: false,
      order: [1, 'asc'],
      columnDefs: [{
        targets: [0],
        orderable: false,
      }, ],
      dom: 'Bfrtip',
      buttons: [{
          extend: 'excelHtml5',
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15]
          }
        },
        {
          extend: 'csvHtml5',
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15]
          }
        },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15]
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

    // date
    $('#dates').daterangepicker({
      ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      locale: {
        "format": "DD-MM-YYYY",
      },
      "alwaysShowCalendars": true,
      // "maxDate": moment()
    }, function(start, end, label) {
      console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });

    // select2
    $('.select2').select2();
  });
</script>
<?= $this->endSection(); ?>