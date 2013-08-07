<?php

class Controller_Validate extends Controller_Template {
  
  public function action_auth() {
    $username = trim(Input::param('username'));
    $password = Input::param('password');
    $user = Model_User::find('first', array(
        'where' => array(
          array('username', $username),
        ),
    ));
    if (is_null($user)) {
      // 錯誤時，清session，重導至登入頁 /validate/login
      Session::set_flash('message', 'Failed Username');
      Response::redirect("validate/login");
    }
    elseif (sha1($password) === $user->password) {
      // 正確登入時，設定session，重導至/hello
      $valid = new stdClass();
      $valid->user = $user->username;
      $valid->id   = $user->id;
      $valid->email = $user->email;
      $valid->level = $user->level;
      Session::set('valid', $valid);
      Response::redirect("/hello");
    } else {
      Session::set_flash('message', 'Failed Password');
      Session::set_flash('username', $username);
      Response::redirect("validate/login");
    }
  }

  public function action_logout() {
    // 登出時，刪除session，重導至/hello
    Session::delete('valid');
    Response::redirect('/hello');
  }
  
  public function action_isvalid() {
    $valid = Session::get('valid');
    $body = json_encode(isset($valid));
    $headers = array (
      'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
      'Expires'       => 'Mon, 26 Jul 1997 05:00:00 GMT',
      'Pragma'        => 'no-cache',
    );
    return new Response($body, 200, $headers);
  }

  public function action_login() {
    $valid = Session::get('valid');
    // 當已登入，則導向至 /hello
    if (isset($valid)) {
      Response::redirect("/hello");
    }

    $this->template->page_title = 'Login';
    
    $data['message'] = Session::get_flash('message');
    $data['username'] = Session::get_flash('username');
    
    $this->template->content = View::forge('validate/login',$data);
    
    $isValidURL = Uri::create('/validate/isvalid');
    $js = <<<END
$(window).load(function() {
  $.getJSON("$isValidURL", function(valid) { if (valid) location.reload() })
});
$(window).unload(function() { });
END;
    $this->template->script = View::forge('script', array('js'=>$js), false);
    
    $css = <<<END
form td {
  padding: 0 15px 6px 0;
}
END;
    $this->template->style = View::forge('style', array('css'=>$css), false);
  }
  
  public function action_expired() {
    $this->template->content = View::forge('validate/expired');
  }
}
