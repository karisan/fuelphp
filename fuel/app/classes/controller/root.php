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
     *
     * 產生js,當使用者無權限時，則會自動reload，被踢出
     *
     */
    public function __construct() {
        // $common_js is too complex to create as a data member directly
        // so define it here instead
        $isValidURL = Uri::create('validate/isvalid');
        $this->common_js = <<<END
$(window).load(function() {
  $.getJSON("$isValidURL", function(valid) { if (!valid) location.reload() })
});
$(window).unload(function() { });
END;
    }

    /**
     * 踢出未登入的使用者，當未登入時，
     * 則會以此功能，配合JS將無權限的user踢出
     *
     * @param   void
     * @return  void
     */
    public function before() {
        parent::before();
        if (is_null(Session::get('valid'))) {
            Response::redirect('/validate/expired');
        }
    }

    /**
     * 顯示購物車功能的首頁
     *
     * @param   void
     * @return  void
     */
    public function action_index() {
        $this->template->page_title = 'Home';
        $data = array('valid' => Session::get('valid'));
        $this->template->content = View::forge('root/index', $data);
    }

    /**
     * 加入購物車 處理
     *
     * @param   void
     * @return  void
     */
    private function getOrder() {
        $order = Session::get('order');
        $dir = Session::get('dir');
        if (is_null($order)) {
            $order = 'title';
            $dir = "asc";
            Session::set('order', $order);
            Session::set('dir', $dir);
        }
        return array($order, $dir);
    }

    /**
     * 書本列表 樣式1
     *
     * @param   void
     * @return  void
     */
    public function action_list1() {
        $this->template->page_title = 'Book List';

        // if things in the session ordering get screwed up, try these:
        //Session::delete('order');
        //Session::delete('dir');
        list($order, $dir) = $this->getOrder();

        Session::set('backto', Uri::string()); // current controller/action

        $data['books'] = Model_Books::find(array(
                'order_by' => array($order => $dir)
            ));

        $this->template->content = View::forge('root/list1', $data);

        $this->template->css_files = array('listing.css');
    }

    /**
     * 修改訂單 處理
     *
     * @param   void
     * @return  void
     */
    public function action_changeorder() {
        $order = Input::param('order');
        if ($order == Session::get('order')) {
            if (Session::get('dir') == 'asc') {
                Session::set('dir', 'desc');
            } else {
                Session::set('dir', 'asc');
            }
        } else {
            Session::set('dir', 'asc');
        }
        Session::set('order', $order);
        Response::redirect(Session::get('backto'));
    }

    /**
     * 顯示書本詳細資料
     *
     * @param   void
     * @return  void
     */
    public function action_details() {
        $id = Input::param('id');
        $book = Model_Books::find_by_pk($id);
        is_null($id) || !isset($book) and Response::redirect('/');

        $data['book'] = $book;
        $data['valid'] = Session::get('valid');
        $data['message'] = Session::get_flash('message');

        $this->template->content = View::forge('root/details', $data);

        $css = <<<END
#details td {
  padding: 0 15px 6px 0;
}
form {
  margin: 0;
  padding-bottom: 10px;
}
form.mod {
  display: table-cell;
  padding-right: 20px;
}
form.mod button {
  color: #c00;
  font-weight: bold;
}
END;
        $this->template->style = View::forge('style', array('css' => $css), false);

        $js = <<<END
$(document).ready(function(){
  $("button[name='delete']").click(function(){
    return confirm("Are you sure?");
  });
});
END;
        $this->template->script = View::forge('script', array('js' => $js), false);
    }

    /**
     * 書本列表 樣式2
     *
     * @param   void
     * @return  void
     */
    public function action_list2() {
        $this->template->page_title = 'Book List: paginate';

        list($order, $dir) = $this->getOrder();

        $curr_page = Input::param('page', 0);  // second arg. is default value
        $perpage = 8;
        $offset = $curr_page * $perpage;

        $total = Model_Books::count();

        Session::set('backto', Uri::string());

        $data['numpages'] = ceil($total / $perpage);
        $data['curr_page'] = $curr_page;
        $data['books'] = Model_Books::find(array(
                'order_by' => array($order => $dir),
                'offset' => $offset,
                'limit' => $perpage,
            ));

        $this->template->content = View::forge('root/list2', $data);

        $this->template->css_files = array('listing.css');

        $css = <<<END
#content table { margin-top: 10px; }
#page_bar {
  background: #333;
  color: #fff;
  font-size: 14px;
  margin-bottom: 15px;
  position: relative;
  cursor: pointer;
  line-height: 28px;
  height: 28px;
  padding: 0 10px;
  border-radius: 4px;
  display: table-cell;
  min-width: 300px;
}
#page_bar a {
  color: magenta;
  font-weight: bold;
  padding: 0 5px;
  text-decoration: none;
}
#page_bar a:hover { color: #9ff; }
#page_bar a.sel {
  border: solid 2px #9ff;
}
END;
        $this->template->style = View::forge('style', array('css' => $css), false);
    }


    /**
     * css 捲動樣式
     *
     * @param   void
     * @return  void
     */
    private $css_scroll = <<<END
