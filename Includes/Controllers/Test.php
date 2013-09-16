<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

echo 'Test Page!';

$testArray = [
    'lolwhut' => '2323413',
    '1234',
    '9' => DateTime::CreateFromFormat('U', time())
];

$cache = new DataCache('Cache/DataCache/');

$cache->store('test', $testArray, '1 hour');

$testArrayAfter = $cache->fetch('test');

Debug::dump($testArrayAfter);