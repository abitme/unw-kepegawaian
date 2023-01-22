<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PertanyaanKategoriModel;

class PertanyaanKategori extends AdminBaseController
{
	protected $PertanyaanKategoriModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PertanyaanKategoriModel = new PertanyaanKategoriModel();
				
		$this->menuSlug = 'pertanyaan-kategori';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		$this->PertanyaanKategoriModel->table = 'pertanyaan_kategori';
		$input = (object) $this->request->getPost();

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->PertanyaanKategoriModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->kategori;
				$row[] = $list->nilai_min;
				$row[] = $list->nilai_max;
				$row[] = $list->range_desc;
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
				"recordsTotal" => $this->PertanyaanKategoriModel->count_all(),
				"recordsFiltered" => $this->PertanyaanKategoriModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Kategori Pertanyaan',
			// 'periode' => $this->db->table('periode')->where('is_active', 1)->get()->getRow(),
		];

		return $this->view('pages/admin/pertanyaan-kategori/index', $data);
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
			'kategori' => $input->kategori,
			'nilai_min' => $input->nilai_min,
			'nilai_max' => $input->nilai_max,
			'range_desc' => $input->range_desc,
		];
		$this->db->table('pertanyaan_kategori')->insert($data);

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
		$dataDb = $this->PertanyaanKategoriModel->where('id', $id)->first();
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
			'kategori' => $input->kategori,
			'nilai_min' => $input->nilai_min,
			'nilai_max' => $input->nilai_max,
			'range_desc' => $input->range_desc,
		];
		$this->db->table('pertanyaan_kategori')->where('id', $id)->update($data);

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
		$dataDb = $this->PertanyaanKategoriModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('pertanyaan_kategori')->where('id', $id)->delete()) {
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
		if (!$this->validate($this->PertanyaanKategoriModel->getValidationRules())) {
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
			$builder = $this->db->table('pertanyaan_kategori')->orderBy('kategori', 'asc');
		} else {
			$search = $_POST['searchTerm'];
			$builder = $this->db->table('pertanyaan_kategori')->like('kategori', $search)->orderBy('kategori', 'asc');
		}

		$query    = $builder->select('id, kategori')->get();

		if ($builder->countAllResults(false) >= 1) {

			$options[] = [
				'id' => '',
				'text' => '- Pilih Kategori -',
			];

			foreach ($query->getResult() as $row) {
				$dataArr = [
					'id' => $row->id,
					'text' => $row->kategori,
				];
				array_push($options, $dataArr);
			}

			echo json_encode($options);
			return;
		}

		$options    = [
			'id' => '',
			'text' => '- Pilih Kategori -',
		];
		echo json_encode($options);
		return;
	}
	//--------------------------------------------------------------------

}
