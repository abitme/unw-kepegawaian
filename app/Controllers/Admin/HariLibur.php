<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\HariLiburModel;

class HariLibur extends AdminBaseController
{
	protected $HariLiburModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->HariLiburModel = new HariLiburModel();

		$this->menuSlug = 'hari-libur';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		$this->HariLiburModel->table = 'hari_libur_view';
		$input = (object) $this->request->getPost();

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->HariLiburModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");

			foreach ($lists as $list) {
				$tanggalAwal =  \date('d-m-Y', \strtotime($list->tanggal_awal));
				$tanggalAkhir = \date('d-m-Y', \strtotime($list->tanggal_akhir));
				$no++;
				$unitPiketArr = \explode(';', $list->id_unit_piket);
				$unitPiket = $this->db->table('unit')->select('id, nama_unit')->havingIn('id', $unitPiketArr)->get()->getResultArray();
				$row = [];
				$row[] = $no;
				$row[] = $list->keterangan;
				$row[] = $tanggalAwal != $tanggalAkhir ? "$tanggalAwal - $tanggalAkhir" : $tanggalAwal;
				$row[] = implode(', ', array_column($unitPiket, 'nama_unit'));
				$row[] = '
					<a href="javascript:void(0)" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Edit" onclick="updateData(' . "'" . $list->kode_unik . "'" . ')">
						<i class="fas fa-edit"></i>
					</a>
					<a href="javascript:void(0)" class="btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top" title="Delete" onclick="destroyData(' . "'" . $list->kode_unik . "'" . ')">
						<i class="fas fa-trash"></i>
					</a>
				';
				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->HariLiburModel->count_all(),
				"recordsFiltered" => $this->HariLiburModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Hari Libur',
		];

		return $this->view('pages/admin/hari-libur/index', $data);
	}

	public function create()
	{
		if (!$this->__validate()) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();

		// save data
		$begin = new \DateTime($input->tanggal_awal);
		$end = new \DateTime("$input->tanggal_akhir +1 DAY");
		$daterange = new \DatePeriod($begin, new \DateInterval('P1D'), $end);
		$uniqId = \uniqid();
		$data = [];
		foreach ($daterange as $date) {
			// save data
			$arrData = [
				'kode_unik' => $uniqId,
				'tanggal' => $date->format('Y-m-d'),
				'keterangan' => $input->keterangan,
				'id_unit_piket' => implode(';', $input->id_unit_piket),
			];
			\array_push($data, $arrData);
		}
		$this->db->table('hari_libur')->insertBatch($data);

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

	public function update($kode_unik)
	{
		$dataDb = $this->db->table('hari_libur_view')->where('kode_unik', $kode_unik)->get()->getRow();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		if (!$_POST) {
			// repoulate form
			$input = $dataDb;

			$unitPiketArr = \explode(';', $dataDb->id_unit_piket);
			$unitPiket = $this->db->table('unit')->select('id, nama_unit')->havingIn('id', $unitPiketArr)->get()->getResultArray();

			$input->id_unit_piket = $unitPiket;
			echo json_encode($input);
			return;
		}


		if (!$this->__validate()) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();

		// delete
		$this->db->table('hari_libur')->where('kode_unik', $kode_unik)->delete();

		// save data
		$begin = new \DateTime($input->tanggal_awal);
		$end = new \DateTime("$input->tanggal_akhir +1 DAY");
		$daterange = new \DatePeriod($begin, new \DateInterval('P1D'), $end);
		$uniqId = \uniqid();
		$data = [];
		foreach ($daterange as $date) {
			// save data
			$arrData = [
				'kode_unik' => $uniqId,
				'tanggal' => $date->format('Y-m-d'),
				'keterangan' => $input->keterangan,
				'id_unit_piket' => implode(';', $input->id_unit_piket),
			];
			\array_push($data, $arrData);
		}
		$this->db->table('hari_libur')->insertBatch($data);

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

	public function delete($kode_unik)
	{
		$dataDb = $this->db->table('hari_libur_view')->where('kode_unik', $kode_unik)->get()->getRow();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('hari_libur')->where('kode_unik', $kode_unik)->delete()) {
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
		if (!$this->validate($this->HariLiburModel->getValidationRules())) {
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
			$builder = $this->db->table('hari_libur')->orderBy('tanggal', 'asc');
		} else {
			$search = $_POST['searchTerm'];
			$builder = $this->db->table('hari_libur')->like('tanggal', $search)->orderBy('tanggal', 'asc');
		}

		$query    = $builder->select('id, tanggal')->get();

		if ($builder->countAllResults(false) >= 1) {

			$options[] = [
				'id' => '',
				'text' => '- Pilih Jabatan Struktural -',
			];

			foreach ($query->getResult() as $row) {
				$dataArr = [
					'id' => $row->id,
					'text' => $row->tanggal,
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
