<?php
class Controller_Protected extends Controller_Template {
  public function before() {
    parent::before();
    if (is_null(Session::get('valid'))) {
      Response::redirect('/validate/expired');
    }
  }
}