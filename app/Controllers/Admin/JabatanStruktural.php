<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\JabatanStrukturalModel;

class JabatanStruktural extends AdminBaseController
{
	protected $JabatanStrukturalModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->JabatanStrukturalModel = new JabatanStrukturalModel();
				
		$this->menuSlug = 'jabatan-struktural';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		$this->JabatanStrukturalModel->table = 'jabatan_struktural';
		$input = (object) $this->request->getPost();

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->JabatanStrukturalModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
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
				"recordsTotal" => $this->JabatanStrukturalModel->count_all(),
				"recordsFiltered" => $this->JabatanStrukturalModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Jabatan Struktural',
			// 'periode' => $this->db->table('periode')->where('is_active', 1)->get()->getRow(),
		];

		return $this->view('pages/admin/jabatan-struktural/index', $data);
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
			'nama_jabatan_struktural' => $input->nama_jabatan_struktural,
		];
		$this->db->table('jabatan_struktural')->insert($data);

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
		$dataDb = $this->JabatanStrukturalModel->where('id', $id)->first();
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
			'nama_jabatan_struktural' => $input->nama_jabatan_struktural,
		];
		$this->db->table('jabatan_struktural')->where('id', $id)->update($data);

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
		$dataDb = $this->JabatanStrukturalModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('jabatan_struktural')->where('id', $id)->delete()) {
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
		if (!$this->validate($this->JabatanStrukturalModel->getValidationRules())) {
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
			$builder = $this->db->table('jabatan_struktural')->orderBy('nama_jabatan_struktural', 'asc');
		} else {
			$search = $_POST['searchTerm'];
			$builder = $this->db->table('jabatan_struktural')->like('nama_jabatan_struktural', $search)->orderBy('nama_jabatan_struktural', 'asc');
		}

		$query    = $builder->select('id, nama_jabatan_struktural')->get();

		if ($builder->countAllResults(false) >= 1) {

			$options[] = [
				'id' => '',
				'text' => '- Pilih Jabatan Struktural -',
			];

			foreach ($query->getResult() as $row) {
				$dataArr = [
					'id' => $row->id,
					'text' => $row->nama_jabatan_struktural,
				];
				array_push($options, $dataArr);
			}

			echo json_encode($options);
			return;
		}

		$options    = [
			'id' => '',
			'text' => '- Pilih Jabatan Struktural -',
		];
		echo json_encode($options);
		return;
	}
	//--------------------------------------------------------------------

}
