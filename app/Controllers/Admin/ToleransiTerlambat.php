<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\ToleransiTerlambatModel;

class ToleransiTerlambat extends AdminBaseController
{
	protected $ToleransiTerlambatModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->ToleransiTerlambatModel = new ToleransiTerlambatModel();

		$this->menuSlug = 'toleransi-terlambat';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		$this->ToleransiTerlambatModel->table = 'toleransi_terlambat';
		$input = (object) $this->request->getPost();

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->ToleransiTerlambatModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama_toleransi_terlambat;
				$row[] = \date('d-m-Y', \strtotime($list->tanggal_mulai));
				$row[] = $list->tanggal_selesai != null ? \date('d-m-Y', \strtotime($list->tanggal_selesai)) : '-';
				$row[] = date('H:i:s', strtotime($list->durasi_toleransi));
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
				"recordsTotal" => $this->ToleransiTerlambatModel->count_all(),
				"recordsFiltered" => $this->ToleransiTerlambatModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Toleransi Terlambat',
		];

		return $this->view('pages/admin/toleransi-terlambat/index', $data);
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
			'nama_toleransi_terlambat' => $input->nama_toleransi_terlambat,
			'tanggal_mulai' => $input->tanggal_mulai,
			'tanggal_selesai' => !empty($input->tanggal_selesai) ? $input->tanggal_selesai : null,
			'durasi_toleransi' => $input->durasi_toleransi,
		];
		$this->db->table('toleransi_terlambat')->insert($data);

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
		$dataDb = $this->ToleransiTerlambatModel->where('id', $id)->first();
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
			'nama_toleransi_terlambat' => $input->nama_toleransi_terlambat,
			'tanggal_mulai' => $input->tanggal_mulai,
			'tanggal_selesai' => $input->tanggal_selesai ?? null,
			'durasi_toleransi' => $input->durasi_toleransi,
		];
		$this->db->table('toleransi_terlambat')->where('id', $id)->update($data);

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
		$dataDb = $this->ToleransiTerlambatModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('toleransi_terlambat')->where('id', $id)->delete()) {
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
		if (!$this->validate($this->ToleransiTerlambatModel->getValidationRules())) {
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
			$builder = $this->db->table('toleransi_terlambat')->orderBy('jam_masuk', 'asc');
		} else {
			$search = $_POST['searchTerm'];
			$builder = $this->db->table('toleransi_terlambat')->like('jam_masuk', $search)->orderBy('jam_masuk', 'asc');
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
