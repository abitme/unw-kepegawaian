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
              <th>Tugas/Izin Belajar</th>
              <th>Sakit</th>
              <th>Cuti</th>
              <th>Alpha</th>
              <th>Cuti Yang Telah Diambil</th>
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
            $arrDateHolidays = array_column($selectHolidays, 'tanggal');
            $implodeHolidays = implode("', '", $arrDateHolidays);
            $notInHolidays = !empty($selectHolidays) ? "AND tanggal NOT IN ('$implodeHolidays')" : '';
            $totalHoliday = $db->query("SELECT COUNT(*) as libur FROM hari_libur WHERE tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5)")->getRow()->libur;
            $durasiKerjaJamJadwal = getWorkingDays($tanggal_awal, $tanggal_akhir, [1, 2, 3, 4, 5]);
            $durasiKerjaJamJadwal -= $totalHoliday;

            foreach ($pegawai as $p) : $no++ ?>
              <?php
              $selectAbsen = $db->query("SELECT tanggal FROM absensi WHERE id_pegawai = $p->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getResultArray();
              $arrDateAbsen = array_column($selectAbsen, 'tanggal');
              $implodeAbsen = implode("', '", $arrDateAbsen);
              $notInAbsen = !empty($selectAbsen) ? "AND tanggal NOT IN ('$implodeAbsen')" : '';
              $selectLumsum0 = $db->query("SELECT tanggal FROM tugas_dinas WHERE id_pegawai = $p->id AND lumsum = 0 AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays $notInAbsen")->getResultArray();
              $arrDateLumsum0 = array_column($selectLumsum0, 'tanggal');
              $implodeLumsum0 = implode("', '", $arrDateLumsum0);
              $notInLumsum0 = !empty($selectLumsum0) ? "AND tanggal NOT IN ('$implodeLumsum0')" : '';
              $selectLumsum1 = $db->query("SELECT tanggal FROM tugas_dinas WHERE id_pegawai = $p->id AND lumsum = 1 AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays $notInAbsen")->getResultArray();
              $arrDateLumsum1 = array_column($selectLumsum1, 'tanggal');
              $implodeLumsum1 = implode("', '", $arrDateLumsum1);
              $notInLumsum1 = !empty($selectLumsum1) ? "AND tanggal NOT IN ('$implodeLumsum1')" : '';
              $selectHadir = $db->query("SELECT tanggal FROM view_rekap_presensi WHERE id_pegawai = $p->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays $notInAbsen $notInLumsum0 $notInLumsum1")->getResultArray();
              $arrDateHadir = array_column($selectHadir, 'tanggal');

              $hadir = $db->query("SELECT COUNT(*) as hadir FROM view_rekap_presensi WHERE id_pegawai = $p->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' $notInHolidays $notInAbsen $notInLumsum0 $notInLumsum1")->getRow()->hadir;
              $izin = $db->query("SELECT COUNT(*) as izin FROM absensi WHERE id_pegawai = $p->id AND jenis_absensi = 'Izin' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->izin;
              $tugasIzinBelajar = $db->query("SELECT COUNT(*) as tugasIzinBelajar FROM absensi WHERE id_pegawai = $p->id AND jenis_absensi = 'Tugas/Izin Belajar' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->tugasIzinBelajar;
              $sakit = $db->query("SELECT COUNT(*) as sakit FROM absensi WHERE id_pegawai = $p->id AND jenis_absensi = 'Sakit' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->sakit;
              $cutiUmum = $db->query("SELECT COUNT(*) as cuti_umum FROM absensi WHERE id_pegawai = $p->id AND jenis_absensi = 'Cuti Umum' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->cuti_umum;
              $cutiTahunan = $db->query("SELECT COUNT(*) as cuti_tahunan FROM absensi WHERE id_pegawai = $p->id AND jenis_absensi = 'Cuti Tahunan' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->cuti_tahunan;
              $cutiSosial = $db->query("SELECT COUNT(*) as cuti_sosial FROM absensi WHERE id_pegawai = $p->id AND jenis_absensi = 'Cuti Sosial' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->cuti_sosial;
              $cuti = $cutiTahunan + $cutiUmum + $cutiSosial;

              $terlambat = 0;
              $begin = new DateTime($tanggal_awal);
              $end = new DateTime(date('Y-m-d', strtotime("$tanggal_akhir +1 day")));
              $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
              $arrDateWeekend = [];
              foreach ($daterange as $date) {
                $dateFormatted = $date->format('Y-m-d');
                $toleransiTerlambat = $db->query("SELECT durasi_toleransi FROM `toleransi_terlambat` WHERE '$dateFormatted' BETWEEN tanggal_mulai AND CASE WHEN tanggal_selesai IS NULL THEN CURDATE() + INTERVAL 1 YEAR ELSE tanggal_selesai END")->getRow();
                $durasiToleransi = $toleransiTerlambat->durasi_toleransi ?? '00:00:00';
                $terlambat += $db->query("SELECT COUNT(*) as terlambat FROM view_rekap_presensi WHERE id_pegawai = $p->id AND SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1) > ADDTIME(SUBSTRING_INDEX(SUBSTRING_INDEX(jadwal_jam_masuk_pulang, ' - ', 1), ' - ', -1), '$durasiToleransi') AND tanggal = '$dateFormatted' $notInHolidays $notInAbsen $notInLumsum0 $notInLumsum1")->getRow()->terlambat;
                if ($date->format("N") == 6 || $date->format("N") == 7) {
                  array_push($arrDateWeekend, $dateFormatted);
                }
              }

              $pulangCepat = $db->query("SELECT COUNT(*) as pulang_cepat FROM view_rekap_presensi WHERE id_pegawai = $p->id AND jam_timediff != '00:00:00.000000' AND jam_timediff < jadwal_timediff AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->getRow()->pulang_cepat;

              $tugasDinas = $db->query(" SELECT * FROM tugas_dinas WHERE id_pegawai=$p->id AND lumsum = false AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->getResult();
              $dinas_dengan_lumsum = $db->query("SELECT COUNT(*) as dinas_dengan_lumsum FROM tugas_dinas WHERE id_pegawai = $p->id AND lumsum = 1 AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays $notInAbsen")->getRow()->dinas_dengan_lumsum;
              $dinas_tanpa_lumsum = $db->query("SELECT COUNT(*) as dinas_tanpa_lumsum FROM tugas_dinas WHERE id_pegawai = $p->id AND lumsum = 0 AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays $notInAbsen")->getRow()->dinas_tanpa_lumsum;
              $durasiJamTugasDinasLumsum0 = 0;
              if ($selectLumsum0) {
                $durasiJamTugasDinasLumsum0 = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND tanggal IN ('$implodeLumsum0')")->getRow()->total_hours ?? '0';
              }

              $jabatanStrukturalUser = $db->table('pegawai_jabatan_struktural_u_view')->select('nama_jabatan_struktural')->where('id_pegawai', $p->id)->get()->getRow();
              $jabatanUser = $db->table('pegawai_jabatan_u_view')->select('nama_jabatan')->where('id_pegawai', $p->id)->get()->getRow();
              if ($jabatanStrukturalUser && $jabatanStrukturalUser->nama_jabatan_struktural == 'Wakil Rektor') {
                $hadir = $durasiKerjaJamJadwal - $izin - $tugasIzinBelajar - $sakit - $cuti - $dinas_dengan_lumsum;
                $alpha = 0;
              } else {
                $hadir += $dinas_tanpa_lumsum;
                $alpha = $durasiKerjaJamJadwal - $hadir - $izin - $tugasIzinBelajar - $sakit - $cuti - $dinas_tanpa_lumsum - $dinas_dengan_lumsum;
              }
              $givenDates = array_merge($arrDateWeekend, $arrDateHolidays, $arrDateAbsen, $arrDateLumsum0, $arrDateLumsum1, $arrDateHadir);
              $missingDates = [];
              foreach ($daterange as $date) {
                $dateFormatted = $date->format('Y-m-d');
                if (!in_array($dateFormatted, $givenDates))
                  $missingDates[] = $date->format('d/m/Y');
              }
              $alphaDate = $missingDates;

              $selectLupaPresensi = $db->query("SELECT tanggal FROM presensi_lupa WHERE id_pegawai = $p->id AND status = 'Diterima' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getResultArray();
              $selectIzin = $db->query("SELECT tanggal FROM presensi_izin WHERE id_pegawai = $p->id AND status2 = 'Diterima' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getResultArray();
              $implodeLupaPresensi = implode("', '", array_column($selectLupaPresensi, 'tanggal'));
              $implodeIzin = implode("', '", array_column($selectIzin, 'tanggal'));
              $notInLupaPresensi = !empty($selectLupaPresensi) ? "AND tanggal NOT IN ('$implodeLupaPresensi')" : '';
              $notInIzin = !empty($selectIzin) ? "AND tanggal NOT IN ('$implodeIzin')" : '';

              $konfigurasiPresensi = $db->table('konfigurasi_presensi')->get()->getRow();
              $isBetweenDifferentRule = false;
              $arrDifferentRule = [];
              foreach ($daterange as $date) {
                $date = $date->format('Y-m-d');
                array_push($arrDifferentRule, $date >= $konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali);
              }
              if (sizeof(array_unique($arrDifferentRule)) >= 2) $isBetweenDifferentRule = true;

              if ($isBetweenDifferentRule) {
                // durasi jam kerja biasa
                $durasiKerjaJamPegawai1 = $db->query("SELECT SUM(TIME_TO_SEC(jam_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND (alasan_pulang_cepat != 'Mengajar' OR  alasan_pulang_cepat IS NULL) AND tanggal BETWEEN '$tanggal_awal' AND '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' - INTERVAL 1 DAY $notInLupaPresensi $notInIzin $notInHolidays $notInAbsen $notInLumsum0 $notInLumsum1")->getRow()->total_hours ?? '0';
                // durasi jam kerja full sesuai jadwal jam kerja karena mengajar kelas regulaer diluar jam kerja
                $durasiKerjaJamPegawaiMengajar = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND alasan_pulang_cepat = 'Mengajar' AND tanggal BETWEEN '$tanggal_awal' AND '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' - INTERVAL 1 DAY $notInLupaPresensi $notInIzin $notInHolidays $notInAbsen $notInLumsum0 $notInLumsum1")->getRow()->total_hours ?? '0';
                // durasi ditambah 1 jam karena lupa absen pulang 
                $durasiKerjaJamPegawaiPlus1 = $db->query("SELECT SUM(TIME_TO_SEC('01:00:00.000000')/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND jam_timediff = '00:00:00.000000' AND tanggal BETWEEN '$tanggal_awal' AND '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' - INTERVAL 1 DAY $notInLupaPresensi $notInIzin $notInHolidays $notInAbsen $notInLumsum0 $notInLumsum1")->getRow()->total_hours ?? '0';
                // durasi ditambah durasi lupa presensi yang tervalidasi
                $durasiKerjaJamPegawaiLupaPresensi = $db->query("SELECT SUM(TIME_TO_SEC(jam_timediff)/3600) as total_hours FROM view_rekap_presensi_lupa WHERE id_pegawai = $p->id AND tanggal BETWEEN '$tanggal_awal' AND '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' - INTERVAL 1 DAY")->getRow()->total_hours ?? '0';
                // durasi ditambah durasi izin presensi yang tervalidasi
                $durasiKerjaJamPegawaiIzin = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi_izin WHERE id_pegawai = $p->id AND tanggal BETWEEN '$tanggal_awal' AND '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' - INTERVAL 1 DAY")->getRow()->total_hours ?? '0';
                $durasiKerjaJamPegawai1 = $durasiKerjaJamPegawai1 + $durasiKerjaJamPegawaiMengajar + $durasiKerjaJamPegawaiPlus1 + $durasiKerjaJamPegawaiLupaPresensi + $durasiKerjaJamPegawaiIzin;

                if ($jabatanUser && $jabatanUser->nama_jabatan == 'Dosen') {
                  $durasiKerjaJamPegawai2 = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND tanggal BETWEEN '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' AND '$tanggal_akhir' $notInLupaPresensi")->getRow()->total_hours ?? '0';
                } else {
                  // durasi jam kerja biasa
                  $durasiKerjaJamPegawai2 = $db->query("SELECT SUM(TIME_TO_SEC(jam_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND (alasan_pulang_cepat != 'Mengajar' OR  alasan_pulang_cepat IS NULL) AND tanggal BETWEEN '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin $notInHolidays $notInAbsen $notInLumsum0 $notInLumsum1")->getRow()->total_hours ?? '0';
                  // durasi jam kerja full sesuai jadwal jam kerja karena mengajar kelas regulaer diluar jam kerja
                  $durasiKerjaJamPegawaiMengajar = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND alasan_pulang_cepat = 'Mengajar' AND tanggal BETWEEN '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin $notInHolidays $notInAbsen $notInLumsum0 $notInLumsum1")->getRow()->total_hours ?? '0';
                  // durasi ditambah 1 jam karena lupa absen pulang 
                  $durasiKerjaJamPegawaiPlus1 = $db->query("SELECT SUM(TIME_TO_SEC('01:00:00.000000')/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND jam_timediff = '00:00:00.000000' AND tanggal BETWEEN '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin $notInHolidays $notInAbsen $notInLumsum0 $notInLumsum1")->getRow()->total_hours ?? '0';
                  // durasi ditambah durasi lupa presensi yang tervalidasi
                  $durasiKerjaJamPegawaiLupaPresensi = $db->query("SELECT SUM(TIME_TO_SEC(jam_timediff)/3600) as total_hours FROM view_rekap_presensi_lupa WHERE id_pegawai = $p->id AND tanggal BETWEEN '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' AND '$tanggal_akhir'")->getRow()->total_hours ?? '0';
                  // durasi ditambah durasi izin presensi yang tervalidasi
                  $durasiKerjaJamPegawaiIzin = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi_izin WHERE id_pegawai = $p->id AND tanggal BETWEEN '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' AND '$tanggal_akhir'")->getRow()->total_hours ?? '0';
                  $durasiKerjaJamPegawai2 = $durasiKerjaJamPegawai2 + $durasiKerjaJamPegawaiMengajar + $durasiKerjaJamPegawaiPlus1 + $durasiKerjaJamPegawaiLupaPresensi + $durasiKerjaJamPegawaiIzin;
                }
                $durasiKerjaJamPegawai = $durasiKerjaJamPegawai1 + $durasiKerjaJamPegawai2;

                // durasi dikurangi durasi istirahat
                $durasiKerjaJamPegawaiKurangiIstirahat = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff_istirahat)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND jam_timediff != '00:00:00.000000' AND SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1) >= AddTime(SUBSTRING_INDEX(SUBSTRING_INDEX(jadwal_jam_istirahat, ' - ', 2), ' - ', -1), '00:01:00') AND tanggal BETWEEN '$konfigurasiPresensi->tanggal_mulai_durasi_jam_kerja_dikurangi_istirahat' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin $notInHolidays $notInAbsen $notInLumsum0 $notInLumsum1")->getRow()->total_hours ?? '0';
                $durasiKerjaJamPegawai -= $durasiKerjaJamPegawaiKurangiIstirahat;
              } else {
                if ($jabatanUser && $jabatanUser->nama_jabatan == 'Dosen' && $tanggal_awal >= $konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali) {
                  $durasiKerjaJamPegawai = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' $notInLupaPresensi")->getRow()->total_hours ?? '0';
                } else {
                  // durasi jam kerja biasa
                  $durasiKerjaJamPegawai = $db->query("SELECT SUM(TIME_TO_SEC(jam_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND (alasan_pulang_cepat != 'Mengajar' OR  alasan_pulang_cepat IS NULL) AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin $notInHolidays $notInAbsen $notInLumsum0 $notInLumsum1")->getRow()->total_hours ?? '0';
                  // durasi jam kerja full sesuai jadwal jam kerja karena mengajar kelas regulaer diluar jam kerja
                  $durasiKerjaJamPegawaiMengajar = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND alasan_pulang_cepat = 'Mengajar' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin $notInHolidays $notInAbsen $notInLumsum0 $notInLumsum1")->getRow()->total_hours ?? '0';
                  // durasi ditambah 1 jam karena lupa absen pulang 
                  $durasiKerjaJamPegawaiPlus1 = $db->query("SELECT SUM(TIME_TO_SEC('01:00:00.000000')/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND jam_timediff = '00:00:00.000000' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin $notInHolidays $notInAbsen $notInLumsum0 $notInLumsum1")->getRow()->total_hours ?? '0';
                  // durasi ditambah durasi lupa presensi yang tervalidasi
                  $durasiKerjaJamPegawaiLupaPresensi = $db->query("SELECT SUM(TIME_TO_SEC(jam_timediff)/3600) as total_hours FROM view_rekap_presensi_lupa WHERE id_pegawai = $p->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->getRow()->total_hours ?? '0';
                  // durasi ditambah durasi izin presensi yang tervalidasi
                  $durasiKerjaJamPegawaiIzin = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi_izin WHERE id_pegawai = $p->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->getRow()->total_hours ?? '0';
                  $durasiKerjaJamPegawai = $durasiKerjaJamPegawai + $durasiKerjaJamPegawaiMengajar + $durasiKerjaJamPegawaiPlus1 + $durasiKerjaJamPegawaiLupaPresensi + $durasiKerjaJamPegawaiIzin;
                }

                if ($tanggal_awal >= $konfigurasiPresensi->tanggal_mulai_durasi_jam_kerja_dikurangi_istirahat) {
                  // durasi dikurangi durasi istirahat
                  $durasiKerjaJamPegawaiKurangiIstirahat = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff_istirahat)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $p->id AND jam_timediff != '00:00:00.000000' AND SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1) >= AddTime(SUBSTRING_INDEX(SUBSTRING_INDEX(jadwal_jam_istirahat, ' - ', 2), ' - ', -1), '00:01:00') AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin $notInHolidays $notInAbsen $notInLumsum0 $notInLumsum1")->getRow()->total_hours ?? '0';
                  $durasiKerjaJamPegawai -= $durasiKerjaJamPegawaiKurangiIstirahat;
                }
              }
              $durasiKerjaJamPegawai += $durasiJamTugasDinasLumsum0;

              $durasiKerjaHariJadwal = getWorkingHours($tanggal_awal, $tanggal_akhir, $p->id);
              if ($durasiKerjaJamPegawai < $durasiKerjaHariJadwal) {
                $durasiKerjaHariPegawai = $durasiKerjaHariJadwal - $durasiKerjaJamPegawai;
                $pembagi = $durasiKerjaHariJadwal / $durasiKerjaJamJadwal;
                $durasiKerjaHariPegawai = floor($durasiKerjaHariPegawai / $pembagi);
                if ($terlambat == 0 && $pulangCepat == 0) {
                  $durasiKerjaHariPegawai = $hadir;
                } else if ($durasiKerjaHariPegawai <= $hadir) {
                  $durasiKerjaHariPegawai = $hadir - $durasiKerjaHariPegawai;
                  $hasil = $durasiKerjaJamPegawai / $pembagi;
                  $durasiKerjaHariPegawai = ceil($hasil);
                } else {
                  $durasiKerjaHariPegawai = 0;
                }
              } else {
                $durasiKerjaHariPegawai = $durasiKerjaJamJadwal;
              }
              $persentaseKehadiran = floor((85 / 100) * ($hadir + $alpha + $dinas_tanpa_lumsum + $dinas_dengan_lumsum)); //change 90 to 85 (1/23/2023)
              $tepatWaktu = ($hadir + $dinas_tanpa_lumsum + $dinas_dengan_lumsum) - $terlambat;
              if ($tepatWaktu >= $persentaseKehadiran) {
                $bonusKehadiran = '<i class="fas fa-check-circle text-success"></i> <span class="d-none">Dapat</span>';
              } else {
                $bonusKehadiran = '<i class="fas fa-times-circle text-danger"></i> <span class="d-none">Tidak Dapat</span>';
              }

              $alpha = $alpha < 1 ? 0 : $alpha;
              $cutiDiambil = $cutiUmum + $cutiTahunan + $izin + $alpha;
              ?>
              <tr>
                <td></td>
                <td><?= $p->nama ?></td>
                <td><?= $durasiKerjaJamJadwal ?></td>
                <td><?= $durasiKerjaHariJadwal ?></td>
                <td><?= $hadir ?></td>
                <td><?= $dinas_dengan_lumsum ?></td>
                <td><?= $dinas_tanpa_lumsum ?></td>
                <td><?= $tugasIzinBelajar ?></td>
                <td><?= $sakit ?></td>
                <td><?= $cuti ?></td>
                <td><?= $alpha ?> <br> <?= implode(', ', $alphaDate) ?></td>
                <td><?= $cutiDiambil ?></td>
                <td class="td-terlambat"><?= $terlambat  ?></td>
                <td class="td-pulang-cepat"><?= $pulangCepat ?></td>
                <td><?= number_format($durasiKerjaJamPegawai, 2) ?></td>
                <td><?= $durasiKerjaHariPegawai ?></td>
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
<script>
  $(document).ready(function() {
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
            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16]
          }
        },
        {
          extend: 'csvHtml5',
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16]
          }
        },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16]
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