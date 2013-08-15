<?php
/**
 *
 * 書籍相關處理
 *
 * @package
 * @category
 * @author    karisan
 */
class Controller_Books extends Controller_Protected {
    /**
     * 基本js code
     *
     */
    public function __construct()
    {
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
     *  基本css設定
     *
     */
    private $common_css = <<<END
form { margin: 15px 0; }
form td { padding: 5px; }
.message { color: red; }
span.message { padding-left: 5px; }
input[name='title'] { width: 400px; }
input[name='qty'] { width: 50px; }
END;

    /**
     * 驗證書本相關欄位
     *
     */
    private function getValidator()
    {
        $validator = Validation::forge();
        $validator->add_field('title', 'title', 'trim|required');
        $num_rule = function ($val) {
            return (bool) preg_match('/^\d+$/', $val);
        };
        $validator->add('qty', 'qty')
            ->add_rule('trim')
            ->add_rule('required')
            ->add_rule(array('num' => $num_rule));
        $validator
            ->set_message('required', ':label is required')
            ->set_message('num', ':label must be non-neg. integer');
        return $validator;
    }

    /**
     * 新增 書本
     *
     */
    public function action_add()
    {
        $this->template->page_title = 'Add Book';
        $data = array(
            'bindings' => array('paper', 'cloth'),
            'title' => Input::param('title'),
            'qty' => Input::param('qty'),
            'binding' => Input::param('binding', 'paper'),
            'message' => null,
        );

        if (!is_null(Input::param('doit'))) {
            $validator = $this->getValidator();
            try {
                if (!$validator->run(Input::all())) {
                    $data['errors'] = e($validator->error());
                    throw new Exception('Validation Error');
                }

                $book = Model_Books::forge();
                $book->title = $validator->validated('title');
                $book->quantity = $validator->validated('qty');
                $book->binding = $data['binding'];
                $book->save();
                Response::redirect('/root/details?id='.$book->id);
            } catch (Exception $ex) {
                $data['message'] = $ex->getMessage();
            }
        }

        $this->template->content = View::forge('books/add', $data);

        $this->template->style
            = View::forge('style', array('css' => $this->common_css), false);
        $this->template->script
            = View::forge('script', array('js' => $this->common_js), false);
    }

    /**
     * 修改書本
     *
     */
    public function action_modify()
    {
        $this->template->page_title = 'Modify Book';
        $id = Input::param('id');

        $book = Model_Books::find_by_pk($id);
        is_null($id) || is_null($book) and Response::redirect('/');

        $data = array(
            'bindings' => array('paper', 'cloth'),
            'id' => $id,
            'title' => Input::param('title', $book->title),
            'qty' => Input::param('qty', $book->quantity),
            'binding' => Input::param('binding', $book->binding),
            'message' => null,
        );

        if (!is_null(Input::param('doit'))) {
            $validator = $this->getValidator();
            try {
                if (!$validator->run(Input::all())) {
                    $data['errors'] = e($validator->error());
                    throw new Exception('Validation Error');
                }

                $book->title = $validator->validated('title');
                $book->quantity = $validator->validated('qty');
                $book->binding = $data['binding'];
                $book->save();
                Response::redirect('/root/details?id='.$book->id);
            } catch (Exception $ex) {
                $data['message'] = $ex->getMessage();
            }
        }

        $this->template->content = View::forge('books/modify', $data);

        $this->template->style
            = View::forge('style', array('css' => $this->common_css), false);
        $this->template->script
            = View::forge('script', array('js' => $this->common_js), false);
    }

    /**
     * 刪除書本
     *
     */
    public function action_delete()
    {
        $id = Input::param('id');
        $book = Model_Books::find_by_pk($id);
        is_null($id) || is_null($book) and Response::redirect('/');
        $book->delete();
        Response::redirect('/');
    }
}
