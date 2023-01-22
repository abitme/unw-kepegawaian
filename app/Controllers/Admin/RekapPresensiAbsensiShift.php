<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
// use App\Models\Admin\RekapPresensiAbsensiShiftModel;

class RekapPresensiAbsensiShift extends AdminBaseController
{
	// protected $RekapPresensiAbsensiShiftModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		// $this->RekapPresensiAbsensiShiftModel = new RekapPresensiAbsensiShiftModel();

		$this->menuSlug = 'rekap-presensi-absensi-shift';
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
			'title' => 'Rekap Shift',
			'pegawai' => $pegawai,
			'input' => $input
		];

		// // filter
		// if (isset($data['input']->dates)) {
		// 	$date = explode(" - ", $data['input']->dates);

		// 	$date[0] = date('Y-m-d', strtotime($date[0]));
		// 	if (isset($date[1])) {
		// 		$date[1] = date('Y-m-d', strtotime($date[1] . "+1 days"));
		// 	} else {
		// 		$date[1] = date('Y-m-d', strtotime($date[0] . "+1 days"));
		// 	}

		// 	$tanggal_awal = $date[0];
		// 	$tanggal_akhir = $date[1];
		// 	$data['content']    = $this->db->query("SELECT * FROM pemakaian_detail_view WHERE jenis = 'Tidak Habis Pakai' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' ORDER BY tanggal DESC")->getResult();
		// 	// $data['content']    = $this->db->query("SELECT * FROM pemakaian_list_view WHERE jenis = 'Tidak Habis Pakai' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir' ORDER BY tanggal DESC")->getResult();
		// } else {
		// 	$data['content']    = $this->db->table('pemakaian_detail_view')->where('jenis', 'Tidak Habis Pakai')->orderBy('tanggal', 'desc')->get()->getResult();
		// 	// $data['content']    = $this->db->table('pemakaian_list_view')->where('jenis', 'Tidak Habis Pakai')->orderBy('tanggal', 'desc')->get()->getResult();
		// }

		return $this->view('pages/admin/rekap-presensi-absensi-shift/index', $data);
	}

	//--------------------------------------------------------------------
}
