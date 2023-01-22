<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PresensiIzinValidasiModel;

class PresensiIzinValidasi extends AdminBaseController
{
	protected $PresensiIzinValidasiModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PresensiIzinValidasiModel = new PresensiIzinValidasiModel();

		$pegawai = $this->db->table('users')->select('id_pegawai')->where('id', session('user_id'))->get()->getRow();
		$this->id_pegawai = $pegawai->id_pegawai ?? '';

		$checkVerification = $this->db->table('verifikasi_form_presensi')->select('id_pegawai')->where('id_pegawai_verifikasi', $this->id_pegawai)->get()->getRow();
		if (!$checkVerification && !checkGroupUser([1])) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		$this->PresensiIzinValidasiModel->table = 'presensi_izin_view';

		if ($this->request->getMethod(true) == 'POST') {
			$idPegawai = $this->db->table('verifikasi_form_presensi')->select('id_pegawai')->where('id_pegawai_verifikasi', $this->id_pegawai)->get()->getResultArray();
			$konfigurasiPresensi = $this->db->table('konfigurasi_presensi')->get()->getRow();
			if ($idPegawai && $konfigurasiPresensi->id_pegawai_verifikasi_akhir != $this->id_pegawai) {
				$lists = $this->PresensiIzinValidasiModel->get_datatables(null, null, ['id_pegawai' => \array_column($idPegawai, 'id_pegawai')]);
			} else {
				$lists = $this->PresensiIzinValidasiModel->get_datatables();
			}
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama;
				$row[] = $list->tanggal;
				$row[] = $list->jam_masuk;
				$row[] = $list->jam_pulang;
				$row[] = $list->keterangan;
				$row[] = $list->status1;
				$row[] = $list->status2;
				$row[] = '
				<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="updateData(' . "'" . $list->id . "'" . ')">
				<i class="fas fa-edit"></i> Edit
				</a>
				';

				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->PresensiIzinValidasiModel->count_all(),
				"recordsFiltered" => $this->PresensiIzinValidasiModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Validasi izin Presensi',
		];

		return $this->view('pages/admin/presensi-izin-validasi/index', $data);
	}

	public function update($id)
	{
		$dataDb = $this->PresensiIzinValidasiModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		$konfigurasiPresensi = $this->db->table('konfigurasi_presensi')->get()->getRow();
		if (!$_POST) {
			// repoulate form
			$input = $dataDb;
			if ($this->id_pegawai != $konfigurasiPresensi->id_pegawai_verifikasi_akhir && !checkGroupUser([1])) {
				$input->status = $input->status1;
			} else {
				$input->status = $input->status2;
			}
			echo json_encode($input);
			return;
		}

		if (!$this->__validate()) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();

		$checkVerification = $this->db->table('verifikasi_form_presensi')->select('id_pegawai')->where('id_pegawai', $input->id_pegawai)->where('id_pegawai_verifikasi', $this->id_pegawai)->get()->getRow();
		if (!$checkVerification && $this->id_pegawai != $konfigurasiPresensi->id_pegawai_verifikasi_akhir && !checkGroupUser([1])) {
			$response = [
				'status' => false,
				'message' => 'Unauthorized',
			];
			echo json_encode($response);
			return false;
		}

		// save data
		if ($this->id_pegawai != $konfigurasiPresensi->id_pegawai_verifikasi_akhir && !checkGroupUser([1])) {
			$data = [
				'status1' => $input->status,
			];
		} else {
			if ($dataDb->status1 != 'Diterima') {
				$response = [
					'status' => false,
					'message' => 'Status 1 Belum Diterima',
				];
				echo json_encode($response);
				return false;
			}
			$data = [
				'status2' => $input->status,
			];
		}
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

	private function __validate()
	{
		// validate and set error message
		if (!$this->validate($this->PresensiIzinValidasiModel->getValidationRules())) {
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
