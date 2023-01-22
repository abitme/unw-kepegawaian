<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\AdminBaseController;

class DokumenSOP extends AdminBaseController
{
	public function index()
	{
		$data = [
			'title' => 'SOP',
		];

		return view('pages/admin/dokumen-sop/index', $data);
	}
}
