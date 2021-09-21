<?php
namespace renpengpeng\cache\driver;

use renpengpeng\cache\Driver;

class File extends Driver {

	private $options;

	public function __construct($options){
		$this->options 	=	$options;
		if(is_dir($this->options['file']['cache_dir']) == false){
			mkdir($this->options['file']['cache_dir'],0777,true);
		}
	}

	/**
	 * 设置缓存
	 * @param String $key    	键
	 * @param Any $value  		值
	 * @param Int|null $expire 有效期
	 */
	public function set($key,$value,$expire=0){
		$cacheFile 	=	$this->positionCacheFile($key);
		$data 		=	$this->creationCacheValue($key,$value,$expire,$this->options);
		return file_put_contents($cacheFile,$data) ? true : false;
	}

	/**
	 * 获取缓存
	 * @param  string $key     键
	 * @param  null   $default 默认
	 * @return none
	 */
	public function get($key,$default=null){
		$read 	=	$this->readCache($key);
		if( $read == false){
			return $default;
		}
		return $read['value'];
	}

	/**
	 * 是否有某个缓存
	 * @param  String  $key 键
	 * @return boolean
	 */
	public function has($key){
		return $this->get($key) ? true : false;
	}

	/**
	 * 给缓存延时
	 * @param  String  $key    键
	 * @param  integer $expire 值
	 * @return Boolean
	 */
	public function delay($key,$expire=0){
		$read 	=	$this->readCache($key);
		if( $read  == false){
			return false;
		}
		// 转换为永久缓存
		if($expire == 0){
			return $this->set($key,$read['value'],$expire);
		}
		$stopTime 	= 	(($read['stop_time'] == 0) ? time() : $read['stop_time']) + $expire;
		if(time() > $stopTime){
			// 设置过期
			return $this->delete($key);
		}
		$expire 	=	$stopTime - time();
		return $this->set($key,$read['value'],$expire);
	}

	/**
	 * 缓存自增
	 * @param  String  $key  键
	 * @param  integer $step 步长
	 * @return Boolean
	 */
	public function increment($key,$step=1){
		$read 	=	$this->readCache($key);
		if( $read == false ){
			return false;
		}
		// 重新写入
		return $this->set($key,$this->sum($read['value'],$step), $read['stop_time'] == 0 ? null : $read['stop_time'] - time());
	}

	/**
	 * 缓存自减
	 * @param  String  $key  键
	 * @param  integer $step 步长
	 * @return Boolean
	 */
	public function reduction($key,$step){
		return $this->increment($key, 0 - $step);
	}

	/**
	 * 删除缓存
	 * @param  String $key 键
	 * @return bool
	 */
	public function delete($key){
		$cacheFile 	=	$this->positionCacheFile($key);
		if(is_file($cacheFile) == false || unlink($cacheFile) == false){
			return false;
		}
		return true;
	}

	/**
	 * 清空所有的缓存
	 * @return Boolean
	 */
	public function clear(){
		$dir 	=	$this->options['file']['cache_dir'].DIRECTORY_SEPARATOR;
		$scan 	=	@scandir($dir);
		if($scan == false){
			return false;
		}

		if(count($scan) == 2){
			return true;
		}

		foreach($scan as $key => $value){
			if($value == '.' || $value == '..'){
				continue;
			}
			$unlink 	=	unlink($dir.$value);
			if(!$unlink){
				return false;
			}
		}
		return true;
	}

	// ###################  分割线  ###################

	/**
	 *定位缓存目录
	 * @param  String $key     键
	 * @return String
	 */
	protected function positionCacheFile($key){
		return $this->options['file']['cache_dir'].DIRECTORY_SEPARATOR.md5($key);
	}

	/**
	 * 读取缓存文件
	 * @param  String   $key 键
	 * @return Cache
	 */
	protected function readCache($key){
		$cacheFile 	=	$this->positionCacheFile($key);
		if( !is_file($cacheFile) || !$read = file_get_contents($cacheFile) ){
			return false;
		}
		$read 		=	unserialize(@gzinflate($read));
		if(!$read || ( $read['stop_time'] < time()  && $read['stop_time'] != 0 ) ){
			// 过期了删除缓存
			$this->delete($key);
			return false;
		}
		return $read;
	}

	/**
	 * 生成存储值
	 * @param  String $key     		键
	 * @param  String $value   		值
	 * @param  int|null $expire  	生效时间
	 * @param  Array $options 		配置参数
	 * @return gzdeflate String
	 */
	protected function creationCacheValue($key,$value,$expire,$options){
		$data 	=	[
			'key'			=>	$key,
			'value'			=>	$value,
			'expire'		=>	$expire,
			'start_time'	=>	time(),
			'stop_time'		=>	$expire ? (time() + $expire) : $options['expire']
		];
		return gzdeflate(serialize($data));
	}
}