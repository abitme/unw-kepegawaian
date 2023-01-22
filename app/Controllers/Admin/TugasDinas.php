<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\TugasDinasModel;

class TugasDinas extends AdminBaseController
{
	protected $TugasDinasModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->TugasDinasModel = new TugasDinasModel();

		$this->menuSlug = 'tugas-dinas';
		if (!is_allow('', $this->menuSlug) && !is_allow('', 'input-tugas-dinas')) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		$this->TugasDinasModel->table = 'tugas_dinas_view';
		$input = (object) $this->request->getPost();

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->TugasDinasModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				if ($list->id_user) {
					$user = $this->db->table('users')->where('id', $list->id_user)->get()->getRow();
					if ($user->id_pegawai) {
						$pegawai = $this->db->table('pegawai')->where('id', $user->id_pegawai)->get()->getRow();
						$user = $pegawai->nama;
					} else {
						$user = "N/A";
					}
				} else {
					$user = "N/A";
				}
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama;
				$row[] = $list->lumsum ? 'Dapat' : 'Tidak Dapat';
				$row[] = $list->keterangan;
				$row[] = $list->tanggal_awal;
				$row[] = $list->tanggal_akhir;
				$row[] = $user;
				$row[] = $list->created_at ?? "N/A";
				$row[] = '
					<a href="javascript:void(0)" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Edit" onclick="updateData(' . "'" . $list->kode_tugas_dinas . "'" . ')">
						<i class="fas fa-edit"></i>
					</a>
					<a href="javascript:void(0)" class="btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top" title="Delete" onclick="destroyData(' . "'" . $list->kode_tugas_dinas . "'" . ')">
						<i class="fas fa-trash"></i>
					</a>
				';
				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->TugasDinasModel->count_all(),
				"recordsFiltered" => $this->TugasDinasModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Tugas Dinas',
			// 'periode' => $this->db->table('periode')->where('is_active', 1)->get()->getRow(),
		];

		return $this->view('pages/admin/tugas-dinas/index', $data);
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
			$check = $this->db->table('tugas_dinas')->where('id_pegawai', $input->id_pegawai)->where('tanggal', $date->format('Y-m-d'))->get()->getRow();
			$nama = $this->db->table('pegawai')->select('nama')->where('id', $input->id_pegawai)->get()->getRow()->nama;
			if ($check) {
				$response = [
					'status' => 500,
					'message' => "Surat tugas dengan nama $nama dan hari {$date->format('Y-m-d')} sudah diinput",
					'data' =>  $input,
				];
				echo json_encode($response);
				return;
			}
			// save data
			$arrData = [
				'id_user' => $this->user_id,
				'id_pegawai' => $input->id_pegawai,
				'kode_tugas_dinas' => $uniqId,
				'lumsum' => isset($input->lumsum) ? 1 : 0,
				'keterangan' => $input->keterangan,
				'tanggal' => $date->format('Y-m-d'),
				'created_at' => \date('Y-m-d H:i:s'),
			];
			\array_push($data, $arrData);
		}
		$this->db->table('tugas_dinas')->insertBatch($data);

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

	public function update($kode_tugas_dinas)
	{
		$dataDb = $this->db->table('tugas_dinas_view')->where('kode_tugas_dinas', $kode_tugas_dinas)->get()->getRow();
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
			$check = $this->db->table('tugas_dinas')->where('kode_tugas_dinas !=', $kode_tugas_dinas)->where('id_pegawai', $input->id_pegawai)->where('tanggal', $date->format('Y-m-d'))->get()->getRow();
			$nama = $this->db->table('pegawai')->select('nama')->where('id', $input->id_pegawai)->get()->getRow()->nama;
			if ($check) {
				$response = [
					'status' => 500,
					'message' => "Surat tugas dengan nama $nama dan hari {$date->format('Y-m-d')} sudah diinput",
					'data' =>  $input,
				];
				echo json_encode($response);
				return;
			}
			// delete before insert batch
			$this->db->table('tugas_dinas')->where('kode_tugas_dinas', $kode_tugas_dinas)->delete();
			// save data
			$arrData = [
				'id_user' => $this->user_id,
				'id_pegawai' => $input->id_pegawai,
				'kode_tugas_dinas' => $uniqId,
				'lumsum' => isset($input->lumsum) ? 1 : 0,
				'keterangan' => $input->keterangan,
				'tanggal' => $date->format('Y-m-d'),
				'created_at' => \date('Y-m-d H:i:s'),
			];
			\array_push($data, $arrData);
		}
		$this->db->table('tugas_dinas')->insertBatch($data);

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

	public function delete($kode_tugas_dinas)
	{
		$dataDb = $this->db->table('tugas_dinas_view')->where('kode_tugas_dinas', $kode_tugas_dinas)->get()->getRow();
		if (!$this->__checkDataExist($dataDb)) {
			return;
		}

		// delete data && send response
		if ($this->db->table('tugas_dinas')->where('kode_tugas_dinas', $kode_tugas_dinas)->delete()) {
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
		if (!$this->validate($this->TugasDinasModel->getValidationRules())) {
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
