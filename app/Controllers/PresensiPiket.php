<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PresensiPiketModel;
use DateTime;

class PresensiPiket extends BaseController
{
	protected $PresensiPiketModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PresensiPiketModel = new PresensiPiketModel();
		if (\logged_in()) {
			$user = $this->db->table('users')->select('id_pegawai')->select('id, id_pegawai')->where('id', session('user_id'))->get()->getRow();
			$unitPiket = unitPiket($user->id_pegawai);
			if (!$unitPiket && !checkGroupUser(1)) {
				throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
			}
		}
	}

	public function ajax_list()
	{
		$this->PresensiPiketModel->table = 'presensi_piket_view';
		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->PresensiPiketModel->get_datatables();
			$countAll = $this->PresensiPiketModel->count_all();
			$countFiltered = $this->PresensiPiketModel->count_filtered();

			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {

				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama;
				if (!empty($list->photo) && file_exists("assets/img/presensi-piket/{$list->photo}")) {
					$row[] = '<img src="' . base_url() . '/assets/img/presensi-piket/' . $list->photo . '" alt="" class="img-thumb-list mr-2 mb-2 mb-md-0">';
				} else {
					$row[] = '<img src="' . base_url() . '/assets/img/presensi-piket/default.jpg" alt="" class="img-thumb-list mr-2 mb-2 mb-md-0">';
				}
				$row[] = $list->tipe;
				$row[] = $list->waktu;

				// lokasi - distance in km
				if ($list->coord_latitude && $list->coord_longitude) {
					$distance1 = getDistance(-7.15111, 110.40805, $list->coord_latitude, $list->coord_longitude);
					$distance2 = getDistance(-7.15173, 110.40726, $list->coord_latitude, $list->coord_longitude);
					$distance3 = getDistance(-7.15263, 110.40709, $list->coord_latitude, $list->coord_longitude);
					$distance4 = getDistance(-7.154620, 110.407700, $list->coord_latitude, $list->coord_longitude); //farmasi
					if (($distance1 > 0.13 && $distance2 > 0.14 && $distance3 > 0.076) && $distance4 > 0.1) {
						$row[] = "Luar UNW";
					} else {
						$row[] = "UNW";
					}
				} else {
					$row[] = '';
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
			'title'     => 'Absen',
			'validation' => \Config\Services::validation(),
		];
		return $this->view('pages/presensi-piket/index', $data);
	}

	public function data()
	{
		$data = [
			'title'     => 'Data Presensi Piket',
		];

		return $this->view('pages/presensi-piket/data', $data);
	}

	public function scan()
	{
		$data = [
			'title'     => 'Scan',
			'validation' => \Config\Services::validation(),
		];
		return $this->view('pages/presensi-piket/scan', $data);
	}

	public function create()
	{
		// set input data
		$input = (object) $this->request->getPost();
		$date = date('Y-m-d');
		$today = date('N');
		$now = \date('Y-m-d H:i:s');
		// $date = date('Y-m-d', \strtotime('2023-01-22'));
		// $today = date('N', \strtotime('2023-01-22'));
		// $now = '2023-01-22 08:45:19';

		// validate and set error message
		if (!$this->validate($this->PresensiPiketModel->rulesPresensiPiket())) {
			$response = [
				'status' => 400,
				'message' => 'Photo harus dicapture terlebih dahulu',
			];
			echo json_encode($response);
			return;
		}

		// cek diluar unw
		$distance1 = getDistance(-7.15111, 110.40805, $input->coord_latitude, $input->coord_longitude);
		$distance2 = getDistance(-7.15173, 110.40726, $input->coord_latitude, $input->coord_longitude);
		$distance3 = getDistance(-7.15263, 110.40709, $input->coord_latitude, $input->coord_longitude);
		$distance4 = getDistance(-7.154620, 110.407700, $input->coord_latitude, $input->coord_longitude); //farmasi
		if (($distance1 > 0.13 && $distance2 > 0.14 && $distance3 > 0.076) && $distance4 > 0.1) {
			$response = [
				'status' => 400,
				'message' => 'Gagal, Lokasi perangkat anda terdeteksi di luar UNW',
			];
			echo json_encode($response);
			return;
		}

		$pegawai = $this->db->table('pegawai')->select('id')->where('nik', $input->nik)->get()->getRow();
		// cek jadwal pegawai
		$jadwalPegawai = $this->db->table('jadwal_pegawai')->select('id_jadwal_kerja')->where('id_pegawai', $pegawai->id)->get()->getRow();
		$jadwalAutoPegawai = $this->db->table('jadwal_auto_pegawai')->select('id')->where('id_pegawai', $pegawai->id)->get()->getRow();
		if (!$jadwalPegawai && !$jadwalAutoPegawai) {
			$response = [
				'status' => 400,
				'message' => 'Jadwal kerja pegawai belum ditetapkan',
			];
			echo json_encode($response);
			return;
		}

		// cek is piket
		$unitPiket = unitPiket($pegawai->id);
		if (!$unitPiket) {
			$response = [
				'status' => 400,
				'message' => 'Unit/Department anda tidak ada piket',
			];
			echo json_encode($response);
			return;
		}

		// cek hari libur tapi bisa piket
		$pegawaiJabatanUnit = $this->db->table('pegawai_jabatan_u_view')->select('id_unit, nama_jabatan')->where('id_pegawai', $pegawai->id)->get()->getRow();
		$pegawaiJabatanStrukturalUnit = $this->db->table('pegawai_jabatan_struktural_u_view')->select('id_unit')->where('id_pegawai', $pegawai->id)->get()->getRow();
		$hariLibur = $this->db->table('hari_libur')->where('tanggal', $date)->get()->getRow();
		if ($hariLibur && $hariLibur->id_unit_piket) {
			$hariLiburUnitPiket = explode(';', $hariLibur->id_unit_piket);
			if (($pegawaiJabatanUnit && !in_array($pegawaiJabatanUnit->id_unit,  $hariLiburUnitPiket)) || ($pegawaiJabatanStrukturalUnit && !in_array($pegawaiJabatanStrukturalUnit->id_unit,  $hariLiburUnitPiket))) {
				$response = [
					'status' => 400,
					'message' => 'Gagal, hari ini bukan hari piket',
				];
				echo json_encode($response);
				return;
			}
		} else {
			// cek hari piket
			if (!in_array($today, \explode(';', $unitPiket->day))) {
				$response = [
					'status' => 400,
					'message' => 'Gagal, hari ini bukan hari piket',
				];
				echo json_encode($response);
				return;
			}
		}

		// cek sudah absen masuk/pulang hari ini
		$checkPresensiPiket = $this->db->table('presensi_piket')->select('id')->where(['id_pegawai' => $pegawai->id, 'tipe' => $input->tipe])->like('waktu', $date)->get()->getRow();
		if ($checkPresensiPiket) {
			$response = [
				'status' => 400,
				'message' => "Gagal, presensi $input->tipe sudah diisi hari ini",
			];
			echo json_encode($response);
			return;
		}
		
		// cek absen masuk lebih dari 14:30
		if ($pegawaiJabatanUnit && $pegawaiJabatanUnit->nama_jabatan == 'Tendik' && $input->tipe == 'Masuk' && date("H:i") > '14:30') {
			$response = [
				'status' => 400,
				'message' => "Gagal, presensi masuk hanya bisa dilakukan sebelum 14:30",
			];
			echo json_encode($response);
			return;
		}

		// cek absen pulang tapi belum masuk
		if ($input->tipe == 'Pulang') {
			$checkPresensiPiket = $this->db->table('presensi_piket')->select('id')->where(['id_pegawai' => $pegawai->id, 'tipe' => 'Masuk'])->like('waktu', $date)->get()->getRow();
			if (!$checkPresensiPiket) {
				$response = [
					'status' => 400,
					'message' => "Gagal, anda belum melakukan presensi masuk",
				];
				echo json_encode($response);
				return;
			}
		}

		// set file
		$base64 = base64_decode($input->photo);
		$fileName = date('YmdHi') . "-id-$pegawai->id-" . uniqid() . '.png';
		file_put_contents($fileName, $base64); // placed iapn public folder
		// $photo = new \CodeIgniter\Files\File(ROOTPATH . "/public/$file");

		// save data
		$data = [
			'id_pegawai' => $pegawai->id,
			'photo' => $fileName,
			'tipe' => $input->tipe,
			'coord_latitude' => $input->coord_latitude,
			'coord_longitude' => $input->coord_longitude,
			'waktu' => $now,
		];
		$this->db->table('presensi_piket')->insert($data);

		// move file and create new folder if not exist
		if (!file_exists('assets/img/presensi-piket')) {
			mkdir('assets/img/presensi-piket', 755);
		}

		// fit image
		if (\file_exists($fileName)) {
			\Config\Services::image()
				->withFile($fileName)
				->resize(300, 300, 'center')
				->save("assets/img/presensi-piket/{$fileName}");
			\unlink("$fileName");
		}

		$response = [
			'status' => 200,
			'message' => "Berhasil menyimpan presensi $input->tipe",
			'data' =>  $data,
		];
		echo json_encode($response);
		return;
	}

	public function createScan()
	{
		// set input data
		$input = (object) $this->request->getPost();
		$date = date('Y-m-d');
		$today = date('N');
		$now = \date('Y-m-d H:i:s');
		// $date = date('Y-m-d', \strtotime('2022-12-18'));
		// $today = date('N', \strtotime('2022-12-18'));
		// $now = '2022-12-18 08:45:19';

		// validate and set error message
		if (!$this->validate($this->PresensiPiketModel->rulesPresensiScan())) {
			$response = [
				'status' => 400,
				'message' => 'NIK Invalid',
			];
			echo json_encode($response);
			return;
		}

		$pegawai = $this->db->table('pegawai')->select('id')->where('nik', $input->nik)->get()->getRow();
		// cek jadwal pegawai
		$jadwalPegawai = $this->db->table('jadwal_pegawai')->select('id_jadwal_kerja')->where('id_pegawai', $pegawai->id)->get()->getRow();
		$jadwalAutoPegawai = $this->db->table('jadwal_auto_pegawai')->select('id')->where('id_pegawai', $pegawai->id)->get()->getRow();
		if (!$jadwalPegawai && !$jadwalAutoPegawai) {
			$response = [
				'status' => 400,
				'message' => 'Jadwal kerja pegawai belum ditetapkan',
			];
			echo json_encode($response);
			return;
		}

		// cek is piket
		$unitPiket = unitPiket($pegawai->id);
		if (!$unitPiket) {
			$response = [
				'status' => 400,
				'message' => 'Unit/Department anda tidak ada piket',
			];
			echo json_encode($response);
			return;
		}

		// cek hari libur tapi bisa piket
		$pegawaiJabatanUnit = $this->db->table('pegawai_jabatan_u_view')->select('id_unit, nama_jabatan')->where('id_pegawai', $pegawai->id)->get()->getRow();
		$pegawaiJabatanStrukturalUnit = $this->db->table('pegawai_jabatan_struktural_u_view')->select('id_unit')->where('id_pegawai', $pegawai->id)->get()->getRow();
		$hariLibur = $this->db->table('hari_libur')->where('tanggal', $date)->get()->getRow();
		if ($hariLibur && $hariLibur->id_unit_piket) {
			$hariLiburUnitPiket = explode(';', $hariLibur->id_unit_piket);
			if (($pegawaiJabatanUnit && !in_array($pegawaiJabatanUnit->id_unit,  $hariLiburUnitPiket)) || ($pegawaiJabatanStrukturalUnit && !in_array($pegawaiJabatanStrukturalUnit->id_unit,  $hariLiburUnitPiket))) {
				$response = [
					'status' => 400,
					'message' => 'Gagal, hari ini bukan hari piket',
				];
				echo json_encode($response);
				return;
			}
		} else {
			// cek hari piket
			if (!in_array($today, \explode(';', $unitPiket->day))) {
				$response = [
					'status' => 400,
					'message' => 'Gagal, hari ini bukan hari piket',
				];
				echo json_encode($response);
				return;
			}
		}

		// cek sudah absen masuk dan pulang
		$checkPresensi = $this->db->table('presensi_piket')->where(['id_pegawai' => $pegawai->id, 'tipe' => 'Pulang'])->like('waktu', $date)->get()->getRow();
		if ($checkPresensi) {
			$response = [
				'status' => 400,
				'message' => 'Gagal, Sudah absen masuk dan pulang',
			];
			echo json_encode($response);
			return;
		}

		//  set tipe absen
		$checkPresensiPiket = $this->db->table('presensi_piket')->where('id_pegawai', $pegawai->id)->like('waktu', $date)->get()->getRow();
		if (!$checkPresensiPiket) {
			$tipe = 'Masuk';
		} else {
			$tipe = 'Pulang';
		}
		
		// cek absen masuk lebih dari 14:30
		if ($pegawaiJabatanUnit && $pegawaiJabatanUnit->nama_jabatan == 'Tendik' && $input->tipe == 'Masuk' && date("G:i") > '14:30') {
			$response = [
				'status' => 400,
				'message' => "Gagal, presensi masuk hanya bisa dilakukan sebelum 14:30",
			];
			echo json_encode($response);
			return;
		}

		// set file
		$base64 = base64_decode($input->photo);
		$fileName = date('YmdHi') . "-id-$pegawai->id-" . uniqid() . '.png';
		file_put_contents($fileName, $base64); // placed iapn public folder
		// $photo = new \CodeIgniter\Files\File(ROOTPATH . "/public/$file");

		// save data
		$data = [
			'id_pegawai' => $pegawai->id,
			'photo' => $fileName,
			'tipe' => $tipe,
			'coord_latitude' => '',
			'coord_longitude' => '',
			'waktu' => $now,
		];
		$this->db->table('presensi_piket')->insert($data);

		// move file and create new folder if not exist
		if (!file_exists('assets/img/presensi-piket')) {
			mkdir('assets/img/presensi-piket', 755);
		}

		// fit image
		if (\file_exists($fileName)) {
			\Config\Services::image()
				->withFile($fileName)
				->resize(300, 300, 'center')
				->save("assets/img/presensi-piket/{$fileName}");
			\unlink("$fileName");
		}

		$response = [
			'status' => 200,
			'message' => "Berhasil menyimpan presensi $tipe",
			'data' =>  $data,
		];
		echo json_encode($response);
		return;
	}

	//--------------------------------------------------------------------

}
