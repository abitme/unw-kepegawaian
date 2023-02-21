<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PresensiIzinPengajuanModel;

class PresensiIzinPengajuan extends AdminBaseController
{
	protected $PresensiIzinPengajuanModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PresensiIzinPengajuanModel = new PresensiIzinPengajuanModel();

		$this->menuSlug = 'presensi-izin-pengajuan';

		$pegawai = $this->db->table('users')->select('id_pegawai')->where('id', session('user_id'))->get()->getRow();
		$this->id_pegawai = $pegawai->id_pegawai ?? '';
	}

	public function ajax_list()
	{
		$this->PresensiIzinPengajuanModel->table = 'presensi_izin_view';
		$input = (object) $this->request->getPost();

		if ($this->request->getMethod(true) == 'POST') {
			if (checkGroupUser([1])) {
				$lists = $this->PresensiIzinPengajuanModel->get_datatables();
			} else {
				$lists = $this->PresensiIzinPengajuanModel->get_datatables(['id_pegawai' => $this->id_pegawai]);
			}
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$status1At = $list->status1_at != null ? \date('d/m/Y H:i', \strtotime($list->status1_at)) : '-';
				$status2At = $list->status2_at != null ? \date('d/m/Y H:i', \strtotime($list->status2_at)) : '-';
				$row = [];
				$row[] = $no;
				$row[] = $list->nama;
				$row[] = $list->tanggal;
				$row[] = $list->jam_masuk;
				$row[] = $list->jam_pulang;
				$row[] = $list->keterangan;
				$row[] = "$list->status1 </br> <small>$status1At</small>";
				$row[] = "$list->status1 </br> <small>$status2At</small>";
				if (checkGroupUser([1])) {
					$row[] = '
						<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="updateData(' . "'" . $list->id . "'" . ')">
							<i class="fas fa-edit"></i> Edit
						</a>
						<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="destroyData(' . "'" . $list->id . "'" . ')">
							<i class="fas fa-trash"></i> Delete
						</a>
					';
				} else	if ($list->status1 != 'Diterima') {
					$row[] = '
						<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="updateData(' . "'" . $list->id . "'" . ')">
							<i class="fas fa-edit"></i> Edit
						</a>
						<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="destroyData(' . "'" . $list->id . "'" . ')">
							<i class="fas fa-trash"></i> Delete
						</a>
					';
				} else {
					$row[] = '';
				}

				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->PresensiIzinPengajuanModel->count_all(),
				"recordsFiltered" => $this->PresensiIzinPengajuanModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Pengajuan izin Presensi',
			'optionsShift' => $this->db->table('jadwal_kerja_auto_detail_view')->get()->getResult(),
		];

		return $this->view('pages/admin/presensi-izin-pengajuan/index', $data);
	}

	public function create()
	{
		if (!$this->__validate()) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();
		$today = date('N', \strtotime($input->tanggal));

		// cek harus sudah presensi masuk
		if (!$this->db->table('presensi')->select('id')->where('id_pegawai', $this->user->id_pegawai)->where('tipe', 'Masuk')->like('waktu', $input->tanggal)->get()->getRow()) {
			$response = [
				'status' => false,
				'message' => 'Anda belum melakukan presensi masuk pada tanggal tersebut',
			];
			echo json_encode($response);
			return false;
		}

		$jadwalPegawai = $this->db->table('jadwal_pegawai')->where('id_pegawai', $this->id_pegawai)->get()->getRow();
		$jadwalAutoPegawai = $this->db->table('jadwal_auto_pegawai')->where('id_pegawai', $this->id_pegawai)->get()->getRow();
		if (!$jadwalPegawai && !$jadwalAutoPegawai) {
			$response = [
				'status' => 400,
				'message' => 'Jadwal kerja pegawai belum ditetapkan',
			];
			echo json_encode($response);
			return;
		}
		if ($jadwalPegawai) {
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
		}

		// save data
		$data = [
			'id_pegawai' => $this->id_pegawai,
			'id_jadwal_kerja_auto_detail' => !empty($input->shift) ? $input->shift : null,
			'tanggal' => $input->tanggal,
			'jadwal_jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang",
			'jam_masuk' => $input->jam_masuk,
			'jam_pulang' => $input->jam_pulang,
			'keterangan' => $input->keterangan,
			'status1' => 'Menunggu',
			'status2' => 'Menunggu',
			'status1_at' => \date('Y-m-d H:i'),
			'status2_at' => \date('Y-m-d H:i'),
		];
		$this->db->table('presensi_izin')->insert($data);

		// send response
		if ($this->db->affectedRows()) {
			$response = [
				'status' => 200,
				'message' => 'Data berhasil ditambahkan',
				'data' =>  $input,
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

	public function update($id)
	{
		$dataDb = $this->PresensiIzinPengajuanModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		if ($this->id_pegawai != $dataDb->id_pegawai && !checkGroupUser([1])) {
			$response = [
				'status' => false,
				'message' => 'Maaf! Data tidak ditemukan',
			];
			echo json_encode($response);
			return false;
		}

		if (!$_POST) {
			// repoulate form
			$input = $dataDb;
			echo json_encode($input);
			return;
		}

		if ($dataDb->status == 'Diterima' && !checkGroupUser([1])) {
			$response = [
				'status' => false,
				'message' => 'Pengajuan sudah divalidasi',
			];
			echo json_encode($response);
			return false;
		}

		if (!$this->__validate()) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();
		$today = date('N', \strtotime($input->tanggal));

		$jadwalPegawai = $this->db->table('jadwal_pegawai')->where('id_pegawai', $dataDb->id_pegawai)->get()->getRow();
		$jadwalAutoPegawai = $this->db->table('jadwal_auto_pegawai')->where('id_pegawai', $dataDb->id_pegawai)->get()->getRow();
		if (!$jadwalPegawai && !$jadwalAutoPegawai) {
			$response = [
				'status' => 400,
				'message' => 'Jadwal kerja pegawai belum ditetapkan',
			];
			echo json_encode($response);
			return;
		}
		if ($jadwalPegawai) {
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
		}

		// save data
		$data = [
			'id_pegawai' => $dataDb->id_pegawai,
			'id_jadwal_kerja_auto_detail' => !empty($input->shift) ? $input->shift : null,
			'tanggal' => $input->tanggal,
			'jadwal_jam_masuk_pulang' => "$jadwalKerjaDetail->jam_masuk - $jadwalKerjaDetail->jam_pulang",
			'jam_masuk' => $input->jam_masuk,
			'jam_pulang' => $input->jam_pulang,
			'keterangan' => $input->keterangan,
			'status1' => 'Menunggu',
			'status2' => 'Menunggu',
		];
		$this->db->table('presensi_izin')->where('id', $id)->update($data);

		// send response
		if ($this->db->affectedRows() || $this->db->affectedRows() == 0) {
			$response = [
				'status' => 200,
				'message' => 'Data berhasil diubah',
				'data' =>  $input,
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

	public function delete($id)
	{
		$dataDb = $this->PresensiIzinPengajuanModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		if ($this->id_pegawai != $dataDb->id_pegawai && !checkGroupUser([1])) {
			$response = [
				'status' => false,
				'message' => 'Maaf! Data tidak ditemukan',
			];
			echo json_encode($response);
			return false;
		}

		if ($dataDb->status == 'Diterima' && !checkGroupUser([1])) {
			$response = [
				'status' => false,
				'message' => 'Pengajuan sedang divalidasi',
			];
			echo json_encode($response);
			return false;
		}

		// delete data && send response
		if ($this->db->table('presensi_izin')->where('id', $id)->delete()) {
			$response = [
				'status' => 200,
				'message' => 'Data berhasil dihapus',
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

	private function __validate()
	{
		// validate and set error message
		if (!$this->validate($this->PresensiIzinPengajuanModel->getValidationRules())) {
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
