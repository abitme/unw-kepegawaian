<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\PenilaianModel;

class Penilaian extends AdminBaseController
{
	protected $Penilaian;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->PenilaianModel = new PenilaianModel();

		$this->menuSlug = 'penilaian';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function index()
	{

		$userPenilai = $this->db->table('users')->where('id', $this->user_id)->get()->getRow();
		$jabatanUserPenilai = $this->db->table('pegawai_jabatan_struktural_u')->where('id_pegawai', $userPenilai->id_pegawai)->get()->getRow();

		$idJabatanDinilai = [];
		foreach ($jabatanUserPenilai as $row) {
			$jabatanAtasanBawahan =	$this->db->table('jabatan_atasan_bawahan')->select('id_jabatan_bawahan')->where('id_jabatan_atasan', $row->id_jabatan)->get()->getResultArray();
			$idJabatanDinilai = array_merge($idJabatanDinilai, array_column($jabatanAtasanBawahan, 'id_jabatan_bawahan'));
		}

		if ($idJabatanDinilai) {
			$pegawaiDinilai = $this->db->table('pegawai_jabatan_view')->havingIn('id_jabatan', $idJabatanDinilai)->get()->getResult();
			$content = $pegawaiDinilai;
		} else {
			$content = [];
		}
		$data = [
			'title' => 'Penilaian',
			'content' => $content,
		];

		return $this->view('pages/admin/penilaian/index', $data);
	}

	public function show($id)
	{
		$periode = $this->db->table('periode')->where('is_aktif', 1)->get()->getRow();
		if (!$periode) {
			$this->session->setFlashdata('warning', 'Data tidak ditemukan');
			return redirect()->to('/penilaian-setting');
		}
		$pegawaiDinilai = $this->db->table('pegawai_jabatan_view')->where('id', $id)->get()->getRow();
		if (!$pegawaiDinilai) {
			$this->session->setFlashdata('warning', 'Data tidak ditemukan');
			return redirect()->to('/penilaian');
		}
		// cek penilaian sudah dimulai
		$penilaianSetting = $this->db->table('penilaian_setting_view')->where('is_aktif', 1)->get()->getRow();
		$today = date('Y-m-d');
		$startDate = $penilaianSetting->tanggal_mulai;
		$endDate = $penilaianSetting->tanggal_selesai;
		if ($today < $startDate) {
			$this->session->setFlashdata('warning', 'Belum waktunya penilaian');
			return redirect()->to("penilaian");
		}
		if ($today > $endDate) {
			$this->session->setFlashdata('warning', 'Penilaian sudah ditutup');
			return redirect()->to("penilaian");
		}

		// cek penilai sudah menilai
		$userPenilai = $this->db->table('users')->where('id', $this->user_id)->get()->getRow();
		$check = $this->db->table('penilaian')->where('id_pegawai_penilai', $userPenilai->id_pegawai)->where('id_pegawai_dinilai', $pegawaiDinilai->id_pegawai)->countAllResults();

		if ($check > 0) {
			$this->session->setFlashdata('warning', 'Karyawan ini sudah anda nilai');
			return redirect()->to('/penilaian');
		}

		$jabatanUserDinilai = $this->db->table('pegawai_jabatan')->where('id_pegawai', $id)->get()->getResult();
		$userPenilai = $this->db->table('users')->where('id', $this->user_id)->get()->getRow();
		$jabatanPenilai = $this->db->table('pegawai_jabatan')->select('id_jabatan')->where('id_pegawai', $userPenilai->id_pegawai)->get()->getResultArray();
		$jabatanPenilai = array_column($jabatanPenilai, 'id_jabatan');

		$idJabatanDinilai = [];
		foreach ($jabatanUserDinilai as $row) {
			$jabatanAtasanBawahan =	$this->db->table('jabatan_atasan_bawahan')->where('id_jabatan_bawahan', $row->id_jabatan)->havingIn('id_jabatan_atasan', $jabatanPenilai)->get()->getResult();
			$idJabatanDinilai = array_merge($idJabatanDinilai, array_column($jabatanAtasanBawahan, 'id_jabatan_bawahan'));
		}
		

		if ($idJabatanDinilai) {
			$pegawaiDinilai = $this->db->table('pegawai_jabatan_view')->havingIn('id_jabatan', $idJabatanDinilai)->get()->getRow();
			$content = $pegawaiDinilai;
		} else {
			$content = [];
		}
		$pertanyaan_periode = $this->db->table('pertanyaan_periode_view')->where('is_aktif', 1)->get()->getResult();

		$data = [
			'title' => 'Penilaian',
			'periode' => $periode,
			'content' => $content,
			'pertanyaan_periode' => $pertanyaan_periode,
			'validation' => \Config\Services::validation()
		];

		return $this->view('pages/admin/penilaian/show', $data);
	}

	public function menilai($id)
	{
		// validate and set error message
		if (!$this->validate($this->PenilaianModel->rules())) {
			return redirect()->to("/penilaian/$id")->withInput();
		}

		// set input data
		$input = (object) $this->request->getPost();

		// cek penilai sudah menilai
		$userPenilai = $this->db->table('users')->where('id', $this->user_id)->get()->getRow();
		$check = $this->db->table('penilaian')->where('id_pegawai_penilai', $userPenilai->id_pegawai)->where('id_pegawai_dinilai', $input->id_pegawai_dinilai)->countAllResults();

		if ($check > 0) {
			$this->session->setFlashdata('warning', 'Karyawan ini sudah anda nilai');
			return redirect()->to('/penilaian');
		}

		// set input data array
		$input = (object) $this->request->getPost();

		// insert penilaian
		$dataPenilaian = [
			'id_pegawai_penilai' => $userPenilai->id_pegawai,
			'id_pegawai_dinilai' => $input->id_pegawai_dinilai,
			'nilai_lain' => $input->nilai_lain,
		];
		$this->db->table('penilaian')->insert($dataPenilaian);
		$idPenilaian = $this->db->insertID();
		// \var_dump($dataPenilaian);
		// insert penilaian_detail
		$no = 0;
		$dataPenilaianDetail = [];
		$arrData = [];
		foreach ($input->id_pertanyaan_periode as $row) {
			$no++;
			$nilai = 'nilai' . $no;
			$keterangan = 'keterangan' . $no;
			$arrData = [
				'id_penilaian' => $idPenilaian,
				'id_pertanyaan_periode' => $row,
				'nilai' => $input->$nilai,
				'keterangan' => $input->$keterangan
			];
			array_push($dataPenilaianDetail, $arrData);
		}
		$this->db->table('penilaian_detail')->insertBatch($dataPenilaianDetail);
		
		$this->session->setFlashdata('success', 'Karyawan berhasil dinilai');
		return redirect()->to("/penilaian");
		//--------------------------------------------------------------------
	}
}
