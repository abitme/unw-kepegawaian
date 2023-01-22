<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Presensi::index');

// $routes->get('/presensi-piket', 'PresensiPiket::index');
// $routes->get('/scan-piket', 'PresensiPiket::scan');

$routes->group('dashboard', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'Dashboard::index');
});

$routes->group('dashboard-presensi-absensi', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'DashboardPresensiAbsensi::index');
});

$routes->group('allowed-ip-presensi', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'AllowedIpPresensi::index');
    $routes->post('ajax_list',              'AllowedIpPresensi::ajax_list');
    $routes->post('create',                 'AllowedIpPresensi::create');
    $routes->post('update/(:num)',          'AllowedIpPresensi::update/$1');
    $routes->post('delete/(:num)',          'AllowedIpPresensi::delete/$1');
});

$routes->group('periode', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'Periode::index');
    $routes->post('ajax_list',              'Periode::ajax_list');
    $routes->post('create',                 'Periode::create');
    $routes->post('update/(:num)',          'Periode::update/$1');
    $routes->post('delete/(:num)',          'Periode::delete/$1');
    $routes->add('import',                  'Periode::import');
});

$routes->group('unit', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'Unit::index');
    $routes->post('ajax_list',              'Unit::ajax_list');
    $routes->post('create',                 'Unit::create');
    $routes->post('update/(:num)',          'Unit::update/$1');
    $routes->post('delete/(:num)',          'Unit::delete/$1');
    $routes->add('import',                  'Unit::import');
});

$routes->group('unit-piket', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->post('ajax_list',              'UnitPiket::ajax_list');
    $routes->post('ajax_select2',           'UnitPiket::ajax_select2');
    $routes->get('/',                       'UnitPiket::index');
    $routes->get('(:num)',                  'UnitPiket::show/$1');
    $routes->post('create',                 'UnitPiket::create');
    $routes->post('update/(:num)',          'UnitPiket::update/$1');
    $routes->post('delete/(:num)',          'UnitPiket::delete/$1');
});

$routes->group('unit-relation', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'UnitRelation::index');
    $routes->post('ajax_list',              'UnitRelation::ajax_list');
    $routes->post('ajax_select2',           'UnitRelation::ajax_select2');
    $routes->post('create',                 'UnitRelation::create');
    $routes->post('update/(:num)',          'UnitRelation::update/$1');
    $routes->post('delete/(:num)',          'UnitRelation::delete/$1');
    $routes->add('import',                  'UnitRelation::import');
});

$routes->group('jabatan', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'Jabatan::index');
    $routes->post('ajax_list',              'Jabatan::ajax_list');
    $routes->post('ajax_select2',           'Jabatan::ajax_select2');
    $routes->post('create',                 'Jabatan::create');
    $routes->post('update/(:num)',          'Jabatan::update/$1');
    $routes->post('delete/(:num)',          'Jabatan::delete/$1');
    $routes->add('import',                  'Jabatan::import');
});

$routes->group('acara', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'Acara::index');
    $routes->post('ajax_list',              'Acara::ajax_list');
    $routes->post('create',                 'Acara::create');
    $routes->post('update/(:num)',          'Acara::update/$1');
    $routes->post('delete/(:num)',          'Acara::delete/$1');
});

$routes->group('jabatan-unit', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'JabatanUnit::index');
    $routes->post('ajax_list',              'JabatanUnit::ajax_list');
    $routes->post('create',                 'JabatanUnit::create');
    $routes->post('update/(:num)',          'JabatanUnit::update/$1');
    $routes->post('delete/(:num)',          'JabatanUnit::delete/$1');
    $routes->add('import',                  'JabatanUnit::import');
});

$routes->group('jabatan-struktural', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'JabatanStruktural::index');
    $routes->post('ajax_list',              'JabatanStruktural::ajax_list');
    $routes->post('ajax_select2',           'JabatanStruktural::ajax_select2');
    $routes->post('create',                 'JabatanStruktural::create');
    $routes->post('update/(:num)',          'JabatanStruktural::update/$1');
    $routes->post('delete/(:num)',          'JabatanStruktural::delete/$1');
    $routes->add('import',                  'JabatanStruktural::import');
});

