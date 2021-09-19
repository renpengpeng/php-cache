<?php
namespace renpengpeng\cache\driver;

use renpengpeng\cache\Driver;

class Redis extends Driver {

	/**
	 * 配置参数
	 * @var Array
	 */
	private $options;

	/**
	 * Redis Handle
	 * @var Object
	 */
	private $handle;

	public function __construct($options){
		$this->options 	=	$options;
		if(extension_loaded('redis') == false){
			throw new \Exception("Redis not ready!", 1);
		}
		$this->handle 	=	new \Redis();
		if($this->options['redis']['pconnect']){
			$this->handle->pconnect($this->options['redis']['host'],$this->options['redis']['port'],$this->options['redis']['timeout']);
		}else{
			$this->handle->connect($this->options['redis']['host'],$this->options['redis']['port'],$this->options['redis']['timeout']);
		}
		if($this->options['redis']['password']){
			$this->handle->auth($this->options['redis']['password']);
		}
		if($this->options['redis']['select']){
			$this->handle->select($this->options['redis']['select']);
		}
	}

	/**
	 * 设置缓存
	 * @access public
	 * @param String $key    	键
	 * @param Any $value  		值
	 * @param Int|null $expire 有效期
	 */
	public function set($key,$value,$expire=0){
		$value 		=	is_scalar($value) ?: serialize($value);
		if($expire){
			$set 	=	$this->handle->setex($key,$expire,$value);
		}else{
			$set 	=	$this->handle->set($key,$value);
		}
		return $set;
	}

	/**
	 * 获取缓存
	 * @param  string $key     键
	 * @param  null   $default 默认
	 * @return none
	 */
	public function get($key,$default=null){
		$value 	=	$this->handle->get($key);
		if($value === false){
			return $default;
		}
		return @unserialize($value) ?: ($value ? $value : $default);
	}

	/**
	 * 是否有某个缓存
	 * @param  String  $key 键
	 * @return boolean
	 */
	public function has($key){
		return $this->handle->exists($key);
	}

	/**
	 * 给缓存延时
	 * @param  String  $key    键
	 * @param  integer $expire 值
	 * @return Boolean
	 */
	public function delay($key,$expire=0){
		$get 	=	$this->get($key);
		if(!$get){
			return false;
		}
		$ttl 	=	$this->handle->ttl($key);
		if($expire != 0){
			$expire 	=	($ttl == '-1' ? 0 : $ttl) + $expire;
		}
		return $this->set($key,$get,$expire);
	}

	/**
	 * 缓存自增
	 * @param  String  $key  键
	 * @param  integer $step 步长
	 * @return Boolean
	 */
	public function increment($key,$step=1){
		return $this->handle->incrby($key,$step);
	}

	/**
	 * 缓存自减
	 * @param  String  $key  键
	 * @param  integer $step 步长
	 * @return Boolean
	 */
	public function reduction($key,$step=1){
		return $this->handle->decrby($key, $step);
	}

	/**
	 * 删除缓存
	 * @param  String $key 键
	 * @return bool
	 */
	public function delete($key){
		return $this->handle->delete($key) ? true : false;
	}

	/**
	 * 清空所有的缓存
	 * @return Boolean
	 */
	public function clear(){
		return $this->handle->flushDB();
	}
}