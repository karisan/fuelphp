<?php

class Controller_Root extends Controller_Template {

  public function action_index() {
    $this->template->page_title = 'Home';
    $data = array('valid' => Session::get('valid'));
    $this->template->content = View::forge('root/index', $data);
  }

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

  function action_cart() {
    $this->template->page_title = 'Cart';
    $cart = Session::get('cart');
    $data['cart'] = $cart;
    $this->template->content = View::forge('root/cart', $data);
  }

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

    function action_adduser() {
        /*
        $id = Input::param('id');
        $cart = Session::get('cart');
        if (is_null($cart) || !isset($cart[$id])) {
            $cart[$id] = 1;
        } else {
            ++$cart[$id];
        }
        */
        //Session::set('cart', $cart);
        //Response::redirect("root/cart");
        $view = View::forge('root/adduser');
        //$view->data = $entry;
        //$view->name = 'karisan';
        $view->valid = Session::get('valid');
        return Response::forge($view);

    }

    function action_doadduser() {
        //print_r(Input::post());

        $custmsg = '';
        $doflag = true;

        // 取消新增時，返回首頁
        if (!empty($_POST['Cancel'])) {
            return Response::redirect('/hello', 'refresh');
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
        $val->set_message('valid_string[alpha,numeric]', ':label 只允許英文、數字.');

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
                    'm_time' => date("Y/m/d H:i:s"),
                    'level' => Input::post('level'),
                    'created_at' => time(),
                    'updated_at' => time(),
                    'group' => '1',
                    'last_login' => time(),
                    'login_hash' => '',
                    'profile_fields' => '',
                ));
            // 新增使用者
            $user->save();

            $custmsg = '新增帳號成功!';
            //成功時回到原頁面
            return Response::redirect('/hello', 'refresh');
        }

        //失敗時回原新增使用者介面
        //Session::set('message', $custmsg);
        //Session::set_flash('username', Input::post('username'));
        //Session::set_flash('email', Input::post('email'));
        //return Response::redirect('/root/adduser');

        //return Response::forge($view);
        //$view = View::forge('empty');

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

  function action_clearcart() {
    Session::delete('cart');
    Response::redirect("root/cart");
  }

}
