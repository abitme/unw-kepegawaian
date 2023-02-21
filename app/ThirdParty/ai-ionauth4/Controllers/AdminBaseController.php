<?php

namespace IonAuth\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
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

class AdminBaseController extends Controller
{
	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = ['form', 'ai'];
	protected $ionAuth;
	protected $menuSlug;

	/**
	 * Constructor.
	 *
	 * @param RequestInterface  $request
	 * @param ResponseInterface $response
	 * @param LoggerInterface   $logger
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
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

		// load ion auth libraries
		$this->ionAuth = new \IonAuth\Libraries\IonAuth();
		if (!$this->ionAuth->loggedIn()) {
			return redirect()->to('/login');
		}


		$this->db = \Config\Database::connect();

		// set id user for global controller
		$this->user_id = $this->session->get('user_id');

		$this->user = $this->db->table('users')->select('id, id_pegawai')->where('id', session('user_id'))->get()->getRow();
		$this->pegawaiJabatan = $this->user ? $this->db->table('pegawai_jabatan_u_view')->select('nama_jabatan')->where('id_pegawai', $this->user->id_pegawai)->get()->getRow() : null;

		// set timezone to Asia/Bangkok
		date_default_timezone_set('Asia/Bangkok');
	}

	public function view($pages, $data)
	{
		$data['menu'] = $this->menuSlug;
		$data['jabatanUser'] = $this->pegawaiJabatan;
		$verifikasiFormPresensi = $this->db->table('verifikasi_form_presensi')->where('id_pegawai_verifikasi', $this->user->id_pegawai)->get()->getRow();
		$data['isVerifying'] = $verifikasiFormPresensi ?? '';
		return view($pages, $data);
	}
}
