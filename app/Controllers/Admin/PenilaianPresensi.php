<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PenilaianPresensiModel;

class PenilaianPresensi extends AdminBaseController
{
	protected $PenilaianPresensiModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PenilaianPresensiModel = new PenilaianPresensiModel();

		$this->menuSlug = 'penilaian-presensi';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function import()
	{
		$data = [
			'title' => 'Import Excel Presensi Penilaian',
			'validation' => \Config\Services::validation(),
			'user_id' => $this->user_id,
			'form_action' => base_url("penilaian-presensi/import"),
		];

		// accessesed with get method
		if (!$_POST) {
			return $this->view('pages/admin/penilaian-presensi/import', $data);
		};

		// validate when accessesed with post method
		if (!$this->validate($this->PenilaianPresensiModel->getrulesImport()) && empty($this->request->getPost('import'))) {
			return redirect()->to("/penilaian-presensi/import")->withInput();
		}

		// import proccess
		$file = $this->request->getFile('excel');
		$import = $this->request->getPost('import');

		$resultImport = $this->PenilaianPresensiModel->import($file, $import);

		if ($resultImport == 'error') {
			return redirect()->to('/penilaian-presensi/import');
		} else if ($resultImport == 'success') {
			return redirect()->to('/penilaian-presensi');
		} else {
			$data['error'] = $resultImport['error'];
			$data['preview'] = $resultImport['preview'];
		}

		return $this->view('pages/admin/penilaian-presensi/import', $data);
	}

	public function ajax_list()
	{
		$this->PenilaianPresensiModel->table = 'pegawai';
		$input = (object) $this->request->getPost();

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->PenilaianPresensiModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$penilaianPresensi = $this->db->table('penilaian_presensi')->where('id_pegawai', $list->id)->get()->getRow();
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama;
				$row[] = $penilaianPresensi->cuti ?? '-';
				$row[] = $penilaianPresensi->alpha ?? '-';
				$row[] = $penilaianPresensi->total_cuti ?? '-';
				$row[] = $penilaianPresensi->terlambat ?? '-';
				$row[] = '
					<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="updateData(' . "'" . $list->id . "'" . ')">
						<i class="fas fa-edit"></i> Edit
					</a>
				';
				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->PenilaianPresensiModel->count_all(),
				"recordsFiltered" => $this->PenilaianPresensiModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$pegawai = $this->db->table('pegawai')->orderBy('nama', 'asc')->get()->getResult();
		$data = [
			'title' => 'Presensi Penilaian',
			'pegawai' => $pegawai,
		];

		return $this->view('pages/admin/penilaian-presensi/index', $data);
	}


	public function update($id)
	{
		$dataDbPegawai = $this->db->table('pegawai')->where('id', $id)->get()->getRowArray();
		$dataDbPenilaianPresensi = $this->db->table('penilaian_presensi')->where('id_pegawai', $id)->get()->getRowArray() ?? [];
		$dataDb = \array_merge($dataDbPegawai, $dataDbPenilaianPresensi);
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
			'id_pegawai' => $id,
			'cuti' => $input->cuti,
			'alpha' => $input->alpha,
			'total_cuti' => $input->total_cuti,
			'terlambat' => $input->terlambat,
		];
		if (!$dataDbPenilaianPresensi) {
			$this->db->table('penilaian_presensi')->insert($data);
		}
		$this->db->table('penilaian_presensi')->where('id_pegawai', $id)->update($data);

		// send response
		if ($this->db->affectedRows() || $this->db->affectedRows() == 0) {
			$response = [
				'status' => 200,
				'message' => 'Data berhasil disimpan',
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

	private function __validate()
	{
		// validate and set error message
		if (!$this->validate($this->PenilaianPresensiModel->getValidationRules())) {
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
