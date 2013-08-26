<?php
/**
 * Created by JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/22
 * Time: 下午 5:30
 * To change this template use File | Settings | File Templates.
 */

/**
 * 練習使用 Fieldset
 *
 *
 * @package
 * @extends  Controller
 */
class Controller_Fieldset extends Controller
{

    /**
     * 使用 Fieldset 的範例程式 - 新增書本
     *
     * @access  public
     * @return  Response
     */
    public function action_index()
    {
        /*
        $article_form = Fieldset::forge('article');
        $validation = $article_form->validation();

        $form = $article_form->form();

        // Radio 範例
        $ops = array('male', 'female');
        $form->add(
            'gender', '',
            array('options' => $ops, 'type' => 'radio', 'value' => 'true')
        );

        // Email input 範例，使用驗證規則
        $form->add(
            'email', 'E-mail',
            array('type' => 'email', 'class' => 'pretty_input'),
            array('required', 'valid_email')
        );

        // text input 範例，使用陣列表示的驗證規則
        $form->add(
            'name', 'Full name',
            array('type' => 'name', 'class' => 'pretty_input'),
            array(array('required'), array('valid_string', array('alpha-numeric', 'dots', 'spaces')))
        );

        */

        // ONLY Orm\Model support
        //$fieldset = Fieldset::forge()->add_model('Model_Books');

        $fieldset = Fieldset::forge();
        $form = $fieldset->form();

        $form->add(
            'title', '書本名稱',
            array('type' => 'text', 'class' => 'pretty_input'),
            array(array('required'), array('valid_string', array('alpha-numeric', 'dots', 'spaces')))
        );

        $form->add(
            'binding', '包裝方式',
            array('type' => 'text', 'class' => 'pretty_input'),
            array(array('required'), array('match_value','paper|cloth'))
        );

        $form->add(
            'quantity', '數量',
            array('type' => 'text', 'class' => 'pretty_input'),
            array(array('required'), array('valid_string', array('alpha-numeric')))
        );

        $form->add('submit', '', array('type' => 'submit', 'value' => '送出', 'class' => 'btn medium primary'));

        /*
        // 尋找所有書本資料
        $entry = Model_Books::find(
            array(
                'order_by' => array('id' => 'asc'),
            )
        );
        echo '<pre>';
        foreach ($entry as $row) {
            foreach ($row as $key => $value) {
                echo $key.' => '.$value."\n";
            }
        }
        echo '</pre>';
        */

        $view = View::forge('empty');
        $view->set('content', $form->build('fieldset/add'), false);
        return Response::forge($view);
    }


    /**
     * 使用 Fieldset 的範例程式 - 執行 新增書本
     *
     * @access  public
     * @return  Response
     */
    public function action_add()
    {

        $fieldset = Fieldset::forge();
        $form     = $fieldset->form();
        $form->add('submit', '', array('type' => 'submit', 'value' => '送出', 'class' => 'btn medium primary'));
        $form->input('form_title');

        if($fieldset->validation()->run() == true)
        {
            $fields = $fieldset->validated();

            //print_r($fieldset);
            //print_r($form);
            /*
            $post = new Model_Books;
            $post->title     = $fields['form_title'];
            $post->binding   = $fields['form_binding'];
            $post->quantity    = $fields['form_quantity'];
            $post->created_at   = time();
            $post->updated_at = time();
            if($post->save()) {
                \Response::redirect('.');
            }
            */
        } else {
            $view = View::forge('empty');
            $view->messages = $fieldset->validation()->errors();
        }
        $view = View::forge('empty');
        $view->set('content', $form->build('fieldset/add'), false);
    }
}