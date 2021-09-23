## PHP-Cache 

### 简介

php-cache 封装了Redis，Memcache，Yac，Apcu缓存方式，让PHP缓存更便捷。

[READEME.md英文版](READEME.md)

### 安装

`composer require renpengpeng/php-cache`

### 示例操作

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

### 使用手册

语雀：https://www.yuque.com/ha-renpengpeng/php-cache