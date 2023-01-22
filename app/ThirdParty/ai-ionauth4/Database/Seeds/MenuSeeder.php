<?php
namespace IonAuth\Database\Seeds;

/**
 * @package CodeIgniter-Ion-Auth
 */

class MenuSeeder extends \CodeIgniter\Database\Seeder
{
	public function run()
	{
		$menus = [
			[
				'label'     => 'Super Admin',
				'link' 		=> '#',
				'parent' 	=> '0',
				'sort'	 	=> '1',
				'icon'	 	=> 'no-icon',
			],
			[
				'label'     => 'Menus',
				'link' 		=> 'menus',
				'parent' 	=> '1',
				'sort'	 	=> '2',
				'icon'	 	=> 'fas fa-fw fa-bars',
			],
			[
				'label'     => 'Users',
				'link' 		=> 'users',
				'parent' 	=> '1',
				'sort'	 	=> '3',
				'icon'	 	=> 'fas fa-fw fa-user',
			],
			[
				'label'     => 'Groups',
				'link' 		=> 'groups',
				'parent' 	=> '1',
				'sort'	 	=> '4',
				'icon'	 	=> 'fas fa-fw fa-users',
			],
		];
		$this->db->table('menus')->insertBatch($menus);

		$menus_access = [
			[
				'menu_id'     	=> '1',
				'insert' 		=> '0',
				'update' 		=> '0',
				'delete'	 	=> '0',
				'validate'	 	=> '0',
			],
			[
				'menu_id'     	=> '2',
				'insert' 		=> '0',
				'update' 		=> '0',
				'delete'	 	=> '0',
				'validate'	 	=> '0',
			],
			[
				'menu_id'     	=> '3',
				'insert' 		=> '0',
				'update' 		=> '0',
				'delete'	 	=> '0',
				'validate'	 	=> '0',
			],
			[
				'menu_id'     	=> '4',
				'insert' 		=> '0',
				'update' 		=> '0',
				'delete'	 	=> '0',
				'validate'	 	=> '0',
			],
		];
		$this->db->table('menus_access')->insertBatch($menus_access);

	}
}