$routes->group('jabatan-struktural-unit', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'JabatanStrukturalUnit::index');
    $routes->post('ajax_list',              'JabatanStrukturalUnit::ajax_list');
    $routes->post('ajax_select2',           'JabatanStrukturalUnit::ajax_select2');
    $routes->post('create',                 'JabatanStrukturalUnit::create');
    $routes->post('update/(:num)',          'JabatanStrukturalUnit::update/$1');
    $routes->post('delete/(:num)',          'JabatanStrukturalUnit::delete/$1');
    $routes->add('import',                  'JabatanStrukturalUnit::import');
});

$routes->group('jabatan-fungsional', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'JabatanFungsional::index');
    $routes->post('ajax_list',              'JabatanFungsional::ajax_list');
    $routes->post('create',                 'JabatanFungsional::create');
    $routes->post('update/(:num)',          'JabatanFungsional::update/$1');
    $routes->post('delete/(:num)',          'JabatanFungsional::delete/$1');
    $routes->add('import',                  'JabatanFungsional::import');
});

$routes->group('jam-kerja', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'JamKerja::index');
    $routes->post('ajax_list',              'JamKerja::ajax_list');
    $routes->post('ajax_select2',           'JamKerja::ajax_select2');
    $routes->post('create',                 'JamKerja::create');
    $routes->post('update/(:num)',          'JamKerja::update/$1');
    $routes->post('delete/(:num)',          'JamKerja::delete/$1');
});

$routes->group('toleransi-terlambat', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'ToleransiTerlambat::index');
    $routes->post('ajax_list',              'ToleransiTerlambat::ajax_list');
    $routes->post('ajax_select2',           'ToleransiTerlambat::ajax_select2');
    $routes->post('create',                 'ToleransiTerlambat::create');
    $routes->post('update/(:num)',          'ToleransiTerlambat::update/$1');
    $routes->post('delete/(:num)',          'ToleransiTerlambat::delete/$1');
});

$routes->group('jadwal-kerja', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->post('ajax_list',              'JadwalKerja::ajax_list');
    $routes->post('ajax_select2',           'JadwalKerja::ajax_select2');
    $routes->get('/',                       'JadwalKerja::index');
    $routes->get('(:num)',                  'JadwalKerja::show/$1');
    $routes->post('create',                 'JadwalKerja::create');
    $routes->post('update/(:num)',          'JadwalKerja::update/$1');
    $routes->post('delete/(:num)',          'JadwalKerja::delete/$1');
});

$routes->group('jadwal-kerja-auto', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->post('ajax_list',              'JadwalKerjaAuto::ajax_list');
    $routes->post('ajax_select2',           'JadwalKerjaAuto::ajax_select2');
    $routes->get('/',                       'JadwalKerjaAuto::index');
    $routes->get('(:num)',                  'JadwalKerjaAuto::show/$1');
    $routes->post('create',                 'JadwalKerjaAuto::create');
    $routes->post('update/(:num)',          'JadwalKerjaAuto::update/$1');
    $routes->post('delete/(:num)',          'JadwalKerjaAuto::delete/$1');
});

$routes->group('jadwal-pegawai', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->post('ajax_list',              'JadwalPegawai::ajax_list');
    $routes->post('ajax_select2',           'JadwalPegawai::ajax_select2');
    $routes->get('/',                       'JadwalPegawai::index');
    $routes->post('create',                 'JadwalPegawai::create');
    $routes->post('update/(:num)',          'JadwalPegawai::update/$1');
    $routes->post('delete/(:num)',          'JadwalPegawai::delete/$1');
    $routes->add('import',                  'JadwalPegawai::import');
});

