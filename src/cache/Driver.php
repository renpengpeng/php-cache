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
	
}