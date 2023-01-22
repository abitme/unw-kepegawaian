<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PresensiPiketModel;

class PresensiPiket extends AdminBaseController
{
	protected $PresensiPiketModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PresensiPiketModel = new PresensiPiketModel();

		$this->menuSlug = 'admin/presensi-piket';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$pegawai = $this->db->table('users')->select('id_pegawai')->where('id', session('user_id'))->get()->getRow();
		$this->id_pegawai = $pegawai->id_pegawai ?? '';
	}

	public function ajax_list()
	{
		$this->PresensiPiketModel->table = 'presensi_piket_view';
		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->PresensiPiketModel->get_datatables();
			$countAll = $this->PresensiPiketModel->count_all();
			$countFiltered = $this->PresensiPiketModel->count_filtered();

			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {

				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama;
				if (!empty($list->photo) && file_exists("assets/img/presensi-piket/{$list->photo}")) {
					$row[] = '<img src="' . base_url() . '/assets/img/presensi-piket/' . $list->photo . '" alt="" class="img-thumb-list mr-2 mb-2 mb-md-0">';
				} else {
					$row[] = '<img src="' . base_url() . '/assets/img/presensi/default.jpg" alt="" class="img-thumb-list mr-2 mb-2 mb-md-0">';
				}
				$row[] = $list->tipe;
				$row[] = $list->waktu;

				// lokasi - distance in km
				$distance1 = getDistance(-7.15111, 110.40805, $list->coord_latitude, $list->coord_longitude);
				$distance2 = getDistance(-7.15173, 110.40726, $list->coord_latitude, $list->coord_longitude);
				$distance3 = getDistance(-7.15263, 110.40709, $list->coord_latitude, $list->coord_longitude);
				$distance4 = getDistance(-7.154620, 110.407700, $list->coord_latitude, $list->coord_longitude); //farmasi
				if (($distance1 > 0.13 && $distance2 > 0.14 && $distance3 > 0.076) && $distance4 > 0.1) {
					$row[] = "Luar UNW";
				} else {
					$row[] = "UNW";
				}

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
			'title' => 'Presensi Piket',
		];

		return $this->view('pages/admin/presensi-piket/index', $data);
	}

	private function __validate($rules = null)
	{
		if ($rules == null) {
			$rules = $this->PresensiPiketModel->getValidationRules();
		}

		// validate and set error message
		if (!$this->validate($rules)) {
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
