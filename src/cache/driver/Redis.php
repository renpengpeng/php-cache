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
	public function set($key,$value,$expire=null){
		$value 		=	$this->creationCacheValue($key,$value,$expire,$this->options);
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
		return $this->readCache($key) ? $this->readCache($key)['value'] : $default;
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
		$read 	=	$this->readCache($key);
		if($read == false){
			return false;
		}
		if($expire == 0){
			return $this->set($key,$read['value']);
		}
		$stopTime 	=	( $read['stop_time'] == 0 ) ? time() + $expire : $read['stop_time'] + $expire;
		if($stopTime <  time()){
			return $this->delete($key);
		}
		return $this->set($key,$read['value'],$stopTime - time());
	}

	/**
	 * 缓存自增
	 * @param  String  $key  键
	 * @param  integer $step 步长
	 * @return Boolean
	 */
	public function increment($key,$step=1){
		$read 	=	$this->readCache($key);
		if($read == false){
			return false;
		}
		$value 	=	(int)$read['value'] + $step;
		return $this->set($key,$value,$read['stop_time'] == 0 ? null : $read['stop_time'] - time());
	}

	/**
	 * 缓存自减
	 * @param  String  $key  键
	 * @param  integer $step 步长
	 * @return Boolean
	 */
	public function reduction($key,$step=1){
		return $this->increment($key, 0 - $setp);
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

	/**
	 * 读取缓存
	 * @param  String $key 读取的Key
	 * @return Boolean || Array
	 */
	protected function readCache($key){
		$read 	=	$this->handle->get($key);
		if(!$read || ! $read = unserialize(gzinflate($read))){
			return false;
		}
		return $read;
	}
}