$routes->group('hari-libur', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->post('ajax_list',              'HariLibur::ajax_list');
    $routes->post('ajax_select2',           'HariLibur::ajax_select2');
    $routes->get('/',                       'HariLibur::index');
    $routes->post('create',                 'HariLibur::create');
    $routes->post('update/(:segment)',      'HariLibur::update/$1');
    $routes->post('delete/(:segment)',      'HariLibur::delete/$1');
    $routes->add('import',                  'HariLibur::import');
});

$routes->group('pegawai', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->post('ajax_list',              'Pegawai::ajax_list');
    $routes->post('ajax_select2',           'Pegawai::ajax_select2');
    $routes->get('/',                       'Pegawai::index');
    $routes->get('(:num)',                  'Pegawai::show/$1');
    $routes->get('new',                     'Pegawai::new');
    $routes->post('create',                 'Pegawai::create/$1');
    $routes->get('(:num)/edit',             'Pegawai::edit/$1');
    $routes->put('(:num)/update',           'Pegawai::update/$1');
    $routes->delete('(:num)/delete',        'Pegawai::delete/$1');
    $routes->add('import',                  'Pegawai::import');
    $routes->get('spreadsheet',             'Pegawai::spreadsheet');
});

$routes->group('pegawai', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    $routes->get('search-nik',              'Pegawai::searchNik');
});

$routes->group('pegawai-jabatan', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('ajax_list/(:num)',        'PegawaiJabatan::ajax_list/$1');
    $routes->post('create',                 'PegawaiJabatan::create');
    $routes->post('update/(:num)',          'PegawaiJabatan::update/$1');
    $routes->post('delete/(:num)',          'PegawaiJabatan::delete/$1');
});

$routes->group('pegawai-jabatan-fungsional', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('ajax_list/(:num)',        'PegawaiJabatanFungsional::ajax_list/$1');
    $routes->post('create',                 'PegawaiJabatanFungsional::create');
    $routes->post('update/(:num)',          'PegawaiJabatanFungsional::update/$1');
    $routes->post('delete/(:num)',          'PegawaiJabatanFungsional::delete/$1');
});

$routes->group('pegawai-jabatan-struktural-unit', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('ajax_list/(:num)',        'PegawaiJabatanStrukturalUnit::ajax_list/$1');
    $routes->post('create',                 'PegawaiJabatanStrukturalUnit::create');
    $routes->post('update/(:num)',          'PegawaiJabatanStrukturalUnit::update/$1');
    $routes->post('delete/(:num)',          'PegawaiJabatanStrukturalUnit::delete/$1');
});

$routes->group('pegawai-dokumen', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('ajax_list/(:num)',        'PegawaiDokumen::ajax_list/$1');
    $routes->post('create',                 'PegawaiDokumen::create');
    $routes->post('update/(:num)',          'PegawaiDokumen::update/$1');
    $routes->post('delete/(:num)',          'PegawaiDokumen::delete/$1');
});

$routes->group('verifikasi-form-presensi', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'VerifikasiFormPresensi::index');
    $routes->post('ajax_list',              'VerifikasiFormPresensi::ajax_list');
    $routes->post('create',                 'VerifikasiFormPresensi::create');
    $routes->post('update/(:num)',          'VerifikasiFormPresensi::update/$1');
    $routes->post('delete/(:num)',          'VerifikasiFormPresensi::delete/$1');
    $routes->add('import',                  'VerifikasiFormPresensi::import');
});

$routes->group('pertanyaan-kategori', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'PertanyaanKategori::index');
    $routes->post('ajax_list',              'PertanyaanKategori::ajax_list');
    $routes->post('ajax_select2',           'PertanyaanKategori::ajax_select2');
    $routes->post('create',                 'PertanyaanKategori::create');
    $routes->post('update/(:num)',          'PertanyaanKategori::update/$1');
    $routes->post('delete/(:num)',          'PertanyaanKategori::delete/$1');
    $routes->add('import',                  'PertanyaanKategori::import');
});

