<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\AbsensiModel;

class Absensi extends AdminBaseController
{
	protected $AbsensiModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->AbsensiModel = new AbsensiModel();

		$this->menuSlug = 'absensi';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		$this->AbsensiModel->table = 'absensi_view';
		$input = (object) $this->request->getPost();

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->AbsensiModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama;
				$row[] = $list->jenis_absensi;
				$row[] = $list->keterangan;
				$row[] = \date('d-m-Y', \strtotime($list->tanggal_awal));
				$row[] = \date('d-m-Y', \strtotime($list->tanggal_akhir));
				$row[] = '
					<a href="javascript:void(0)" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Edit" onclick="updateData(' . "'" . $list->kode_absensi . "'" . ')">
						<i class="fas fa-edit"></i>
					</a>
					<a href="javascript:void(0)" class="btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top" title="Delete" onclick="destroyData(' . "'" . $list->kode_absensi . "'" . ')">
						<i class="fas fa-trash"></i>
					</a>
				';
				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->AbsensiModel->count_all(),
				"recordsFiltered" => $this->AbsensiModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Absensi',
			// 'periode' => $this->db->table('periode')->where('is_active', 1)->get()->getRow(),
		];

		return $this->view('pages/admin/absensi/index', $data);
	}

	public function create()
	{
		if (!$this->__validate()) {
			return;
		}

		// set input data
		$input = (object) $this->request->getPost();

		$begin = new \DateTime($input->tanggal_awal);
		$end = new \DateTime("$input->tanggal_akhir +1 DAY");
		$daterange = new \DatePeriod($begin, new \DateInterval('P1D'), $end);
		$uniqId = \uniqid();
		$data = [];
		foreach ($daterange as $date) {
			$check = $this->db->table('absensi')->where('id_pegawai', $input->id_pegawai)->where('tanggal', $date->format('Y-m-d'))->get()->getRow();
			$nama = $this->db->table('pegawai')->select('nama')->where('id', $input->id_pegawai)->get()->getRow()->nama;
			if ($check) {
				$response = [
					'status' => 500,
					'message' => "Absensi dengan nama $nama dan hari {$date->format('Y-m-d')} sudah diinput",
					'data' =>  $input,
				];
				echo json_encode($response);
				return;
			}
			// save data
			$arrData = [
				'id_pegawai' => $input->id_pegawai,
				'kode_absensi' => $uniqId,
				'jenis_absensi' => $input->jenis_absensi,
				'keterangan' => $input->keterangan,
				'tanggal' => $date->format('Y-m-d'),
			];
			\array_push($data, $arrData);
		}
		$this->db->table('absensi')->insertBatch($data);

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

	public function update($kode_absensi)
	{
		$dataDb = $this->db->table('absensi_view')->where('kode_absensi', $kode_absensi)->get()->getRow();
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

		// insert
		$begin = new \DateTime($input->tanggal_awal);
		$end = new \DateTime("$input->tanggal_akhir +1 DAY");
		$daterange = new \DatePeriod($begin, new \DateInterval('P1D'), $end);
		$uniqId = \uniqid();
		$data = [];
		foreach ($daterange as $date) {
			$check = $this->db->table('absensi')->where('kode_absensi !=', $kode_absensi)->where('id_pegawai', $input->id_pegawai)->where('tanggal', $date->format('Y-m-d'))->get()->getRow();
			$nama = $this->db->table('pegawai')->select('nama')->where('id', $input->id_pegawai)->get()->getRow()->nama;
			if ($check) {
				$response = [
					'status' => 500,
					'message' => "Absensi dengan nama $nama dan hari {$date->format('Y-m-d')} sudah diinput",
					'data' =>  $input,
				];
				echo json_encode($response);
				return;
			}
			// delete before insert batch
			$this->db->table('absensi')->where('kode_absensi', $kode_absensi)->delete();
			// save data
			$arrData = [
				'id_pegawai' => $input->id_pegawai,
				'kode_absensi' => $uniqId,
				'jenis_absensi' => $input->jenis_absensi,
				'keterangan' => $input->keterangan,
				'tanggal' => $date->format('Y-m-d'),
			];
			\array_push($data, $arrData);
		}
		$this->db->table('absensi')->insertBatch($data);

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

	public function delete($kode_absensi)
	{
		$dataDb = $this->db->table('absensi_view')->where('kode_absensi', $kode_absensi)->get()->getRow();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('absensi')->where('kode_absensi', $kode_absensi)->delete()) {
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
		if (!$this->validate($this->AbsensiModel->getValidationRules())) {
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
