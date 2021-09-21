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
	 * 相加计算
	 * @param  Any 			$value 需要相加的内容
	 * @param  int/float 	$step  步长
	 * @return int/float
	 */
	protected function sum($value,$step){
		$type 	=	gettype($value);
		switch ($type) {
			case 'boolean':
				$value 	=	(int)$value;
			break;

			case 'string':
				$value 	=	(float)$value;
			break;

			case 'integer':
				$value 	=	$value;
			break;

			case 'float':
				$value 	=	$value;
			break;
			
			default:
				$value 	=	0;
			break;
		}
		return $value + $step;
	}
}