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



        echo '<pre>';
        var_dump($redis->get('key'));
        print_r(json_decode($redis->get('data')));

        echo '<hr>';
        print_r($new_entry);
        echo '</pre>';

    }

}
