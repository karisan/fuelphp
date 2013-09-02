<?php
/**
 * Created by JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/30
 * Time: 下午 3:12
 * To change this template use File | Settings | File Templates.
 */

class Controller_Resttest extends Controller_Rest
{

    public function get_list()
    {
        $foo = Input::get('foo');
        Log::write('Link', '======== Test ========');
        Log::write('Link', print_r($foo,true));

        if ($foo == 'csv') {
            return $this->response(array("abc", "ccc", "foo", "bar"));
        } else {
            return $this->response(array(
                    'foo' => Input::get('foo'),
                    'baz' => array(
                        1, 50, 219
                    ),
                    'empty' => null
                ));
        }
    }

    /**
     * 留言版 列表頁, 單筆資料頁, 修改顯示頁
     * @return  Rest Response
     */
    public function get_show()
    {

    }

    /**
     * 留言版 新增
     * @return  Rest Response
     */
    public function get_create()
    {

    }

    /**
     * 留言版 刪除
     * @return  Rest Response
     */
    public function get_delete()
    {

    }

    /**
     * 留言版 修改
     * @return  Rest Response
     */
    public function get_update()
    {

    }

}
