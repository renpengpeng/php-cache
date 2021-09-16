<?php
namespace renpengpeng\cache;

abstract class Driver {

	abstract function set($key,$value,$expire);

	abstract function get($key,$default);

	abstract function delay($key,$expire);

	abstract function increment($key,$step);

	abstract function reduction($key,$step);

	abstract function delete($key);

	abstract function clear();

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