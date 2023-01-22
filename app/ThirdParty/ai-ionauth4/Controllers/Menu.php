<?php

namespace IonAuth\Controllers;

use IonAuth\Models\MenuModel;

class Menu extends AdminBaseController
{
	protected $db;
	protected $MenuModel;

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->MenuModel = new MenuModel();

		// only can be accessed by super admin
		if (!$this->ionAuth->isAdmin()) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('You must be an administrator to view this page.');
		}
	}

	public function index()
	{
		$data = [
			'title' => 'Menus',
		];

		return view('IonAuth\Views\menu\index', $data);
	}

	public function create()
	{
		// validate
		if (!$this->validate($this->MenuModel->getValidationRules())) {

			$validation = \Config\Services::validation();

			$data = [
				'label' => $validation->getError('label'),
				'link' => $validation->getError('link'),
			];

			echo json_encode($data);
			return;
		}

		$input = (object) $this->request->getPost();

		// add menus
		$builder = $this->db->table('menus');
		$last = $builder->orderBy('sort', 'desc')
			->limit(1)
			->get()
			->getRow();

		$dataMenu = [
			'label' => $input->label,
			'link' => $input->link,
			'sort' => $builder->countAllResults() > 0 ? $last->sort + 1 : 1,
			'icon'  => $input->icon,
		];

		$builder->insert($dataMenu);
		$idMenu = $this->db->insertID();

		// add groups access
		$builder = $this->db->table('groups_access');
		if (isset($input->role)) {
			foreach ($input->role as $roleID) {
				$dataAccess = [
					'group_id' => $roleID,
					'menu_id' => $idMenu,
				];

				$builder->insert($dataAccess);
			}
		}

		// add menus access 
		$builder = $this->db->table('menus_access');
		isset($input->insert) ? $input->insert : $input->insert = 0;
		isset($input->update) ? $input->update : $input->update = 0;
		isset($input->delete) ? $input->delete : $input->delete = 0;
		isset($input->validate) ? $input->validate : $input->validate = 0;
		$data = [
			'menu_id' => $idMenu,
			'insert' => $input->insert,
			'update' => $input->update,
			'delete' => $input->delete,
			'validate' => $input->validate
		];

		$builder->insert($data);
		$this->session->setFlashdata('success', 'Menu has been added!');
	}

	public function update($id)
	{
		$content = $this->MenuModel->where('id', $id)->first();

		if (!$content) {
			$this->session->setFlashdata('warning', 'Maaf! Data tidak ditemukan');
			return redirect()->to('menus');
		}

		if (!$_POST) {
			// repoulate form
			$input = $content;
			echo json_encode($input);
			return;
		} else {
			// user input for update data
			$input = (object) $this->request->getPost();
		}

		// validate
		if (!$this->validate($this->MenuModel->getValidationRules())) {

			$validation = \Config\Services::validation();

			$data = [
				'label' => $validation->getError('label'),
				'link' => $validation->getError('link'),
			];

			echo json_encode($data);
			return;
		}

		// update menus
		$builder = $this->db->table('menus');
		$data = [
			'label' => $input->label,
			'link'  => $input->link,
			'icon'  => $input->icon,
		];

		$builder->where('id', $input->id)->update($data);

		// update groups access
		$builder = $this->db->table('groups_access');
		$idMenu = $input->id;

		if (!empty($input->role)) {
			$usersAccess = $builder->where('menu_id', $idMenu)->get()->getResult();

			$dataGroupId = [];
			if ($usersAccess) {
				foreach ($usersAccess as $row) {
					array_push($dataGroupId, $row->group_id);
				}
			}

			$inputGroupId = [];
			foreach ($input->role as $roleID) {
				array_push($inputGroupId, $roleID);
			}

			// check if remove access then delete groups access
			foreach ($dataGroupId as $roleID) {
				if (!in_array($roleID, $inputGroupId)) {
					$usersAccess = $builder->where('group_id', $roleID)->where('menu_id', $idMenu)->delete();
				}
			}

			// check if add access then add groups access
			foreach ($inputGroupId as $roleID) {
				if (!in_array($roleID, $dataGroupId)) {

					$dataAccess = [
						'group_id' => $roleID,
						'menu_id' => $idMenu,
					];

					$builder->insert($dataAccess);
				}
			}
		}

		// update menus access
		$builder = $this->db->table('menus_access');
		isset($input->insert) ? $input->insert : $input->insert = 0;
		isset($input->update) ? $input->update : $input->update = 0;
		isset($input->delete) ? $input->delete : $input->delete = 0;
		isset($input->validate) ? $input->validate : $input->validate = 0;
		$data = [
			'menu_id' => $idMenu,
			'insert' => $input->insert,
			'update' => $input->update,
			'delete' => $input->delete,
			'validate' => $input->validate
		];

		if ($builder->countAllResults() < 1) {
			$builder->insert($data);
		} else {
			$builder->where('menu_id', $idMenu)->update($data);

			// to check whether crud menus access 0 then set crud groups access to 0
			$builder = $this->db->table('groups_access');
			if ($input->insert != 1) {
				$builder->where('menu_id', $idMenu)->update(['insert' => $input->insert,]);
			}
			if ($input->update != 1) {
				$builder->where('menu_id', $idMenu)->update(['update' => $input->update,]);
			}
			if ($input->delete != 1) {
				$builder->where('menu_id', $idMenu)->update(['delete' => $input->delete,]);
			}
			if ($input->validate != 1) {
				$builder->where('menu_id', $idMenu)->update(['validate' => $input->validate,]);
			}
		}

		$this->session->setFlashdata('success', 'Menu has been updated!');
	}

	public function delete($menuId)
	{
		if (!$_POST) {
			return redirect()->to('menus');
		}

		if (!$this->MenuModel->where('id', $menuId)->first()) {
			$this->session->setFlashdata('warning', 'Maaf! Data tidak ditemukan');
			return redirect()->to('menus');
		}

		// delete menus
		$builder = $this->db->table('menus');
		$builder->where('id', $menuId)->delete();

		// delete groups access
		$builder = $this->db->table('groups_access');
		$usersAccess = $builder->where('menu_id', $menuId)->get()->getResult();
		foreach ($usersAccess as $row) {
			$builder->where('menu_id', $row->menu_id)->delete();
		}

		// delete menus access
		$builder = $this->db->table('menus_access');
		$usersAccess = $builder->where('menu_id', $menuId)->get()->getResult();
		foreach ($usersAccess as $row) {
			$builder->where('menu_id', $row->menu_id)->delete();
		}

		$this->session->setFlashdata('success', 'Menu has been deleted!');
	}

	public function sortMenu()
	{
		$data = json_decode($_POST['nestable-output']);
		// var_dump($data);
		function parseJsonArray($jsonArray, $parentID = 0)
		{

			$return = array();
			foreach ($jsonArray as $subArray) {

				$returnSubSubArray = array();

				if (isset($subArray->children)) {
					$returnSubSubArray = parseJsonArray($subArray->children, $subArray->id);
				}

				$return[] = array('id' => $subArray->id, 'parentID' => $parentID);
				$return = array_merge($return, $returnSubSubArray);
			}

			return $return;
		}

		$readbleArray = parseJsonArray($data);

		if ($this->MenuModel->updateSort($readbleArray)) {
			$this->session->setFlashdata('success', 'Menu has been sorted!');
		} else {
			$this->session->setFlashdata('error', 'Oops! Terjadi suatu kesalahan');
		}

		return redirect()->to('/menus');
	}

	public function groupSelected($id)
	{
		// $id = json_decode($_POST['id']);
		$builder = $this->db->table('groups_access');
		$data = $builder->where('menu_id', $id)->get()->getResult();
		echo json_encode($data);
	}

	public function crudSelected($id)
	{
		// $id = json_decode($_POST['id']);

		$builder = $this->db->table('menus_access');
		$data = $builder->where('menu_id', $id)->get()->getRow();
		echo json_encode($data);
	}
	//--------------------------------------------------------------------

}
