<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\JamKerjaModel;

class JamKerja extends AdminBaseController
{
	protected $JamKerjaModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->JamKerjaModel = new JamKerjaModel();
										
		$this->menuSlug = 'jam-kerja';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		$this->JamKerjaModel->table = 'jam_kerja';
		$input = (object) $this->request->getPost();

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->JamKerjaModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama_jam_kerja;
				$row[] = date('H:i', strtotime($list->jam_masuk));
				$row[] = date('H:i', strtotime($list->jam_istirahat_mulai)) . ' - ' . date('H:i', strtotime($list->jam_istirahat_selesai));
				$row[] = date('H:i', strtotime($list->jam_pulang));
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
				"recordsTotal" => $this->JamKerjaModel->count_all(),
				"recordsFiltered" => $this->JamKerjaModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Jam Kerja',
		];

		return $this->view('pages/admin/jam-kerja/index', $data);
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
			'nama_jam_kerja' => $input->nama_jam_kerja,
			'jam_masuk' => $input->jam_masuk,
			'jam_istirahat_mulai' => $input->jam_istirahat_mulai,
			'jam_istirahat_selesai' => $input->jam_istirahat_selesai,
			'jam_pulang' => $input->jam_pulang,
		];
		$this->db->table('jam_kerja')->insert($data);

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
		$dataDb = $this->JamKerjaModel->where('id', $id)->first();
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
			'nama_jam_kerja' => $input->nama_jam_kerja,
			'jam_masuk' => $input->jam_masuk,
			'jam_istirahat_mulai' => $input->jam_istirahat_mulai,
			'jam_istirahat_selesai' => $input->jam_istirahat_selesai,
			'jam_pulang' => $input->jam_pulang,
		];
		$this->db->table('jam_kerja')->where('id', $id)->update($data);

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
		$dataDb = $this->JamKerjaModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('jam_kerja')->where('id', $id)->delete()) {
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
		if (!$this->validate($this->JamKerjaModel->getValidationRules())) {
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
			$builder = $this->db->table('jam_kerja')->orderBy('jam_masuk', 'asc');
		} else {
			$search = $_POST['searchTerm'];
			$builder = $this->db->table('jam_kerja')->like('jam_masuk', $search)->orderBy('jam_masuk', 'asc');
		}

		$query    = $builder->select('id, jam_masuk')->get();

		if ($builder->countAllResults(false) >= 1) {

			$options[] = [
				'id' => '',
				'text' => '- Pilih Jam Kerja -',
			];

			foreach ($query->getResult() as $row) {
				$dataArr = [
					'id' => $row->id,
					'text' => $row->jam_masuk,
				];
				array_push($options, $dataArr);
			}

			echo json_encode($options);
			return;
		}

		$options    = [
			'id' => '',
			'text' => '- Pilih Jam Kerja -',
		];
		echo json_encode($options);
		return;
	}
	//--------------------------------------------------------------------

}
