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
     *
     * @access  public
     * @return  Response
     */
    public function action_index()
    {
        //$process = 0;

        echo 'abc';

        // 建立 Redis 'mystore' 實例
        $redis = \Fuel\Core\Redis::forge('default');

        // 建立一些測試資料
        $redis->rpush('particles', 'proton');
        $redis->rpush('particles', 'electron');
        $redis->rpush('particles', 'neutron');

        // 提取範圍
        $particles = $redis->lrange('particles', 0, -1);

        // 計算元素的數量
        $particle_count = $redis->llen('particles');

        // 顯示結果
        echo "<p>The {$particle_count} particles that make up atoms are:</p>";
        echo "<ul>";
        foreach ($particles as $particle) {
            echo "<li>{$particle}</li>";
        }
        echo "</ul>";



        $view = View::forge('empty');
        return Response::forge($view);
    }
}
