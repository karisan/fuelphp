<?php
/**
 * Created by JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/5
 * Time: 上午 10:04
 * To change this template use File | Settings | File Templates.
 */

/**
 * 儲存留言資料
 * @author  karisan
 */

class Model_Message extends \Model_Crud {

    /**
     * @var Table 名稱
     */
    protected static $_table_name = 'message';

    /**
     * @var Table PK
     */
    protected static $_primary_key = 'm_id';

    /**
     * @var Table 欄位
     */
    protected static $_properties = array(
        'm_id',
        'm_name',
        'm_email',
        'm_time',
        'm_context',
    );

}
