<?php
/**
 * Use this file to override global defaults.
 *
 * See the individual environment DB configs for specific config information.
 */

return array(

    'development' => array(
        'type'           => 'mysql',
        'connection'     => array(
            'hostname'       => 'localhost',
            'port'           => '3306',
            'database'       => 'test',
            'username'       => 'root',
            'password'       => 'mysql1234',
            'persistent'     => false,
            'compress'       => false,
        ),
        'identifier'   => '`',
        'table_prefix'   => '',
        'charset'        => 'utf8',
        'enable_cache'   => true,
        'profiling'      => false,
    ),
    /*
    'default' => array(
        'connection'  => array(
            'dsn'   => 'mysql:host=localhost;dbname=test',
            'username' => 'root',
            'password' => 'mysql1234',
        ),
    ),
    */
);
