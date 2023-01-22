<?php

namespace IonAuth\Controllers;

/**
 * Class Auth
 *
 * @property Ion_auth|Ion_auth_model $ionAuth      The ION Auth spark
 * @package  CodeIgniter-Ion-Auth
 * @author   Ben Edmunds <ben.edmunds@gmail.com>
 * @author   Benoit VRIGNAUD <benoit.vrignaud@zaclys.net>
 * @license  https://opensource.org/licenses/MIT	MIT License
 */
class Auth extends \CodeIgniter\Controller
{

	/**
	 *
	 * @var array
	 */
	public $data = [];

	/**
	 * Configuration
	 *
	 * @var \IonAuth\Config\IonAuth
	 */
	protected $configIonAuth;

	/**
	 * IonAuth library
	 *
	 * @var \IonAuth\Libraries\IonAuth
	 */
	protected $ionAuth;

	/**
	 * Session
	 *
	 * @var \CodeIgniter\Session\Session
	 */
	protected $session;

	/**
	 * Validation library
	 *
	 * @var \CodeIgniter\Validation\Validation
	 */
	protected $validation;

	/**
	 * Validation list template.
	 *
	 * @var string
	 * @see https://bcit-ci.github.io/CodeIgniter4/libraries/validation.html#configuration
	 */
	protected $validationListTemplate = 'list';