$routes->group('pertanyaan', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'Pertanyaan::index');
    $routes->post('ajax_list',              'Pertanyaan::ajax_list');
    $routes->post('create',                 'Pertanyaan::create');
    $routes->post('update/(:num)',          'Pertanyaan::update/$1');
    $routes->post('delete/(:num)',          'Pertanyaan::delete/$1');

    // $routes->add('import',                  'Pertanyaan::import');
});

$routes->group('setting-penilaian', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('ajax_list',               'SettingPenilaian::ajax_list');
    $routes->get('/',                       'SettingPenilaian::index');
    $routes->get('(:num)',                  'SettingPenilaian::show/$1');
    $routes->get('new',                     'SettingPenilaian::new');
    $routes->post('create',                 'SettingPenilaian::create/$1');
    $routes->get('(:num)/edit',             'SettingPenilaian::edit/$1');
    $routes->put('(:num)/update',           'SettingPenilaian::update/$1');
    $routes->delete('(:num)/delete',        'SettingPenilaian::delete/$1');
});

$routes->group('penilaian-rekap', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->post('ajax_list',              'PenilaianRekap::ajax_list');
    $routes->post('ajax_list_show/(:num)',  'PenilaianRekap::ajax_list_show/$1');
    $routes->get('/',                       'PenilaianRekap::index');
    $routes->get('(:num)',                  'PenilaianRekap::show/$1');
    $routes->get('(:num)/(:num)',           'PenilaianRekap::showList/$1/$2');
    $routes->get('print/(:segment)',        'PenilaianRekap::print/$1');
});

$routes->group('penilaian-rekap-recalculate', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('calc/(:num)',             'PenilaianRekapRecalculate::calc/$1');
});

$routes->group('penilaian-presensi', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('ajax_list',               'PenilaianPresensi::ajax_list');
    $routes->get('/',                       'PenilaianPresensi::index');
    $routes->post('update/(:num)',          'PenilaianPresensi::update/$1');
    $routes->add('import',                  'PenilaianPresensi::import');
});

$routes->group('penilaian', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                           'Penilaian::index');
    $routes->post('(:num)/upsert',              'Penilaian::upsert/$1');
    $routes->get('(:num)/(:segment)',           'Penilaian::show/$1/$2');
    $routes->get('new',                         'Penilaian::new');
    $routes->post('(:num)/(:segment)/menilai',  'Penilaian::menilai/$1/$2');
});

$routes->group('admin/presensi', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('ajax_list',               'Presensi::ajax_list');
    $routes->get('/',                       'Presensi::index');
    $routes->post('validate-data/(:num)',   'Presensi::validateDataPresensi/$1');

    $routes->get('new-masuk-pulang',        'Presensi::newMasukPulang');
    $routes->post('create-masuk-pulang',    'Presensi::createMasukPulang');
    $routes->get('new-izin',                'Presensi::newIzin');
    $routes->post('create-izin',            'Presensi::createIzin');
});

$routes->group('admin/presensi-piket', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('ajax_list',               'PresensiPiket::ajax_list');
    $routes->get('/',                       'PresensiPiket::index');
});

$routes->group('absensi', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('ajax_list',               'Absensi::ajax_list');
    $routes->get('/',                       'Absensi::index');
    $routes->post('create',                 'Absensi::create');
    $routes->post('update/(:segment)',      'Absensi::update/$1');
    $routes->post('delete/(:segment)',      'Absensi::delete/$1');
});

$routes->group('tugas-dinas', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('ajax_list',               'TugasDinas::ajax_list');
    $routes->get('/',                       'TugasDinas::index');
    $routes->post('create',                 'TugasDinas::create');
    $routes->post('update/(:segment)',      'TugasDinas::update/$1');
    $routes->post('delete/(:segment)',      'TugasDinas::delete/$1');
});

