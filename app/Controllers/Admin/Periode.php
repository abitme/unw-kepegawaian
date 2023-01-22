<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PeriodeModel;

class Periode extends AdminBaseController
{
	protected $PeriodeModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PeriodeModel = new PeriodeModel();

		$this->menuSlug = 'periode';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->PeriodeModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->tahun;
				$row[] = $list->semester;
				$row[] = date('d-F-Y', strtotime($list->tanggal_awal));
				$row[] = date('d-F-Y', strtotime($list->tanggal_akhir));
				$row[] = $list->is_active == 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>';
				if (is_allow('update', $this->menuSlug) || is_allow('delete', $this->menuSlug)) {
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
				"recordsTotal" => $this->PeriodeModel->count_all(),
				"recordsFiltered" => $this->PeriodeModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Periode',
			'content' => $this->PeriodeModel->get(),
		];

		return $this->view('pages/admin/periode/index', $data);
	}

	public function create()
	{
		if (!is_allow('insert', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
		if (!$this->__validate()) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();

		// save data
		$data = [
			'tahun' => $input->tahun,
			'semester' => $input->semester,
			'tanggal_awal' => date('Ymd', strtotime($input->tanggal_awal)),
			'tanggal_akhir' => date('Ymd', strtotime($input->tanggal_akhir)),
			'is_active' => isset($input->is_active) ? 1 : 0,
		];
		$this->db->table('periode')->insert($data);

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
		if (!is_allow('update', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
		$dataDb = $this->PeriodeModel->where('id', $id)->first();
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
			'tahun' => $input->tahun,
			'semester' => $input->semester,
			'tanggal_awal' => date('Ymd', strtotime($input->tanggal_awal)),
			'tanggal_akhir' => date('Ymd', strtotime($input->tanggal_akhir)),
			'is_active' => isset($input->is_active) ? 1 : 0,
		];
		$this->db->table('periode')->where('id', $id)->update($data);

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
		if (!is_allow('delete', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
		$dataDb = $this->PeriodeModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('periode')->where('id', $id)->delete($id)) {
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
		if (!$this->validate($this->PeriodeModel->getValidationRules())) {
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
