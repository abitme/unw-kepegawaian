<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\DashboardModel;

class Dashboard extends AdminBaseController
{
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->DashboardModel = new DashboardModel();
	}

	public function index()
	{
		$data = [
			'title' 			=> 'Dashboard',
		];
		$data['pegawai'] = $this->db->table('pegawai')->countAllResults();
		$data['pegawaiSD'] = $this->db->table('pegawai')->where('pendidikan', 'Sekolah Dasar / Setara')->countAllResults();
		$data['pegawaiSLTP'] = $this->db->table('pegawai')->where('pendidikan', 'Sekolah Lanjutan Tingkat Pertama / Setara')->countAllResults();
		$data['pegawaiSLTA'] = $this->db->table('pegawai')->where('pendidikan', 'Sekolah Lanjutan Tingkat Atas / Setara')->countAllResults();
		$data['pegawaiDI'] = $this->db->table('pegawai')->where('pendidikan', 'Diploma I')->countAllResults();
		$data['pegawaiDII'] = $this->db->table('pegawai')->where('pendidikan', 'Diploma II')->countAllResults();
		$data['pegawaiDIII'] = $this->db->table('pegawai')->where('pendidikan', 'Diploma III')->countAllResults();
		$data['pegawaiDIV'] = $this->db->table('pegawai')->where('pendidikan', 'Diploma IV')->countAllResults();
		$data['pegawaiS1'] = $this->db->table('pegawai')->where('pendidikan', 'Sarjana (S1)')->countAllResults();
		$data['pegawaiS2'] = $this->db->table('pegawai')->where('pendidikan', 'Magister (S2)')->countAllResults();
		$data['pegawaiS3'] = $this->db->table('pegawai')->where('pendidikan', 'Doktoral (S3)')->countAllResults();

		$data['unit'] = $this->db->table('unit')->countAllResults();
		$data['jabatanStrukturalUnit'] = $this->db->table('jabatan_struktural_u')->countAllResults();

		return $this->view('pages/admin/dashboard/index', $data);
	}
	//--------------------------------------------------------------------

}
