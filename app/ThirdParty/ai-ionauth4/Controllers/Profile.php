<?php

namespace IonAuth\Controllers;

use IonAuth\Models\ProfileModel;

class Profile extends AdminBaseController
{
	protected $ProfileModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->ProfileModel = new ProfileModel();
		$pegawai = $this->db->table('users')->select('id_pegawai')->where('id', session('user_id'))->get()->getRow();
		$this->id_pegawai = $pegawai->id_pegawai ?? '';
	}

	public function index()
	{
		$user = $this->db->table('users')->where('id', $this->user_id)->get()->getRow();
		$pegawai = $this->db->table('pegawai')->where('id', $user->id_pegawai)->get()->getRow();
		$data = [
			'title' => 'My Profile',
			'user' => $user,
			'pegawai' => $pegawai,
		];

		return $this->view('IonAuth\Views\profile\index', $data);
	}

	public function edit()
	{
		$user = $this->db->table('users')->where('id', $this->user_id)->get()->getRow();
		$pegawai = $this->db->table('pegawai')->where('id', $user->id_pegawai)->get()->getRow();
		$data = [
			'title' => 'Edit Profile',
			'user' => $user,
			'pegawai' => $pegawai,
			'validation' => \Config\Services::validation(),
		];

		return $this->view('IonAuth\Views\profile\edit', $data);
	}

	public function update()
	{

		// set method to post, to make validation work correctly
		if ($this->request->getMethod() == 'put') {
			$this->request->setMethod('post');
		}

		// validate
		if (!$this->validate($this->ProfileModel->getValidationRules())) {
			return redirect()->to("/profile/edit")->withInput();
		}

		// set data
		$data = $this->request->getPost();
		$data['email'] = !empty($this->request->getPost('email')) ? $this->request->getPost('email') : null;
		$data['id'] = $this->user_id;

		// get uploaded image
		$image = $this->request->getFile('image');

		// check image exist
		if ($image->getError() == 4) {
			$data['image'] = $data['fileLama'];
		} else {
			$nik = isset($data['nik']) ? $data['nik'] . '-' : '';
			$data['image'] = url_title("{$nik}{$data['name']}", '-', true) . '.' . $image->getExtension();
		}

		// save data
		$this->ProfileModel->save($data);

		// move file to public
		if ($image->getError() != 4) {
			// delete old file
			if ($data['fileLama'] != 'default.jpg' && !empty($data['fileLama']) && file_exists("assets/img/users/{$data['fileLama']}")) {
				unlink("assets/img/users/{$data['fileLama']}");
			}
			if ($data['fileLama'] != 'default.jpg' && !empty($data['fileLama']) && file_exists("assets/img/users/thumbnail/{$data['fileLama']}")) {
				unlink("assets/img/users/thumbnail/{$data['fileLama']}");
			}

			// fit image
			if (!file_exists('assets/img/users')) {
				mkdir('assets/img/users', 755);
			}
			\Config\Services::image()
				->withFile($image->getTempName())
				->resize(600, 600, 'center')
				->save("assets/img/users/{$data['image']}");

			// create thumbnail image
			if (!file_exists('assets/img/users/thumbnail')) {
				mkdir('assets/img/users/thumbnail', 755);
			}
			\Config\Services::image()
				->withFile($image->getTempName())
				->resize(300, 300, 'center')
				->save("assets/img/users/thumbnail/{$data['image']}");

			// $image->move("assets/img/users", $data['image']);
		}

		// get input data
		$input = (object) $this->request->getPost();
		if ($input->id_pegawai) {
			$dataPegawai = [
				'nik' => $input->nik ?? '',
				'nama' => $input->name ?? '',
				'tempat_lahir' => $input->tempat_lahir ?? '',
				'tanggal_lahir' => !empty($input->tanggal_lahir) ? $input->tanggal_lahir : null,
				'jenis_kelamin' => $input->jenis_kelamin ?? '',
				'alamat' => $input->alamat ?? '',
				'agama' => $input->agama ?? '',
				'pendidikan' => $input->pendidikan ?? '',
			];
			$this->db->table('pegawai')->where('id', $this->id_pegawai)->update($dataPegawai);
		}

		$this->session->setFlashdata('success', 'data berhasil diubah');
		return redirect()->to('/profile');
	}

	//--------------------------------------------------------------------

}
