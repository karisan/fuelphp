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
        $username = Input::post('username');
        $email = Input::post('email');
        $context = Input::post('context');

        if (empty($username)) {
            echo "<script>alert('參數有誤，返回原頁面');</script>";
            return Response::redirect('/welcome', 'refresh');
        }

        // 寫入DB，將留言資料顯示
        $user = Model_Message::forge()->set(
            array('m_name' => $username,
                  'm_email' => $email,
                  'm_context' => $context,
                  'm_time' => date('Y/m/d H:i:s'))
        );

        // 新增留言
        $result = $user->save();

        // log 處理
        $tmp_username = 'guest';
        if (!is_null(Session::get('valid'))) {
            $tmp_username = Session::get('valid')->user;
        }

        // 新增的內容串起來
        $tmp_info = '['.$tmp_username.'] - add_message'."\n";
        $tmp_info .= 'name:'.$username."\n";
        $tmp_info .= 'email:'.$email."\n";
        $tmp_info .= 'context:'.$context."\n";
        // 寫入 log
        $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);
        $mylog->user_action_log($tmp_username, 'add_message', 'S', $tmp_info);

        echo "<script>alert('新增留言成功');</script>";
        return Response::redirect('/welcome', 'refresh');
    }

        /**
     * 刪除留言,使用get取得del_id即刪除
     *
     * @access  public
     * @return  Response
     */
    public function action_delmsg()
    {
        // 檢查是否有id參數
        $del_id = Input::param('del_id');
        if (empty($del_id)) {
            echo "<script>alert('參數有誤，返回原頁面');</script>";
            return Response::redirect('/welcome', 'refresh');
        }

        // 檢查該筆留言是否存在
        $message = Model_Message::find_by_pk($del_id);
        if (!$message) {
            echo "<script>alert('參數有誤，返回原頁面');</script>";
            return Response::redirect('/welcome', 'refresh');
        }

        // log 處理
        $tmp_username = 'guest';
        if (!is_null(Session::get('valid'))) {
            $tmp_username = Session::get('valid')->user;
        }

        // 刪除
        $message->delete();

        // 新增的內容串起來
        $tmp_info = 'id:'.$message->m_id."\n";
        $tmp_info .= 'name:'.$message->m_name."\n";
        $tmp_info .= 'email:'.$message->m_email."\n";
        $tmp_info .= 'context:'.$message->m_context."\n";

        // 寫入 log
        $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);
        $mylog->user_action_log($tmp_username, 'del_message', 'S', $tmp_info);

        echo "<script>alert('刪除留言成功');</script>";
        return Response::redirect('/welcome', 'refresh');
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
        $entry = Model_Message::find(
            array(
                'order_by' => array('m_id' => 'desc'),
                'limit' => 20,)
        );

        $view = View::forge('welcome/hello');
        $view->data = $entry;
        $view->name = 'karisan';
        $view->valid = Session::get('valid');
        return Response::forge($view);
    }

    /**
     * 展示rest功能的頁面
     *
     * @access  public
     * @return  Response
     */
    public function action_rest_index()
    {
        $content = '<li>'.Html::anchor('resttest/list.xml?foo=bar', 'Rest範例 - API with xml').'</li>'."\n";
        $content .= '<li>'.Html::anchor('resttest/list.json?foo=bar', 'Rest範例 - API with json').'</li>'."\n";
        $content .= '<li>'.Html::anchor('resttest/list.csv?foo=csv', 'Rest範例 - API with csv').'</li>'."\n";
        $content .= '<li>'.Html::anchor('resttest/list.html?foo=bar', 'Rest範例 - API with html').'</li>'."\n";
        $content .= '<li>'.Html::anchor('resttest/list.php?foo=bar', 'Rest範例 - API with php').'</li>'."\n";
        $content .= '<li>'.Html::anchor('resttest/list.serialize?foo=bar', 'Rest範例 - API with serialize').'</li>'."\n";
        $content .= '<li>'.Html::anchor('welcome/curl', 'Rest範例 - Client').'</li>'."\n";


        $view = View::forge('empty');
        $view->set('content', $content, false);
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

    /**
     * 簡單的 Rest Client (Request Curl)
     *
     * @access  public
     * @return  Response
     */
    public function action_curl()
    {

        // 建立一個 Request_Curl 物件
        $curl = Request::forge('http://localhost/fuelphp/public/resttest/list.php?foo=bar', 'curl');
        //$curl = Request::forge('resttest/list.json');

        $curl->set_method('get');

        // 假設一些參數和選項已經被設定，並且執行
        //$curl->execute();
        print_r($curl);
        echo 'after curl';
    }


}
