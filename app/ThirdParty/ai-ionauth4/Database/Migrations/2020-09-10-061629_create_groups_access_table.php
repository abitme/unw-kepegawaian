<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGroupsAccessTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id'          => [
				'type'           => 'INT',
				'constraint'     => 11,
				'unsigned'       => true,
				'auto_increment' => true,
			],
			'group_id'          => [
				'type'           => 'INT',
				'constraint'     => 1,
			],
			'menu_id'          => [
				'type'           => 'INT',
				'constraint'     => 1,
			],
			'insert'          => [
				'type'           => 'TINYINT',
				'constraint'     => 1,
			],
			'update'         => [
				'type'           => 'TINYINT',
				'constraint'     => 1,
			],
			'delete'         => [ 
				'type'           => 'TINYINT',
				'constraint'     => 1,
			],
			'validate'         => [ 
				'type'           => 'TINYINT',
				'constraint'     => 1,
			],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('groups_access');
	}

	public function down()
	{
		$this->forge->dropTable('groups_access');
	}
}
