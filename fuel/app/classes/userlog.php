<?php
/**
 * Created by JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/13
 * Time: 上午 11:55
 * To change this template use File | Settings | File Templates.
 */

/**
 *
 * 記錄使用者操作記錄
 *
 * @package
 * @category
 * @author    karisan
 */
class UserLog
{
    /**
     * @var 將 magic constant 串成參數，之後會放在info欄位開頭
     */
    protected static $init_info = '';

    /**
     * Forges new Controller_Userlog objects.
     *
     * @param	$c_file = '',		傳入__FILE__
     * @param	$c_function = '',	傳入__FUNCTION__
     * @param	$c_class = '',		傳入__CLASS__
     * @param	$c_method = ''		傳入__METHOD__
     * @return  Controller_Userlog
     */
    public static function forge(
        $c_file = '',
        $c_function = '',
        $c_class = '',
        $c_method = ''
    ) {
        $tmp = '';
        $tmp .= 'FILE: '.$c_file."\n";
        $tmp .= 'FUNCTION: '.$c_function."\n";
        $tmp .= 'CLASS: '.$c_class."\n";
        $tmp .= 'METHOD: '.$c_method."\n";
        static::$init_info = $tmp;
        return new static(static::$init_info);
    }

    /**
     * 使用者操作 記錄處理
     *
     * @param	$log_user = 'guest'		傳入帳號
     * @param	$log_action = 'login',	傳入操作動作
     * @param	$log_status = 'S',	傳入狀態
     * @param	$log_info = 'none',	傳入其它訊息
     * @return  void
     */

    public static function user_action_log(
        $log_user = 'guest',
        $log_action = 'login',
        $log_status = 'S',
        $log_info = 'none'
    ) {
        // 串成info的內容
        $tmp_info = '';
        $tmp_info .= static::$init_info;
        $tmp_info .= $log_info;

        if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $var_from_global = array(
                'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
                'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
                'REQUEST_URI' => $_SERVER['REQUEST_URI'],
            );
        } else {
            $var_from_global = array(
                'REMOTE_ADDR' => '',
                'REQUEST_METHOD' => '',
                'REQUEST_URI' => '',
            );
        }

        // 新增 操作log
        $action_log = Model_Actionlog::forge()->set(
            array(
                'username' => $log_user,
                'time' => date('Y/m/d H:i:s'),
                'ip' => $var_from_global['REMOTE_ADDR'],
                'action' => $log_action,
                'status' => $log_status,
                'url' => $var_from_global['REQUEST_METHOD'].' '.$var_from_global['REQUEST_URI'],
                'info' => $tmp_info,
            )
        );
        $action_log->save();
    }
    /**
     */
    public function __call($name, $args)
    {
        // build the Redis unified protocol command
        array_unshift($args, strtoupper($name));

        $command = sprintf('*%d%s%s%s', count($args), CRLF, implode(array_map(function($arg) {
                        return sprintf('$%d%s%s', strlen($arg), CRLF, $arg);
                    }, $args), CRLF), CRLF);

        print_r($command);
        /*
        // add it to the pipeline queue
        $this->queue[] = $command;

        if ($this->pipelined)
        {
            return $this;
        }
        else
        {
            return $this->execute();
        }
        */
    }

}
