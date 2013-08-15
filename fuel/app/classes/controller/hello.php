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
class Controller_Hello extends Controller
{
    /**
     *
     * 輸入 /hello/index 時，會進入此頁，顯示基本文字訊息
     *
     * @param   void
     * @return  void
     */
    public function action_index()
    {
        // phpinfo();
        echo '__NAMESPACE__:'.__NAMESPACE__;
        //echo "Hello World!";
        if (0) {
            echo '';
        } else if (1) {
            echo '';
        }
    }

    public function qq($argv = '')
    {
        return $argv;
        //echo "Hello World!";
    }

    /**
     *
     * 輸入 /hello/buddy 時，會進入此頁，顯示基本文字訊息
     *
     * @param   $name
     * @return  void
     */
    public function action_buddy($name = 'buddy')
    {
        $view = View::forge('hello');
        $view->name = $name;
        return Response::forge($view);
    }
}
