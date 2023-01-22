<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\JadwalKerjaAutoModel;

class JadwalKerjaAuto extends AdminBaseController
{
	protected $JadwalKerjaAutoModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->JadwalKerjaAutoModel = new JadwalKerjaAutoModel();

		$this->menuSlug = 'jadwal-kerja-auto';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		$this->JadwalKerjaAutoModel->table = 'jadwal_kerja_auto';

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->JadwalKerjaAutoModel->get_datatables();
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
				"recordsTotal" => $this->JadwalKerjaAutoModel->count_all(),
				"recordsFiltered" => $this->JadwalKerjaAutoModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Jadwal Kerja Auto',
			'optionsJamKerja' => getDropdownList('jam_kerja', ['id', 'nama_jam_kerja'], '', '- Pilih Jam Kerja -', 'id', 'asc', ''),
		];

		return $this->view('pages/admin/jadwal-kerja-auto/index', $data);
	}

	public function show($id)
	{
		$data['item'] =  $this->db->table('jadwal_kerja_auto')->get()->getRow();
		$data['itemDetail'] =  $this->db->table('jadwal_kerja_auto_detail_view')->where('id_jadwal_kerja_auto', $id)->get()->getResult();

		if (!$this->__checkDataExist($data)) {
			return;
		}

		// $jadwal = $dataDb;
		// $jadwalDetail = $this->db->table('jadwal_kerja_auto_detail')->where('id_jadwal_kerja_auto', $jadwal['id'])->get()->getResultArray();
		// $content = \array_merge($jadwal, $jadwalDetail);

		// send response
		if ($data) {
			$response = [
				'status' => 200,
				'message' => 'Data berhasil diambil',
				'data' =>  $data,
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
		// set input data & validate
		$input = (object) $this->request->getPost();
		if (!$this->__validate($input)) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();

		// insert jadwal_kerja_auto
		$dataJadwalKerjaAuto = [
			'nama_jadwal_kerja' => $input->nama_jadwal_kerja,
			// 'is_default' => isset($input->is_default) ? 1 : 0,
		];
		$this->db->table('jadwal_kerja_auto')->insert($dataJadwalKerjaAuto);
		$idJadwalKerjaAuto = $this->db->insertID();

		// set array jadwal_kerja_auto_detail
		$input = $this->request->getPost();
		foreach ($input as $key => $val) {
			$i = 0;
			// get input data to array for insert/update batch
			if (is_array($val)) {
				foreach ($val as $k => $v) {
					if ($key == 'id_jam_kerja') {
						$arrJadwalKerjaAutoDetail[$i][$key] = !empty($v) ? $v : null;
					}
					$i++;
				}
			}
		}

		// insert jadwal_kerja_auto_detail
		$dataJadwalKerjaAutoDetail = [];
		foreach ($arrJadwalKerjaAutoDetail as $row) {
			// merge
			$result = array_merge($row, ['id_jadwal_kerja_auto' => $idJadwalKerjaAuto]);
			// push
			array_push($dataJadwalKerjaAutoDetail, $result);
		}
		$this->db->table('jadwal_kerja_auto_detail')->insertBatch($dataJadwalKerjaAutoDetail);

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
		$data['item'] =  $this->db->table('jadwal_kerja_auto')->get()->getRow();
		$data['itemDetail'] =  $this->db->table('jadwal_kerja_auto_detail')->where('id_jadwal_kerja_auto', $id)->get()->getResult();

		if (!$this->__checkDataExist($data)) {
			return;
		}

		// $dataDb = $this->db->table('jadwal_kerja_auto')->where('id', $id)->get()->getRowArray();
		// $jadwal = $dataDb;
		// $jadwalDetail = $this->db->table('jadwal_kerja_auto_detail')->where('id_jadwal_kerja_auto', $jadwal['id'])->get()->getResultArray();
		// $content = \array_merge($jadwal, $jadwalDetail);

		if (!$_POST) {
			// repoulate form
			$input = $data;
			echo json_encode($input);
			return;
		}

		// set input data & validate
		$input = (object) $this->request->getPost();
		if (!$this->__validate($input)) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();

		// update jadwal_kerja_auto
		$dataJadwalKerjaAuto = [
			'nama_jadwal_kerja' => $input->nama_jadwal_kerja,
			// 'is_default' => isset($input->is_default) ? 1 : 0,
		];
		$this->db->table('jadwal_kerja_auto')->where('id', $id)->update($dataJadwalKerjaAuto);

		// delete diff id
		$input = (object) $this->request->getPost();
		$idItemDb  = array_column($this->db->table('jadwal_kerja_auto_detail')->select('id_jam_kerja')->where('id_jadwal_kerja_auto', $id)->get()->getResult(), 'id_jam_kerja');
		$idItemInput =  $input->id_jam_kerja;
		$idDelete = array_diff($idItemDb, $idItemInput);
		if (!empty($idDelete)) {
			$this->db->table('jadwal_kerja_auto_detail')->whereIn('id_jam_kerja', $idDelete)->delete();
		}

		// set array jadwal_kerja_auto_detail
		$input = $this->request->getPost();
		foreach ($input as $key => $val) {
			$i = 0;
			// get input data to array for insert/update batch
			if (is_array($val)) {
				foreach ($val as $k => $v) {
					if ($key == 'id_jadwal_kerja_auto_detail') {
						$arrJadwalKerjaAutoDetail[$i]['id'] = $v;
					}
					if ($key == 'id_jam_kerja') {
						$arrJadwalKerjaAutoDetail[$i][$key] = !empty($v) ? $v : null;
					}
					$i++;
				}
			}
		}

		// update jadwal_kerja_auto_detail
		$dataJadwalKerjaAutoDetail = [];
		foreach ($arrJadwalKerjaAutoDetail as $row) {
			// merge
			$result = array_merge($row, ['id_jadwal_kerja_auto' => $id]);
			// push
			array_push($dataJadwalKerjaAutoDetail, $result);
		}
		$this->db->table('jadwal_kerja_auto_detail')->updateBatch($dataJadwalKerjaAutoDetail, 'id_jam_kerja');

		// insert new jadwal_kerja_auto_detail
		$arrtemInsert = [];
		foreach ($input as $key => $val) {
			$i = 0;
			if (is_array($val)) {
				foreach ($val as $k => $v) {
					if ($key == 'id_jam_kerja' && !in_array($v, $idItemDb)) {
						$arrtemInsert[$i][$key] = $v;
					}
					$i++;
				}
			}
		}
		$datatemInsert = [];
		foreach ($arrtemInsert as $row) {
			// merge
			$result = array_merge($row, ['id_jadwal_kerja_auto' => $id]);
			// push
			array_push($datatemInsert, $result);
		}
		if (!empty($datatemInsert)) {
			$this->db->table('jadwal_kerja_auto_detail')->insertBatch($datatemInsert);
		}

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
		$dataDb = $this->db->table('jadwal_kerja_auto')->where('id', $id)->get()->getRow();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('jadwal_kerja_auto')->where('id', $id)->delete()) {
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

	private function __validate($input = '')
	{
		// validate and set error message
		if (!$this->validate($this->JadwalKerjaAutoModel->rules())) {
			$validation = \Config\Services::validation();
			$response = [
				'status' => false,
				'message' => 'Error form validation',
				'data' =>  [
					'errors' => $validation->getErrors(),
					'input' => $input
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
			$builder = $this->db->table('jadwal_kerja_auto')->orderBy('nama_jadwal_kerja', 'asc');
		} else {
			$search = $_POST['searchTerm'];
			$builder = $this->db->table('jadwal_kerja_auto')->like('nama_jadwal_kerja', $search)->orderBy('nama_jadwal_kerja', 'asc');
		}

		$query    = $builder->select('id, nama_jadwal_kerja')->get();

		if ($builder->countAllResults(false) >= 1) {

			$options[] = [
				'id' => '',
				'text' => '- Pilih Jadwal Kerja Auto -',
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
			'text' => '- Pilih Jadwal Kerja Auto -',
		];
		echo json_encode($options);
		return;
	}
	//--------------------------------------------------------------------

}
