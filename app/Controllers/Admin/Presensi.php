<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PresensiModel;

class Presensi extends AdminBaseController
{
	protected $PresensiModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PresensiModel = new PresensiModel();

		$this->menuSlug = 'admin/presensi';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$pegawai = $this->db->table('users')->select('id_pegawai')->where('id', session('user_id'))->get()->getRow();
		$this->id_pegawai = $pegawai->id_pegawai ?? '';
	}

	public function ajax_list()
	{
		$this->PresensiModel->table = 'presensi_view';
		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->PresensiModel->get_datatables();
			$countAll = $this->PresensiModel->count_all();
			$countFiltered = $this->PresensiModel->count_filtered();

			$data = [];
			$no = $this->request->getPost("start");
			$konfigurasiPresensi = $this->db->table('konfigurasi_presensi')->get()->getRow();
			foreach ($lists as $list) {

				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama;
				if (!empty($list->photo) && file_exists("assets/img/presensi/{$list->photo}")) {
					$row[] = '<img src="' . base_url() . '/assets/img/presensi/' . $list->photo . '" alt="" class="img-thumb-list mr-2 mb-2 mb-md-0">';
				} else {
					$row[] = '<img src="' . base_url() . '/assets/img/presensi/default.jpg" alt="" class="img-thumb-list mr-2 mb-2 mb-md-0">';
				}
				$row[] = $list->tipe;
				$row[] = $list->waktu;

				// status
				$jamMasukPulang = explode(' - ', $list->jam_masuk_pulang);
				$jamIstirahat = explode(' - ', $list->jam_istirahat);
				if (!empty($jamMasukPulang[0]) || !empty($jamMasukPulang[1])) {
					$jamMasuk = date('H:i', \strtotime("$jamMasukPulang[0]"));
					$jamPulang = date('H:i', \strtotime($jamMasukPulang[1]));
					$jamIstirahatMulai = date('H:i', \strtotime("$jamIstirahat[0]"));
					$jamIstirahatSelesai = date('H:i', \strtotime($jamIstirahat[1]));
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
						$presenceToday = $this->db->table('presensi')->where('id_pegawai', $list->id_pegawai)->where('tipe', 'Masuk')->where('diffday_code', $list->diffday_code)->like('waktu',  date('Y-m-d', \strtotime($list->waktu)), 'after')->get()->getRow();
						if ($presenceToday) {
							$jamPresensiMasuk = \date('H:i', \strtotime($presenceToday->waktu));
							$jamMasuk = \date('H:i', \strtotime($presenceToday->waktu));
						} else {
							$presenceYesterday = $this->db->table('presensi')->where('id_pegawai', $list->id_pegawai)->where('tipe', 'Masuk')->where('diffday_code', $list->diffday_code)->like('waktu',  date('Y-m-d', \strtotime($list->waktu . ' -1 DAY')), 'after')->get()->getRow();
							$jamPresensiMasuk = \date('H:i', \strtotime($presenceYesterday->waktu));
							$jamMasuk = \date('H:i:s', \strtotime($presenceYesterday->waktu));
						}
						$jamPulang =  date('H:i:s', \strtotime($list->waktu));
						$durasiKerja = \timeDifference($jamMasuk, $jamPulang);
						$durasiIstirahat = \timeDifference($jamIstirahatMulai, $jamIstirahatSelesai);
						if ($jamPulang > $jamIstirahatSelesai && \date('Y-m-d', \strtotime($list->waktu)) > $konfigurasiPresensi->tanggal_mulai_durasi_jam_kerja_dikurangi_istirahat) {
							$durasiKerja -= $durasiIstirahat;
						}
						$durasiKerja = convertToHoursMins($durasiKerja);
						if ($durasiKerja != '00:00') {
							$durasiKerja = explode(':', $durasiKerja);
							$durasiKerjaJam =  ltrim($durasiKerja[0], '0') . ' Jam';
							$durasiKerjaMenit =  ltrim($durasiKerja[1], '0') . ' Menit';
							$durasiKerja = "$durasiKerjaJam $durasiKerjaMenit";
						} else {
							$durasiKerja = '0 Menit';
						}
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

				$row[] = $list->alasan_pulang_cepat ?? '-';

				// action
				$row[] = '
				<a href="javascript:void(0)" class="btn btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Validasi" onclick="validateData(' . "'" . $list->id . "'" . ')">
					<i class="fas fa-user-check"></i>
				</a>
			';

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
			'title' => 'Presensi',
		];

		return $this->view('pages/admin/presensi/index', $data);
	}

	public function validateDataPresensi($id)
	{
		$dataDb = $this->PresensiModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		if (!$_POST) {
			// repoulate form
			$input = $dataDb;
			echo json_encode($input);
			return;
		}

		if (!$this->__validate($this->PresensiModel->rulesValidate())) {
			return;
		}
		// set input data
		$input = (object) $this->request->getPost();

		// save data
		$data = [
			'validitas' => $input->validitas,
		];
		$this->db->table('presensi')->where('id', $id)->update($data);

		// send response
		if ($this->db->affectedRows() || $this->db->affectedRows() == 0) {
			$response = [
				'status' => 200,
				'message' => 'Status berhasil diubah',
				'data' => $input,
			];
		} else {
			$response = [
				'status' => 500,
				'message' => 'Oops terjadi kesalahan',
			];
		}

		echo json_encode($response);
		return;
	}

	private function __validate($rules = null)
	{
		if ($rules == null) {
			$rules = $this->PresensiModel->getValidationRules();
		}

		// validate and set error message
		if (!$this->validate($rules)) {
			$validation = \Config\Services::validation();
			$response = [
				'status' => false,
				'message' => 'Error form validation',
				'data' =>  [
					'errors' => $validation->getErrors(),
				],
			];
			echo json_encode($response);
			return false;
		}
		return true;
	}

	private function __checkDataExist($data)
	{
		if (!$data) {
			$response = [
				'status' => false,
				'message' => 'Maaf! Data tidak ditemukan',
			];
			echo json_encode($response);
			return false;
		}
		return true;
	}

	//--------------------------------------------------------------------
}
