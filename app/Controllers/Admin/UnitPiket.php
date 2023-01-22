<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\UnitPiketModel;

class UnitPiket extends AdminBaseController
{
	protected $UnitPiketModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->UnitPiketModel = new UnitPiketModel();

		$this->menuSlug = 'unit-piket';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		$this->UnitPiketModel->table = 'unit_piket_view';

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->UnitPiketModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama_unit;
				$row[] = array_map("dayFromNumber", explode(';', $list->day));
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
				"recordsTotal" => $this->UnitPiketModel->count_all(),
				"recordsFiltered" => $this->UnitPiketModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Unit Piket',
		];

		return $this->view('pages/admin/unit-piket/index', $data);
	}

	public function show($id)
	{
		$dataDb = $this->db->table('unit_piket')->where('id', $id)->get()->getRowArray();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		$jadwal = $dataDb;
		$jadwalDetail = $this->db->table('unit_piket_detail')->where('id_unit_piket', $jadwal['id'])->get()->getResultArray();
		$content = \array_merge($jadwal, $jadwalDetail);

		// send response
		if ($jadwal) {
			$response = [
				'status' => 200,
				'message' => 'Data berhasil diambil',
				'data' =>  $content,
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

	public function create()
	{
		if (!$this->__validate()) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();

		// insert unit_piket
		$dataUnitPiket = [
			'id_unit' => $input->id_unit,
			'day' => \implode(';', \array_keys($input->piket)),
		];
		$this->db->table('unit_piket')->insert($dataUnitPiket);

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
		$dataDb = $this->db->table('unit_piket')->where('id', $id)->get()->getRow();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		if (!$_POST) {
			// repoulate form
			$input = $dataDb;
			$input->piket = \explode(';', $dataDb->day);
			echo json_encode($input);
			return;
		}

		if (!$this->__validate()) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();

		// update unit_piket
		$dataUnitPiket = [
			'id_unit' => $input->id_unit,
			'day' => \implode(';', \array_keys($input->piket)),
		];
		$this->db->table('unit_piket')->where('id', $id)->update($dataUnitPiket);

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
		$dataDb = $this->db->table('unit_piket')->where('id', $id)->get()->getRow();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('unit_piket')->where('id', $id)->delete()) {
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
		if (!$this->validate($this->UnitPiketModel->rules())) {
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
