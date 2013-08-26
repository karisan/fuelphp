<?php

/**
 * 儲存書籍資料，原本套件內附的Table
 * @author  karisan
 */

class Model_Books extends \Model_Crud {

    /**
     * @var Table 名稱
     */
    protected static $_table_name = 'books';

    /**
     * @var Table PK
     */
    protected static $_primary_key = 'id';

    /**
     * @var Table 欄位
     */
    protected static $_properties = array(
        'id',
        'title',
        'binding',
        'quantity',
        'created_at',
        'updated_at'
    );

    protected static $_created_at = 'created_at';

    protected static $_updated_at = 'updated_at';



}
