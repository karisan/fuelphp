<?php

namespace Fuel\Migrations;

class Create_users_providers
{
	public function up()
	{
		\DBUtil::create_table('users_providers', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'parent_id' => array('constraint' => 11, 'type' => 'int'),
			'provider' => array('constraint' => 50, 'type' => 'varchar'),
			'uid' => array('constraint' => 255, 'type' => 'varchar'),
			'secret' => array('constraint' => 255, 'type' => 'varchar'),
			'access_token' => array('constraint' => 255, 'type' => 'varchar'),
			'expires' => array('constraint' => 12, 'type' => 'int'),
			'refresh_token' => array('constraint' => 255, 'type' => 'varchar'),
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('users_providers');
	}
}