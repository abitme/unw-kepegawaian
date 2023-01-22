<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PresensiLupaValidasiModel;

class PresensiLupaValidasi extends AdminBaseController
{
	protected $PresensiLupaValidasiModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PresensiLupaValidasiModel = new PresensiLupaValidasiModel();

		$pegawai = $this->db->table('users')->select('id_pegawai')->where('id', session('user_id'))->get()->getRow();
		$this->id_pegawai = $pegawai->id_pegawai ?? '';

		$checkVerification = $this->db->table('verifikasi_form_presensi')->select('id_pegawai')->where('id_pegawai_verifikasi', $this->id_pegawai)->get()->getRow();
		if (!$checkVerification && !checkGroupUser([1])) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		$this->PresensiLupaValidasiModel->table = 'presensi_lupa_view';

		if ($this->request->getMethod(true) == 'POST') {
			$idPegawai = $this->db->table('verifikasi_form_presensi')->select('id_pegawai')->where('id_pegawai_verifikasi', $this->id_pegawai)->get()->getResultArray();
			if (checkGroupUser([1])) {
				$lists = $this->PresensiLupaValidasiModel->get_datatables();
			} else if ($idPegawai) {
				$lists = $this->PresensiLupaValidasiModel->get_datatables(null, null, ['id_pegawai' => \array_column($idPegawai, 'id_pegawai')]);
			} else {
				$lists = [];
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
				$row[] = $list->status;
				$row[] = '
				<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="updateData(' . "'" . $list->id . "'" . ')">
				<i class="fas fa-edit"></i> Edit
				</a>
				';

				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->PresensiLupaValidasiModel->count_all(),
				"recordsFiltered" => $this->PresensiLupaValidasiModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Validasi Lupa Presensi',
		];

		return $this->view('pages/admin/presensi-lupa-validasi/index', $data);
	}

	public function update($id)
	{
		$dataDb = $this->PresensiLupaValidasiModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		if (!$_POST) {
			// repoulate form
			$input = $dataDb;
			echo json_encode($input);
			return;
		}

		if (!$this->__validate()) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();

		$checkVerification = $this->db->table('verifikasi_form_presensi')->select('id_pegawai')->where('id_pegawai', $input->id_pegawai)->where('id_pegawai_verifikasi', $this->id_pegawai)->get()->getRow();
		if (!$checkVerification && !checkGroupUser([1])) {
			$response = [
				'status' => false,
				'message' => 'Unauthorized',
			];
			echo json_encode($response);
			return false;
		}

		// save data
		$data = [
			'status' => $input->status,
		];
		$this->db->table('presensi_lupa')->where('id', $id)->update($data);

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
		if (!$this->validate($this->PresensiLupaValidasiModel->getValidationRules())) {
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
