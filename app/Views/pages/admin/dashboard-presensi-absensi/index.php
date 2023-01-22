<?= $this->extend('layouts/admin'); ?>

<?= $this->section('title'); ?>
<?= $title ?>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<!-- Page Heading -->
<h1 class="h3 text-gray-800 mb-1"><?= $title ?></h1>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
</div>

<!-- filter button -->
<div class="row">
  <div class="col-md">
    <div class="card card-filter shadow mb-3">
      <div class="card-body">
        <form action="<?= base_url('dashboard-presensi-absensi') ?>" method="GET">
          <div class="form-row align-items-end">
            <div class="form-group mb-0 col-md-3">
              <label for="dates">Tanggal</label>
              <input type="text" class="form-control" id="dates" name="dates" value="<?= isset($input->dates) ? $input->dates  : '' ?>">
            </div>
            <div class="form-group mb-0 col-md-6">
              <?php if (isset($input->dates)) : ?>
                <a href="<?= base_url('dashboard-presensi-absensi') ?>" class="btn btn-secondary shadow-sm">Hapus Filter</a>
              <?php endif ?>
              <button type="submit" class="btn btn-primary shadow-sm "><i class="fas fa-filter"></i> Filter</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$db    = \Config\Database::connect();
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

$pegawaiId = $pegawai->id ?? false;
if ($pegawaiId) {
  $selectAbsen = $db->query("SELECT tanggal FROM absensi WHERE id_pegawai = $pegawai->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getResultArray();
  $implodeAbsen = implode("', '", array_column($selectAbsen, 'tanggal'));
  $notInAbsen = !empty($selectAbsen) ? "AND tanggal NOT IN ('$implodeAbsen')" : '';
  $selectLumsum0 = $db->query("SELECT tanggal FROM tugas_dinas WHERE id_pegawai = $pegawai->id AND lumsum = 0 AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays $notInAbsen")->getResultArray();
  $implodeLumsum0 = implode("', '", array_column($selectLumsum0, 'tanggal'));
  $notInLumsum0 = !empty($selectLumsum0) ? "AND tanggal NOT IN ('$implodeLumsum0')" : '';
  $hadir = $db->query("SELECT COUNT(*) as hadir FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' $notInHolidays $notInAbsen $notInLumsum0")->getRow()->hadir;
  $izin = $db->query("SELECT COUNT(*) as izin FROM absensi WHERE id_pegawai = $pegawai->id AND jenis_absensi = 'Izin' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->izin;
  $tugasIzinBelajar = $db->query("SELECT COUNT(*) as tugasIzinBelajar FROM absensi WHERE id_pegawai = $pegawai->id AND jenis_absensi = 'Tugas/Izin Belajar' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->tugasIzinBelajar;
  $sakit = $db->query("SELECT COUNT(*) as sakit FROM absensi WHERE id_pegawai = $pegawai->id AND jenis_absensi = 'Sakit' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->sakit;
  $cutiUmum = $db->query("SELECT COUNT(*) as cuti_umum FROM absensi WHERE id_pegawai = $pegawai->id AND jenis_absensi = 'Cuti Umum' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->cuti_umum;
  $cutiTahunan = $db->query("SELECT COUNT(*) as cuti_tahunan FROM absensi WHERE id_pegawai = $pegawai->id AND jenis_absensi = 'Cuti Tahunan' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->cuti_tahunan;
  $cutiSosial = $db->query("SELECT COUNT(*) as cuti_sosial FROM absensi WHERE id_pegawai = $pegawai->id AND jenis_absensi = 'Cuti Sosial' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getRow()->cuti_sosial;
  $cuti = $cutiTahunan + $cutiUmum + $cutiSosial;

  $terlambat = 0;
  $begin = new DateTime($tanggal_awal);
  $end = new DateTime(date('Y-m-d', strtotime("$tanggal_akhir +1 day")));
  $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
  foreach ($daterange as $date) {
    $date = $date->format('Y-m-d');
    $toleransiTerlambat = $db->query("SELECT durasi_toleransi FROM `toleransi_terlambat` WHERE '$date' BETWEEN tanggal_mulai AND CASE WHEN tanggal_selesai IS NULL THEN CURDATE() + INTERVAL 1 YEAR ELSE tanggal_selesai END")->getRow();
    $durasiToleransi = $toleransiTerlambat->durasi_toleransi ?? '00:00:00';
    $terlambat += $db->query("SELECT COUNT(*) as terlambat FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 1), ' - ', -1) > ADDTIME(SUBSTRING_INDEX(SUBSTRING_INDEX(jadwal_jam_masuk_pulang, ' - ', 1), ' - ', -1), '$durasiToleransi') AND tanggal = '$date' $notInAbsen")->getRow()->terlambat;
  }
  $pulangCepat = $db->query("SELECT COUNT(*) as pulang_cepat FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND jam_timediff != '00:00:00.000000' AND jam_timediff < jadwal_timediff AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->getRow()->pulang_cepat;

  $tugasDinas = $db->query(" SELECT * FROM tugas_dinas WHERE id_pegawai=$pegawai->id AND lumsum = false AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->getResult();
  $tugas_dinas_lumsum1 = $db->query("SELECT COUNT(*) as tugas_dinas_lumsum1 FROM tugas_dinas WHERE id_pegawai = $pegawai->id AND lumsum = 1 AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays $notInAbsen")->getRow()->tugas_dinas_lumsum1;
  $tugas_dinas_lumsum0 = $db->query("SELECT COUNT(*) as tugas_dinas_lumsum0 FROM tugas_dinas WHERE id_pegawai = $pegawai->id AND lumsum = 0 AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays $notInAbsen")->getRow()->tugas_dinas_lumsum0;
  if ($tugasDinas) {
    $durasiJamTugas = 0;
    foreach ($tugasDinas as $t) {
      $durasiJamTugas += getWorkingHoursSimple($t->tanggal, $pegawai->id);
    }
  }

  $jabatanStrukturalUser = $db->table('pegawai_jabatan_struktural_u_view')->select('nama_jabatan_struktural')->where('id_pegawai', $pegawai->id)->get()->getRow();
  $jabatanUser = $db->table('pegawai_jabatan_u_view')->select('nama_jabatan')->where('id_pegawai', $pegawai->id)->get()->getRow();
  if ($jabatanStrukturalUser && $jabatanStrukturalUser->nama_jabatan_struktural == 'Wakil Rektor') {
    $hadir = $totalWorkingDays - $izin - $sakit - $cuti - $tugas_dinas_lumsum1;
    $alpha = 0;
  } else {
    $hadir += $tugas_dinas_lumsum0;
    $alpha = $totalWorkingDays - $hadir - $izin - $tugasIzinBelajar - $sakit - $cuti - $tugas_dinas_lumsum0 - $tugas_dinas_lumsum1;
  }

  $selectLupaPresensi = $db->query("SELECT tanggal FROM presensi_lupa WHERE id_pegawai = $pegawai->id AND status = 'Diterima' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getResultArray();
  $selectIzin = $db->query("SELECT tanggal FROM presensi_izin WHERE id_pegawai = $pegawai->id AND status2 = 'Diterima' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' AND CASE WHEN DATE_FORMAT(tanggal, '%w') = 0 THEN 7 ELSE DATE_FORMAT(tanggal, '%w') END in (1, 2, 3, 4, 5) $notInHolidays")->getResultArray();
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
    $durasiJamKerja1 = $db->query("SELECT SUM(TIME_TO_SEC(jam_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND (alasan_pulang_cepat != 'Mengajar' OR  alasan_pulang_cepat IS NULL) AND tanggal BETWEEN '$tanggal_awal' AND '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' - INTERVAL 1 DAY $notInLupaPresensi $notInIzin")->getRow()->total_hours ?? '0';
    // durasi jam kerja full sesuai jadwal jam kerja karena mengajar kelas regulaer diluar jam kerja
    $durasiJamKerjaMengajar = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND alasan_pulang_cepat = 'Mengajar' AND tanggal BETWEEN '$tanggal_awal' AND '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' - INTERVAL 1 DAY $notInLupaPresensi $notInIzin")->getRow()->total_hours ?? '0';
    // durasi ditambah 1 jam karena lupa absen pulang 
    $durasiJamKerjaPlus1 = $db->query("SELECT SUM(TIME_TO_SEC('01:00:00.000000')/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND jam_timediff = '00:00:00.000000' AND tanggal BETWEEN '$tanggal_awal' AND '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' - INTERVAL 1 DAY $notInLupaPresensi $notInIzin")->getRow()->total_hours ?? '0';
    // durasi ditambah durasi lupa presensi yang tervalidasi
    $durasiJamKerjaLupaPresensi = $db->query("SELECT SUM(TIME_TO_SEC(jam_timediff)/3600) as total_hours FROM view_rekap_presensi_lupa WHERE id_pegawai = $pegawai->id AND tanggal BETWEEN '$tanggal_awal' AND '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' - INTERVAL 1 DAY")->getRow()->total_hours ?? '0';
    // durasi ditambah durasi izin presensi yang tervalidasi
    $durasiJamKerjaIzin = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi_izin WHERE id_pegawai = $pegawai->id AND tanggal BETWEEN '$tanggal_awal' AND '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' - INTERVAL 1 DAY")->getRow()->total_hours ?? '0';
    $durasiJamKerja1 = $durasiJamKerja1 + $durasiJamKerjaMengajar + $durasiJamKerjaPlus1 + $durasiJamKerjaLupaPresensi + $durasiJamKerjaIzin;

    if ($jabatanUser && $jabatanUser->nama_jabatan == 'Dosen') {
      $durasiJamKerja2 = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND tanggal BETWEEN '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' AND '$tanggal_akhir' $notInLupaPresensi")->getRow()->total_hours ?? '0';
    } else {
      // durasi jam kerja biasa
      $durasiJamKerja2 = $db->query("SELECT SUM(TIME_TO_SEC(jam_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND (alasan_pulang_cepat != 'Mengajar' OR  alasan_pulang_cepat IS NULL) AND tanggal BETWEEN '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin")->getRow()->total_hours ?? '0';
      // durasi jam kerja full sesuai jadwal jam kerja karena mengajar kelas regulaer diluar jam kerja
      $durasiJamKerjaMengajar = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND alasan_pulang_cepat = 'Mengajar' AND tanggal BETWEEN '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin")->getRow()->total_hours ?? '0';
      // durasi ditambah 1 jam karena lupa absen pulang 
      $durasiJamKerjaPlus1 = $db->query("SELECT SUM(TIME_TO_SEC('01:00:00.000000')/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND jam_timediff = '00:00:00.000000' AND tanggal BETWEEN '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin")->getRow()->total_hours ?? '0';
      // durasi ditambah durasi lupa presensi yang tervalidasi
      $durasiJamKerjaLupaPresensi = $db->query("SELECT SUM(TIME_TO_SEC(jam_timediff)/3600) as total_hours FROM view_rekap_presensi_lupa WHERE id_pegawai = $pegawai->id AND tanggal BETWEEN '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' AND '$tanggal_akhir'")->getRow()->total_hours ?? '0';
      // durasi ditambah durasi izin presensi yang tervalidasi
      $durasiJamKerjaIzin = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi_izin WHERE id_pegawai = $pegawai->id AND tanggal BETWEEN '$konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali' AND '$tanggal_akhir'")->getRow()->total_hours ?? '0';
      $durasiJamKerja2 = $durasiJamKerja2 + $durasiJamKerjaMengajar + $durasiJamKerjaPlus1 + $durasiJamKerjaLupaPresensi + $durasiJamKerjaIzin;
    }
    $durasiJamKerja = $durasiJamKerja1 + $durasiJamKerja2;

    // durasi dikurangi durasi istirahat
    $durasiJamKerjaKurangiIstirahat = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff_istirahat)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND jam_timediff != '00:00:00.000000' AND SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1) >= AddTime(SUBSTRING_INDEX(SUBSTRING_INDEX(jadwal_jam_istirahat, ' - ', 2), ' - ', -1), '00:01:00') AND tanggal BETWEEN '$konfigurasiPresensi->tanggal_mulai_durasi_jam_kerja_dikurangi_istirahat' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin")->getRow()->total_hours ?? '0';
    $durasiJamKerja -= $durasiJamKerjaKurangiIstirahat;
  } else {
    if ($jabatanUser && $jabatanUser->nama_jabatan == 'Dosen' && $tanggal_awal >= $konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali) {
      $durasiJamKerja = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' $notInLupaPresensi")->getRow()->total_hours ?? '0';
    } else {
      // durasi jam kerja biasa
      $durasiJamKerja = $db->query("SELECT SUM(TIME_TO_SEC(jam_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND (alasan_pulang_cepat != 'Mengajar' OR  alasan_pulang_cepat IS NULL) AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin")->getRow()->total_hours ?? '0';
      // durasi jam kerja full sesuai jadwal jam kerja karena mengajar kelas regulaer diluar jam kerja
      $durasiJamKerjaMengajar = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND alasan_pulang_cepat = 'Mengajar' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin")->getRow()->total_hours ?? '0';
      // durasi ditambah 1 jam karena lupa absen pulang 
      $durasiJamKerjaPlus1 = $db->query("SELECT SUM(TIME_TO_SEC('01:00:00.000000')/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND jam_timediff = '00:00:00.000000' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin")->getRow()->total_hours ?? '0';
      // durasi ditambah durasi lupa presensi yang tervalidasi
      $durasiJamKerjaLupaPresensi = $db->query("SELECT SUM(TIME_TO_SEC(jam_timediff)/3600) as total_hours FROM view_rekap_presensi_lupa WHERE id_pegawai = $pegawai->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->getRow()->total_hours ?? '0';
      // durasi ditambah durasi izin presensi yang tervalidasi
      $durasiJamKerjaIzin = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff)/3600) as total_hours FROM view_rekap_presensi_izin WHERE id_pegawai = $pegawai->id AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")->getRow()->total_hours ?? '0';
      $durasiJamKerja = $durasiJamKerja + $durasiJamKerjaMengajar + $durasiJamKerjaPlus1 + $durasiJamKerjaLupaPresensi + $durasiJamKerjaIzin;
    }

    if ($tanggal_awal >= $konfigurasiPresensi->tanggal_mulai_durasi_jam_kerja_dikurangi_istirahat) {
      // durasi dikurangi durasi istirahat
      $durasiJamKerjaKurangiIstirahat = $db->query("SELECT SUM(TIME_TO_SEC(jadwal_timediff_istirahat)/3600) as total_hours FROM view_rekap_presensi WHERE id_pegawai = $pegawai->id AND jam_timediff != '00:00:00.000000' AND SUBSTRING_INDEX(SUBSTRING_INDEX(jam_masuk_pulang, ' - ', 2), ' - ', -1) >= AddTime(SUBSTRING_INDEX(SUBSTRING_INDEX(jadwal_jam_istirahat, ' - ', 2), ' - ', -1), '00:01:00') AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' $notInLupaPresensi $notInIzin")->getRow()->total_hours ?? '0';
      $durasiJamKerja -= $durasiJamKerjaKurangiIstirahat;
    }
  }

  $jadwalDurasiJamKerja = getWorkingHours($tanggal_awal, $tanggal_akhir, $pegawai->id);
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
    $bonusKehadiran = '<i class="fas fa-check-circle text-success"></i> <span class="d-inline">Dapat</span>';
  } else {
    $bonusKehadiran = '<i class="fas fa-times-circle text-danger"></i> <span class="d-inline">Tidak Dapat</span>';
  }

  $alpha = $alpha < 1 ? 0 : $alpha;
  $cutiDiambil = $cutiUmum + $cutiTahunan + $izin + $alpha;
}
?>
<!-- row card -->
<div class="row">
  <!-- Card -->
  <div class="col-md-3 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Hari Kerja</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($totalWorkingDays, 0, ',', '.') ?>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Card -->
  <div class="col-md-3 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Hadir</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($hadir) ? number_format($hadir, 0, ',', '.') : '-' ?>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Card -->
  <div class="col-md-3 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Izin</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($izin) ? number_format($izin, 0, ',', '.') : '-' ?>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Card -->
  <div class="col-md-3 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tugas / Izin Belajar</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($tugasIzinBelajar) ? number_format($tugasIzinBelajar, 0, ',', '.') : '-' ?>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Card -->
  <div class="col-md-3 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Sakit</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($sakit) ? number_format($sakit, 0, ',', '.') : '-' ?>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Card -->
  <div class="col-md-3 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Alpha</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($alpha) ? number_format($alpha, 0, ',', '.') : '-' ?>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Card -->
  <div class="col-md-3 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Terlambat</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($terlambat) ? number_format($terlambat, 0, ',', '.') : '-' ?>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Card -->
  <div class="col-md-3 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pulang Cepat</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($pulangCepat) ? number_format($pulangCepat, 0, ',', '.') : '-' ?>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Card -->
  <div class="col-md-3 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Cuti Tahunan</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($cutiTahunan) ? number_format($cutiTahunan, 0, ',', '.') : '-' ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Card -->
  <div class="col-md-3 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Cuti Umum</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($cutiUmum) ? number_format($cutiUmum, 0, ',', '.') : '-' ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Card -->
  <div class="col-md-3 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Cuti Sosial</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($cutiSosial) ? number_format($cutiSosial, 0, ',', '.') : '-' ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Card -->
  <div class="col-md-3 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Cuti yang Diambil</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($cutiDiambil) ? number_format($cutiDiambil, 0, ',', '.') : '-' ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card -->
  <div class="col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Durasi Kerja (Hari)</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($durasiKerjaHari) ? number_format($durasiKerjaHari, 0, ',', '.') : '-' ?>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Card -->
  <div class="col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Bonus Kehadiran</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= isset($bonusKehadiran) ? $bonusKehadiran : '-' ?>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Row chart -->
<div class="row">
  <!-- Area Chart -->
  <!-- <div class="col-xl-8 col-lg-7">
    <div class="card shadow mb-4">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary"></h6>
      </div>
      <div class="card-body">
        <div class="chart-area">
          <canvas id="myAreaChart"></canvas>
        </div>
      </div>
    </div>
  </div>  -->
</div>
<?= $this->endSection(); ?>

<?= $this->section('append-style'); ?>
<!-- daterange -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<!-- datatable -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.css">
<?= $this->endSection(); ?>

<?= $this->section('append-script'); ?>
<!-- daterange -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- datatable js -->
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js"></script>
<!-- chart js -->
<script src="<?= base_url() ?>/assets/backend/libs/sbadmin2/vendor/chart.js/Chart.min.js"></script>
<script>
  $(document).ready(function() {
    // $('#form').on('change', function() {
    //   $('#form').submit();
    // });
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

    // chart js
    // Set new default font family and font color to mimic Bootstrap's default styling
    // Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    // Chart.defaults.global.defaultFontColor = '#858796';

  });
</script>
<?= $this->endSection(); ?>