<?php
/**
 * Created by JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/5
 * Time: 上午 10:04
 * To change this template use File | Settings | File Templates.
 */

/**
 * 儲存使用者資料
 * @author  karisan
 */
class Model_Users extends \Model_Crud {
    /**
     * @var Table 名稱
     */
    protected static $_table_name = 'users';

    /**
     * @var Table PK
     */
    protected static $_primary_key = 'id';

    /**
     * @var Table 欄位
     */
    protected static $_properties = array(
        'id',
        'username',
        'password',
        'level',
        'email',
        'created_at',
        'updated_at',
        'group',
        'last_login',
        'last_hash',
        'porfile_fields',
    );

}
