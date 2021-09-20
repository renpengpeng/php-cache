<?php
namespace renpengpeng\cache\driver;

use renpengpeng\cache\Driver;

class Memcache extends Driver {

	/**
	 * 配置参数
	 * @var Array
	 */
	private $options;

	/**
	 * Memcached Handle
	 * @var Object
	 */
	private $handle;

	public function __construct($options){
		$this->options 	=	$options;
		if(extension_loaded('memcache') == false){
			throw new \Exception("Memcache not ready!", 1);
			return false;
		}
		$this->handle 	=	new \Memcache();
		if($this->options['memcache']['pconnect']){
			$this->handle->pconnect($this->options['memcache']['host'],$this->options['memcache']['port'],$this->options['memcache']['timeout']);
			;
		}else{
			$this->handle->connect($this->options['memcache']['host'],$this->options['memcache']['port'],$this->options['memcache']['timeout']);
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
		$value 		=	serialize([
			'value'			=>	$value,
			'start_time'	=>	time(),
			'stop_time'		=>	$expire ? time() + $expire : 0
		]);
		return $this->handle->set($key,$value,0,$expire);
	}

	/**
	 * 获取缓存
	 * @param  string $key     键
	 * @param  null   $default 默认
	 * @return none
	 */
	public function get($key,$default=null){
		$get 	=	$this->readCache($key);
		return $get === false ? $default : $get['value'];
	}

	/**
	 * 是否有某个缓存
	 * @param  String  $key 键
	 * @return boolean
	 */
	public function has($key){
		return $this->handle->get($key) === false ? false : true;
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
			return $this->set($key,$read['value'],$expire);
		}
		$stopTime 	=	$read['stop_time'] + $expire;
		if($stopTime < time()){
			// 过期 = 直接删除
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
		return $this->set($key,(float)$read['value'] + $step,$read['stop_time'] == 0 ? 0 : $read['stop_time'] - time());
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
		return $this->handle->flush();
	}

	/**
	 * 读取缓存
	 * @param  String $key 读取的key
	 * @return Any
	 */
	public function readCache($key){
		$get 	=	$this->handle->get($key);
		if(!$get){
			return false;
		}
		return unserialize($get) ?: false;
	}
}