<?php
/**
 *
 * 購物車 及 新增管理者 相關功能
 *
 * @package
 * @category
 * @author    karisan
 */
class Controller_Root extends Controller_Template {

    /**
     * 踢出未登入的使用者，當未登入時，
     * 則會以此功能，將無權限的user踢出
     *
     * @param   void
     * @return  void
     */
    public function before()
    {
        parent::before();
        if (is_null(Session::get('valid'))) {
            Response::redirect('/validate/expired');
        }
    }

    /**
     * 新增使用者 顯示頁
     *
     * @param   void
     * @return  Response
     */
    public function action_adduser()
    {
        $view = View::forge('root/adduser', null, false);
        $view->valid = Session::get('valid');
        return Response::forge($view);
    }

    /**
     * 新增使用者 處理頁
     *
     * @param   void
     * @return  Response
     */
    public function action_doadduser()
    {
        // log 處理
        $tmp_username = 'guest';
        $tmp_log_action = 'add_user';

        if (!is_null(Session::get('valid'))) {
            $tmp_username = Session::get('valid')->user;
        }

        $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);


        $custmsg = '';
        $doflag = true;

        $cancel = Input::post('Cancel');
        // 取消新增時，返回首頁
        if (isset($cancel)) {
            return Response::redirect('/welcome', 'refresh');
        }

        $val = Validation::forge('my_validation');

        // 驗證username 1、不重複 2、長度1~20字 3、字元允許 A-Za-z0-9
        $val->add_field('username', '名稱', 'required|trim|min_length[1]|max_length[20]|valid_string[alpha,numeric]');

        // 驗證password 1、長度1~30字
        $val->add_field('password', '密碼', 'required|trim|min_length[1]|max_length[30]');

        // 驗證email 1、符合有效email格式
        $val->add_field('email', 'Email', 'required|trim|valid_email');

        $val->set_message('required', ':label 為必填.');
        $val->set_message('mix_length', ':label 字數過短.');
        $val->set_message('max_length', ':label 字數過長.');
        $val->set_message('valid_email', ':label 需要是正確的email格式');
        $val->set_message('valid_string', ':label 只允許英文、數字.');

        $errors = array();
        if ($val->run()) {
            $custmsg = '驗證成功';
        } else {
            $errors = $val->error();
            $custmsg = '驗證失敗-由validation';
            $doflag = false;
        }

        // 檢查密碼二次是否一致
        if (Input::post('password') != Input::post('repassword')) {
            array_push($errors, '密碼不一致!');
            $doflag = false;
        }


        // 檢查帳號是否重複
        $user = Model_Users::find_one_by('username', Input::post('username'));
        if ($user != null) {
            array_push($errors, '帳號重複!請重新輸入!');
            $doflag = false;
        }

        if ($doflag) {
            // 寫入DB，將留言資料顯示
            $user = Model_Users::forge()->set(
                array(
                    'username' => Input::post('username'),
                    'email' => Input::post('email'),
                    'password' => sha1(Input::post('password')),
                    'level' => Input::post('level'),
                    'created_at' => time(),
                    'updated_at' => time(),
                    'group' => '1',
                    'last_login' => '0',
                    'login_hash' => '',
                    'profile_fields' => '',)
            );

            // 新增使用者
            $user->save();

            // 將log內容串起來
            $tmp_info = '['.$tmp_username.'] - '.$tmp_log_action."\n";
            $tmp_info .= 'username:'.Input::post('username')."\n";
            $tmp_info .= 'email:'.Input::post('email')."\n";
            $tmp_info .= 'level:'.Input::post('level')."\n";
            // 寫入 log
            $mylog->user_action_log($tmp_username, $tmp_log_action, 'S', $tmp_info);

            $custmsg = '新增帳號成功!';
            //成功時回到原頁面
            return Response::redirect('/welcome', 'refresh');
        }

        //失敗時回原新增使用者介面
        $custmsg = '新增帳號失敗-欄位驗證失敗!';

