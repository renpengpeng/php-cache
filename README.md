## PHP-Cache

### Introduce

Php-cache encapsulates Redis, Memcache, Yac, and Apcu cache modes to make PHP cache more convenient.

[READEME.md Chinese](README_CN.md)

### Install
`composer require renpengpeng/php-cache`

### Example

```php
<?php
require '../vendor/autoload.php';

use renpengpeng\Cache;

Cache::connect([
	'type'	=>	'File',
	'file'	=>	[
		'cache_dir'	=>	realpath(__DIR__).DIRECTORY_SEPARATOR.'cache'
	]
],true);

// Set the cache 60 seconds
Cache::set('version','1.0.0',60);

// Get the cache
Cache::get('version','1.0.0');

// Since the increase
Cache::increment('version');

// Since the reduction of
Cache::reduction('version');

// Delay is permanent
Cache::delay('version');

// Delete the cache
Cache::delete('version');

// Clear the cache
Cache::clear();
```

### Manual
Yuque：https://www.yuque.com/ha-renpengpeng/php-cache
