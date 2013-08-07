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
	 * The basic welcome message
	 *
	 * @access  public
	 * @return  Response
	 */
    public function post_update()
    {
        return Response::forge(View::forge('welcome/update'));
    }

	public function action_index()
	{
		return Response::forge(View::forge('welcome/index'));
	}

	/**
	 * A typical "Hello, Bob!" type example.  This uses a ViewModel to
	 * show how to use them.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_hello()
	{
        print_r($_POST);
        print_r($_GET);
        //echo "action_hello()";
        if (!empty($_GET['update_id'])) {

            // fdaedaedae
            $user = Model_Message::find_by_pk($_GET['update_id']);
            if($user === null) {
                // 沒找到
            } else {
                /*
                if () {
                    // 顯示資料供修改
                    echo '進行修改';
                    $user->name = 'New Name';

                } else {
                    // 確認修改
                    echo '執行修改';
                    $user->m_name = 'kkk';
                    $user->m_email = 'cc@mail.com';
                    $user->m_context = 'New context';
                    // 更新使用者
                    $user->save();
                }
                */
            }


        } else if (!empty($_GET['del_id'])) {

            // 刪除留言
            $user = Model_Message::find_by_pk($_GET['del_id']);
            if($user)
            {
                $user->delete();
                echo '成功刪除';
            }

        } else if (!empty($_POST['username'])) {

            // 寫入DB，將留言資料顯示
            $user = Model_Message::forge()->set(array(
                    'm_name' => $_POST['username'],
                    'm_email' => $_POST['email'],
                    'm_context' => $_POST['context'],
                    'm_time' => date("Y/m/d H:i:s"),
                ));

            // 新增留言
            $result = $user->save();

        }

        // 讀取DB，顯示留言資料
        //$mydata['entries'] = Model_Message::find_all();
        //成功語法
        //$mydata = DB::query('SELECT * FROM message order by m_id desc')->execute();
        //$mydata['message'] = DB::SELECT('m_id','m_name','m_email','m_datetime','m_context')->execute();
        //print_r($mydata);


        // 尋找所有主題，顯示最新20筆
        $entry = Model_Message::find(array(
                'order_by' => array('m_id' => 'desc'),
                'limit' => 20,
            ));

        //print_r($entry);


        $view = View::forge('welcome/hello');
        $view->data = $entry;
        $view->name = 'karisan';

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