$routes->group('input-tugas-dinas', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->add('ajax_list',               'TugasDinas::ajax_list');
    $routes->get('/',                       'TugasDinas::index');
    $routes->post('create',                 'TugasDinas::create');
    $routes->post('update/(:segment)',      'TugasDinas::update/$1');
    $routes->post('delete/(:segment)',      'TugasDinas::delete/$1');
});

$routes->group('rekap-presensi-absensi', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'RekapPresensiAbsensi::index');
});

$routes->group('rekap-presensi-absensi-shift', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'RekapPresensiAbsensiShift::index');
});

$routes->group('rekap-presensi-piket', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'RekapPresensiPiket::index');
});

$routes->group('presensi-history', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->post('ajax_list',              'PresensiHistory::ajax_list');
    $routes->get('/',                       'PresensiHistory::index');
});

$routes->group('presensi', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->post('ajax_list',              'Presensi::ajax_list');
    $routes->get('/',                       'Presensi::index');
    $routes->get('data',                    'Presensi::data');
    $routes->post('create',                 'Presensi::create');
    $routes->get('scan',                    'Presensi::Scan');
    $routes->post('create-scan',            'Presensi::CreateScan');
});

$routes->group('presensi-piket', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->post('ajax_list',              'PresensiPiket::ajax_list');
    $routes->get('/',                       'PresensiPiket::index');
    $routes->get('data',                    'PresensiPiket::data');
    $routes->post('create',                 'PresensiPiket::create');
    $routes->get('scan',                    'PresensiPiket::Scan');
    $routes->post('create-scan',            'PresensiPiket::createScan');
});

$routes->group('presensi-acara', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->post('ajax_list',              'PresensiAcara::ajax_list');
    $routes->get('/',                       'PresensiAcara::index');
    $routes->get('data',                    'PresensiAcara::data');
    $routes->post('create',                 'PresensiAcara::create');
});

$routes->group('setting', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'Setting::index');
    $routes->post('ajax_list',              'Setting::ajax_list');
    $routes->post('update/(:num)',          'Setting::update/$1');
});

$routes->group('presensi-lupa-pengajuan', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'PresensiLupaPengajuan::index');
    $routes->post('ajax_list',              'PresensiLupaPengajuan::ajax_list');
    $routes->post('create',                 'PresensiLupaPengajuan::create');
    $routes->post('update/(:num)',          'PresensiLupaPengajuan::update/$1');
    $routes->post('delete/(:num)',          'PresensiLupaPengajuan::delete/$1');
});

$routes->group('presensi-lupa-validasi', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'PresensiLupaValidasi::index');
    $routes->post('ajax_list',              'PresensiLupaValidasi::ajax_list');
    $routes->post('create',                 'PresensiLupaValidasi::create');
    $routes->post('update/(:num)',          'PresensiLupaValidasi::update/$1');
    $routes->post('delete/(:num)',          'PresensiLupaValidasi::delete/$1');
});

$routes->group('presensi-izin-pengajuan', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'PresensiIzinPengajuan::index');
    $routes->post('ajax_list',              'PresensiIzinPengajuan::ajax_list');
    $routes->post('create',                 'PresensiIzinPengajuan::create');
    $routes->post('update/(:num)',          'PresensiIzinPengajuan::update/$1');
    $routes->post('delete/(:num)',          'PresensiIzinPengajuan::delete/$1');
});

$routes->group('presensi-izin-validasi', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login'], function ($routes) {
    $routes->get('/',                       'PresensiIzinValidasi::index');
    $routes->post('ajax_list',              'PresensiIzinValidasi::ajax_list');
    $routes->post('create',                 'PresensiIzinValidasi::create');
    $routes->post('update/(:num)',          'PresensiIzinValidasi::update/$1');
    $routes->post('delete/(:num)',          'PresensiIzinValidasi::delete/$1');
});

$routes->get('dokumen-sop', 'DokumenSop::index', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login']);
$routes->get('my-barcode', 'MyBarcode::index', ['namespace' => 'App\Controllers\Admin', 'filter' => 'login']);

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
