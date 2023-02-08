<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PegawaiDokumenModel;

class PegawaiDokumen extends AdminBaseController
{
	protected $PegawaiDokumenModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PegawaiDokumenModel = new PegawaiDokumenModel();
	}

	public function ajax_list($idPegawai)
	{
		$this->PegawaiDokumenModel->table = 'pegawai_dokumen';
		$this->PegawaiDokumenModel->column_order = array('id', 'nama_dokumen');
		$this->PegawaiDokumenModel->column_search = array('nama_dokumen');
		$this->PegawaiDokumenModel->order = array('nama_dokumen' => 'asc');

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->PegawaiDokumenModel->get_datatables(['id_pegawai' => $idPegawai]);
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
					$pathtofile = base_url("assets/files/pegawai-dokumen/$list->dokumen");
					$row[] = !empty($list->nama_dokumen) ? $list->nama_dokumen : '-';
					$row[] = '
					<a href="' . $pathtofile . '" class="btn btn-sm btn-info" download>
						<i class="fas fa-download"></i>
						</a>
					<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="updateDataDokumen(' . "'" . $list->id . "'" . ')" data-toggle="tooltip" data-placement="top" title="Edit"">
						<i class="fas fa-edit"></i>
					</a>
					<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="destroyDataDokumen(' . "'" . $list->id . "'" . ')" data-toggle="tooltip" data-placement="top" title="Delete" onclick="return confirm(`Apakah anda yakin menghapus data?`)">
						<i class="fas fa-trash"></i>
					</a>
					';
				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->PegawaiDokumenModel->count_all(['id_pegawai' => $idPegawai]),
				"recordsFiltered" => $this->PegawaiDokumenModel->count_filtered(['id_pegawai' => $idPegawai]),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function create()
	{
		if (!$this->__validate($this->PegawaiDokumenModel->rulesCreate())) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();
		$pegawai = $this->db->table('pegawai')->select('nama')->where('id', $input->id_pegawai)->get()->getRow();

		// get uploaded file
		$dokumen = $this->request->getFile('dokumen');

		// check if uploading an file
		if ($dokumen->getError() == 4) {
			$filenameDokumen = '';
		} else {
			$filenameDokumen = url_title("$input->nama_dokumen", '-', true) . '-' . url_title("$pegawai->nama", '-', true) . '-' . date('YmdHis') . '.' .  $dokumen->getExtension();
		}

		// save data
		$data = [
			'id_pegawai' => $input->id_pegawai,
			'nama_dokumen' => $input->nama_dokumen,
			'dokumen' => $filenameDokumen,
		];
		$this->db->table('pegawai_dokumen')->insert($data);

		// move file to public
		if ($dokumen->getError() != 4) {
			$dokumen->move("assets/files/pegawai-dokumen", $filenameDokumen);
		}

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
		$dataDb = $this->PegawaiDokumenModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		if (!$_POST) {
			// repoulate form
			$input = $dataDb;
			echo json_encode($input);
			return;
		}

		if (!$this->__validate($this->PegawaiDokumenModel->rulesUpdate())) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();
		$pegawai = $this->db->table('pegawai')->select('nama')->where('id', $input->id_pegawai)->get()->getRow();
		
		// get uploaded file
		$dokumen = $this->request->getFile('dokumen');

		// check if uploading an file
		if ($dokumen->getError() == 4) {
			$filenameDokumen = $dataDb->dokumen;
		} else {
			$filenameDokumen = url_title("$input->nama_dokumen", '-', true) . '-' . url_title("$pegawai->nama", '-', true) . '-' . date('YmdHis') . '.' .  $dokumen->getExtension();
		}

		// save data
		$data = [
			// 'id_pegawai' => $input->id_pegawai,
			'nama_dokumen' => $input->nama_dokumen,
			'dokumen' => $filenameDokumen,
		];
		$this->db->table('pegawai_dokumen')->where('id', $id)->update($data);

		// move file to public
		if ($dokumen->getError() != 4) {
			// delete old file
			if (!empty($dataDb->dokumen) && file_exists("assets/files/pegawai-dokumen/{$dataDb->dokumen}")) {
				unlink("assets/files/pegawai-dokumen/{$dataDb->dokumen}");
			}
			$dokumen->move("assets/files/pegawai-dokumen", $filenameDokumen);
		}

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
		$dataDb = $this->PegawaiDokumenModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete file
		if (!empty($dataDb->dokumen) && file_exists("assets/files/pegawai-dokumen/{$dataDb->dokumen}")) {
			unlink("assets/files/pegawai-dokumen/$dataDb->dokumen");
		}

		// delete data && send response
		if ($this->db->table('pegawai_dokumen')->where('id', $id)->delete()) {
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

	private function __validate($rules = null)
	{
		// validate and set error message
		if ($rules == null) {
			$rules = $this->PegawaiDokumenModel->getValidationRules();
		}

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
