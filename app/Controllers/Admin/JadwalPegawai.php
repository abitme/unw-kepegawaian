<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\JadwalPegawaiModel;

class JadwalPegawai extends AdminBaseController
{
	protected $JadwalPegawaiModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->JadwalPegawaiModel = new JadwalPegawaiModel();

		$this->menuSlug = 'jadwal-pegawai';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function import()
	{
		$data = [
			'title' => 'Import Excel Jadwal Pegawai',
			'validation' => \Config\Services::validation(),
			'user_id' => $this->user_id,
			'form_action' => base_url("jadwal-pegawai/import"),
		];

		// accessesed with get method
		if (!$_POST) {
			return $this->view('pages/admin/jadwal-pegawai/import', $data);
		};

		// validate when accessesed with post method
		if (!$this->validate($this->JadwalPegawaiModel->getrulesImport()) && empty($this->request->getPost('import'))) {
			return redirect()->to("/jadwal-pegawai/import")->withInput();
		}

		// import proccess
		$file = $this->request->getFile('excel');
		$import = $this->request->getPost('import');

		$resultImport = $this->JadwalPegawaiModel->import($file, $import);

		if ($resultImport == 'error') {
			return redirect()->to('/jadwal-pegawai/import');
		} else if ($resultImport == 'success') {
			return redirect()->to('/jadwal-pegawai');
		} else {
			$data['error'] = $resultImport['error'];
			$data['preview'] = $resultImport['preview'];
		}

		return $this->view('pages/admin/jadwal-pegawai/import', $data);
	}

	public function ajax_list()
	{
		$this->JadwalPegawaiModel->table = 'pegawai';
		$this->JadwalPegawaiModel->column_order = array('id', 'nama');
		$this->JadwalPegawaiModel->column_search = array('nama');
		$this->JadwalPegawaiModel->order = array('nama' => 'asc');

		$input = (object) $this->request->getPost();

		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->JadwalPegawaiModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$jadwalPegawai = $this->db->table('jadwal_pegawai_view')->select('nama_jadwal_kerja')->where('id_pegawai', $list->id)->get()->getRow()->nama_jadwal_kerja ?? '-';
				$jadwalAutoPegawai = $this->db->table('jadwal_auto_pegawai_view')->select('nama_jadwal_kerja')->where('id_pegawai', $list->id)->get()->getRow()->nama_jadwal_kerja ?? '-';
				$pegawaiJabatan = $this->db->table('pegawai_jabatan_u_view')->select('nama_unit, nama_jabatan')->where('id_pegawai', $list->id)->get()->getRow();
				$jabatan = !\is_null($pegawaiJabatan) ? "$pegawaiJabatan->nama_jabatan - $pegawaiJabatan->nama_unit" : '-';
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = $list->nama;
				$row[] = $jabatan;
				$row[] = $jadwalPegawai != '-' ? $jadwalPegawai : $jadwalAutoPegawai;
				$row[] = '
					<a href="javascript:void(0)" class="btn btn-sm btn-success" onclick="updateData(' . "'" . $list->id . "'" . ')">
						<i class="fas fa-edit"></i> Edit
					</a>
				';
				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->JadwalPegawaiModel->count_all(),
				"recordsFiltered" => $this->JadwalPegawaiModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Jadwal Pegawai',
			// 'periode' => $this->db->table('periode')->where('is_active', 1)->get()->getRow(),
		];

		return $this->view('pages/admin/jadwal-pegawai/index', $data);
	}

	public function update($id)
	{
		$dataDbPegawai = $this->db->table('pegawai')->where('id', $id)->get()->getRowArray();
		$dataDbJadwalPegawai = $this->db->table('jadwal_pegawai_view')->select('id_jadwal_kerja, nama_jadwal_kerja')->where('id_pegawai', $id)->get()->getRowArray() ?? [];
		$dataDbJadwalAutoPegawai = $this->db->table('jadwal_auto_pegawai_view')->select('id_jadwal_kerja_auto, nama_jadwal_kerja as nama_jadwal_kerja_auto')->where('id_pegawai', $id)->get()->getRowArray() ?? [];
		$dataDb = \array_merge($dataDbPegawai, $dataDbJadwalPegawai);
		$dataDb = \array_merge($dataDb, $dataDbJadwalAutoPegawai);
		if (!$_POST) {
			// repoulate form
			$input = $dataDb;
			echo json_encode($input);
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

		if ($input->id_jadwal_kerja && $input->id_jadwal_kerja_auto) {
			$response = [
				'status' => 500,
				'message' => 'Pilih salah satu jadwal',
			];

			echo json_encode($response);
			return;
		}

		// save data
		if ($input->id_jadwal_kerja) {
			$data = [
				'id_jadwal_kerja' => $input->id_jadwal_kerja,
				'id_pegawai' => $input->id_pegawai,
			];
			if (!$dataDbJadwalPegawai) {
				$this->db->table('jadwal_pegawai')->insert($data);
			}
			$this->db->table('jadwal_auto_pegawai')->where('id_pegawai', $id)->delete();
			$this->db->table('jadwal_pegawai')->where('id_pegawai', $id)->update($data);
		}
		if ($input->id_jadwal_kerja_auto) {
			$data = [
				'id_jadwal_kerja_auto' => $input->id_jadwal_kerja_auto,
				'id_pegawai' => $input->id_pegawai,
			];
			if (!$dataDbJadwalAutoPegawai) {
				$this->db->table('jadwal_auto_pegawai')->insert($data);
			}
			$this->db->table('jadwal_pegawai')->where('id_pegawai', $id)->delete();
			$this->db->table('jadwal_auto_pegawai')->where('id_pegawai', $id)->update($data);
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

	private function __validate()
	{
		// validate and set error message
		if (!$this->validate($this->JadwalPegawaiModel->getValidationRules())) {
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
