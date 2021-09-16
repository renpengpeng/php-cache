<?php

namespace renpengpeng;

use renpengpeng\cache\Driver;

// 屏蔽错误
error_reporting(0);

class Cache {
	/**
	 * options 配置
	 * @var Array
	 */
	static $options 	=	[
		// 缓存使用驱动，可选 ： File(文件) / Redis / Memcached
		'type'		=>	'File',
		// 缓存前缀
		'prefix'	=>	'cache_',
		// 默认缓存时间： 0 = 永久，单位：s
		'expire'	=>	0,
		// File 缓存配置
		'file'		=>	[
			'cache_dir'	=>	null,
		],
		// Redis 缓存配置
		'redis'		=>	[

		],
	];

	/**
	 * 将实例缓存起来
	 * @var array
	 */
	static $instance 	=	[];

	/**
	 * 连接当前的一个实例 && 并且返回
	 * @param  array   $options 配置
	 * @param  boolean $name   起个名
	 * @return Driver
	 */
	public static function connect(array $options=[],string $name=''){
		// 默认 File 类型
		$options['type'] 	=	isset($options['type']) ? ucwords($options['type']) : 'File';
		self::$options 		=	array_merge(self::$options,$options);

		if($options['type'] == 'File' && empty(self::$options['file']['cache_dir'])){
			return null;
		}
		if(empty($name)){
			$name 	=	md5(serialize(self::$options));
		}
		if(!isset(self::$instance[$name])){
			$class 		=	'renpengpeng\\cache\driver\\'.self::$options['type'];
			self::$instance[$name] 	=	new $class(self::$options);
		}
		return self::$instance[$name];
	}

	/**
	 * 设置缓存
	 * @param String  $key    设置的键
	 * @param Any  	  $value  设置的值
	 * @param int 	  $expire 设置时间,不设置的话默认使用全局配置
	 */
	public static function set($key,$value,$expire=null){
		return self::connect()->set($key,$value,($expire === null) ? self::$options['expire'] : $expire);
	}

	/**
	 * 获取缓存
	 * @param  String 		$key      键
	 * @param  Any|null  $default  值
	 * @return CacheResult
	 */
	public static function get($key,$default=null){
		return self::connect()->get($key,$default);
	}

	/**
	 * 是否有缓存
	 * @param  String  $key 键
	 * @return boolean 
	 */
	public static function has($key){
		return self::connect()->has($key);
	}

	/**
	 * 给缓存延时
	 * @param  String  $key    键
	 * @param  integer $expire 值
	 * @return Boolean
	 */
	public static function delay($key,$expire=0){
		return self::connect()->delay($key,$expire);
	}

	/**
	 * 缓存自增
	 * @param  String  $key  键
	 * @param  integer $step 步长
	 * @return Boolean
	 */
	public static function increment($key,$step=1){
		return self::connect()->increment($key,$step);
	}

	/**
	 * 缓存自减
	 * @param  String  $key  键
	 * @param  integer $step 步长
	 * @return Boolean
	 */
	public static function reduction($key,$step=1){
		return self::connect()->reduction($key,$step);
	}

	/**
	 * 删除缓存
	 * @param String $key 要删除的缓存键
	 * @return Boolean
	 */
	public static function delete($key){
		return self::connect()->delete($key);
	}

	/**
	 * 清空所有的缓存
	 * @return Boolean
	 */
	public static function clear(){
		return self::connect()->clear();
	}
}