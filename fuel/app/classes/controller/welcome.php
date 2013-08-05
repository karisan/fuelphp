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
        //print_r($_POST);
        //echo "action_hello()";

        if (isset($_POST['username'])) {

            // 寫入DB，將留言資料顯示
            /*
             *
             list($insert_id, $rows_affected) = DB::insert('message')->set(array(
                    'm_name' => $_POST['username'],
                    'm_email' => $_POST['email'],
                    'm_context' => $_POST['context'],
                    'm_time' => date("Y/m/d H:i:s"),
                ))->execute();
            */

            $user = Model_Message::forge()->set(array(
                    'm_name' => $_POST['username'],
                    'm_email' => $_POST['email'],
                    'm_context' => $_POST['context'],
                    'm_time' => date("Y/m/d H:i:s"),
                ));
            // 新增使用者
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
        $view->name = 'karisan(default)';
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
