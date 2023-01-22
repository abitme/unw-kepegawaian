<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\JabatanModel;

class Jabatan extends AdminBaseController
{
	protected $JabatanModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->JabatanModel = new JabatanModel();
				
		$this->menuSlug = 'jabatan';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		$this->JabatanModel->table = 'jabatan';
		$input = (object) $this->request->getPost();

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->JabatanModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama_jabatan;
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
				"recordsTotal" => $this->JabatanModel->count_all(),
				"recordsFiltered" => $this->JabatanModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Jabatan',
			// 'periode' => $this->db->table('periode')->where('is_active', 1)->get()->getRow(),
		];

		return $this->view('pages/admin/jabatan/index', $data);
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
			'nama_jabatan' => $input->nama_jabatan,
		];
		$this->db->table('jabatan')->insert($data);

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
		$dataDb = $this->JabatanModel->where('id', $id)->first();
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
			'nama_jabatan' => $input->nama_jabatan,
		];
		$this->db->table('jabatan')->where('id', $id)->update($data);

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
		$dataDb = $this->JabatanModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('jabatan')->where('id', $id)->delete()) {
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
		if (!$this->validate($this->JabatanModel->getValidationRules())) {
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

	function ajax_select2()
	{
		if (!isset($_POST['searchTerm'])) {
			$builder = $this->db->table('jabatan')->orderBy('nama_jabatan', 'asc');
		} else {
			$search = $_POST['searchTerm'];
			$builder = $this->db->table('jabatan')->like('nama_jabatan', $search)->orderBy('nama_jabatan', 'asc');
		}

		$query    = $builder->select('id, nama_jabatan')->get();

		if ($builder->countAllResults(false) >= 1) {

			$options[] = [
				'id' => '',
				'text' => '- Pilih Jabatan -',
			];

			foreach ($query->getResult() as $row) {
				$dataArr = [
					'id' => $row->id,
					'text' => $row->nama_jabatan,
				];
				array_push($options, $dataArr);
			}

			echo json_encode($options);
			return;
		}

		$options    = [
			'id' => '',
			'text' => '- Pilih Jabatan -',
		];
		echo json_encode($options);
		return;
	}
	//--------------------------------------------------------------------

}