	/**
	 * Views folder
	 * Set it to 'auth' if your views files are in the standard application/Views/auth
	 *
	 * @var string
	 */
	protected $viewsFolder = 'IonAuth\Views\auth';

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->ionAuth    = new \IonAuth\Libraries\IonAuth();
		$this->validation = \Config\Services::validation();
		helper(['form', 'url', 'ai']);
		$this->configIonAuth = config('IonAuth');
		$this->session       = \Config\Services::session();
		$this->db = \Config\Database::connect();
		if (!empty($this->configIonAuth->templates['errors']['list'])) {
			$this->validationListTemplate = $this->configIonAuth->templates['errors']['list'];
		}
		$this->data['urlApiUnw'] = 'https://base-unw.test';
	}

	public function index()
	{
		// return redirect()->to('/');
		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
	}

	/**
	 * Groups
	 *
	 * @return string|\CodeIgniter\HTTP\RedirectResponse
	 */
	public function groups()
	{
		if (!$this->ionAuth->loggedIn()) {
			// redirect them to the login page
			return redirect()->to('/login');
		} else if (!$this->ionAuth->isAdmin()) // remove this elseif if you want to enable this for non-admins
		{
			// redirect them to the home page because they must be an administrator to view this
			//show_error('You must be an administrator to view this page.');
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('You must be an administrator to view this page.');
		} else {
			$this->data['title'] = 'Groups';

			// set the flash data error message if there is one
			// $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');

			//list the groups
			$this->data['groups'] = $this->ionAuth->groups()->result();
			return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'group', $this->data);
		}
	}

	/**
	 * Redirect if needed, otherwise display the user list
	 *
	 * @return string|\CodeIgniter\HTTP\RedirectResponse
	 */
	public function users()
	{
		if (!$this->ionAuth->loggedIn()) {
			// redirect them to the login page
			return redirect()->to('/login');
		} else if (!$this->ionAuth->isAdmin()) // remove this elseif if you want to enable this for non-admins
		{
			// redirect them to the home page because they must be an administrator to view this
			//show_error('You must be an administrator to view this page.');
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('You must be an administrator to view this page.');
		} else {
			$this->data['title'] = lang('Auth.index_heading');

			// set the flash data error message if there is one
			// $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');

			//list the users
			$this->data['users'] = $this->ionAuth->users()->result();
			foreach ($this->data['users'] as $k => $user) {
				$this->data['users'][$k]->groups = $this->ionAuth->getUsersGroups($user->id)->getResult();
			}
			return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'user', $this->data);
		}
	}

	/**
	 * Log the user in
	 *
	 * @return string|\CodeIgniter\HTTP\RedirectResponse
	 */
	public function login()
	{
		if ($this->ionAuth->loggedIn()) {
			return redirect()->to('/profile');
		}

		$this->data['title'] = lang('Auth.login_heading');

		// validate form input
		$this->validation->setRule('identity', str_replace(':', '', lang('Auth.login_identity_label')), 'required');
		$this->validation->setRule('password', str_replace(':', '', lang('Auth.login_password_label')), 'required');

		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			// check to see if the user is logging in
			// check for "remember me"
			$remember = (bool)$this->request->getVar('remember');

			if ($this->ionAuth->login($this->request->getVar('identity'), $this->request->getVar('password'), $remember)) {
				//if the login is successful

				$this->session->setFlashdata('success', $this->ionAuth->messages());

				$redirectURL = $this->session->get('redirect_url');
				unset($_SESSION['redirect_url']);

				if (isset($redirectURL)) {
					// redirect them to previous url
					return redirect()->to($redirectURL)->withCookies();
				}

				//redirect them back to the default page
				return redirect()->to('/profile')->withCookies();
			} else {
				// if the login was un-successful
				// redirect them back to the login page
				$this->session->setFlashdata('danger', $this->ionAuth->errors($this->validationListTemplate));
				// use redirects instead of loading views for compatibility with MY_Controller libraries
				return redirect()->back()->withInput();
			}
		} else {
			// the user is not logging in so display the login page
			// set the flash data error message if there is one
			$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');
			$this->data['validation'] = \Config\Services::validation();

			$this->data['identity'] = [
				'name'  => 'identity',
				'id'    => 'identity',
				'type'  => 'text',
			];

			$this->data['password'] = [
				'name' => 'password',
				'id'   => 'password',
				'type' => 'password',
			];

			return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'login', $this->data);
		}
	}

	/**
	 * Log the user out
	 *
	 * @return \CodeIgniter\HTTP\RedirectResponse
	 */
	public function logout()
	{
		$this->data['title'] = 'Logout';

		// log the user out
		$this->ionAuth->logout();

		// redirect them to the login page
		$this->session->setFlashdata('message', $this->ionAuth->messages());
		return redirect()->to('/')->withCookies();
	}

	/**
	 * Change password
	 *
	 * @return string|\CodeIgniter\HTTP\RedirectResponse
	 */
	public function change_password()
	{
		if (!$this->ionAuth->loggedIn()) {
			return redirect()->to('/login');
		}

		$this->validation->setRule('old', lang('Auth.change_password_validation_old_password_label'), 'required');
		$this->validation->setRule('new', lang('Auth.change_password_validation_new_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']|matches[new_confirm]');
		$this->validation->setRule('new_confirm', lang('Auth.change_password_validation_new_password_confirm_label'), 'required');

		$user = $this->ionAuth->user()->row();

		if (!$this->request->getPost() || $this->validation->withRequest($this->request)->run() === false) {
			// display the form

			// set the flash data error message if there is one
			// $this->data['message'] = ($this->validation->getErrors()) ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');

			$this->data['title'] = 'Change Password';
			$this->data['validation'] = \Config\Services::validation();

			$this->data['minPasswordLength'] = $this->configIonAuth->minPasswordLength;
			$this->data['old_password'] = [
				'name' => 'old',
				'id'   => 'old',
				'type' => 'password',
			];
			$this->data['new_password'] = [
				'name'    => 'new',
				'id'      => 'new',
				'type'    => 'password',
				'pattern' => '^.{' . $this->data['minPasswordLength'] . '}.*$',
			];
			$this->data['new_password_confirm'] = [
				'name'    => 'new_confirm',
				'id'      => 'new_confirm',
				'type'    => 'password',
				'pattern' => '^.{' . $this->data['minPasswordLength'] . '}.*$',
			];
			$this->data['user_id'] = [
				'name'  => 'user_id',
				'id'    => 'user_id',
				'type'  => 'hidden',
				'value' => $user->id,
			];

			// render
			return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'change_password', $this->data);
		} else {
			$identity = $this->session->get('identity');

			$change = $this->ionAuth->changePassword($identity, $this->request->getPost('old'), $this->request->getPost('new'));

			if ($change) {
				//if the password was successfully changed
				$this->session->setFlashdata('success', $this->ionAuth->messages());
				return $this->logout();
			} else {
				$this->session->setFlashdata('danger', $this->ionAuth->errors($this->validationListTemplate));
				return redirect()->to('/auth/change_password');
			}
		}
	}

	/**
	 * Forgot password
	 *
	 * @return string|\CodeIgniter\HTTP\RedirectResponse
	 */
	public function forgot_password()
	{
		$this->data['title'] = lang('Auth.forgot_password_heading');

		// setting validation rules by checking whether identity is username or email
		if ($this->configIonAuth->identity !== 'email') {
			$this->validation->setRule('identity', lang('Auth.forgot_password_identity_label'), 'required');
		} else {
			$this->validation->setRule('identity', lang('Auth.forgot_password_validation_email_label'), 'required|valid_email');
		}

		if (!($this->request->getPost() && $this->validation->withRequest($this->request)->run())) {
			$this->data['type'] = $this->configIonAuth->identity;
			// setup the input
			$this->data['identity'] = [
				'name' => 'identity',
				'id'   => 'identity',
			];

			if ($this->configIonAuth->identity !== 'email') {
				$this->data['identity_label'] = lang('Auth.forgot_password_identity_label');
			} else {
				$this->data['identity_label'] = lang('Auth.forgot_password_email_identity_label');
			}

			// set any errors and display the form
			$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');
			$this->data['validation'] = \Config\Services::validation();

			return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'forgot_password', $this->data);
		} else {
			$this->db = \Config\Database::connect();
			$identityColumn = $this->configIonAuth->identity;
			$identity = $this->db->table('users')->where($identityColumn, $this->request->getPost('identity'))->orWhere('email', $this->request->getPost('identity'))->get()->getRow();

			if (empty($identity)) {
				if ($this->configIonAuth->identity !== 'email') {
					$this->ionAuth->setError('Auth.forgot_password_identity_not_found');
				} else {
					$this->ionAuth->setError('Auth.forgot_password_email_not_found');
				}

				$this->session->setFlashdata('error', $this->ionAuth->errors($this->validationListTemplate));
				return redirect()->to('/auth/forgot_password');
			}

			// run the forgotten password method to email an activation code to the user
			$forgotten = $this->ionAuth->forgottenPassword($identity->{$this->configIonAuth->identity});

			if ($forgotten) {
				// if there were no errors
				$this->session->setFlashdata('success', $this->ionAuth->messages());
				return redirect()->to('/login'); //we should display a confirmation page here instead of the login page
			} else {
				$this->session->setFlashdata('danger', $this->ionAuth->errors($this->validationListTemplate));
				return redirect()->to('/auth/forgot_password');
			}
		}
	}

	/**
	 * Reset password - final step for forgotten password
	 *
	 * @param string|null $code The reset code
	 *
	 * @return string|\CodeIgniter\HTTP\RedirectResponse
	 */
	public function reset_password($code = null)
	{
		if (!$code) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$this->data['title'] = lang('Auth.reset_password_heading');

		$user = $this->ionAuth->forgottenPasswordCheck($code);

		if ($user) {
			// if the code is valid then display the password reset form

			$this->validation->setRule('new', lang('Auth.reset_password_validation_new_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']|matches[new_confirm]');
			$this->validation->setRule('new_confirm', lang('Auth.reset_password_validation_new_password_confirm_label'), 'required');

			if (!$this->request->getPost() || $this->validation->withRequest($this->request)->run() === false) {
				// display the form

				// set the flash data error message if there is one
				$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');
				$this->data['validation'] = \Config\Services::validation();

				$this->data['minPasswordLength'] = $this->configIonAuth->minPasswordLength;
				$this->data['new_password'] = [
					'name'    => 'new',
					'id'      => 'new',
					'type'    => 'password',
					'pattern' => '^.{' . $this->data['minPasswordLength'] . '}.*$',
					'class' => 'form-control form-control-user',
					'placeholder' => 'Enter New Password...',
				];
				$this->data['new_password_confirm'] = [
					'name'    => 'new_confirm',
					'id'      => 'new_confirm',
					'type'    => 'password',
					'pattern' => '^.{' . $this->data['minPasswordLength'] . '}.*$',
					'class' => 'form-control form-control-user',
					'placeholder' => 'Confirm New Password...',
				];
				$this->data['user_id'] = [
					'name'  => 'user_id',
					'id'    => 'user_id',
					'type'  => 'hidden',
					'value' => $user->id,
				];
				$this->data['code'] = $code;

				// render
				return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'reset_password', $this->data);
			} else {
				$identity = $user->{$this->configIonAuth->identity};

				// do we have a valid request?
				if ($user->id != $this->request->getPost('user_id')) {
					// something fishy might be up
					$this->ionAuth->clearForgottenPasswordCode($identity);

					throw new \Exception(lang('Auth.error_security'));
				} else {
					// finally change the password
					$change = $this->ionAuth->resetPassword($identity, $this->request->getPost('new'));

					if ($change) {
						// if the password was successfully changed
						$this->session->setFlashdata('message', $this->ionAuth->messages());
						return redirect()->to('/login');
					} else {
						$this->session->setFlashdata('message', $this->ionAuth->errors($this->validationListTemplate));
						return redirect()->to('/auth/reset_password/' . $code);
					}
				}
			}
		} else {
			// if the code is invalid then send them back to the forgot password page
			$this->session->setFlashdata('message', $this->ionAuth->errors($this->validationListTemplate));
			return redirect()->to('/auth/forgot_password');
		}
	}

	/**
	 * Activate the user
	 *
	 * @param integer $id   The user ID
	 * @param string  $code The activation code
	 *
	 * @return \CodeIgniter\HTTP\RedirectResponse
	 */
	public function activate(int $id, string $code = ''): \CodeIgniter\HTTP\RedirectResponse
	{
		$uri = service('uri');
		$activation = false;

		if ($code) {
			$activation = $this->ionAuth->activate($id, $code);
		} else if (in_array(1, getGroupId()) || in_array(2, getGroupId())) {
			$activation = $this->ionAuth->activate($id);
		}

		if ($activation) {
			// redirect them to the auth page
			$this->session->setFlashdata('success', $this->ionAuth->messages());
			if ($uri->getSegment(1) == 'url') {
				return redirect()->to('/url');
			} else {
				return redirect()->to('/users');
			}
		} else {
			// redirect them to the forgot password page
			$this->session->setFlashdata('danger', $this->ionAuth->errors($this->validationListTemplate));
			return redirect()->to('/auth/forgot_password');
		}
	}

	/**
	 * Deactivate the user
	 *
	 * @param integer $id The user ID
	 *
	 * @throw Exception
	 *
	 * @return string|\CodeIgniter\HTTP\RedirectResponse
	 */
	public function deactivate(int $id = 0)
	{
		$uri = service('uri');

		if (!$this->ionAuth->loggedIn() || (!in_array(1, getGroupId()) && !in_array(2, getGroupId()))) {
			// redirect them to the home page because they must be an administrator to view this
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('You must be an administrator to view this page.');
			// TODO : I think it could be nice to have a dedicated exception like '\IonAuth\Exception\NotAllowed
		}

		$this->data['title'] = 'Deactivate User';

		$this->validation->setRule('confirm', lang('Auth.deactivate_validation_confirm_label'), 'required');
		$this->validation->setRule('id', lang('Auth.deactivate_validation_user_id_label'), 'required|integer');

		if (!$this->validation->withRequest($this->request)->run()) {
			$this->data['user'] = $this->ionAuth->user($id)->row();
			return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'deactivate_user', $this->data);
		} else {
			// do we really want to deactivate?
			if ($this->request->getPost('confirm') === 'yes') {
				// do we have a valid request?
				if ($id !== $this->request->getPost('id', FILTER_VALIDATE_INT)) {
					throw new \Exception(lang('Auth.error_security'));
				}

				// do we have the right userlevel?
				if ($this->ionAuth->loggedIn() && (in_array(1, getGroupId()) || in_array(2, getGroupId()))) {
					$message = $this->ionAuth->deactivate($id) ? $this->ionAuth->messages() : $this->ionAuth->errors($this->validationListTemplate);
					$this->session->setFlashdata('success', $message);
				}
			}

			// redirect them back to the auth page
			if ($uri->getSegment(1) == 'url') {
				return redirect()->to('/url');
			} else {
				return redirect()->to('/users');
			}
		}
	}

	/**
	 * Create a new user
	 *
	 * @return string|\CodeIgniter\HTTP\RedirectResponse
	 */
	public function create_user()
	{
		$this->data['title'] = lang('Auth.create_user_heading');

		$uri = service('uri');

		if ($uri->getSegment(1) != 'register') {
			if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
				return redirect()->to('/users');
			}
		}

		if ($uri->getSegment(1) == 'register') {
			if ($this->ionAuth->loggedIn()) {
				return redirect()->to('/profile');
			}
		}

		$tables                        = $this->configIonAuth->tables;
		$identityColumn                = $this->configIonAuth->identity;
		$this->data['identity_column'] = $identityColumn;

		// validate form input
		if ($uri->getSegment(1) == 'register') {
			$this->validation->setRules([
				'identity' => [
					'label' => lang('Auth.create_user_validation_identity_label'),
					'rules'  => 'trim|required|alpha_dash_period|is_unique[' . $tables['users'] . '.' . $identityColumn . ']',
					'errors' => [
						'is_unique'    => 'Username tersebut sudah terdaftar, silakan gunakan username lain.',
					]
				],
				'email' => [
					'label' => lang('Auth.create_user_validation_email_label'),
					'rules'  => 'trim|required|valid_email|is_unique[' . $tables['users'] . '.email]',
					'errors' => [
						'is_unique'    => 'email tersebut sudah terdaftar, silakan gunakan email lain.',
					]
				],
				'password' => [
					'label' => lang('Auth.create_user_validation_password_label'),
					'rules'  => 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']|matches[password_confirm]',
				],
				'password_confirm' => [
					'label' => lang('Auth.create_user_validation_password_confirm_label'),
					'rules'  => 'required',
				],
				'nik' => [
					'rules'  => 'trim|required',
				],
				'name' => [
					'label' => 'Nama',
					'rules'  => 'trim|required',
				],
				'jenis_kelamin' => [
					'rules'  => 'trim|required',
				],
				'tempat_lahir' => [
					'rules'  => 'trim|required',
				],
				'tanggal_lahir' => [
					'rules'  => 'trim|required',
				],
				'alamat' => [
					'rules'  => 'trim|required',
				],
				'agama' => [
					'rules'  => 'trim|required',
				],
			]);
		} else {
			$this->validation->setRule('name', lang('Auth.create_user_validation_name_label'), 'trim|required');
			if ($identityColumn !== 'email') {
				$this->validation->setRule('identity', lang('Auth.create_user_validation_identity_label'), 'trim|required|alpha_dash_period|is_unique[' . $tables['users'] . '.' . $identityColumn . ']');
				$this->validation->setRule('email', lang('Auth.create_user_validation_email_label'), 'trim|required|valid_email|is_unique[' . $tables['users'] . '.email]');
			} else {
				$this->validation->setRule('email', lang('Auth.create_user_validation_email_label'), 'trim|required|valid_email|is_unique[' . $tables['users'] . '.email]');
			}
			$this->validation->setRule('password', lang('Auth.create_user_validation_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']|matches[password_confirm]');
			$this->validation->setRule('password_confirm', lang('Auth.create_user_validation_password_confirm_label'), 'required');
		}

		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$input = (object) $this->request->getPost();
			$email    = strtolower($this->request->getPost('email'));
			$identity = ($identityColumn === 'email') ? $email : $this->request->getPost('identity');
			$password = $this->request->getPost('password');

			if ($uri->getSegment(1) == 'register') {
				$additionalData = [
					'name' => $this->request->getPost('name'),
					'image' => 'default.jpg',
					'pegawai' => [
						'nik' => $input->nik,
						'nama' => $input->name,
						'tempat_lahir' => $input->tempat_lahir,
						'tanggal_lahir' => $input->tanggal_lahir,
						'jenis_kelamin' => $input->jenis_kelamin,
						'alamat' => $input->alamat,
						'agama' => $input->agama,
					],
				];
			} else {
				$additionalData = [
					'name' => $this->request->getPost('name'),
					'image' => 'default.jpg',
				];
			}
		}
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run() && $this->ionAuth->register($identity, $password, $email, $additionalData)) {
			// check to see if we are creating the user
			// redirect them back to the admin page
			$this->session->setFlashdata('success', $this->ionAuth->messages());
			if ($uri->getSegment(1) == 'register') {
				return redirect()->to('/login');
			}
			return redirect()->to('/users');
		} else {
			// display the create user form

			// // set the flash data error message if there is one
			// $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : ($this->ionAuth->errors($this->validationListTemplate) ? $this->ionAuth->errors($this->validationListTemplate) : $this->session->getFlashdata('message'));
			$this->data['validation'] = \Config\Services::validation();

			$this->data['name'] = [
				'name'  => 'name',
				'id'    => 'name',
				'type'  => 'text',
			];
			$this->data['identity'] = [
				'name'  => 'identity',
				'id'    => 'identity',
				'type'  => 'text',
			];
			$this->data['email'] = [
				'name'  => 'email',
				'id'    => 'email',
				'type'  => 'email',
			];
			$this->data['password'] = [
				'name'  => 'password',
				'id'    => 'password',
				'type'  => 'password',
			];
			$this->data['password_confirm'] = [
				'name'  => 'password_confirm',
				'id'    => 'password_confirm',
				'type'  => 'password',
			];

			if ($uri->getSegment(1) == 'register') {
				return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'register', $this->data);
			}
			return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'create_user', $this->data);
		}
	}

	// import user
	public function import_user()
	{
		$data = [
			'title' => 'Import Excel Users',
			'validation' => \Config\Services::validation(),
			// 'user_id' => $this->user_id,
			'form_action' => base_url("users/import"),
		];

		// accessesed with get method
		if (!$_POST) {
			return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'import_user', $data);
		};

		// validate when accessesed with post method
		if (!$this->validate($this->ionAuth->getrulesImportUser()) && empty($this->request->getPost('import'))) {
			return redirect()->to("/users/import")->withInput();
		}

		// import proccess
		$file = $this->request->getFile('excel');
		$import = $this->request->getPost('import');

		$resultImport = $this->ionAuth->import_user($file, $import);

		if ($resultImport == 'error') {
			return redirect()->to('/users/import');
		} else if ($resultImport == 'success') {
			return redirect()->to('/users');
		} else {
			$data['error'] = $resultImport['error'];
			$data['preview'] = $resultImport['preview'];
		}

		return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'import_user', $data);
	}

	/**
	 * Redirect a user checking if is admin
	 *
	 * @return \CodeIgniter\HTTP\RedirectResponse
	 */
	public function redirectUser()
	{
		if ($this->ionAuth->isAdmin()) {
			return redirect()->to('/users');
		}
		return redirect()->to('/');
	}

	/**
	 * Edit a user
	 *
	 * @param integer $id User id
	 *
	 * @return string string|\CodeIgniter\HTTP\RedirectResponse
	 */
	public function edit_user(int $id)
	{
		$this->data['title'] = lang('Auth.edit_user_heading');

		if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !($this->ionAuth->user()->row()->id == $id))) {
			return redirect()->to('/users');
		}

		$user          = $this->ionAuth->user($id)->row();
		$groups        = $this->ionAuth->groups()->resultArray();
		$currentGroups = $this->ionAuth->getUsersGroups($id)->getResult();
		$identityColumn                = $this->configIonAuth->identity;
		$this->data['identity_column'] = $identityColumn;

		if (!empty($_POST)) {
			// validate form input
			$this->validation->setRule('name', lang('Auth.edit_user_validation_name_label'), 'trim|required');

			// do we have a valid request?
			if ($id !== $this->request->getPost('id', FILTER_VALIDATE_INT)) {
				//show_error(lang('Auth.error_security'));
				throw new \Exception(lang('Auth.error_security'));
			}

			// update the password if it was posted
			if ($this->request->getPost('password')) {
				$this->validation->setRule('password', lang('Auth.edit_user_validation_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']|matches[password_confirm]');
				$this->validation->setRule('password_confirm', lang('Auth.edit_user_validation_password_confirm_label'), 'required');
			}

			if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
				$email    = strtolower($this->request->getPost('email'));
				$identity = ($identityColumn === 'email') ? $email : $this->request->getPost('identity');
				$data = [
					'id_pegawai' => !empty($this->request->getPost('id_pegawai')) ? $this->request->getPost('id_pegawai') : null,
					'email' => !empty($email) ? $email : null,
					'username' => $identity,
					'name' => $this->request->getPost('name'),
				];

				// update the password if it was posted
				if ($this->request->getPost('password')) {
					$data['password'] = $this->request->getPost('password');
				}

				// Only allow updating groups if user is admin
				if ($this->ionAuth->isAdmin()) {
					// Update the groups user belongs to
					$groupData = $this->request->getPost('groups');

					if (!empty($groupData)) {
						$this->ionAuth->removeFromGroup('', $id);

						foreach ($groupData as $grp) {
							$this->ionAuth->addToGroup($grp, $id);
						}
					}
				}

				// check to see if we are updating the user
				if ($this->ionAuth->update($user->id, $data)) {
					$this->session->setFlashdata('success', $this->ionAuth->messages());
				} else {
					$this->session->setFlashdata('danger', $this->ionAuth->errors($this->validationListTemplate));
				}
				// redirect them back to the admin page if admin, or to the base url if non admin
				return $this->redirectUser();
			}
		}

		// display the edit user form

		// set the flash data error message if there is one
		// $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : ($this->ionAuth->errors($this->validationListTemplate) ? $this->ionAuth->errors($this->validationListTemplate) : $this->session->getFlashdata('message'));
		$this->data['validation'] = \Config\Services::validation();

		// pass the user to the view
		$this->data['user']          = $user;
		$this->data['groups']        = $groups;
		$this->data['currentGroups'] = $currentGroups;
		$this->data['id_pegawai'] 	 = $user->id_pegawai;

		$this->data['name'] = [
			'name'  => 'name',
			'id'    => 'name',
			'type'  => 'text',
			'value' => set_value('name', $user->name ?: ''),
			'class' => 'form-control',
		];
		$this->data['identity'] = [
			'name'  => 'identity',
			'id'    => 'identity',
			'type'  => 'text',
			'value' => set_value('identity', $user->username ?: ''),
		];
		$this->data['email'] = [
			'name'  => 'email',
			'id'    => 'email',
			'type'  => 'email',
			'value' => set_value('email', $user->email ?: ''),
		];
		$this->data['password'] = [
			'name' => 'password',
			'id'   => 'password',
			'type' => 'password',
			'class' => 'form-control',
		];
		$this->data['password_confirm'] = [
			'name' => 'password_confirm',
			'id'   => 'password_confirm',
			'type' => 'password',
			'class' => 'form-control',
		];
		$this->data['ionAuth'] = $this->ionAuth;

		return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'edit_user', $this->data);
	}

	/**
	 * Create a new group
	 *
	 * @return string string|\CodeIgniter\HTTP\RedirectResponse
	 */
	public function create_group()
	{
		$this->data['title'] = lang('Auth.create_group_title');

		if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
			return redirect()->to('/groups');
		}

		// validate form input
		$this->validation->setRule('group_name', lang('Auth.create_group_validation_name_label'), 'trim|required|alpha_dash');

		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$newGroupId = $this->ionAuth->createGroup($this->request->getPost('group_name'), $this->request->getPost('description'));
			if ($newGroupId) {
				// check to see if we are creating the group
				// redirect them back to the admin page
				$this->session->setFlashdata('success', $this->ionAuth->messages());
				return redirect()->to('/groups');
			}
		} else {
			// display the create group form
			// set the flash data error message if there is one
			$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : ($this->ionAuth->errors($this->validationListTemplate) ? $this->ionAuth->errors($this->validationListTemplate) : $this->session->getFlashdata('message'));
			$this->data['validation'] = \Config\Services::validation();

			$this->data['group_name'] = [
				'name'  => 'group_name',
				'id'    => 'group_name',
				'type'  => 'text',
				'value' => set_value('group_name'),
				'class' => 'form-control',
			];
			$this->data['description'] = [
				'name'  => 'description',
				'id'    => 'description',
				'type'  => 'text',
				'value' => set_value('description'),
				'class' => 'form-control',
			];

			return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'create_group', $this->data);
		}
	}

	/**
	 * Edit a group
	 *
	 * @param integer $id Group id
	 *
	 * @return string|CodeIgniter\Http\Response
	 */
	public function edit_group(int $id = 0)
	{
		$this->data['title'] = lang('Auth.edit_group_title');

		if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
			return redirect()->to('/groups');
		}

		$group = $this->ionAuth->group($id)->row();

		// validate form input
		$this->validation->setRule('group_name', lang('Auth.edit_group_validation_name_label'), 'required|alpha_dash');

		if ($this->request->getPost()) {
			if ($this->validation->withRequest($this->request)->run()) {
				$groupUpdate = $this->ionAuth->updateGroup($id, $this->request->getPost('group_name'), ['description' => $this->request->getPost('group_description')]);

				if ($groupUpdate) {
					$this->session->setFlashdata('success', lang('Auth.edit_group_saved'));
				} else {
					$this->session->setFlashdata('danger', $this->ionAuth->errors($this->validationListTemplate));
				}
				return redirect()->to('/groups');
			}
		}

		// set the flash data error message if there is one
		$this->data['message'] = $this->validation->listErrors($this->validationListTemplate) ?: ($this->ionAuth->errors($this->validationListTemplate) ?: $this->session->getFlashdata('message'));
		$this->data['validation'] = \Config\Services::validation();

		// pass the user to the view
		$this->data['group'] = $group;

		$readonly = $this->configIonAuth->adminGroup === $group->name ? 'readonly' : '';

		$this->data['group_name']        = [
			'name'    => 'group_name',
			'id'      => 'group_name',
			'type'    => 'text',
			'value'   => set_value('group_name', $group->name),
			$readonly => $readonly,
			'class' => 'form-control',
		];
		$this->data['group_description'] = [
			'name'  => 'group_description',
			'id'    => 'group_description',
			'type'  => 'text',
			'value' => set_value('group_description', $group->description),
			'class' => 'form-control',
		];

		return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'edit_group', $this->data);
	}

	/**
	 * delete a group
	 *
	 * @param integer $id Group id
	 *
	 * @return string|CodeIgniter\Http\Response
	 */
	public function delete_group($id)
	{
		if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
			return redirect()->to('/users');
		}

		// pass the right arguments and it's done
		$group_delete = $this->ionAuth->deleteGroup($id);

		if ($group_delete) {
			// check to see if we are deleting the group
			// redirect them back to the admin page
			$this->session->setFlashdata('success', $this->ionAuth->messages());
		} else {
			$this->session->setFlashdata('error', $this->ionAuth->errors());
		}

		return redirect()->to('/groups');
	}

	/**
	 * set access group view
	 *
	 * @param integer $id Group id
	 *
	 * @return string|CodeIgniter\Http\Response
	 */
	public function access_group($id)
	{
		$this->data['title'] = 'Group Access';

		if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
			return redirect()->to('/groups');
		}
		if ($id == 1) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$db      = \Config\Database::connect();
		$group = $this->ionAuth->group($id)->row();

		if (!$group) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Group Not Found');
		}

		$this->data['group'] = $group;

		$builder = $db->table('groups_access');
		$builder->select('menu_id');
		$builder->where('group_id', $group->id);
		$this->data['groups_access'] = $builder->get()->getResultArray();

		return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'access_group', $this->data);
	}

	/**
	 * ajax to create modal menu access (crud form)
	 *
	 */
	public function menu_access($menu_id)
	{
		$db      = \Config\Database::connect();
		$builder = $db->table('menus_access');

		$data = $builder->where('menu_id', $menu_id)->get()->getRow();
		echo json_encode($data);
	}

	/**
	 * ajax get selected groups access (crud for groups access)
	 *
	 */
	public function groups_access($group_id, $menu_id)
	{
		$db      = \Config\Database::connect();
		$builder = $db->table('groups_access');

		$builder->where('group_id', $group_id);
		$builder->where('menu_id', $menu_id);
		$data = $builder->get()->getRow();
		echo json_encode($data);
	}

	/**
	 * change groups access
	 *
	 */
	public function change_access()
	{
		if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$db      = \Config\Database::connect();
		$builder = $db->table('groups_access');

		$input  = (object) $this->request->getPost();
		$data   = [
			'group_id' => $input->roleId,
			'menu_id' => $input->menuId,
		];

		$result = $builder->where($data)->countAllResults();

		if ($result < 1) {
			$builder->insert($data);
		} else {
			$builder->delete($data);
		}
	}

	/**
	 * change groups access of crud (insert, update, delete, validate)
	 *
	 */
	public function change_crud_access()
	{
		if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$db      = \Config\Database::connect();
		$builder = $db->table('groups_access');

		$input  = (object) $this->request->getPost();
		$data = [
			'insert' => isset($input->insert) ? 1 : 0,
			'update' => isset($input->update) ? 1 : 0,
			'delete' => isset($input->delete) ? 1 : 0,
			'validate' => isset($input->validate) ? 1 : 0,
		];

		$builder->where('group_id', $input->group_id);
		$builder->where('menu_id', $input->menu_id);
		if ($builder->countAllResults() > 0) {
			$builder->where('group_id', $input->group_id);
			$builder->where('menu_id', $input->menu_id);
			$builder->update($data);
		} else {
			$this->session->setFlashdata('danger', 'The group doesn\'t have access to the menu');
			return redirect()->to("/groups/access/$input->group_id");
		}

		if ($db->affectedRows() > 0) {
			$this->session->setFlashdata('success', 'Group access has been saved');
		} else {
			$this->session->setFlashdata('danger', 'There\'s no data has changed or something went wrong');
		}

		return redirect()->to("/groups/access/$input->group_id");
	}

	/**
	 * Render the specified view
	 *
	 * @param string     $view       The name of the file to load
	 * @param array|null $data       An array of key/value pairs to make available within the view.
	 * @param boolean    $returnHtml If true return html string
	 *
	 * @return string|void
	 */
	protected function renderPage(string $view, $data = null, bool $returnHtml = true): string
	{
		$viewdata = $data ?: $this->data;

		$viewHtml = view($view, $viewdata);

		if ($returnHtml) {
			return $viewHtml;
		} else {
			echo $viewHtml;
		}
	}
}
