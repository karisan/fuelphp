<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.6
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Welcome extends Controller
{

    /**
     * 新增留言,使用post
     *
     * @access  public
     * @return  Response
     */
    public function action_addmsg()
    {
        if (!empty($_POST['username'])) {

            // 寫入DB，將留言資料顯示
            $user = Model_Message::forge()->set(array(
                    'm_name' => $_POST['username'],
                    'm_email' => $_POST['email'],
                    'm_context' => $_POST['context'],
                    'm_time' => date("Y/m/d H:i:s"),
                ));

            // 新增留言
            $result = $user->save();
            echo "<script>alert('新增留言成功');</script>";
            return Response::redirect('/welcome', 'refresh');
        }
    }

        /**
     * 刪除留言,使用get取得del_id即刪除
     *
     * @access  public
     * @return  Response
     */
    public function action_delmsg()
    {
        if (!empty($_GET['del_id'])) {
            // 刪除留言
            $user = Model_Message::find_by_pk($_GET['del_id']);
            if($user)
            {
                $user->delete();
                //echo '成功刪除';
                $return_msg = '成功刪除';
                echo "<script>alert('刪除留言成功');</script>";
            }
            return Response::redirect('/welcome', 'refresh');
        }
    }

    /**
     * 登入後的首頁顯示，內含刪除、新增留言的處理
     *
     * @access  public
     * @return  Response
     */
    public function action_index()
    {
        // 尋找所有主題，顯示最新20筆
        $entry = Model_Message::find(array(
                'order_by' => array('m_id' => 'desc'),
                'limit' => 20,
            ));

        //print_r(Session::get('valid'));

        // 無設定值用法
        // return Response::forge(View::forge('welcome/index'));

        $view = View::forge('welcome/hello');
        $view->data = $entry;
        $view->name = 'karisan';
        $view->valid = Session::get('valid');
        return Response::forge($view);
    }

    /**
     * The 404 action for the application.
     *
     * @access  public
     * @return  Response
     */
    public function action_404()
    {
        return Response::forge(ViewModel::forge('welcome/404'), 404);
    }
}
