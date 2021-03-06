<?php
/**
 * Created by JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/7
 * Time: 下午 5:09
 * To change this template use File | Settings | File Templates.
 */

/**
 *
 * 其它套件的登入Controller，使用Auth，但目前似乎未實際運作
 *
 * @package
 * @category
 * @author    karisan
 */
class Controller_MyBooks extends Controller_Protected {

    /**
     *
     * 未登入時，導向/validate/expired頁面
     *
     * @param   void
     * @return  void
     */
    public function action_login()
    {
        $data = array();

        // 如果你按下提交按鈕，讓我們跑整個步驟。
        if (Input::post()) {
            // 檢查認證，這裡假設你已經建立上表，且
            // 你已經使用如上所述的資料表定義和配置。
            if (Auth::login()) {
                // 認證成功，進入。
                Response::redirect('success_page');
            } else {
                // 哎呀，沒你的湯，再試著登入一次。設定一些值來
                // 重填使用者名稱欄位，並給一些錯誤的文字到視圖。
                $data['username']    = Input::post('username');
                $data['login_error'] = 'Wrong username/password combo. Try again';
            }
        }

        // 顯示登入表單。
        echo View::forge('auth/login', $data);
    }
}