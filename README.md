# PHP Cache Function

Function to store and retrieve variables from memcache with a filecache failback.

## Usage

```PHP
// Load the library
require('cache.php');

// Unique key for the data we are storing/fetching
$key = 'my_cache_key';

// Store data in the cache
$info = array('some' => 'data', 'more' => 'stuff');
cache($key,$info);

// Store data in the cache with a TTL
cache($key,$info,'+5 minutes');

// Fetch the data from the cache
$data = cache($key);
```

## About

Copyright (c) 2013 Brett O'Donnell <cornernote@gmail.com>

Source Code: https://github.com/cornernote/php-cache-function

## License

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

