<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PresensiHistoryModel;

class PresensiHistory extends AdminBaseController
{
	protected $PresensiHistoryModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PresensiHistoryModel = new PresensiHistoryModel();
		$pegawai = $this->db->table('users')->select('id_pegawai')->where('id', session('user_id'))->get()->getRow();
		$this->id_pegawai = $pegawai->id_pegawai ?? '';
	}

	public function ajax_list()
	{
		$this->PresensiHistoryModel->table = 'presensi_view';
		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->PresensiHistoryModel->get_datatables(['id_pegawai' => $this->id_pegawai]);
			$countAll = $this->PresensiHistoryModel->count_all(['id_pegawai' => $this->id_pegawai]);
			$countFiltered = $this->PresensiHistoryModel->count_filtered(['id_pegawai' => $this->id_pegawai]);

			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {

				$no++;
				$row = [];
				$row[] = $no;
				if (!empty($list->photo) && file_exists("assets/img/presensi/{$list->photo}")) {
					$row[] = '<img src="' . base_url() . '/assets/img/presensi/' . $list->photo . '" alt="" class="img-thumb-list mr-2 mb-2 mb-md-0">';
				} else {
					$row[] = '<img src="' . base_url() . '/assets/img/presensi/default.jpg" alt="" class="img-thumb-list mr-2 mb-2 mb-md-0">';
				}
				$row[] = $list->tipe;
				$row[] = $list->waktu;

				// status
				$jamMasukPulang = explode(' - ', $list->jam_masuk_pulang);
				if (!empty($jamMasukPulang[0]) || !empty($jamMasukPulang[1])) {
					$jamMasuk = date('H:i', \strtotime("$jamMasukPulang[0]"));
					$jamPulang = date('H:i', \strtotime($jamMasukPulang[1]));
					$date = \date('Y-m-d', \strtotime($list->waktu));
					$toleransiTerlambat = $this->db->query("SELECT * FROM `toleransi_terlambat` WHERE '$date' BETWEEN tanggal_mulai AND CASE WHEN tanggal_selesai IS NULL THEN CURDATE() + INTERVAL 1 YEAR ELSE tanggal_selesai END")->getRow();
					$durasiToleransi = $toleransiTerlambat->durasi_toleransi ?? '00:00:00';
					sscanf($durasiToleransi, "%d:%d:%d", $hours, $minutes, $seconds);
					$durasiToleransi = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
					$jamMasukToleransi = date('H:i', \strtotime("$jamMasukPulang[0] +$durasiToleransi Seconds"));

					if ($list->tipe == 'Masuk') {
						$jamPresensiMasuk = date('H:i', \strtotime($list->waktu));
						if ($jamPresensiMasuk >= $jamMasuk && $jamPresensiMasuk < $jamMasukToleransi) {
							$row[] = 'Masuk Tepat Waktu';
						} else if (($jamPresensiMasuk < $jamMasuk)) {
							$row[] = '<img src="' . base_url() . '/assets/img/medal.png" alt="" class="mr-2 mb-2 mb-md-0" style="width:24px"> Masuk Lebih Awal';
						} else {
							$interval = \datetimeDifference($jamMasukToleransi, $jamPresensiMasuk);
							$row[] = "Terlambat $interval";
						}
					}
					if ($list->tipe == 'Pulang') {
						$presenceToday = $this->db->table('presensi')->where('id_pegawai', $list->id_pegawai)->where('tipe', 'Masuk')->like('waktu',  date('Y-m-d', \strtotime($list->waktu)), 'after')->get()->getRow();
						if ($presenceToday) {
							$jamPresensiMasuk = \date('H:i', \strtotime($presenceToday->waktu));
							$datetimeMasuk = \date('Y-m-d H:i:s', \strtotime($presenceToday->waktu));
						} else {
							$presenceYesterday = $this->db->table('presensi')->where('id_pegawai', $list->id_pegawai)->where('tipe', 'Masuk')->like('waktu',  date('Y-m-d', \strtotime($list->waktu . ' -1 DAY')), 'after')->get()->getRow();
							$jamPresensiMasuk = \date('H:i', \strtotime($presenceYesterday->waktu));
							$datetimeMasuk = \date('Y-m-d H:i:s', \strtotime($presenceYesterday->waktu));
						}
						$datetimePulang =  date('Y-m-d H:i:s', \strtotime($list->waktu));
						$durasiKerja = \datetimeDifference($datetimeMasuk, $datetimePulang);
						$row[] = "Durasi Kerja $durasiKerja";
					}
				} else {
					$row[] = '-';
				}

				// lokasi - distance in km
				$distance1 = getDistance(-7.15111, 110.40805, $list->coord_latitude, $list->coord_longitude);
				$distance2 = getDistance(-7.15173, 110.40726, $list->coord_latitude, $list->coord_longitude);
				$distance3 = getDistance(-7.15263, 110.40709, $list->coord_latitude, $list->coord_longitude);
				$distance4 = getDistance(-7.154620, 110.407700, $list->coord_latitude, $list->coord_longitude); //farmasi
				if (($distance1 > 0.13 && $distance2 > 0.14 && $distance3 > 0.076) && $distance4 > 0.1) {
					$row[] = "Luar UNW";
				} else {
					$row[] = "UNW";
				}
				
				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $countAll,
				"recordsFiltered" => $countFiltered,
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Riwayat Presensi',
		];

		return $this->view('pages/admin/presensi-history/index', $data);
	}

	//--------------------------------------------------------------------
}
