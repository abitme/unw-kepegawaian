<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
// use App\Models\Admin\RekapPresensiAbsensiPiketModel;

class RekapPresensiPiket extends AdminBaseController
{
	// protected $RekapPresensiAbsensiPiketModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		// $this->RekapPresensiAbsensiPiketModel = new RekapPresensiAbsensiPiketModel();

		$this->menuSlug = 'rekap-presensi-piket';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function index()
	{
		$input = (object) $this->request->getGet();
		if (isset($input->id_unit) && !empty($input->id_unit)) {
			$pegawai = $this->db->table('pegawai_unit_view')->where('id_unit', $input->id_unit)->orderBy('nama', 'asc')->get()->getResult();
		} else {
			// $pegawai = $this->db->table('pegawai')->where('id', 192)->orderBy('nama', 'asc')->get()->getResult();
			$pegawai = [];
		}
		$data = [
			'title' => 'Rekap Presensi Piket',
			'pegawai' => $pegawai,
			'input' => $input
		];

		return $this->view('pages/admin/rekap-presensi-piket/index', $data);
	}

	//--------------------------------------------------------------------
}
