<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;
// use App\Models\Admin\RekapPresensiAbsensiModel;

class RekapPresensiAbsensi extends AdminBaseController
{
	// protected $RekapPresensiAbsensiModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		// $this->RekapPresensiAbsensiModel = new RekapPresensiAbsensiModel();

		$this->menuSlug = 'rekap-presensi-absensi';
		if (!is_allow('', $this->menuSlug)) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	}

	public function index()
	{
		$input = (object) $this->request->getGet();
		if (isset($input->id_unit) && !empty($input->id_unit)) {
			$parent = $input->id_unit;
			$child = $this->db->table('view_unit_relations')->where('parent', $input->id_unit)->where('depth >', 0)->get()->getResult();
			$idUnit = array_merge([$parent], \array_column($child, 'child'));
			$input->unit = $this->db->table('unit')->havingIn('id', $idUnit)->get()->getResult();
			$pegawai = $this->db->table('pegawai_unit_view')->havingIn('id_unit', $idUnit)->orderBy('nama', 'asc')->get()->getResult();
			// $pegawai = $this->db->table('pegawai_unit_view')->where('id_unit', $input->id_unit)->orderBy('nama', 'asc')->get()->getResult();
		} else {
			$pegawai = $this->db->table('pegawai')->where('id', 194)->orderBy('nama', 'asc')->get()->getResult();
		}
		$data = [
			'title' => 'Rekap Presensi dan Absensi',
			'pegawai' => $pegawai,
			'input' => $input
		];
		// return $this->db->query("SELECT COUNT(id) from presensi");

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

		return $this->view('pages/admin/rekap-presensi-absensi/index', $data);
	}

	//--------------------------------------------------------------------
}
