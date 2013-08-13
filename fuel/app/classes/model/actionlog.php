<?php
/**
 * Created by JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/12
 * Time: 下午 6:33
 * To change this template use File | Settings | File Templates.
 */

/**
 * 儲存user各種操作的記錄
 * @author  karisan
 */
class Model_Actionlog extends \Model_Crud {
    /**
     * @var Table 名稱
     */
    protected static $_table_name = 'action_log';

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
        'time',
        'ip',
        'action',
        'status',
        'url',
        'info',
    );

}
