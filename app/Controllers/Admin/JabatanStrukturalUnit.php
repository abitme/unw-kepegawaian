<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\JabatanStrukturalUnitModel;

class JabatanStrukturalUnit extends AdminBaseController
{
	protected $JabatanStrukturalUnitModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->JabatanStrukturalUnitModel = new JabatanStrukturalUnitModel();
								
		$this->menuSlug = 'jabatan-struktural-unit';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function import()
	{
		$data = [
			'title' => 'Import Excel Jabatan Struktural',
			'validation' => \Config\Services::validation(),
			'user_id' => $this->user_id,
			'form_action' => base_url("jabatan-struktural-unit/import"),
		];

		// accessesed with get method
		if (!$_POST) {
			return $this->view('pages/admin/jabatan-struktural-unit/import', $data);
		};

		// validate when accessesed with post method
		if (!$this->validate($this->JabatanStrukturalUnitModel->getrulesImport()) && empty($this->request->getPost('import'))) {
			return redirect()->to("/jabatan-struktural-unit/import")->withInput();
		}

		// import proccess
		$file = $this->request->getFile('excel');
		$import = $this->request->getPost('import');

		$resultImport = $this->JabatanStrukturalUnitModel->import($file, $import);

		if ($resultImport == 'error') {
			return redirect()->to('/jabatan-struktural-unit/import');
		} else if ($resultImport == 'success') {
			return redirect()->to('/jabatan-struktural-unit');
		} else {
			$data['error'] = $resultImport['error'];
			$data['preview'] = $resultImport['preview'];
		}

		return $this->view('pages/admin/jabatan-struktural-unit/import', $data);
	}

	public function ajax_list()
	{
		$this->JabatanStrukturalUnitModel->table = 'jabatan_struktural_u_view';
		$input = (object) $this->request->getPost();

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->JabatanStrukturalUnitModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama_unit;
				$row[] = $list->nama_jabatan_struktural;
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
				"recordsTotal" => $this->JabatanStrukturalUnitModel->count_all(),
				"recordsFiltered" => $this->JabatanStrukturalUnitModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Unit Jabatan Struktural',
			// 'periode' => $this->db->table('periode')->where('is_active', 1)->get()->getRow(),
		];

		return $this->view('pages/admin/jabatan-struktural-unit/index', $data);
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
			'id_unit' => $input->id_unit,
			'id_jabatan_struktural' => $input->id_jabatan_struktural,
		];
		$this->db->table('jabatan_struktural_u')->insert($data);

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
		$dataDb = $this->JabatanStrukturalUnitModel->where('id', $id)->first();
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
			'id_unit' => $input->id_unit,
			'id_jabatan_struktural' => $input->id_jabatan_struktural,
		];
		$this->db->table('jabatan_struktural_u')->where('id', $id)->update($data);

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
		$dataDb = $this->JabatanStrukturalUnitModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('jabatan_struktural_u')->where('id', $id)->delete()) {
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
		if (!$this->validate($this->JabatanStrukturalUnitModel->getValidationRules())) {
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
