<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PenilaianSettingModel;

class PenilaianSetting extends AdminBaseController
{
	protected $PenilaianSettingModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PenilaianSettingModel = new PenilaianSettingModel();

		$this->menuSlug = 'penilaian-setting';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function ajax_list()
	{
		if ($this->request->getMethod(true) == 'POST') {
			$lists = $this->PenilaianSettingModel->get_datatables();
			$data = [];
			$no = $this->request->getPost("start");
			foreach ($lists as $list) {
				$no++;
				$row = [];
				$row[] = $no;
				$row[] = date('d/m/Y', strtotime($list->tanggal_awal)) . ' - ' . date('d/m/Y', strtotime($list->tanggal_akhir));
				if (is_allow('update', $this->menuSlug) || is_allow('delete', $this->menuSlug)) {
					$row[] = '
							<a href="' . base_url("penilaian-setting/$list->id") . '" class="btn btn-primary">
								<i class="fas fa-cog"></i> Atur Penilaian
							</a>
					';
				} else {
					$row[] = '';
				}
				$data[] = $row;
			}
			$output = [
				"draw" => $this->request->getPost('draw'),
				"recordsTotal" => $this->PenilaianSettingModel->count_all(),
				"recordsFiltered" => $this->PenilaianSettingModel->count_filtered(),
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

		return $this->view('pages/admin/penilaian-setting/index', $data);
	}

	public function show($id)
	{
		$periode = $this->db->table('periode')->where('id', $id)->get()->getRow();
		if (!$periode) {
			$this->session->setFlashdata('warning', 'Data tidak ditemukan');
			return redirect()->to('/penilaian-setting');
		}
		$pertanyaan = $this->db->table('pertanyaan')->get()->getResult();
		$penilaian_setting = $this->db->table('penilaian_setting')->where('id_periode', $id)->get()->getRow();
		$pertanyaan_periode = $this->db->table('pertanyaan_periode_view')->where('id_periode', $id)->orderBy('no_urut', 'asc')->get()->getResult();
		$data = [
			'title' => 'Pengaturan Penilaian',
			'periode' => $periode,
			'pertanyaan' => $pertanyaan,
			'penilaian_setting' => $penilaian_setting,
			'pertanyaan_periode' => $pertanyaan_periode,
			'validation' => \Config\Services::validation()
		];

		return $this->view('pages/admin/penilaian-setting/show', $data);
	}

	public function upsert($id)
	{
		// // validate and set error message
		// if (!$this->validate($this->PenilaianSettingModel->rulesUpdate())) {
		// 	return redirect()->to("/penilaian-setting/$id")->withInput();
		// }

		// set input data
		$input = (object) $this->request->getPost();

		$check = $this->db->table('penilaian_setting')->where('id_periode', $id)->get()->getRow();

		// insert or update data penilaian_setting
		if (!$check) {
			$dataPenilaianSetting = [
				'id_periode' => $id,
				'tanggal_mulai' => $input->tanggal_mulai,
				'tanggal_selesai' => $input->tanggal_selesai,
			];
			$this->db->table('penilaian_setting')->insert($dataPenilaianSetting);
		} else {
			$dataPenilaianSetting = [
				'id_periode' => $id,
				'tanggal_mulai' => $input->tanggal_mulai,
				'tanggal_selesai' => $input->tanggal_selesai,
			];
			$this->db->table('penilaian_setting')->where('id_periode', $id)->update($dataPenilaianSetting);
		}

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

		// insert pertanyaan_periode
		$this->db->table('pertanyaan_periode')->where('id_periode', $id)->delete();
		$dataPertanyaan = [];
		foreach ($pertanyaan as $row) {
			// merge
			$result = array_merge($row, ['id_periode' => $id]);
			// push
			array_push($dataPertanyaan, $result);
		}
		$this->db->table('pertanyaan_periode')->insertBatch($dataPertanyaan);

		$this->session->setFlashdata('success', 'Data berhasil disimpan');
		return redirect()->to("/penilaian-setting/$id");
		//--------------------------------------------------------------------
	}
}
