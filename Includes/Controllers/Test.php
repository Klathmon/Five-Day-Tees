<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

for($x = 0; $x != 1000; $x++)
{
    try{
        $cacheCrap = DataCache::fetch('test');
    }catch(Exception $e){
        $cacheCrap = [
            'lol' => 'testing',
            'RAWR',
            '4' => 45,
            'dtobject' => DateTime::createFromFormat('U', time())
        ];
        DataCache::store('test', $cacheCrap, '1 hour');
    }
}

var_dump($cacheCrap);