<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
use App\Models\Admin\DashboardPresensiAbsensiModel;

class DashboardPresensiAbsensi extends AdminBaseController
{
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->DashboardPresensiAbsensiModel = new DashboardPresensiAbsensiModel();
		$pegawai = $this->db->table('users')->select('id_pegawai')->where('id', session('user_id'))->get()->getRow();
		$this->id_pegawai = $pegawai->id_pegawai ?? '';
	}

	public function index()
	{
		$data = [
			'title' 	=> 'Dashboard',
			'input' => (object) $this->request->getGet(),
			'pegawai' => $this->db->table('pegawai')->where('id', $this->id_pegawai)->get()->getRow(),
		];
		// $data['unit'] = $this->db->table('unit')->countAllResults();
		// $data['jabatanStrukturalUnit'] = $this->db->table('jabatan_struktural_u')->countAllResults();

		return $this->view('pages/admin/dashboard-presensi-absensi/index', $data);
	}
	//--------------------------------------------------------------------

}
