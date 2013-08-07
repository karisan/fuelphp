<?php

class Model_Books extends \Model_Crud {

    protected static $_table_name = 'books';

    protected static $_primary_key = 'id';

    protected static $_properties = array(
        'id',
        'title',
        'binding',
        'quantity',
        'created_at',
        'updated_at',
    );

}