#display-container {
  display: table-cell;
  border: solid 1px black;
}
#display {
  overflow: scroll;
  overflow-x: hidden;
  padding-right: 10px;
  height: 350px;
}
END;

    /**
     * 書本列表 樣式3
     *
     * @param   void
     * @return  void
     */
    public function action_list3() {
        $this->template->page_title = 'Book List: fixed-height scroll';

        list($order, $dir) = $this->getOrder();

        Session::set('backto', Uri::string());

        $data['books'] = Model_Books::find(array(
                'order_by' => array($order => $dir)
            ));

        $this->template->content = View::forge('root/list3', $data);

        $this->template->css_files = array('listing.css');

        $this->template->style
            = View::forge('style', array('css' => $this->css_scroll), false);
    }

    /**
     * 書本列表 樣式4
     *
     * @param   void
     * @return  void
     */
    public function action_list4() {
        $this->template->page_title = 'Book List: variable-height scroll';

        list($order, $dir) = $this->getOrder();

        Session::set('backto', Uri::string());

        $data['books'] = Model_Books::find(array(
                'order_by' => array($order => $dir)
            ));

        $this->template->content = View::forge('root/list4', $data);

        $this->template->css_files = array('listing.css');

        $this->template->style
            = View::forge('style', array('css' => $this->css_scroll), false);

        $js = <<<END
function setHeight() {
  var newHeight =
    $("#display").height() + $(window).height() - $('body').height() - 50;
  $("#display").height( newHeight );
}
$(window).resize( setHeight );
$(document).ready(function() { setHeight(); });
END;
        $this->template->script = View::forge('script', array('js' => $js), false);
    }

    /**
     * 顯示購物車內容
     *
     * @param   void
     * @return  void
     */
    function action_cart() {
        $this->template->page_title = 'Cart';
        $cart = Session::get('cart');
        $data['cart'] = $cart;
        $this->template->content = View::forge('root/cart', $data);
    }

    /**
     * 加至購物車，加到session
     *
     * @param   void
     * @return  void
     */
    function action_addtocart() {
        $id = Input::param('id');
        $cart = Session::get('cart');
        if (is_null($cart) || !isset($cart[$id])) {
            $cart[$id] = 1;
        } else {
            ++$cart[$id];
        }
        Session::set('cart', $cart);
        Response::redirect("root/cart");
    }

    /**
     * 新增使用者 顯示頁
     *
     * @param   void
     * @return  Response
     */
    function action_adduser() {
        //Session::set('cart', $cart);
        //Response::redirect("root/cart");
        $view = View::forge('root/adduser',null,false);
        $view->valid = Session::get('valid');
        // 驗證是否登入的js
        $view->js = $this->common_js;
        return Response::forge($view);
    }

    /**
     * 新增使用者 處理頁
     *
     * @param   void
     * @return  Response
     */
    function action_doadduser() {

        // log 處理
        $tmp_username = 'guest';
        $tmp_log_action = 'add_user';

        if (!is_null(Session::get('valid'))) {
            $tmp_username = Session::get('valid')->user;
        }

        $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);


        $custmsg = '';
        $doflag = true;

        // 取消新增時，返回首頁
        if (!empty($_POST['Cancel'])) {
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
        if ($val->run())
        {
            // 在驗證成功時處理你的東西
            $custmsg = '驗證成功';
        } else {
            $errors = $val->error();
            //print_r($errors);

            $custmsg = '驗證失敗-由validation';
            $doflag = false;
        }

        // 檢查密碼二次是否一致
        if (Input::post('password') != Input::post('repassword')) {
            array_push($errors,'密碼不一致!');
            $doflag = false;
        }


        // 檢查帳號是否重複
        $user = Model_Users::find_one_by('username', Input::post('username'));
        if ($user!=null) {
            array_push($errors,'帳號重複!請重新輸入!');
            $doflag = false;
        }

        //$doflag = false;
        if ($doflag) {
            // 寫入DB，將留言資料顯示
            $user = Model_Users::forge()->set(array(
                    'username' => Input::post('username'),
                    'email' => Input::post('email'),
                    'password' => sha1(Input::post('password')),
                    'level' => Input::post('level'),
                    'created_at' => time(),
                    'updated_at' => time(),
                    'group' => '1',
                    'last_login' => '0',
                    'login_hash' => '',
                    'profile_fields' => '',
                ));

            // 將log內容串起來
            $tmp_info = '['.$tmp_username.'] - '.$tmp_log_action."\n";
            $tmp_info .= 'username:'.Input::post('username')."\n";
            $tmp_info .= 'email:'.Input::post('email')."\n";
            $tmp_info .= 'level:'.Input::post('level')."\n";

            // 新增使用者
            $user->save();

            // 寫入 log
            $mylog->user_action_log($tmp_username, $tmp_log_action, 'S', $tmp_info);

            $custmsg = '新增帳號成功!';
            //成功時回到原頁面
            return Response::redirect('/welcome', 'refresh');
        }

        //失敗時回原新增使用者介面
        //Session::set('message', $custmsg);
        //Session::set_flash('username', Input::post('username'));
        //Session::set_flash('email', Input::post('email'));
        //return Response::redirect('/root/adduser');

        //return Response::forge($view);

        // 測試用空白頁
        // $view = View::forge('empty');
        // return Response::forge($view);

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
     * 清空購物車
     *
     * @param   void
     * @return  void
     */
    function action_clearcart() {
        Session::delete('cart');
        Response::redirect("root/cart");
    }

    /**
     * 使用者管理頁面
     *
     * @param   void
     * @return  Response
     */
    function action_show_user() {

        $id = '1';
        if (isset(Session::get('valid')->id)) {
            $id = Session::get('valid')->id;
        }
        // 尋找所有使用者, 但不顯示自己
        $entry = Model_Users::find(array(
                'where' => array(
                    array('id', '<>', $id),
                ),
                'order_by' => array('id' => 'asc'),
            ));

        $view = View::forge('root/show_user');
        $view->data = $entry;

        // 若未登入時，不允許進入此頁
        $view->valid = Session::get('valid');
        return Response::forge($view);

    }

    /**
     * 執行 刪除使用者
     *
     * @param   void
     * @return  Response
     */
    function action_do_del_user() {

        if (!empty($_GET['id'])) {
            // log 處理
            $tmp_username = 'guest';
            $tmp_log_action = 'del_user';

            if (!is_null(Session::get('valid'))) {
                $tmp_username = Session::get('valid')->user;
            }

            $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);

            // 刪除留言
            $user = Model_Users::find_by_pk($_GET['id']);
            if($user)
            {
                // 將log內容串起來
                $tmp_info = '['.$tmp_username.'] - '.$tmp_log_action."\n";
                $tmp_info .= 'id:'.$user->id."\n";
                $tmp_info .= 'name:'.$user->username."\n";

                $user->delete();

                // 寫入 log
                $mylog->user_action_log($tmp_username, $tmp_log_action, 'S', $tmp_info);

                echo "<script>alert('刪除成功');</script>";
                $return_msg = '成功刪除';
            }
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
    function action_edit_user() {

        $id = Input::get('id');
        $entry = Model_Users::find_by_pk($id);

        if($entry === null) {
            // 沒找到時，重導向至 使用者管理頁面
            return Response::redirect('root/show_user', 'refresh');
            //echo 'no user';
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
    function action_do_edit_user() {

        if (!empty($_POST['Cancel'])) {

        } elseif (!empty($_POST['submit'])) {

            // log 處理
            $tmp_username = 'guest';
            $tmp_log_action = 'edit_user';

            if (!is_null(Session::get('valid'))) {
                $tmp_username = Session::get('valid')->user;
            }

            $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);


            $user = Model_Users::find_by_pk($_POST['id']);
            if($user === null) {
                // 沒找到

                // log 內容串起來
                $tmp_info = '['.$tmp_username.'] - '.$tmp_log_action."\n";
                $tmp_info .= 'id:'.$_POST['id']." not found.\n";

                // 寫入 log
                $mylog->user_action_log($tmp_username, $tmp_log_action, 'F', $tmp_info);

            } else {

                $val = Validation::forge('my_validation');

                // 驗證email 1、符合有效email格式
                $val->add_field('email', 'Email', 'required|trim|valid_email');

                $val->set_message('required', ':label 為必填.');
                $val->set_message('valid_email', ':label 需要是正確的email格式');

                $errors = array();
                if ($val->run())
                {
                    $custmsg = '驗證成功';

                    // 更新使用者資料
                    $user->email = Input::post('email');
                    $user->level = Input::post('level');
                    $user->updated_at = time();

                    // 新增的內容串起來
                    $tmp_info = '['.$tmp_username.'] - '.$tmp_log_action."\n";
                    $tmp_info .= 'id:'.$user->id."\n";
                    $tmp_info .= 'name:'.$user->username."\n";
                    $tmp_info .= 'email:'.Input::post('email')."\n";
                    $tmp_info .= 'level:'.Input::post('level')."\n";

                    $user->save();

                    // 寫入 log
                    $mylog->user_action_log($tmp_username, $tmp_log_action, 'S', $tmp_info);

                    $custmsg = '更新資料成功!';
                    echo "<script>alert('修改成功');</script>";

                    //成功時回到原頁面
                    return Response::redirect('root/show_user', 'refresh');

                } else {
                    $custmsg = '修改使用者資料失敗-由validation';

                    // 新增的內容串起來
                    $tmp_info = '['.$tmp_username.'] - '.$tmp_log_action."\n";
                    $tmp_info .= 'id:'.$user->id."\n";
                    $tmp_info .= 'name:'.$user->username."\n";
                    $tmp_info .= 'email:'.Input::post('email')."\n";
                    $tmp_info .= 'level:'.Input::post('level')."\n";
                    $tmp_info .= 'fail:'."$custmsg \n";

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
        }
        // 重導向至 使用者管理頁面
        return Response::redirect('root/show_user', 'refresh');

    }

    /**
     * 重設使用者密碼 顯示頁
     *
     * @param   void
     * @return  Response
     */
    function action_reset_user_pass() {
        $id = Input::get('id');
        $entry = Model_Users::find_by_pk($id);
        if($entry === null) {
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
    function action_do_reset_user_pass() {

        // log 處理
        $tmp_log_action = 'reset_user_password';
        $tmp_username = 'guest';
        if (!is_null(Session::get('valid'))) {
            $tmp_username = Session::get('valid')->user;
        }

        if (!empty($_POST['Cancel'])) {
            // 按下取消時，重導回 管理頁

        } elseif (!empty($_POST['submit'])) {

            // log 宣告
            $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);

            $user = Model_Users::find_by_pk($_POST['id']);
            if($user === null) {

                // 將內容串起來
                $tmp_info = '['.$tmp_username.'] reset id:'.$_POST['id']."\n";
                $mylog->user_action_log($tmp_username, $tmp_log_action, 'F', $tmp_info);

                // 沒找到時，重導回 管理頁
                echo "<script>alert('重設失敗');</script>";
            } else {

                $val = Validation::forge('my_validation');

                // 驗證password 1、長度1~30字
                $val->add_field('password', '密碼', 'required|trim|min_length[1]|max_length[30]');

                $val->set_message('required', ':label 為必填.');
                $val->set_message('mix_length', ':label 字數過短.');
                $val->set_message('max_length', ':label 字數過長.');

                $errors = array();
                if ($val->run())
                {
                    // 在驗證成功時處理你的東西
                    $custmsg = '驗證成功';

                    // 將內容串起來
                    $tmp_info = '['.$tmp_username.'] reset id:'.$_POST['id']."\n";
                    $tmp_info .= 'reset username:'.$user->username."\n";

                    // 確認修改，完成後，重導回 管理頁
                    $user->password = sha1(Input::post('password'));
                    $user->updated_at = time();
                    $user->save();

                    $mylog->user_action_log($tmp_username, $tmp_log_action, 'S', $tmp_info);

                    echo "<script>alert('密碼重設成功');</script>";
                    //echo 'do';

                } else {

                    $custmsg = '密碼重設失敗-由validation';

                    // 將內容串起來
                    $tmp_info = '['.$tmp_username.'] reset id:'.$_POST['id']."\n";
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
        }

        // 重導向至 使用者管理頁面
        return Response::redirect('root/show_user', 'refresh');
    }

    /**
     * 顯示log 列表頁面
     *
     * @param   void
     * @return  Response
     */
    function action_show_log() {

        if (isset(Session::get('valid')->id)) {
            $id = Session::get('valid')->id;
        }

        // 尋找所有使用者, 但不顯示自己
        $entry = Model_Actionlog::find(array(
                'order_by' => array('id' => 'desc'),
            ));

        $view = View::forge('root/show_log');
        $view->data = $entry;

        // 若未登入時，不允許進入此頁
        $view->valid = Session::get('valid');
        return Response::forge($view);

    }

    /**
     * 顯示 單一log 頁面
     *
     * @param   void
     * @return  Response
     */
    function action_show_log_detail() {

        // 顯示單一log
        $id = Input::param('id');
        $entry = Model_Actionlog::find_by_pk($id);
        if (is_null($id) || !isset($entry)) {
            // 重導向至 使用者管理頁面
            return Response::redirect('root/show_user', 'refresh');
        }

        $view = View::forge('root/show_log_detail');
        $view->data = $entry;

        // 若未登入時，不允許進入此頁
        $view->valid = Session::get('valid');
        return Response::forge($view);
    }

}
