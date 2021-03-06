<?php
/**
 * Cache
 *
 * @author Brett O'Donnell <cornernote@gmail.com>
 * @copyright 2013 Mr PHP
 * @link https://github.com/cornernote/php-cache-function
 * @license http://www.gnu.org/copyleft/gpl.html
 */

// prevent conflicts between different applications using the same memcache server
define('CACHE_NAMESPACE', 'my-cache-namespace');

// if memcache is not available this folder will be used to store cache
define('CACHE_FOLDER', '/tmp/cache/');

// memcache server hostname
define('CACHE_MEMCACHED_HOST', 'localhost');

// memcache server port
define('CACHE_MEMCACHED_PORT', '11211');

// Disable attempting to use a type of cache if you need to
// Otherwise we'll try and be smart and pick for you
define('USE_MEMCACHE',true);
define('USE_FILECACHE',true);

/**
 * cache()
 * Read, Write or Clear cached data using a key value pair.
 *
 * @param string $key - the key to use to store and retrieve the cached data
 * @param mixed $value - the data to store in cache
 * @param string $expires - the expiry time of the data
 * @return mixed - the cached data to return
 **/
function cache($key, $value = null, $expires = '+1 year')
{
    // static variables allowing the function to run faster when called multiple times
    static $cache_id, $memcached;

    // get the cache_id used for easy cache clearing
    if ($key != 'cache_id') {
        if (!$cache_id) {
            $cache_id = cache('cache_id', null);
        }
        if (!$cache_id) {
            $cache_id = md5(microtime());
            cache('cache_id', $cache_id);
        }
        $file = CACHE_NAMESPACE . '.' . $cache_id . '.' . $key;
    }
    else {
        $file = CACHE_NAMESPACE . '.' . $key;
    }

    // set the expire time
    $now = time();
    if (!is_numeric($expires)) {
        $expires = strtotime($expires, $now);
    }

    // attempt connection to memcache
    if (USE_MEMCACHE && $memcached === null) {
        if (class_exists('Memcached')) {
            if (!$memcached) {
                $memcached = new Memcached;
                @$memcached->addServer(CACHE_MEMCACHED_HOST, CACHE_MEMCACHED_PORT) or ($memcached = false);
            }
        }
    }

    // handle cache using memcache
    if (USE_MEMCACHE && $memcached) {
        // read cache
        if ($value === null) {
            $time = $memcached->get($file . '.time');
            if (!$expires || $time <= $now) {
                $memcached->delete($file . '.time');
                $memcached->delete($file . '.data');
            }
            else {
                $value = $memcached->get($file . '.data');
                if ($value === false) $value = null;
            }
        }
        // write cache
        else {
            $memcached->set($file . '.data', $value);
            $memcached->set($file . '.time', $expires);
        }
    }

    // handle cache using files
    elseif (USE_FILECACHE) {
        $md5 = md5($key);
        $file = CACHE_FOLDER . substr($md5, 0, 1) . '/' . substr($md5, 0, 2) . '/' . substr($md5, 0, 3) . '/' . $file;
        // read cache
        if ($value === null) {
            if (file_exists($file)) {
                $result = unserialize(file_get_contents($file));
                if (!$expires || $result['time'] <= $now) {
                    @unlink($file);
                }
                else {
                    $value = $result['data'];
                }
            }
        }
        // write cache
        else {
            $dir = dirname($file);
            if (!is_writable($dir)) {
                trigger_error("Cannot create directory $dir", E_USER_WARNING);
                return false;
            }

            if (!file_exists($dir)) {
                mkdir($dir, 0700, true);
            }
            file_put_contents($file, serialize(array('data' => $value, 'time' => $expires)));
        }
    } else {
        trigger_error("No storage engines left to try", E_USER_ERROR);
    }

    // return the data
    return $value;
}

// vim: ai:ts=4:sw=4:expandtab
