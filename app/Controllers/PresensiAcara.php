<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PresensiAcaraModel;
use DateTime;

class PresensiAcara extends BaseController
{
	protected $PresensiAcaraModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PresensiAcaraModel = new PresensiAcaraModel();
		$user = $this->db->table('users')->select('id_pegawai')->select('id, id_pegawai')->where('id', session('user_id'))->get()->getRow();
		$this->pegawai = '';
		if ($user) {
			$pegawai = $this->db->table('pegawai')->select('nik, nama')->where('id', $user->id_pegawai)->get()->getRow();
			$this->pegawai = $pegawai ?? '';
		}
	}

	public function ajax_list()
	{
		$this->PresensiAcaraModel->table = 'presensi_acara_view';
		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->PresensiAcaraModel->get_datatables();
			$countAll = $this->PresensiAcaraModel->count_all();
			$countFiltered = $this->PresensiAcaraModel->count_filtered();

			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {

				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama;
				$row[] = $list->nama_acara;
				$row[] = $list->waktu;

				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $countAll,
				"recordsFiltered" => $countFiltered,
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title'     => 'Presensi Acara',
			'validation' => \Config\Services::validation(),
		];
		if ($this->pegawai) {
			$data['nik'] = $this->pegawai->nik;
			$data['nama'] = $this->pegawai->nama;
		}
		return $this->view('pages/presensi-acara/index', $data);
	}

	public function data()
	{
		$data = [
			'title'     => 'Data Presensi Acara Pegawai',
		];

		return $this->view('pages/presensi-acara/data', $data);
	}

	public function create()
	{
		// set input data
		$input = (object) $this->request->getPost();
		$now = \date('Y-m-d H:i:s');

		// validate and set error message
		if (!$this->validate($this->PresensiAcaraModel->rulesPresensiAcara())) {
			$response = [
				'status' => 400,
				'message' => 'NIK Invalid',
			];
			echo json_encode($response);
			return;
		}

		$pegawai = $this->db->table('pegawai')->select('id')->where('nik', $input->nik)->get()->getRow();
		$acara = $this->db->table('acara')->where('barcode', \trim($input->barcode))->get()->getRow();
		// cek barcode acara
		if (!$acara) {
			$response = [
				'status' => 400,
				'message' => 'Barcode Invalid',
			];
			echo json_encode($response);
			return;
		}

		// cek sudah presensi
		$presensiAcara = $this->db->table('presensi_acara_view')->where('id_pegawai', $pegawai->id)->where('id_acara', $acara->id)->get()->getRow();
		if ($presensiAcara) {
			$response = [
				'status' => 200,
				'message' => 'Sudah melakukan presensi',
			];
			echo json_encode($response);
			return;
		}

		// save data
		$data = [
			'id_pegawai' => $pegawai->id,
			'id_acara' => $acara->id,
			'waktu' => $now,
		];
		$this->db->table('presensi_acara')->insert($data);

		$response = [
			'status' => 200,
			'message' => "Berhasil menyimpan presensi",
			'data' =>  $data,
		];
		echo json_encode($response);
		return;
	}
	//--------------------------------------------------------------------

}
