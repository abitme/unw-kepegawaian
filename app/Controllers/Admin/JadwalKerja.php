<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\JadwalKerjaModel;

class JadwalKerja extends AdminBaseController
{
	protected $JadwalKerjaModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->JadwalKerjaModel = new JadwalKerjaModel();

		$this->menuSlug = 'jadwal-kerja';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		$this->JadwalKerjaModel->table = 'jadwal_kerja';

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->JadwalKerjaModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama_jadwal_kerja;
				$row[] = '
					<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="viewData(' . "'" . $list->id . "'" . ')">
						<i class="fas fa-eye"></i> View
					</a>
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
				"recordsTotal" => $this->JadwalKerjaModel->count_all(),
				"recordsFiltered" => $this->JadwalKerjaModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Jadwal Kerja',
		];

		return $this->view('pages/admin/jadwal-kerja/index', $data);
	}

	public function show($id)
	{
		$dataDb = $this->db->table('jadwal_kerja')->where('id', $id)->get()->getRowArray();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		$jadwal = $dataDb;
		$jadwalDetail = $this->db->table('jadwal_kerja_detail')->where('id_jadwal_kerja', $jadwal['id'])->get()->getResultArray();
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

		// insert jadwal_kerja
		$dataJadwalKerja = [
			'nama_jadwal_kerja' => $input->nama_jadwal_kerja,
			'is_default' => isset($input->is_default) ? 1 : 0,
		];
		$this->db->table('jadwal_kerja')->insert($dataJadwalKerja);
		$idJadwalKerja = $this->db->insertID();

		// set array jadwal_kerja_detail
		$input = $this->request->getPost();
		foreach ($input as $key => $val) {
			$i = 0;
			// get input data to array for insert/update batch
			if (is_array($val)) {
				foreach ($val as $k => $v) {
					if ($key == 'id_jam_kerja') {
						$arrJadwalKerjaDetail[$i][$key] = !empty($v) ? $v : null;
					}
					if ($key == 'day') {
						$arrJadwalKerjaDetail[$i][$key] = $v;
						$arrJadwalKerjaDetail[$i]['libur'] = isset($input['libur'][$i]) ? 1 : 0;
					}
					$i++;
				}
			}
		}

		// insert jadwal_kerja_detail
		$dataJadwalKerjaDetail = [];
		foreach ($arrJadwalKerjaDetail as $row) {
			// merge
			$result = array_merge($row, ['id_jadwal_kerja' => $idJadwalKerja]);
			// push
			array_push($dataJadwalKerjaDetail, $result);
		}
		$this->db->table('jadwal_kerja_detail')->insertBatch($dataJadwalKerjaDetail);

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
		$dataDb = $this->db->table('jadwal_kerja')->where('id', $id)->get()->getRowArray();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		$jadwal = $dataDb;
		$jadwalDetail = $this->db->table('jadwal_kerja_detail')->where('id_jadwal_kerja', $jadwal['id'])->get()->getResultArray();
		$content = \array_merge($jadwal, $jadwalDetail);

		if (!$_POST) {
			// repoulate form
			$input = $content;
			echo json_encode($input);
			return;
		}

		if (!$this->__validate()) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();

		// update jadwal_kerja
		$dataJadwalKerja = [
			'nama_jadwal_kerja' => $input->nama_jadwal_kerja,
			'is_default' => isset($input->is_default) ? 1 : 0,
		];
		$this->db->table('jadwal_kerja')->where('id', $id)->update($dataJadwalKerja);

		// set array jadwal_kerja_detail
		$input = $this->request->getPost();
		foreach ($input as $key => $val) {
			$i = 0;
			// get input data to array for insert/update batch
			if (is_array($val)) {
				foreach ($val as $k => $v) {
					if ($key == 'id_jadwal_kerja_detail') {
						$arrJadwalKerjaDetail[$i]['id'] = $v;
					}
					if ($key == 'id_jam_kerja') {
						$arrJadwalKerjaDetail[$i][$key] = !empty($v) && !isset($input['libur'][$i]) ? $v : null;
					}
					if ($key == 'day') {
						$arrJadwalKerjaDetail[$i][$key] = $v;
						$arrJadwalKerjaDetail[$i]['libur'] = isset($input['libur'][$i]) ? 1 : 0;
					}
					$i++;
				}
			}
		}

		// update jadwal_kerja_detail
		$dataJadwalKerjaDetail = [];
		foreach ($arrJadwalKerjaDetail as $row) {
			// merge
			$result = array_merge($row, ['id_jadwal_kerja' => $id]);
			// push
			array_push($dataJadwalKerjaDetail, $result);
		}
		$this->db->table('jadwal_kerja_detail')->updateBatch($dataJadwalKerjaDetail, 'id');

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
		$dataDb = $this->db->table('jadwal_kerja')->where('id', $id)->get()->getRow();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('jadwal_kerja')->where('id', $id)->delete()) {
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
		if (!$this->validate($this->JadwalKerjaModel->rules())) {
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
			$builder = $this->db->table('jadwal_kerja')->orderBy('nama_jadwal_kerja', 'asc');
		} else {
			$search = $_POST['searchTerm'];
			$builder = $this->db->table('jadwal_kerja')->like('nama_jadwal_kerja', $search)->orderBy('nama_jadwal_kerja', 'asc');
		}

		$query    = $builder->select('id, nama_jadwal_kerja')->get();

		if ($builder->countAllResults(false) >= 1) {

			$options[] = [
				'id' => '',
				'text' => '- Pilih Jadwal Kerja -',
			];

			foreach ($query->getResult() as $row) {
				$dataArr = [
					'id' => $row->id,
					'text' => $row->nama_jadwal_kerja,
				];
				array_push($options, $dataArr);
			}

			echo json_encode($options);
			return;
		}

		$options    = [
			'id' => '',
			'text' => '- Pilih Jadwal Kerja -',
		];
		echo json_encode($options);
		return;
	}
	//--------------------------------------------------------------------

}