        // 將log內容串起來
        $tmp_info = '['.$tmp_username.'] - '.$tmp_log_action."\n";
        $tmp_info .= 'username:'.Input::post('username')."\n";
        $tmp_info .= 'email:'.Input::post('email')."\n";
        $tmp_info .= 'level:'.Input::post('level')."\n";
        $tmp_info .= 'fail:'.$custmsg."\n";
        // 寫入 log
        $mylog->user_action_log($tmp_username, $tmp_log_action, 'F', $tmp_info);

        // 導回新增使用者頁面
        $view = View::forge('root/adduser');

        // 設定錯誤訊息
        $view->messages = $errors;

        // 設定既有資料顯示
        $view->usernmae = Input::post('username');
        $view->email = Input::post('email');
        $view->level = Input::post('level');

        return Response::forge($view);

    }

    /**
     * 使用者管理頁面
     *
     * @param   void
     * @return  Response
     */
    public function action_show_user()
    {

        $id = '1';
        if (isset(Session::get('valid')->id)) {
            $id = Session::get('valid')->id;
        }

        // 尋找所有使用者, 但不顯示自己
        $entry = Model_Users::find(
            array(
                'where' => array(
                    array('id', '<>', $id),
                ),
                'order_by' => array('id' => 'asc'),
            )
        );

        $view = View::forge('root/show_user');
        $view->data = $entry;
        return Response::forge($view);

    }

    /**
     * 執行 刪除使用者
     *
     * @param   void
     * @return  Response
     */
    public function action_do_del_user()
    {
        $id = Input::param('id');
        // 重導向至 使用者管理頁面
        if (empty($id)) {
            echo "<script>alert('參數有誤，返回原頁面');</script>";
            return Response::redirect('root/show_user', 'refresh');
        }

        // log 處理
        $tmp_username = 'guest';
        $tmp_log_action = 'del_user';

        if (!is_null(Session::get('valid'))) {
            $tmp_username = Session::get('valid')->user;
        }

        $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);


        // 刪除使用者
        $user = Model_Users::find_by_pk($id);
        if ($user) {
            // 刪除
            $user->delete();

            // 將log內容串起來
            $tmp_info = '['.$tmp_username.'] - '.$tmp_log_action."\n";
            $tmp_info .= 'id:'.$user->id."\n";
            $tmp_info .= 'name:'.$user->username."\n";
            // 寫入 log
            $mylog->user_action_log($tmp_username, $tmp_log_action, 'S', $tmp_info);

            echo "<script>alert('刪除成功');</script>";
        } else {
            echo "<script>alert('刪除帳號不存在');</script>";
        }

        // 重導向至 使用者管理頁面
        return Response::redirect('root/show_user', 'refresh');
    }

    /**
     * 修改使用者資料 顯示頁
     *
     * @param   void
     * @return  Response
     */
    public function action_edit_user()
    {

        $id = Input::param('id');
        $entry = Model_Users::find_by_pk($id);

        if ($entry === null) {
            // 沒找到時，重導向至 使用者管理頁面
            return Response::redirect('root/show_user', 'refresh');
        } else {
            $view = View::forge('root/edit_user');
            $view->data = $entry;
            return Response::forge($view);
        }
    }

    /**
     * 執行 修改使用者資料
     *
     * @param   void
     * @return  Response
     */
    public function action_do_edit_user()
    {
        $cancel = Input::post('Cancel');
        $submit = Input::post('submit');
        $id = Input::post('id');
        $email = Input::post('email');
        $level = Input::post('level');

        // 按下取消時，重導向至 使用者管理頁面
        if (isset($cancel)) {
            return Response::redirect('root/show_user', 'refresh');
        }

        // submit無資料時，重導向至 使用者管理頁面
        if (is_null($submit)) {
            return Response::redirect('root/show_user', 'refresh');
        }

        // log 處理
        $tmp_username = 'guest';
        $tmp_log_action = 'edit_user';
        if (!is_null(Session::get('valid'))) {
            $tmp_username = Session::get('valid')->user;
        }

        $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);

        $user = Model_Users::find_by_pk($id);
        // 沒找到該使用者時，重導向至 使用者管理頁面
        if ($user === null) {
            // log 內容串起來
            $tmp_info = '['.$tmp_username.'] - '.$tmp_log_action."\n";
            $tmp_info .= 'id:'.$id." not found.\n";
            // 寫入 log
            $mylog->user_action_log($tmp_username, $tmp_log_action, 'F', $tmp_info);
            return Response::redirect('root/show_user', 'refresh');
        }

        $val = Validation::forge('my_validation');

        // 驗證email 1、符合有效email格式
        $val->add_field('email', 'Email', 'required|trim|valid_email');

        $val->set_message('required', ':label 為必填.');
        $val->set_message('valid_email', ':label 需要是正確的email格式');

        $errors = array();
        if ($val->run()) {
            $custmsg = '驗證成功';

            // 更新使用者資料
            $user->email = $email;
            $user->level = $level;
            $user->updated_at = time();
            $user->save();

            // 新增的內容串起來
            $tmp_info = '['.$tmp_username.'] - '.$tmp_log_action."\n";
            $tmp_info .= 'id:'.$user->id."\n";
            $tmp_info .= 'name:'.$user->username."\n";
            $tmp_info .= 'email:'.$email."\n";
            $tmp_info .= 'level:'.$level."\n";
            // 寫入 log
            $mylog->user_action_log($tmp_username, $tmp_log_action, 'S', $tmp_info);

            $custmsg = '更新資料成功!';
            echo "<script>alert('修改成功');</script>";
            return Response::redirect('root/show_user', 'refresh');
        } else {
            $custmsg = '修改使用者資料失敗-由validation';

            // 新增的內容串起來
            $tmp_info = '['.$tmp_username.'] - '.$tmp_log_action."\n";
            $tmp_info .= 'id:'.$user->id."\n";
            $tmp_info .= 'name:'.$user->username."\n";
            $tmp_info .= 'email:'.$email."\n";
            $tmp_info .= 'level:'.$level."\n";
            $tmp_info .= 'fail:'.$custmsg." \n";
            // 寫入 log
            $mylog->user_action_log($tmp_username, $tmp_log_action, 'F', $tmp_info);

            $errors = $val->error();
            $view = View::forge('root/edit_user');
            $view->data = $user;

            // 設定錯誤訊息，導回重設密碼頁
            $view->messages = $errors;
            return Response::forge($view);
        }
    }

    /**
     * 重設使用者密碼 顯示頁
     *
     * @param   void
     * @return  Response
     */
    public function action_reset_user_pass()
    {
        $id = Input::param('id');
        $entry = Model_Users::find_by_pk($id);
        if ($entry === null) {
            // 沒找到時，重導向至 使用者管理頁面
            return Response::redirect('root/show_user', 'refresh');
            //echo 'no user';
        } else {
            $view = View::forge('root/reset_user_pass');
            $view->data = $entry;
            return Response::forge($view);
        }
    }

    /**
     * 執行 重設使用者密碼
     *
     * @param   void
     * @return  Response
     */
    public function action_do_reset_user_pass()
    {
        // log 處理
        $tmp_log_action = 'reset_user_password';
        $tmp_username = 'guest';

        $cancel = Input::post('Cancel');
        $submit = Input::post('submit');
        $id = Input::post('id');
        $password = Input::post('password');

        if (!is_null(Session::get('valid'))) {
            $tmp_username = Session::get('valid')->user;
        }

        if (isset($cancel)) {
            // 按下取消時，重導回 管理頁
            return Response::redirect('root/show_user', 'refresh');
        }

        if (is_null($submit)) {
            // submit 參數不存在時，重導向至 使用者管理頁面
            echo "<script>alert('參數有誤，返回原頁面');</script>";
            return Response::redirect('root/show_user', 'refresh');
        }

        // log 宣告
        $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);

        $user = Model_Users::find_by_pk($id);
        if ($user === null) {
            // 將內容串起來
            $tmp_info = '['.$tmp_username.'] reset id:'.$id."\n";
            $mylog->user_action_log($tmp_username, $tmp_log_action, 'F', $tmp_info);

            // 沒找到時，重導回 管理頁
            echo "<script>alert('重設失敗');</script>";
            return Response::redirect('root/show_user', 'refresh');
        }

        $val = Validation::forge('my_validation');

        // 驗證password 1、長度1~30字
        $val->add_field('password', '密碼', 'required|trim|min_length[1]|max_length[30]');

        $val->set_message('required', ':label 為必填.');
        $val->set_message('mix_length', ':label 字數過短.');
        $val->set_message('max_length', ':label 字數過長.');

        $errors = array();
        if ($val->run()) {
            // 在驗證成功時處理你的東西
            $custmsg = '驗證成功';

            // 確認修改，完成後，重導回 管理頁
            $user->password = sha1($password);
            $user->updated_at = time();
            $user->save();

            // 將內容串起來
            $tmp_info = '['.$tmp_username.'] reset id:'.$id."\n";
            $tmp_info .= 'reset username:'.$user->username."\n";
            $mylog->user_action_log($tmp_username, $tmp_log_action, 'S', $tmp_info);

            echo "<script>alert('密碼重設成功');</script>";
            // 重導向至 使用者管理頁面
            return Response::redirect('root/show_user', 'refresh');
        } else {
            $custmsg = '密碼重設失敗-由validation';

            // 將內容串起來
            $tmp_info = '['.$tmp_username.'] reset id:'.$id."\n";
            $tmp_info .= 'reset username:'.$user->username."\n";
            $tmp_info .= 'fail:'.$custmsg."\n";
            $mylog->user_action_log($tmp_username, $tmp_log_action, 'F', $tmp_info);

            $errors = $val->error();
            $view = View::forge('root/reset_user_pass');
            $view->data = $user;

            // 設定錯誤訊息，導回重設密碼頁
            $view->messages = $errors;
            return Response::forge($view);
        }
    }

    /**
     * 顯示log 列表頁面
     *
     * @param   void
     * @return  Response
     */
    public function action_show_log()
    {

        if (isset(Session::get('valid')->id)) {
            $id = Session::get('valid')->id;
        }

        // 尋找所有log,最新在最前
        $entry = Model_Actionlog::find(
            array(
                'order_by' => array('id' => 'desc'),
            )
        );
        $view = View::forge('root/show_log');
        $view->data = $entry;
        return Response::forge($view);

    }

    /**
     * 顯示 log 列表頁面 以Cache顯示
     *
     * @param   void
     * @return  Response
     */
    public function action_show_log_cache()
    {
        if (isset(Session::get('valid')->id)) {
            $id = Session::get('valid')->id;
        }

        // 尋找最新的log id
        $new_entry = Model_Actionlog::find(
            array(
                'order_by' => array('id' => 'desc'),
                'limit' => '1'
            )
        );

        $db_new_log_id = '0';
        if ($new_entry) {
            $db_new_log_id = $new_entry[0]->id;
        }

        // 取出 Cache 中的最新log id
        try {
            $cache_new_log_id = Cache::get('log_new_id');
        } catch (\CacheNotFoundException $e) {
            Cache::set('log_new_id', $db_new_log_id);
            Log::info('Set log_new_id Cache');
        }


        // 比對是否一致，一致時抓Cache
        if ($db_new_log_id == $cache_new_log_id) {
            // use cache
            try {
                $entry = Cache::get('log');
            } catch (\CacheNotFoundException $e) {
                // 尋找所有log,最新在最前
                $entry = Model_Actionlog::find(
                    array(
                        'order_by' => array('id' => 'desc'),
                    )
                );
                //echo 'create cache<br>';
                Cache::set('log', $entry, 3600);
                Log::info('Set log Cache');
            }
        } else {
            // 不一致時，從DB抓資料，並存到Cache
            $entry = Model_Actionlog::find(
                array(
                    'order_by' => array('id' => 'desc'),
                )
            );

            //echo 'create cache<br>';
            Cache::set('log', $entry, 3600);
            Cache::set('log_new_id', $db_new_log_id);
            Log::info('Set log Cache');
            Log::info('Set log_new_id Cache');
        }

        $view = View::forge('root/show_log');
        $view->data = $entry;
        return Response::forge($view);
    }

    /**
     * 顯示log 列表頁面 for ajax
     *
     * @param   void
     * @return  Response
     */
    public function action_show_log2()
    {
        if (isset(Session::get('valid')->id)) {
            $id = Session::get('valid')->id;
        }

        $view = View::forge('root/show_log_ajax');
        return Response::forge($view);
    }

    /**
     * ajax for 顯示log列表
     *
     * @param   void
     * @return  Response
     */
    public function action_show_log2_ajax()
    {
        if (isset(Session::get('valid')->id)) {
            $id = Session::get('valid')->id;
        }

        // 驗證參數只允許在一定列表內
        $arglist = array('id', 'username', 'time', 'ip', 'action', 'status', 'url', 'info');

        // 拼出查詢參數
        $cond = array();
        foreach ($arglist as $mykey) {
            $myval = Input::param($mykey);
            array_push($cond, array($mykey, 'like', '%'.$myval.'%'));
        }

        //print_r($cond);

        // 尋找所有log,最新在最前
        $entry = Model_Actionlog::find(
            array(
                'where' => $cond,
                'order_by' => array('id' => 'desc'),
            )
        );

        $sResults = '';
        if (isset($entry)) {
            foreach ($entry as $rows) {
                $sResults .= '<tr id="log_'.$rows['id'].'">';
                $sResults .= '<td>'.$rows['id'].'</td>';
                $sResults .= '<td>'.substr($rows['username'], 0, 15).'</td>';
                $sResults .= '<td>'.$rows['time'].'</td>';
                $sResults .= '<td>'.$rows['ip'].'</td>';
                $sResults .= '<td>'.substr($rows['action'], 0, 20).'</td>';
                $sResults .= '<td>'.$rows['status'].'</td>';
                $sResults .= '<td>'.substr($rows['url'], 0, 50).'</td>';
                $sResults .= '<td>'.substr($rows['info'], 0, 50).'</td>';
                $sResults .= '</tr>';
            }
        }

        echo $sResults;

        $view = View::forge('ajax');
        return Response::forge($view);

    }

    /**
     * 顯示 單一log 頁面
     *
     * @param   void
     * @return  Response
     */
    public function action_show_log_detail()
    {
        // 顯示單一log
        $id = Input::param('id');
        $entry = Model_Actionlog::find_by_pk($id);
        if (is_null($id) || !isset($entry)) {
            // 未登入時，重導向至 使用者管理頁面
            return Response::redirect('root/show_user', 'refresh');
        }

        // 驗證參數只允許在一定列表內
        $arglist = array('id', 'username', 'time', 'ip', 'action', 'status', 'url', 'info');
        $msglist = array('編號', '帳號', '時間', 'IP', '動作', '狀態', 'URL', '詳細');

        $q_str = Input::param('q_str');
        $query_array = json_decode($q_str, true);
        //print_r($query_array);

        // 拼出查詢參數
        $cond = array();
        $q_msg = '';
        $i = 0;
        foreach ($arglist as $mykey) {
            $myval = $query_array[$mykey];
            array_push($cond, array($mykey, 'like', '%'.$myval.'%'));
            if ($myval != '') {
                $q_msg .= '['.$msglist[$i].' 含有 '.$myval.'] ';
            }

            $i++;
        }

        //print_r($cond);

        $cond[] = array('id', '>', $id);
        // 產生新一筆、舊一筆log資料
        $newentry = Model_Actionlog::find(
            array(
                'where' => $cond,
                'order_by' => array('id' => 'asc'),
                'limit' => '1',
            )
        );
        array_pop($cond);

        $cond[] = array('id', '<', $id);
        $oldentry = Model_Actionlog::find(
            array(
                'where' => $cond,
                'order_by' => array('id' => 'desc'),
                'limit' => '1',
            )
        );
        array_pop($cond);

        $view = View::forge('root/show_log_detail');
        $view->data = $entry;
        $view->q_str = Input::param('q_str');
        $view->q_msg = $q_msg;
        // 設定新一筆、舊一筆log的id
        if (isset($newentry)) {
            $view->new_id = $newentry[0]->id;
        }

        if (isset($oldentry)) {
            $view->old_id = $oldentry[0]->id;
        }

        return Response::forge($view);
    }
}
