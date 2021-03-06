<?php
/**
 * Created by JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/15
 * Time: 下午 2:44
 * To change this template use File | Settings | File Templates.
 */

/**
 * PHPUnit Test for UserLog
 * @group App
 * @group New
 *
 */
class Test_UserLog extends TestCase
{

    /**
     *
     * 產生預設訊息
     *
     * @param   $c_file = ''
     * @param   $c_function = ''
     * @param   $c_class = ''
     * @param   $c_method = ''
     *
     * @return  String
     */
    public function build_info(
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
        return $tmp;
    }

    /**
     *
     * 帶入參數的log測試
     *
     * @param   void
     * @return  void
     */
    public function test()
    {
        $tmp_username = 'karisan';
        $tmp_log_action = 'Testing';
        $tmp_info = '';

        // 寫入log
        $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);
        $mylog->user_action_log($tmp_username, $tmp_log_action, 'S', $tmp_info);

        $tmp_info = $this->build_info(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);
        // 抓出DB之值，看是否一致
        $entry = Model_Actionlog::find(
            array(
                'order_by' => array(
                    'id' => 'desc',
                ),
                'limit' => 1,
            )
        );

        //print_r($entry[0]->username);
        // 測試是否一致
        $this->assertEquals($tmp_username, $entry[0]->username);
        $this->assertEquals($tmp_log_action, $entry[0]->action);
        $this->assertEquals('S', $entry[0]->status);
        $this->assertEquals($tmp_info, $entry[0]->info);
    }

    /**
     *
     * 未帶入參數的log測試
     *
     * @param   void
     * @return  void
     */
    public function test2()
    {
        $tmp_info = '';

        // 寫入log
        $mylog = UserLog::forge(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);
        $mylog->user_action_log();

        $tmp_info = $this->build_info(__FILE__, __FUNCTION__, __CLASS__, __METHOD__);
        $tmp_info .= 'none';

        // 抓出DB之值，看是否一致
        $entry = Model_Actionlog::find(
            array(
                'order_by' => array(
                    'id' => 'desc',
                ),
                'limit' => 1,
            )
        );

        //print_r($entry[0]->username);
        // 測試是否一致
        $this->assertEquals('guest', $entry[0]->username);
        $this->assertEquals('login', $entry[0]->action);
        $this->assertEquals('S', $entry[0]->status);
        $this->assertEquals($tmp_info, $entry[0]->info);
    }

}