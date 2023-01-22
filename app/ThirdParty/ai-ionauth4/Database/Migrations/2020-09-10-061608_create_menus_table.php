<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMenusTable extends Migration
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
			'label'          => [
				'type'           => 'VARCHAR',
				'constraint'     => 200,
			],
			'link'          => [
				'type'           => 'VARCHAR',
				'constraint'     => 100,
			],
			'parent'         => [
				'type'           => 'INT',
				'constraint'     => 10,
			],
			'sort'         => [ 
				'type'           => 'INT',
				'constraint'     => 10,
			],
			'icon'         => [ 
				'type'           => 'VARCHAR',
				'constraint'     => 255,
			],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('menus');
	}

	public function down()
	{
		$this->forge->dropTable('menus');
	}
}
