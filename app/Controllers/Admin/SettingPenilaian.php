<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\SettingPenilaianModel;

class SettingPenilaian extends AdminBaseController
{
	protected $SettingPenilaianModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->SettingPenilaianModel = new SettingPenilaianModel();

		$this->menuSlug = 'setting-penilaian';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->SettingPenilaianModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = date('d/m/Y', strtotime($list->periode_penilaian_awal)) . ' - ' . date('d/m/Y', strtotime($list->periode_penilaian_akhir));
				$row[] = $list->jenis;
				if (is_allow('update', $this->menuSlug) || is_allow('delete', $this->menuSlug)) {
					$row[] = '
						<a href="' . base_url("setting-penilaian/$list->id/edit") . '" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Edit"">
							<i class="fas fa-edit"></i>
						</a>
						<form action="' . base_url("setting-penilaian/$list->id/delete") . '" method="POST" class="d-inline form-delete">
							' . csrf_field() . '
							<input type="hidden" name="_method" value="DELETE" />
							<button type="submit" class="btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top" title="Delete" onclick="return confirm(`Apakah anda yakin menghapus data?`)">
								<i class="fas fa-trash"></i>
							</button>
						</form>
					';
				} else {
					$row[] = '';
				}
				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->SettingPenilaianModel->count_all(),
				"recordsFiltered" => $this->SettingPenilaianModel->count_filtered(),
				"data" => $data
			];
			echo json_encode($output);
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Pengaturan Penilaian',
			'content' => $this->db->table('periode')->get()->getResult(),
		];

		return $this->view('pages/admin/setting-penilaian/index', $data);
	}

	// public function show($id)
	// {
	// 	$periode = $this->db->table('periode')->where('id', $id)->get()->getRow();
	// 	if (!$periode) {
	// 		$this->session->setFlashdata('warning', 'Data tidak ditemukan');
	// 		return redirect()->to('/setting-penilaian');
	// 	}
	// 	$pertanyaan = $this->db->table('pertanyaan')->get()->getResult();
	// 	$setting_penilaian = $this->db->table('setting_penilaian')->where('id_periode', $id)->get()->getRow();
	// 	$pertanyaan_periode = $this->db->table('pertanyaan_periode_view')->where('id_periode', $id)->orderBy('no_urut', 'asc')->get()->getResult();
	// 	$data = [
	// 		'title' => 'Pengaturan Penilaian',
	// 		'periode' => $periode,
	// 		'pertanyaan' => $pertanyaan,
	// 		'setting_penilaian' => $setting_penilaian,
	// 		'pertanyaan_periode' => $pertanyaan_periode,
	// 		'validation' => \Config\Services::validation()
	// 	];

	// 	return $this->view('pages/admin/setting-penilaian/show', $data);
	// }

	public function new()
	{
		$pertanyaan = $this->db->table('pertanyaan')->get()->getResult();

		$data = [
			'title' => 'Pengaturan Penilaian - Tambah Penilaian',
			'pertanyaan' => $pertanyaan,
			'validation' => \Config\Services::validation(),
			'form_action' => base_url('setting-penilaian/create'),
		];

		return $this->view('pages/admin/setting-penilaian/create', $data);
	}

	public function create()
	{
		// // validate and set error message
		if (!$this->validate($this->SettingPenilaianModel->rules())) {
			return redirect()->to("/setting-penilaian/new")->withInput();
		}

		// set input data
		$input = (object) $this->request->getPost();

		// insert data setting_penilain
		$dataSettingPenilaian = [
			'jenis' => $input->jenis,
			'periode_penilaian_awal' => $input->periode_penilaian_awal,
			'periode_penilaian_akhir' => $input->periode_penilaian_akhir,
			'tanggal_mulai' => $input->tanggal_mulai,
			'tanggal_selesai' => $input->tanggal_selesai,
		];
		$this->db->table('setting_penilaian')->insert($dataSettingPenilaian);
		$idSettingPenilaian = $this->db->insertID();

		// set input data array
		$input = $this->request->getPost();

		// set data array
		foreach ($input as $key => $val) {
			$i = 0;
			// get input data to array for insert/update batch
			if (is_array($val)) {
				foreach ($val as $k => $v) {
					if ($key == 'id_pertanyaan' || $key = 'no_urut') {
						$pertanyaan[$i][$key] = $v;
					}
					$i++;
				}
			}
		}

		// insert data setting_penilaian_detail
		$this->db->table('setting_penilaian_detail')->where('id_setting_penilaian', $idSettingPenilaian)->delete();
		$dataSettingPenilaianDetail = [];
		foreach ($pertanyaan as $row) {
			// merge
			$result = array_merge($row, ['id_setting_penilaian' => $idSettingPenilaian]);
			// push
			array_push($dataSettingPenilaianDetail, $result);
		}
		$this->db->table('setting_penilaian_detail')->insertBatch($dataSettingPenilaianDetail);

		$this->session->setFlashdata('success', 'Data berhasil disimpan');
		return redirect()->to("/setting-penilaian");
		//--------------------------------------------------------------------
	}

	public function edit($id)
	{
		$pertanyaan = $this->db->table('pertanyaan')->get()->getResult();
		$dataDb = $this->db->table('setting_penilaian')->where('id', $id)->get()->getRow();

		if (!$dataDb) {
			$this->session->setFlashdata('warning', 'Maaf! Data tidak ditemukan');
			return redirect()->to('/setting-penilaian');
		}

		$data = [
			'title' => 'Edit Pengaturan Penilaian',
			'settingPenilaian' => $dataDb,
			'settingPenilaianDetail' => $this->db->table('setting_penilaian_detail_view')->where('id_setting_penilaian', $dataDb->id)->get()->getResult(),
			'pertanyaan' => $pertanyaan,
			'validation' => \Config\Services::validation(),
			'form_action' => base_url("setting-penilaian/$id/update"),
		];

		if (!$_POST) {
			// repoulate form
			$data['input'] = $data['settingPenilaian'];
		}
		return $this->view('pages/admin/setting-penilaian/edit', $data);
	}

	public function update($id)
	{
		// // validate and set error message
		if (!$this->validate($this->SettingPenilaianModel->rules())) {
			return redirect()->to("/setting-penilaian/$id/edit")->withInput();
		}

		// set input data
		$input = (object) $this->request->getPost();

		// update data setting_penilain
		$dataSettingPenilaian = [
			'jenis' => $input->jenis,
			'periode_penilaian_awal' => $input->periode_penilaian_awal,
			'periode_penilaian_akhir' => $input->periode_penilaian_akhir,
			'tanggal_mulai' => $input->tanggal_mulai,
			'tanggal_selesai' => $input->tanggal_selesai,
		];
		$this->db->table('setting_penilaian')->where('id', $id)->update($dataSettingPenilaian);

		// set input data array
		$input = $this->request->getPost();

		// set data array
		foreach ($input as $key => $val) {
			$i = 0;
			// get input data to array for insert/update batch
			if (is_array($val)) {
				foreach ($val as $k => $v) {
					if ($key == 'id_pertanyaan' || $key = 'no_urut') {
						$pertanyaan[$i][$key] = $v;
					}
					$i++;
				}
			}
		}

		// update data setting_penilaian_detail
		$this->db->table('setting_penilaian_detail')->where('id_setting_penilaian', $id)->delete();
		$dataSettingPenilaianDetail = [];
		foreach ($pertanyaan as $row) {
			// merge
			$result = array_merge($row, ['id_setting_penilaian' => $id]);
			// push
			array_push($dataSettingPenilaianDetail, $result);
		}
		$this->db->table('setting_penilaian_detail')->insertBatch($dataSettingPenilaianDetail);

		$this->session->setFlashdata('success', 'Data berhasil disimpan');
		return redirect()->to("/setting-penilaian");
		//--------------------------------------------------------------------
	}


	public function delete($id)
	{
		$dataDb = $this->db->table('setting_penilaian')->where('id', $id)->get()->getRow();
		if (!$dataDb) {
			$this->session->setFlashdata('warning', 'Maaf! Data tidak ditemukan');
			return redirect()->to('/setting-penilaian');
		}

		// delete data
		$this->db->table('setting_penilaian')->where('id', $id)->delete();

		$this->session->setFlashdata('success', 'Data berhasil dihapus');
		return redirect()->to('/setting-penilaian');
	}
}
