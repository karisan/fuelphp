<?php
/**
 * Created by JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/5
 * Time: 上午 10:04
 * To change this template use File | Settings | File Templates.
 */

class Model_User extends \Model_Crud {

    protected static $_table_name = 'users';

    protected static $_primary_key = 'id';

    protected static $_properties = array(
        'id',
        'username',
        'password',
        'level',
        'email',
        'created_at',
        'update_at',
    );

}
