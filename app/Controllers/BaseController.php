<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
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
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

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
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * Constructor.
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

        // set id user for global controller
        $this->user_id = $this->session->get('user_id');

        // set timezone to Asia/Bangkok
        date_default_timezone_set('Asia/Bangkok');
    }

    public function view($pages, $data)
    {
        $data['menu'] = $this->menuSlug;
        $this->db = \Config\Database::connect();
        $setting = $this->db->table('setting')->get()->getRow();
        $data['title'] = $setting->title ?? "Welcome";
        $data['subtitle'] = $setting->subtitle ?? "";
        if (\logged_in()) {
            $user = $this->db->table('users')->select('id_pegawai')->where('id', session('user_id'))->get()->getRow();
            $unitPiket = unitPiket($user->id_pegawai);
            if ($unitPiket || checkGroupUser(1)) {
                $data['isPicket'] = 1;
            } else {
                $data['isPicket'] = 0;
            }
        } else {
            $data['isPicket'] = 0;
        }
        return view($pages, $data);
    }
}
