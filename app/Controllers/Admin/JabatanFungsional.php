<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\JabatanFungsionalModel;

class JabatanFungsional extends AdminBaseController
{
	protected $JabatanFungsionalModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->JabatanFungsionalModel = new JabatanFungsionalModel();
				
		$this->menuSlug = 'jabatan-fungsional';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function import()
	{
		$data = [
			'title' => 'Import Excel Jabatan Fungsional',
			'validation' => \Config\Services::validation(),
			'user_id' => $this->user_id,
			'form_action' => base_url("jabatan-fungsional/import"),
		];

		// accessesed with get method
		if (!$_POST) {
			return $this->view('pages/admin/jabatan-fungsional/import', $data);
		};

		// validate when accessesed with post method
		if (!$this->validate($this->JabatanFungsionalModel->getrulesImport()) && empty($this->request->getPost('import'))) {
			return redirect()->to("/jabatan-fungsional/import")->withInput();
		}

		// import proccess
		$file = $this->request->getFile('excel');
		$import = $this->request->getPost('import');

		$resultImport = $this->JabatanFungsionalModel->import($file, $import);

		if ($resultImport == 'error') {
			return redirect()->to('/jabatan-fungsional/import');
		} else if ($resultImport == 'success') {
			return redirect()->to('/jabatan-fungsional');
		} else {
			$data['error'] = $resultImport['error'];
			$data['preview'] = $resultImport['preview'];
		}

		return $this->view('pages/admin/jabatan-fungsional/import', $data);
	}

	public function ajax_list()
	{
		$this->JabatanFungsionalModel->table = 'jabatan_fungsional_view';
		$input = (object) $this->request->getPost();

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->JabatanFungsionalModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama_jabatan;
				$row[] = $list->nama_jabatan_fungsional;
				$row[] = '
					<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="updateData(' . "'" . $list->id . "'" . ')">
						<i class="fas fa-edit"></i> Edit
					</a>
					<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="destroyData(' . "'" . $list->id . "'" . ')">
						<i class="fas fa-trash"></i> Delete
					</a>
				';
				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->JabatanFungsionalModel->count_all(),
				"recordsFiltered" => $this->JabatanFungsionalModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Jabatan Fungsional',
			// 'periode' => $this->db->table('periode')->where('is_active', 1)->get()->getRow(),
		];

		return $this->view('pages/admin/jabatan-fungsional/index', $data);
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
			'id_jabatan' => $input->id_jabatan,
			'nama_jabatan_fungsional' => $input->nama_jabatan_fungsional,
		];
		$this->db->table('jabatan_fungsional')->insert($data);

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
		$dataDb = $this->JabatanFungsionalModel->where('id', $id)->first();
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
			'id_jabatan' => $input->id_jabatan,
			'nama_jabatan_fungsional' => $input->nama_jabatan_fungsional,
		];
		$this->db->table('jabatan_fungsional')->where('id', $id)->update($data);

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
		$dataDb = $this->JabatanFungsionalModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('jabatan_fungsional')->where('id', $id)->delete()) {
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
		if (!$this->validate($this->JabatanFungsionalModel->getValidationRules())) {
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
