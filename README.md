## PHP-Cache

### Introduce

Php-cache encapsulates Redis, Memcache, Yac, and Apcu cache modes to make PHP cache more convenient.

[READEME.md Chinese](READEME.md)

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

// 设置缓存 60秒
Cache::set('version','1.0.0',60);

// 获取缓存
Cache::get('version','1.0.0');

// 自增
Cache::increment('version');

// 自减
Cache::reduction('version');

// 延时为永久
Cache::delay('version');

// 删除缓存
Cache::delete('version');

// 清空缓存
Cache::clear();
```

### Manual
语雀：https://www.yuque.com/ha-renpengpeng/php-cache
