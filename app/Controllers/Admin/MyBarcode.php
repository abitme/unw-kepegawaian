<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;

class MyBarcode extends AdminBaseController
{
	
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$user = $this->db->table('users')->select('id_pegawai')->select('id, id_pegawai')->where('id', session('user_id'))->get()->getRow();
		$this->pegawai = '';
		if ($user) {
			$pegawai = $this->db->table('pegawai')->select('nik, nama')->where('id', $user->id_pegawai)->get()->getRow();
			$this->pegawai = $pegawai ?? '';
		}
	}

	public function index()
	{
		$data = [
			'title' => 'My Barcode',
			'barcode' => $this->pegawai->nik ?? '',
		];

		return view('pages/admin/my-barcode/index', $data);
	}
}
