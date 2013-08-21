<?php
/**
 *
 * 更新留言
 *
 * @package
 * @category
 * @author    karisan
 */
class Controller_Update extends Controller
{
    /**
     * 顯示待修改的留言
     *
     * @param   void
     * @return  Response
     */
    public function action_index()
    {
        $m_id = Input::param('m_id');
        $entry = Model_Message::find_by_pk($m_id);

        $view = View::forge('welcome/update');
        $view->data = $entry;
        $view->m_id = $m_id;
        $view->name = 'karisan';
        return Response::forge($view);
    }

    /**
     * 確認修改 或 取消 時，由此處理
     *
     * @param   void
     * @return  Response
     */
    public function post_index()
    {
        $cancel = Input::post('Cancel');
        $submit = Input::post('submit');

        if (!empty($cancel)) {
            // 按下取消時，返回原頁面
            return Response::redirect('/welcome', 'refresh');
        }

        if (empty($submit)) {
            echo "<script>alert('參數有誤，返回原頁面');</script>";
            return Response::redirect('/welcome', 'refresh');
        }

        $user = Model_Message::find_by_pk(Input::post('m_id'));
        if ($user === null) {
            echo "<script>alert('參數有誤，返回原頁面');</script>";
            return Response::redirect('/welcome', 'refresh');
        }

        // 確認修改
        $user->m_name = Input::post('username');
        $user->m_email = Input::post('email');
        $user->m_context = Input::post('context');
        $user->save();

        $tmp_username = 'guest';
        if (!is_null(Session::get('valid'))) {
            $tmp_username = Session::get('valid')->user;
        }

        $tmp_info = 'name:'.Input::post('username')."\n";
        $tmp_info .= 'email:'.Input::post('email')."\n";
        $tmp_info .= 'context:'.Input::post('context')."\n";

        // log 宣告設定
        $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);
        $mylog->user_action_log($tmp_username, 'edit_message', 'S', $tmp_info);

        echo "<script>alert('修改成功');</script>";
        return Response::redirect('/welcome', 'refresh');
    }
}
