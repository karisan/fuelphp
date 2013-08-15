<?php
/**
 *
 * 判斷是否已登入，未登入時導向/validate/expired頁面
 *
 * @package
 * @category
 * @author    karisan
 */

class Controller_Protected extends Controller_Template {
    /**
     *
     * 未登入時，導向/validate/expired頁面
     *
     * @param   void
     * @return  response
     */
    public function before()
    {
        parent::before();
        if (is_null(Session::get('valid'))) {
            Response::redirect('/validate/expired');
        }
    }
}