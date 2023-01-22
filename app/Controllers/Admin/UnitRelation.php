<?php

namespace App\Controllers\Admin;

use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\UnitRelationModel;

class UnitRelation extends AdminBaseController
{
	protected $UnitRelationModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->UnitRelationModel = new UnitRelationModel();
	}

	public function ajax_list()
	{
		$input = (object) $this->request->getPost();

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->UnitRelationModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->parent_name;
				$row[] = $list->child_name;
				$row[] = $list->depth;
				$row[] = '
					<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="destroyDataUnitRelations(' . "'" . $list->id . "'" . ')">
						<i class="fas fa-trash"></i>
					</a>
				';
				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->UnitRelationModel->count_all(),
				"recordsFiltered" => $this->UnitRelationModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Unit Relation',
			// 'periode' => $this->db->table('periode')->where('is_active', 1)->get()->getRow(),
		];

		return $this->view('pages/admin/unit-relation/index', $data);
	}

	public function create()
	{
		if (!$this->__validate()) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();

		// check link relations already exist
		if ($this->db->table('unit_relations')->where('parent', $input->parent)->where('child', $input->child)->countAllResults() > 0) {
			$response = [
				'status' => 502,
				'message' => 'Relasi tersebut sudah ada',
			];

			echo json_encode($response);
			return;
		}

		// save unit
		$this->db->query("insert into unit_relations(parent, child, depth)
		select p.parent, c.child, p.depth+c.depth+1
		from unit_relations p, unit_relations c
		where p.child=$input->parent and c.parent=$input->child");

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

	public function delete($id)
	{
		$dataDb = $this->UnitRelationModel->where('id', $id)->first();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data & send response
		if ($this->db->table('unit_relations')->where('id', $id)->delete()) {
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
		if (!$this->validate($this->UnitRelationModel->getValidationRules())) {
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
			$builder = $this->db->table('unit')->orderBy('nama_unit', 'asc');
		} else {
			$search = $_POST['searchTerm'];
			$builder = $this->db->table('unit')->like('nama_unit', $search)->orderBy('nama_unit', 'asc');
		}

		$query    = $builder->select('id, nama_unit')->get();

		if ($builder->countAllResults(false) >= 1) {

			$options[] = [
				'id' => '',
				'text' => '- Pilih Unit -',
			];

			foreach ($query->getResult() as $row) {
				$dataArr = [
					'id' => $row->id,
					'text' => $row->nama_unit,
				];
				array_push($options, $dataArr);
			}

			echo json_encode($options);
			return;
		}

		$options    = [
			'id' => '',
			'text' => '- Pilih Unit -',
		];
		echo json_encode($options);
		return;
	}
	//--------------------------------------------------------------------

}
