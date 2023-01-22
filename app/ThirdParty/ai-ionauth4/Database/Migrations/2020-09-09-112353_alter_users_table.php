<?php

namespace IonAuth\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersTable extends Migration
{
	public function up()
	{
		$this->forge->dropColumn('users', ['first_name', 'last_name', 'company', 'phone']);

		$fields = [
			'name'          => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
			],
			'image'          => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
			],
		];
		$this->forge->addColumn('users', $fields);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropColumn('users', ['name', 'image']);

		$fields = [
			'first_name' => [
				'type'       => 'VARCHAR',
				'constraint' => '50',
				'null'       => true,
			],
			'last_name' => [
				'type'       => 'VARCHAR',
				'constraint' => '50',
				'null'       => true,
			],
			'company' => [
				'type'       => 'VARCHAR',
				'constraint' => '100',
				'null'       => true,
			],
			'phone' => [
				'type'       => 'VARCHAR',
				'constraint' => '20',
				'null'       => true,
			],
		];
		$this->forge->addColumn('users', $fields);
	}
}
