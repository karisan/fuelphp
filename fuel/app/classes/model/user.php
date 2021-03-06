<?php
use Orm\Model;

class Model_User extends Model
{
	protected static $_properties = array(
		'id',
		'username',
		'password',
		'level',
		'email',
		'group',
		'last_login',
		'login_hash',
		'profile_fields',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => false,
		),
	);

	public static function validate($factory)
	{
		$val = Validation::forge($factory);
		$val->add_field('username', 'Username', 'required|max_length[255]');
		$val->add_field('password', 'Password', 'required|max_length[40]');
		$val->add_field('level', 'Level', 'required|valid_string[numeric]');
		$val->add_field('email', 'Email', 'required|valid_email|max_length[255]');
		$val->add_field('group', 'Group', 'required|valid_string[numeric]');
		$val->add_field('last_login', 'Last Login', 'required|valid_string[numeric]');
		$val->add_field('login_hash', 'Login Hash', 'required|max_length[255]');
		$val->add_field('profile_fields', 'Profile Fields', 'required');

		return $val;
	}

}
