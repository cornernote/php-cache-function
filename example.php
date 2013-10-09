<?php
// include the function
require('cache.php');

// start the page timer to see how much time we saved
$start = microtime(true);

// set the cache key
$key = 'some-cached-element';

// read cache
$data = cache($key);
if ($data === null) {

    // read failed, get the data from database or other slow storage location
    $data = array('some' => 'data', 'more' => 'stuff');
    sleep(5);

    // write cache
    cache($key, $data, '+30 seconds');

}

// clear this cache key
#cache($key,null,0);

// clear all cache
#cache('cache_id',null,0);

// calculate the running time and output the data
$time = microtime(true) - $start;
debug($time);

// output the data
debug($data);

// helper functions
function debug($debug)
{
    echo '<pre>';
    print_r($debug);
    echo '</pre>';
}