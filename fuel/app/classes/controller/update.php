<?php



class Controller_Update extends Controller
{
	public function action_index()
	{
		echo "Update";

        $m_id = Input::get('m_id');
        $entry = Model_Message::find_by_pk($m_id);

        //echo "\n";
        //echo 'm_name'. $entry->m_name."\n";
        //print_r($entry);

        $view = View::forge('welcome/update');
        $view->data = $entry;
        $view->m_id = $m_id;
        $view->name = 'karisan';
        return Response::forge($view);
	}

    public function post_index()
    {
        echo "Update - post";
        //print_r($_POST);
        if (!empty($_POST['Cancel'])) {

            //echo "Redir";

        } elseif (!empty($_POST['submit'])) {
            $user = Model_Message::find_by_pk($_POST['m_id']);
            print_r($user);
            if($user === null) {
                // 沒找到
            } else {
                // 確認修改
                //echo '執行修改';
                //print_r(Input::post());
                $user['m_name'] = Input::post('username');
                $user->m_email = Input::post('email');
                $user->m_context = Input::post('context');
                $user->save();
                echo "do update";
            }
        }
        $view = View::forge('welcome/hello');
        //$view->data = $entry;
        $view->name = 'karisan';
        //return Response::forge($view);
        return Response::redirect('/hello', 'refresh');
    }

}
