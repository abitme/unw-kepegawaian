<?php

namespace IonAuth\Controllers;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use CodeIgniter\Controller;

class AdminBaseController extends Controller
{

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpersF will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = ['form', 'ai'];
	protected $ionAuth;
	protected $menuSlug;

	/**
	 * Constructor.
	 */
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------

		// load helper
		helper($this->helpers);

		// load session
		$this->session = \Config\Services::session();

		// set id user for global controller
		$this->user_id = $this->session->get('user_id');

		// load ion auth libraries
		$this->ionAuth = new \IonAuth\Libraries\IonAuth();
		if (!$this->ionAuth->loggedIn()) {
			return redirect()->to('/login');
		}
	}

	public function view($pages, $data)
	{
		$data['menu'] = $this->menuSlug;
		$pegawai = $this->db->table('users')->select('id_pegawai')->where('id', session('user_id'))->get()->getRow();
		$pegawaiID = $pegawai->id_pegawai ?? '';
		$verifikasiFormPresensi = $this->db->table('verifikasi_form_presensi')->where('id_pegawai_verifikasi', $pegawaiID)->get()->getRow();
		$data['isVerifying'] = $verifikasiFormPresensi ?? '';
		return view($pages, $data);
	}
}
