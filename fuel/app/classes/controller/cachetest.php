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
class Controller_Cachetest extends Controller
{
    /**
     * 踢出未登入的使用者，當未登入時，
     * 則會以此功能，將無權限的user踢出
     *
     * @param   void
     * @return  void
     */
    public function before()
    {
        parent::before();
        if (is_null(Session::get('valid'))) {
            Response::redirect('/validate/expired');
        }
    }

    /**
     * 使用 Fuelphp Cache 的範例程式
     * @access  public
     * @return  Response
     */
    public function action_index()
    {
        //$process = 0;

        echo 'abc';

        // 尋找最新的log id
        $new_entry = Model_Actionlog::find(
            array(
                'order_by' => array('id' => 'desc'),
                'limit' => '1'
            )
        );

        // 建立 Redis 'mystore' 實例
        $redis = \Fuel\Core\Redis::forge('default');

        // 建立一些測試資料
        $redis->rpush('particles', 'proton');
        $redis->rpush('particles', 'electron');
        $redis->rpush('particles', 'neutron');

        $redis->set('data', json_encode($new_entry));


        // 傳遞一些 redis 命令到流水線，然後執行它們
        $result = $redis->pipeline()
            ->sadd('list', 4)
            ->sadd('list', 1)
            ->sadd('list', 55)
            ->execute();

        // 提取範圍
        $particles = $redis->lrange('particles', 0, -1);

        // 計算元素的數量
        $particle_count = $redis->llen('particles');
        if ($particle_count > 10) {
            $redis->del('particles');
        }

        if ($particle_count > 0) {
            // 顯示結果
            echo "<p>The {$particle_count} particles that make up atoms are:</p>";
            echo "<ul>";
            foreach ($particles as $particle) {
                echo "<li>{$particle}</li>";
            }
            echo "</ul>";
        }

        echo '<pre>';
        print_r(json_decode($redis->get('data')));

        print_r($redis->sort('list'));
        echo '</pre>';

        echo Html::anchor('cachetest/memcached', 'PHP-Memchched 測試').'<br>';
        echo Html::anchor('cachetest/redis', 'PHP-Redis 測試').'<br>';

    }

    public function action_memcached()
    {

        $m = new Memcached();
        $m->addServer('localhost', 11211);


        // 尋找最新的log id
        $new_entry = Model_Actionlog::find(
            array(
                'order_by' => array('id' => 'desc'),
                'limit' => '1'
            )
        );

        $m->set('data', $new_entry);

        $m->set('int', 99);
        $m->set('string', 'a simple string');
        $m->set('array', array(11, 12));
        /* expire 'object' key in 5 minutes */
        $m->set('object', new stdclass, time() + 300);

        echo '<pre>';
        var_dump($m->get('int'));
        var_dump($m->get('string'));
        var_dump($m->get('array'));
        var_dump($m->get('object'));
        var_dump($m->get('data'));
        echo '</pre>';

    }

    public function action_redis()
    {

        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);



        // 尋找最新的log id
        $new_entry = Model_Actionlog::find(
            array(
                'order_by' => array('id' => 'desc'),
                'limit' => '1'
            )
        );

        $redis->set('key', 'value');
        $redis->set('data', json_encode($new_entry));

        /*
        $result = $redis->pipeline()
            ->sadd('list', 4)
            ->sadd('list', 1)
            ->sadd('list', 55)
            ->execute();
        */

        echo '<pre>';
        var_dump($redis->get('key'));
        print_r(json_decode($redis->get('data')));

        echo '<hr>';
        print_r($new_entry);

        print_r($redis->info());

        print_r($redis->get('list'));
        echo '</pre>';

    }

    public function action_test()
    {
        $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);
        $mylog->user_action_log('', '', 'S', '');
        $mylog->rpush('particles', 'proton');

        $a1 = array("1","2","3");
        $a2 = array("a");
        $a3 = array();

        echo "a1 is: '".implode("','",$a1)."'<br>";
        echo "a2 is: '".implode("','",$a2)."'<br>";
        echo "a3 is: '".implode("','",$a3)."'<br>";
    }

    public function action_pagination()
    {

        $config = array(
            'pagination_url' => Uri::create('cachetest/pagination'),
            'total_items'    => Model_Message::count(),
            'per_page'       => 5,
            'uri_segment'    => 3,
            // or if you prefer pagination by query string
            //'uri_segment'    => 'page',
        );

        // Create a pagination instance named 'mypagination'
        $pagination = Pagination::forge('mypagination', $config);

        $data['example_data'] = Model_Message::find(
            array(
                'order_by' => array('m_id' => 'desc'),
                'limit' => $pagination->per_page,
                'offset' => $pagination->offset,
            )
        );

        Log::write('Link', '======== Test ========');
        Log::write('Link', print_r($data['example_data'],true));


        $data['pagination'] = $pagination->render();
        //$this->render('welcome/index', $data);
        // 導回新增使用者頁面
        $view = View::forge('pagination');
        $view->content = $data['example_data'];
        // JS 權限檢查-使用上一頁進入時，會被踢走
        $view->validscript = View::forge('validscript', array('isValidURL' => Uri::create('validate/isvalid')));
        $view->set('paging',$data['pagination'],false);
        return Response::forge($view);
    }
}
