<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PegawaiJabatanModel;

class PegawaiJabatan extends AdminBaseController
{
	protected $PegawaiJabatanModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PegawaiJabatanModel = new PegawaiJabatanModel();
	}

	public function ajax_list($idPegawai)
	{
		$this->PegawaiJabatanModel->table = 'pegawai_jabatan_u_view';
		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->PegawaiJabatanModel->get_datatables(['id_pegawai' => $idPegawai]);
			$countAll = $this->PegawaiJabatanModel->count_all(['id_pegawai' => $idPegawai]);
			$countFiltered = $this->PegawaiJabatanModel->count_filtered(['id_pegawai' => $idPegawai]);

			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama_unit;
				$row[] = $list->nama_jabatan;
				$row[] = $list->tmt;
				$row[] = '
					<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="updateDataJabatan(' . "'" . $list->id . "'" . ')" data-toggle="tooltip" data-placement="top" title="Edit"">
						<i class="fas fa-edit"></i>
					</a>
					<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="destroyDataJabatan(' . "'" . $list->id . "'" . ')" data-toggle="tooltip" data-placement="top" title="Delete" onclick="return confirm(`Apakah anda yakin menghapus data?`)">
						<i class="fas fa-trash"></i>
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

	public function create()
	{
		if (!$this->__validate()) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();

		// save data
		$data = [
			'id_pegawai' => $input->id_pegawai,
			'id_jabatan_u' => $input->id_jabatan_u,
			'tmt' => $input->tmt,
		];
		$this->db->table('pegawai_jabatan_u')->insert($data);

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
		$dataDb = $this->PegawaiJabatanModel->where('id', $id)->first();
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

		// save data
		$data = [
			// 'id_pegawai' => $input->id_pegawai,
			'id_jabatan_u' => $input->id_jabatan_u,
			'tmt' => $input->tmt,
		];
		$this->db->table('pegawai_jabatan_u')->where('id', $id)->update($data);

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
		$dataDb = $this->PegawaiJabatanModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('pegawai_jabatan_u')->where('id', $id)->delete()) {
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
		if (!$this->validate($this->PegawaiJabatanModel->getValidationRules())) {
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
