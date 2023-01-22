<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PresensiModel;
use DateTime;

class Presensi extends BaseController
{
	protected $PresensiModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PresensiModel = new PresensiModel();
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
							$interval = \dateTimeDifference($jamMasukToleransi, $jamPresensiMasuk);
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

				// validitas
				$row[] = $list->validitas;

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
			'optionsShift' => $this->db->table('jadwal_kerja_auto_detail_view')->get()->getResult(),
			'validation' => \Config\Services::validation(),
		];

		if (\logged_in()) {
			$user = $this->db->table('users')->select('id_pegawai')->select('id, id_pegawai')->where('id', session('user_id'))->get()->getRow();
			$pegawai = '';
			if ($user) {
				$pegawai = $this->db->table('pegawai')->select('id, nik, nama')->where('id', $user->id_pegawai)->get()->getRow();
				$pegawai = $pegawai ?? '';
				if ($pegawai) $data['jabatanUser'] = $this->db->table('pegawai_jabatan_u_view')->select('nama_jabatan')->where('id_pegawai', $pegawai->id)->get()->getRow();
			}
		}

		// 		$distance1 = getDistance(-7.15111, 110.40805, -7.1538644, 110.4073188);
		// 		$distance2 = getDistance(-7.15173, 110.40726, -7.1538644, 110.4073188);
		// 		$distance3 = getDistance(-7.15263, 110.40709, -7.1538644, 110.4073188);
		// 		$distance4 = getDistance(-7.154620, 110.407700, -7.1538644, 110.4073188); //farmasi
		//     	var_dump($distance1);
		//     	var_dump($distance2);
		//     	var_dump($distance3);
		//     	var_dump($distance4);
		//     	var_dump($distance1 > 0.13 && $distance2 > 0.14 && $distance3);
		//     	var_dump($distance4 > 0.09);
		//     	var_dump(($distance1 > 0.13 && $distance2 > 0.14 && $distance3 > 0.076) && $distance4 > 0.09);
		// 		die;
		return $this->view('pages/presensi/index', $data);
	}

	public function data()
	{
		$data = [
			'title'     => 'Data Presensi',
		];

		return $this->view('pages/presensi/data', $data);
	}


	public function scan()
	{
		$data = [
			'title'     => 'Scan',
			'optionsShift' => $this->db->table('jadwal_kerja_auto_detail_view')->get()->getResult(),
			'validation' => \Config\Services::validation(),
		];
		return $this->view('pages/presensi/scan', $data);
	}



	public function create()
	{
		// set input data
		$input = (object) $this->request->getPost();
		$diffdayCode = '';
		$date = date('Y-m-d');
		$today = date('N');
		$now = \date('Y-m-d H:i:s');
		// $date = date('Y-m-d', \strtotime('2023-01-09'));
		// $today = date('N', \strtotime('2023-01-09'));
		// $now = '2023-01-09 06:45:19';

		// validate and set error message
		if (!$this->validate($this->PresensiModel->rulesPresensi())) {
			$response = [
				'status' => 400,
				'message' => 'Photo harus dicapture terlebih dahulu',
			];
			echo json_encode($response);
			return;
		}

		$pegawai = $this->db->table('pegawai')->select('id')->where('nik', $input->nik)->get()->getRow();
		$jabatanUser = $this->db->table('pegawai_jabatan_u_view')->select('id_pegawai, nama_unit, nama_jabatan')->where('id_pegawai', $pegawai->id)->get()->getRow();

		// cek diluar unw
		if ($jabatanUser && $jabatanUser->nama_unit == 'Satpam') {
			$distance1 = getDistance(-7.152411, 110.407593, $input->coord_latitude, $input->coord_longitude);
			if ($distance1 > 0.030) {
				$response = [
					'status' => 400,
					'message' => 'Gagal, Lokasi presensi unit satpam harus berada di pos Garuda',
				];
				echo json_encode($response);
				return;
			}
		} else {
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
		}

		// cek jadwal pegawai
		$jadwalPegawai = $this->db->table('jadwal_pegawai')->where('id_pegawai', $pegawai->id)->get()->getRow();
		$jadwalAutoPegawai = $this->db->table('jadwal_auto_pegawai')->where('id_pegawai', $pegawai->id)->get()->getRow();
		if (!$jadwalPegawai && !$jadwalAutoPegawai) {
			$response = [
				'status' => 400,
				'message' => 'Jadwal kerja pegawai belum ditetapkan',
			];
			echo json_encode($response);
			return;
		}

		if ($jadwalPegawai) {
			// cek hari ini adalah jadwal masuk pegawai
			$cekJadwal = $this->db->table('jadwal_kerja_detail')->where('id_jadwal_kerja', $jadwalPegawai->id_jadwal_kerja)->where('day', $today)->get()->getRow();
			if ($cekJadwal->libur == 1) {
				$response = [
					'status' => 400,
					'message' => 'Gagal, presensi tidak bisa dilakukan dihari libur',
				];
				echo json_encode($response);
				return;
			}
			// cek hari ini tanggal merah / hari libur
			$cekHariLibur = $this->db->table('hari_libur')->where('tanggal', $date)->get()->getRow();
			if ($cekHariLibur) {
				$response = [
					'status' => 400,
					'message' => 'Gagal, presensi tidak bisa dilakukan dihari libur',
				];
				echo json_encode($response);
				return;
			}
		}

		// cek durasi absen pulang untuk dosen saja
		if ($input->tipe == 'Pulang') {
			if ($jabatanUser && $jabatanUser->nama_jabatan == 'Dosen') {
				$konfigurasiPresensi = $this->db->table('konfigurasi_presensi')->get()->getRow();
				if ($date >= $konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali) {
					$response = [
						'status' => 400,
						'message' => 'Dosen hanya perlu melakukan presensi masuk saja',
					];
					echo json_encode($response);
					return;
				} else {
					$presensi = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'tipe' => 'Masuk'])->like('waktu', $date)->get()->getRow();
					$jamMasukPulang = explode(' - ', $presensi->jam_masuk_pulang);
					$jamMasuk = date('H:i', \strtotime("$jamMasukPulang[0]"));
					$jamPulang = date('H:i', \strtotime($jamMasukPulang[1]));

					$jamPresensiMasuk =  date('H:i', \strtotime("$presensi->waktu"));;
					$jamPresensiPulang = date('H:i', \strtotime($now));

					$jadwalJamDiff =  abs(\timeDifference($jamMasuk, $jamPulang));
					$jamDiff = \timeDifference($jamPresensiMasuk, $jamPresensiPulang);
					$durasiKurang = $jadwalJamDiff - $jamDiff;
					if ($durasiKurang > 0) {
						$durasiKurang = convertToHoursMins($durasiKurang);
						$durasiKurang = explode(':', $durasiKurang);
						$durasiKurangJam = $durasiKurang[0] != '00' ? ltrim($durasiKurang[0], '0') . ' Jam' : '';

						$durasiKurangMenit = $durasiKurang[1] != '00' ? ltrim($durasiKurang[1], '0') . ' Menit' : '';
						$durasiKurang = "$durasiKurangJam $durasiKurangMenit";

						if ($jamDiff < $jadwalJamDiff && \date('H:i', \strtotime($now)) < $jamPulang && ($input->alasan_pulang_cepat == '' || ($input->alasan_pulang_cepat == 'Lainnya' && $input->alasan_pulang_cepat_lainnya == ''))) {
							$response = [
								'status' => 300,
								'message' => 'Alasan pulang cepat harus diisi',
								'data' =>  $input,
								'durasiKurang' => $durasiKurang,
							];
							echo json_encode($response);
							return;
						}
					}
				}
			}
		}

		// set file
		$base64 = base64_decode($input->photo);
		$fileName = date('YmdHi') . "-id-$pegawai->id-" . uniqid() . '.png';
		file_put_contents($fileName, $base64); // placed iapn public folder
		// $photo = new \CodeIgniter\Files\File(ROOTPATH . "/public/$file");

		// save data
		if ($jadwalPegawai) {
			// cek absen pulang tapi belum masuk
			if ($input->tipe == 'Pulang') {
				$checkPresensi = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'tipe' => 'Masuk'])->like('waktu', $date)->get()->getRow();
				if (!$checkPresensi) {
					$response = [
						'status' => 400,
						'message' => "Gagal, anda belum melakukan presensi masuk",
					];
					echo json_encode($response);
					return;
				}
			}
			// cek sudah absen masuk/pulang hari ini
			$checkPresensi = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'tipe' => $input->tipe])->like('waktu', $date)->get()->getRow();
			if ($checkPresensi) {
				$response = [
					'status' => 400,
					'message' => "Gagal, presensi $input->tipe sudah diisi hari ini",
				];
				echo json_encode($response);
				return;
			}

			$jadwalKerja = $this->db->table('jadwal_kerja')->where('id', $jadwalPegawai->id_jadwal_kerja)->get()->getRow();
			$jadwalKerjaDetail = $this->db->table('jadwal_kerja_detail_view')->where('id_jadwal_kerja', $jadwalKerja->id)->where('day', $today)->get()->getRow();
			if (isset($input->shift) && !empty($input->shift)) {
				$response = [
					'status' => 400,
					'message' => "Anda tidak mempunyai shift silahkan kosongkan pilihan shift",
				];
				echo json_encode($response);
				return;
			}
		}
		if ($jadwalAutoPegawai) {
			// cek user belum memilih shift
			if (isset($input->shift) && empty($input->shift)) {
				$response = [
					'status' => 400,
					'message' => "Silakan pilih shift terlebih dahulu",
				];
				echo json_encode($response);
				return;
			}

			// cek user mempunyai shift yang dipilih
			$jadwalKerja = $this->db->table('jadwal_kerja_auto')->where('id', $jadwalAutoPegawai->id_jadwal_kerja_auto)->get()->getRow();
			$jadwalKerjaDetail = $this->db->table('jadwal_kerja_auto_detail_view')->where('id', $input->shift)->where('id_jadwal_kerja_auto', $jadwalAutoPegawai->id_jadwal_kerja_auto)->get()->getRow();
			if (!$jadwalKerjaDetail) {
				$response = [
					'status' => 400,
					'message' => "Anda tidak punya shift tersebut silakan ganti shift",
				];
				echo json_encode($response);
				return;
			}

			if ($jadwalKerjaDetail->is_diffday == 0) {
				// cek absen pulang tapi belum masuk
				if ($input->tipe == 'Pulang') {
					$checkPresensi = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'tipe' => 'Masuk', 'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang"])->like('waktu', $date)->get()->getRow();
					if (!$checkPresensi) {
						$response = [
							'status' => 400,
							'message' => "Gagal, anda belum melakukan presensi masuk",
						];
						echo json_encode($response);
						return;
					}
				}
				// cek sudah absen masuk/pulang hari ini
				$checkPresensi = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'tipe' => $input->tipe, 'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang"])->like('waktu', $date)->get()->getRow();
				if ($checkPresensi) {
					$response = [
						'status' => 400,
						'message' => "Gagal, presensi $input->tipe sudah diisi hari ini",
					];
					echo json_encode($response);
					return;
				}
			} else {
				// cek absen pulang tapi belum masuk
				if ($input->tipe == 'Pulang') {
					$checkPresensi = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'tipe' => 'Masuk', 'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang"])->like('waktu', $date)->get()->getRow();
					$checkPresensiYesterday = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'tipe' => 'Masuk', 'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang"])->like('waktu', date('Y-m-d', \strtotime($date . ' -1 DAY')))->get()->getRow();
					if (!$checkPresensi && !$checkPresensiYesterday) {
						$response = [
							'status' => 400,
							'message' => "Gagal, anda belum melakukan presensi masuk",
						];
						echo json_encode($response);
						return;
					}
					if ($checkPresensi) {
						$diffdayCode = $checkPresensi->diffday_code;
					}
					if ($checkPresensiYesterday) {
						$diffdayCode = $checkPresensiYesterday->diffday_code;
					}
				}
				if ($input->tipe == 'Masuk') {
					$presensi = $this->db->table('presensi')->select('id')->orderBy('id', 'desc')->get()->getRow();
					$diffdayCode = $presensi->id + 1;
				}
				// cek sudah absen masuk/pulang kemarin
				$checkPresensiYesterdayCome = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'tipe' => $input->tipe, 'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang"])->like('waktu', date('Y-m-d', \strtotime($date . ' -1 DAY')))->get()->getRow();
				if ($checkPresensiYesterdayCome) {
					$response = [
						'status' => 400,
						'message' => "Gagal, presensi $input->tipe sudah diisi kemarin",
					];
					echo json_encode($response);
					return;
				}
				// cek sudah absen masuk/pulang hari ini 
				$checkPresensi = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'tipe' => $input->tipe, 'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang"])->like('waktu', $date)->get()->getRow();
				if ($checkPresensi) {
					$response = [
						'status' => 400,
						'message' => "Gagal, presensi $input->tipe sudah diisi",
					];
					echo json_encode($response);
					return;
				}
			}

			// // update jam_masuk_pulang tipe masuk hari ini in case jika merubah shift yang beda dari masuk
			// if ($input->tipe == 'Pulang') {
			// 	$this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'tipe' => 'Masuk'])->like('waktu', $date)->update(['jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang", 'jam_istirahat' => "$jadwalKerjaDetail->jam_istirahat_mulai - $jadwalKerjaDetail->jam_istirahat_selesai"]);
			// }
		}

		$data = [
			'id_pegawai' => $pegawai->id,
			'photo' => $fileName,
			'tipe' => $input->tipe,
			'coord_latitude' => $input->coord_latitude,
			'coord_longitude' => $input->coord_longitude,
			'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang",
			'jam_istirahat' => "$jadwalKerjaDetail->jam_istirahat_mulai - $jadwalKerjaDetail->jam_istirahat_selesai",
			'waktu' => $now,
			'alasan_pulang_cepat' => $input->alasan_pulang_cepat == 'Mengajar' ? $input->alasan_pulang_cepat : $input->alasan_pulang_cepat_lainnya,
			'diffday_code' => $diffdayCode,
		];
		$this->db->table('presensi')->insert($data);

		// move file and create new folder if not exist
		if (!file_exists('assets/img/presensi')) {
			mkdir('assets/img/presensi', 755);
		}

		// fit image
		if (\file_exists($fileName)) {
			\Config\Services::image()
				->withFile($fileName)
				->resize(300, 300, 'center')
				->save("assets/img/presensi/{$fileName}");
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

	public function CreateScan()
	{
		// set input data
		$input = (object) $this->request->getPost();
		$diffdayCode = '';
		$date = date('Y-m-d');
		$today = date('N');
		$now = \date('Y-m-d H:i:s');
		// $date = date('Y-m-d', \strtotime('2022-11-02'));
		// $today = date('N', \strtotime('2022-11-02'));
		// $now = '2022-11-02 07:00:00';
		// $date = date('Y-m-d', \strtotime('2022-10-31'));
		// $today = date('N', \strtotime('2022-10-31'));
		// $now = '2022-10-31 06:45:19';

		// validate and set error message
		if (!$this->validate($this->PresensiModel->rulesPresensiScan())) {
			$response = [
				'status' => 400,
				'message' => 'NIK Invalid',
			];
			echo json_encode($response);
			return;
		}

		$pegawai = $this->db->table('pegawai')->select('id')->where('nik', $input->nik)->get()->getRow();
		$jabatanUser = $this->db->table('pegawai_jabatan_u_view')->select('id_pegawai, nama_unit, nama_jabatan')->where('id_pegawai', $pegawai->id)->get()->getRow();

		// cek jadwal pegawai
		$jadwalPegawai = $this->db->table('jadwal_pegawai')->where('id_pegawai', $pegawai->id)->get()->getRow();
		$jadwalAutoPegawai = $this->db->table('jadwal_auto_pegawai')->where('id_pegawai', $pegawai->id)->get()->getRow();
		if (!$jadwalPegawai && !$jadwalAutoPegawai) {
			$response = [
				'status' => 400,
				'message' => 'Jadwal kerja pegawai belum ditetapkan',
			];
			echo json_encode($response);
			return;
		}

		if ($jadwalPegawai) {
			// cek hari ini adalah jadwal masuk pegawai
			$cekJadwal = $this->db->table('jadwal_kerja_detail')->where('id_jadwal_kerja', $jadwalPegawai->id_jadwal_kerja)->where('day', $today)->get()->getRow();
			if ($cekJadwal->libur == 1) {
				$response = [
					'status' => 400,
					'message' => 'Gagal, presensi tidak bisa dilakukan dihari libur',
				];
				echo json_encode($response);
				return;
			}
			// cek hari ini tanggal merah / hari libur
			$cekHariLibur = $this->db->table('hari_libur')->where('tanggal', \date('Y-m-d'))->get()->getRow();
			if ($cekHariLibur) {
				$response = [
					'status' => 400,
					'message' => 'Gagal, presensi tidak bisa dilakukan dihari libur',
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
		if ($jadwalPegawai) {
			// cek sudah absen masuk dan pulang
			$checkPresensi = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'tipe' => 'Pulang'])->like('waktu', $date)->get()->getRow();
			if ($checkPresensi) {
				$response = [
					'status' => 400,
					'message' => 'Gagal, Sudah absen masuk dan pulang',
				];
				echo json_encode($response);
				return;
			}

			//  set tipe absen
			$checkPresensi = $this->db->table('presensi')->where('id_pegawai', $pegawai->id)->like('waktu', $date)->get()->getRow();
			if (!$checkPresensi) {
				$tipe = 'Masuk';
			} else {
				$tipe = 'Pulang';
			}

			// cek durasi absen pulang untuk dosen saja
			if ($tipe == 'Pulang') {
				if ($jabatanUser && $jabatanUser->nama_jabatan == 'Dosen') {
					$konfigurasiPresensi = $this->db->table('konfigurasi_presensi')->get()->getRow();
					if ($date >= $konfigurasiPresensi->tanggal_mulai_presensi_dosen_sekali) {
						$response = [
							'status' => 400,
							'message' => 'Dosen hanya perlu melakukan presensi masuk saja',
						];
						echo json_encode($response);
						return;
					} else {
						$presensi = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'tipe' => 'Masuk'])->like('waktu', $date)->get()->getRow();
						$jamMasukPulang = explode(' - ', $presensi->jam_masuk_pulang);
						$jamMasuk = date('H:i', \strtotime("$jamMasukPulang[0]"));
						$jamPulang = date('H:i', \strtotime($jamMasukPulang[1]));

						$jamPresensiMasuk =  date('H:i', \strtotime("$presensi->waktu"));;
						$jamPresensiPulang = date('H:i', \strtotime($now));

						$jadwalJamDiff =  abs(\timeDifference($jamMasuk, $jamPulang));
						$jamDiff = \timeDifference($jamPresensiMasuk, $jamPresensiPulang);
						$durasiKurang = $jadwalJamDiff - $jamDiff;
						if ($durasiKurang >= 0) {
							$durasiKurang = convertToHoursMins($durasiKurang);
							$durasiKurang = explode(':', $durasiKurang);
							$durasiKurangJam = $durasiKurang[0] != '00' ? ltrim($durasiKurang[0], '0') . ' Jam' : '';

							$durasiKurangMenit = $durasiKurang[1] != '00' ? ltrim($durasiKurang[1], '0') . ' Menit' : '';
							$durasiKurang = "$durasiKurangJam $durasiKurangMenit";
							if ($jamDiff < $jadwalJamDiff && \date('H:i', \strtotime($now)) < $jamPulang && ($input->alasan_pulang_cepat == '' || ($input->alasan_pulang_cepat == 'Lainnya' && $input->alasan_pulang_cepat_lainnya == ''))) {
								$response = [
									'status' => 300,
									'message' => 'Alasan pulang cepat harus diisi',
									'data' =>  $input,
									'durasiKurang' => $durasiKurang,
								];
								echo json_encode($response);
								return;
							}
						}
					}
				}
			}

			// cek memilih shift tapi tidak punya shift
			$jadwalKerja = $this->db->table('jadwal_kerja')->where('id', $jadwalPegawai->id_jadwal_kerja)->get()->getRow();
			$jadwalKerjaDetail = $this->db->table('jadwal_kerja_detail_view')->where('id_jadwal_kerja', $jadwalKerja->id)->where('day', $today)->get()->getRow();
			if (isset($input->shift) && !empty($input->shift)) {
				$response = [
					'status' => 400,
					'message' => "Anda tidak mempunyai shift silahkan kosongkan pilihan shift",
				];
				echo json_encode($response);
				return;
			}
		}
		if ($jadwalAutoPegawai) {
			// cek user belum memilih shift
			if (isset($input->shift) && empty($input->shift)) {
				$response = [
					'status' => 400,
					'message' => "Silakan pilih shift terlebih dahulu",
				];
				echo json_encode($response);
				return;
			}

			// cek user mempunyai shift yang dipilih
			$jadwalKerja = $this->db->table('jadwal_kerja_auto')->where('id', $jadwalAutoPegawai->id_jadwal_kerja_auto)->get()->getRow();
			$jadwalKerjaDetail = $this->db->table('jadwal_kerja_auto_detail_view')->where('id', $input->shift)->where('id_jadwal_kerja_auto', $jadwalAutoPegawai->id_jadwal_kerja_auto)->get()->getRow();
			if (!$jadwalKerjaDetail) {
				$response = [
					'status' => 400,
					'message' => "Anda tidak punya shift tersebut silakan ganti shift",
				];
				echo json_encode($response);
				return;
			}

			if ($jadwalKerjaDetail->is_diffday == 0) {
				// cek sudah absen masuk dan pulang
				$checkPresensi = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang", 'tipe' => 'Pulang'])->like('waktu', $date)->get()->getRow();
				if ($checkPresensi) {
					$response = [
						'status' => 400,
						'message' => 'Gagal, Sudah absen masuk dan pulang',
					];
					echo json_encode($response);
					return;
				}
				//  set tipe absen
				$checkPresensi = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang"])->like('waktu', $date)->get()->getRow();
				if (!$checkPresensi) {
					$tipe = 'Masuk';
				} else {
					$tipe = 'Pulang';
				}
			} else {
				// cek sudah absen masuk dan pulang
				$checkPresensi = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang", 'tipe' => 'Pulang'])->like('waktu', $date)->get()->getRow();
				if ($checkPresensi) {
					$response = [
						'status' => 400,
						'message' => 'Gagal, Sudah absen masuk dan pulang',
					];
					echo json_encode($response);
					return;
				}
				//  set tipe absen
				$checkPresensi = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang", 'tipe' => 'Masuk'])->like('waktu', $date)->get()->getRow();
				$checkPresensiYesterday = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang", 'tipe' => 'Masuk'])->like('waktu', date('Y-m-d', \strtotime($date . ' -1 DAY')))->get()->getRow();
				if (!$checkPresensi && !$checkPresensiYesterday) {
					$tipe = 'Masuk';
					$presensi = $this->db->table('presensi')->select('id')->orderBy('id', 'desc')->get()->getRow();
					$diffdayCode = $presensi->id + 1;
				} else {
					$tipe = 'Pulang';
					if ($checkPresensi) {
						// cek sudah absen masuk dan pulang
						$check = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang", 'tipe' => 'Pulang'])->like('waktu', $date)->get()->getRow();
						if ($check) {
							$response = [
								'status' => 400,
								'message' => 'Gagal, Sudah absen masuk dan pulang',
							];
							echo json_encode($response);
							return;
						}
						$diffdayCode = $checkPresensi->diffday_code;
					}
					if ($checkPresensiYesterday) {
						// cek sudah absen masuk dan pulang
						$check = $this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang", 'tipe' => 'Pulang'])->like('waktu', date('Y-m-d', \strtotime($date . ' -1 DAY')))->get()->getRow();
						if ($check) {
							$response = [
								'status' => 400,
								'message' => 'Gagal, Sudah absen masuk dan pulang',
							];
							echo json_encode($response);
							return;
						}
						$diffdayCode = $checkPresensiYesterday->diffday_code;
					}
				}
			}

			// // update jam_masuk_pulang tipe masuk hari ini in case jika merubah shift yang beda dari masuk
			// if ($input->tipe == 'Pulang') {
			// 	$this->db->table('presensi')->where(['id_pegawai' => $pegawai->id, 'tipe' => 'Masuk'])->like('waktu', $date)->update(['jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang", 'jam_istirahat' => "$jadwalKerjaDetail->jam_istirahat_mulai - $jadwalKerjaDetail->jam_istirahat_selesai"]);
			// }
		}

		$data = [
			'id_pegawai' => $pegawai->id,
			'photo' => $fileName,
			'tipe' => $tipe,
			'coord_latitude' => '',
			'coord_longitude' => '',
			'jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang",
			'jam_istirahat' => "$jadwalKerjaDetail->jam_istirahat_mulai - $jadwalKerjaDetail->jam_istirahat_selesai",
			'waktu' => $now,
			'alasan_pulang_cepat' => $input->alasan_pulang_cepat == 'Mengajar' ? $input->alasan_pulang_cepat : $input->alasan_pulang_cepat_lainnya,
			'diffday_code' => $diffdayCode,
		];
		$this->db->table('presensi')->insert($data);

		// move file and create new folder if not exist
		if (!file_exists('assets/img/presensi')) {
			mkdir('assets/img/presensi', 755);
		}

		// fit image
		if (\file_exists($fileName)) {
			\Config\Services::image()
				->withFile($fileName)
				->resize(300, 300, 'center')
				->save("assets/img/presensi/{$fileName}");
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
