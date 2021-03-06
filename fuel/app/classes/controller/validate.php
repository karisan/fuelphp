<?php
/**
 *
 * 登入處理
 *
 * @package
 * @category
 * @author    karisan
 */
class Controller_Validate extends Controller_Template {
    /**
     * 未登入時，導向/validate/expired頁面
     *
     * @param   void
     * @return  void
     */
    public function action_auth()
    {

        $username = trim(Input::param('username'));
        $password = Input::param('password');
        $user = Model_Users::find_one_by_username($username);

        // log 宣告設定
        $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);

        if (is_null($user)) {
            // 新增操作log
            $mylog->user_action_log($username, 'login', 'F', 'no user:'.$username);

            // 錯誤時，清session，重導至登入頁 /validate/login
            Session::set_flash('message', 'Failed Username');
            Response::redirect('validate/login');
        } elseif (sha1($password) === $user->password) {
            // 新更最後登入時間
            $user->last_login = time();
            $user->save();


            // 新增操作log, 成功登入
            $mylog->user_action_log($user->username, 'login', 'S', 'login success:'.$username);

            // 試用 Log 套件
            //Log::write('Link','FILE: '.__FILE__);
            //Log::write('Link','FUNCTION: '.__FUNCTION__);
            //Log::write('Link','CLASS: '.__CLASS__);
            //Log::write('Link','METHOD: '.__METHOD__);

            // 正確登入時，設定session，重導至/hello
            $valid = new stdClass();
            $valid->user = $user->username;
            $valid->id   = $user->id;
            $valid->email = $user->email;
            $valid->level = $user->level;
            Session::set('valid', $valid);
            Response::redirect('welcome');
        } else {
            // 新增操作log
            $mylog->user_action_log($username, 'login', 'F', 'login fail:'.$username);

            Session::set_flash('message', 'Failed Password');
            Session::set_flash('username', $username);
            Response::redirect('validate/login');
        }
    }

    /**
     * 登出的處理
     * @param   void
     * @return  void
     */
    public function action_logout()
    {
        // 新增操作log
        $log_username = 'guest';
        if (isset(Session::get('valid')->user)) {
            $log_username = Session::get('valid')->user;
        }

        // log 宣告設定
        $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);
        // 新增操作log, I - info
        $mylog->user_action_log($log_username, 'logout', 'I', 'logout:'.$log_username);

        // 登出時，刪除session，重導至/hello
        Session::delete('valid');
        Response::redirect('welcome');
    }

    /**
     * 判斷是否已通過驗證
     * @param   void
     * @return  Response
     */
    public function action_isvalid()
    {
        $valid = Session::get('valid');
        $body = json_encode(isset($valid));
        $headers = array (
            'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
            'Expires'       => 'Mon, 26 Jul 1997 05:00:00 GMT',
            'Pragma'        => 'no-cache',
        );
        return new Response($body, 200, $headers);
    }

    /**
     * 登入的處理
     * @param   void
     * @return  void
     */
    public function action_login()
    {
        $valid = Session::get('valid');
        // 當已登入，則導向至 /hello
        if (isset($valid)) {
            Response::redirect('welcome');
        }

        $this->template->page_title = 'Login';

        $data['message'] = Session::get_flash('message');
        $data['username'] = Session::get_flash('username');

        $this->template->content = View::forge('validate/login', $data);

        $isValidURL = Uri::create('/validate/isvalid');
        $js = <<<END
$(window).load(function() {
  $.getJSON("$isValidURL", function(valid) { if (valid) location.reload() })
});
$(window).unload(function() { });
END;
        $this->template->script = View::forge('script', array('js' => $js), false);

        $css = <<<END
form td {
  padding: 0 15px 6px 0;
}
END;
        $this->template->style = View::forge('style', array('css' => $css), false);
    }

    /**
     * 驗證過期的處理
     * @param   void
     * @return  void
     */
    public function action_expired()
    {
        $this->template->content = View::forge('validate/expired');
    }
}
