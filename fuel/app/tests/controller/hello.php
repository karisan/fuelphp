<?php
/**
 * Created by JetBrains PhpStorm.
 * User: karisan
 * Date: 2013/8/14
 * Time: 下午 3:10
 * To change this template use File | Settings | File Templates.
 */

/**
 *
 * @group App
 * @group Hello
 *
 */

class Test_Controller_Hello extends TestCase
{
    public function test_qq()
    {
        $test = new Controller_Hello(Request::forge());
        $this->assertEquals('',$test->qq());
        $this->assertEquals('Hello',$test->qq('Hello'));
        $this->assertEquals('AB',$test->qq('ABC'));
        /*
        $this->assertEquals(
            'ABC',
            $test->qq()
        );
        */
    }
}

/*
class Controller_Hello
{
    public function qq ()
    {
        return 'Hello';
    }

}
*/