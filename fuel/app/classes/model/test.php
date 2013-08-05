<?php

    class Model_Message extends \Model_Crud
{
    // 設定要使用的資料表
    protected static $_table_name = 'message';
    protected static $_primary_key = 'custom_id';


    // 找出所有留言資料
    // SELECT * FROM `users` LIMIT 10 OFFSET 20
    $message = Model_Message::find_all();


    // 新增留言, 寫入資料庫
    $message = Model_Message::forge()->set(array(
    'm_name' => 'My Name',
    'm_email' => 'My Surname',
    'm_context' => 'My Surname',
    ));
    $result = $message->save();

}
