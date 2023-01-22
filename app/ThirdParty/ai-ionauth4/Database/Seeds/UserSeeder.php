<?php
namespace IonAuth\Database\Seeds;

/**
 * @package CodeIgniter-Ion-Auth
 */

class UserSeeder extends \CodeIgniter\Database\Seeder
{
	/**
	 * Dumping data for table 'groups', 'users, 'users_groups'
	 *
	 * @return void
	 */
	public function run()
	{
		$config = config('IonAuth\\Config\\IonAuth');
		$this->DBGroup = empty($config->databaseGroupName) ? '' : $config->databaseGroupName;
		$tables        = $config->tables;

		$groups = [
			[
				'id'          => 1,
				'name'        => 'SuperAdmin',
				'description' => 'Super Administrator',
			],
			[
				'id'          => 2,
				'name'        => 'Admin',
				'description' => 'Administrator',
			],
			[
				'id'          => 3,
				'name'        => 'Members',
				'description' => 'General User',
			],
		];
		$this->db->table($tables['groups'])->insertBatch($groups);

		$users = [
			[
				'ip_address'              => '127.0.0.1',
				'username'                => 'fuwa_aika',
				'password'                => '$2y$12$zkSj/iw.1iGg/8B6LLOb5erwKqm12kavR9yQZzP56ipbOMWerEjni',
				'email'                   => 'aika@zetsuent.tempest',
				'activation_code'         => '',
				'forgotten_password_code' => null,
				'created_on'              => '1268889823',
				'last_login'              => '1268889823',
				'active'                  => '1',
				'name'              	  => 'Fuwa Aika',
				'image'              	  => 'default.jpg',
			],
		];
		$this->db->table($tables['users'])->insertBatch($users);

		$usersGroups = [
			[
				'user_id'  => '1',
				'group_id' => '1',
			],
		];
		$this->db->table($tables['users_groups'])->insertBatch($usersGroups);
	}
}
