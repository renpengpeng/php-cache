<?php

namespace renpengpeng;

use renpengpeng\cache\Driver;

// 屏蔽错误
error_reporting(E_ALL | E_STRICT);

class Cache {
	/**
	 * options 配置
	 * @var Array
	 */
	static $options 	=	[
		'type'		=>	'File',
		'prefix'	=>	'cache_',
		'expire'	=>	0,
		'file'		=>	[
			'cache_dir'	=>	null,
		],
		'redis'		=>	[
			'host'		=>	'127.0.0.1',
			'port'		=>	'6379',
			'password'	=>	'',
			'select'	=>	'',
			'timeout'	=>	'',
			'pconnect'	=>	false
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
	 * @param  string  $name   起个名
	 * @param  boolean $forceInstance   如果为true则不缓存实例
	 * @return Driver
	 */
	public static function connect(array $options=[],$forceInstance=false){
		if(count($options)){
			// 默认 File 类型
			$options['type'] 	=	isset($options['type']) ? ucwords($options['type']) : 'File';
			self::$options 		=	array_merge(self::$options,$options);
		}
		
		if(self::$options['type'] == 'File' && empty(self::$options['file']['cache_dir'])){
			return null;
		}
		$cacheName 	=	md5(serialize(self::$options));
		$class 		=	'renpengpeng\\cache\driver\\'.self::$options['type'];
		if($forceInstance){
			return new $class(self::$options);
		}
		if(!isset(self::$instance[$cacheName])){
			self::$instance[$cacheName] 	=	new $class(self::$options);
		}
		return self::$instance[$cacheName];
	}

	/**
	 * 设置缓存
	 * @param String  $key    设置的键
	 * @param Any  	  $value  设置的值
	 * @param int 	  $expire 设置时间,不设置的话默认使用全局配置
	 */
	public static function set($key,$value,$expire=null){
		return self::connect()->set(self::$options['prefix'].$key,$value,($expire === null) ? self::$options['expire'] : $expire);
	}

	/**
	 * 获取缓存
	 * @param  String 		$key      键
	 * @param  Any|null  $default  值
	 * @return CacheResult
	 */
	public static function get($key,$default=null){
		return self::connect()->get(self::$options['prefix'].$key,$default);
	}

	/**
	 * 是否有缓存
	 * @param  String  $key 键
	 * @return boolean 
	 */
	public static function has($key){
		return self::connect()->has(self::$options['prefix'].$key);
	}

	/**
	 * 给缓存延时
	 * @param  String  $key    键
	 * @param  integer $expire 值
	 * @return Boolean
	 */
	public static function delay($key,$expire=0){
		return self::connect()->delay(self::$options['prefix'].$key,$expire);
	}

	/**
	 * 缓存自增
	 * @param  String  $key  键
	 * @param  integer $step 步长
	 * @return Boolean
	 */
	public static function increment($key,$step=1){
		return self::connect()->increment(self::$options['prefix'].$key,$step);
	}

	/**
	 * 缓存自减
	 * @param  String  $key  键
	 * @param  integer $step 步长
	 * @return Boolean
	 */
	public static function reduction($key,$step=1){
		return self::connect()->reduction(self::$options['prefix'].$key,$step);
	}

	/**
	 * 删除缓存
	 * @param String $key 要删除的缓存键
	 * @return Boolean
	 */
	public static function delete($key){
		return self::connect()->delete(self::$options['prefix'].$key);
	}

	/**
	 * 清空所有的缓存
	 * @return Boolean
	 */
	public static function clear(){
		return self::connect()->clear();
	}

}