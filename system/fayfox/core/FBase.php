<?php
namespace fayfox\core;

use fayfox\core\Config;

class FBase{
	/**
	 * 获取配置文件配置项
	 * @param String $item
	 */
	public function config($item, $filename = 'main', $mode = 'merge'){
		if($item == '*'){
			return Config::getInstance()->getFile($filename, $mode);
		}else{
			return Config::getInstance()->get($item, $filename, $mode);
		}
	}
	
	/**
	 * 动态配置配置项
	 */
	public function setConfig($item, $value, $filename = 'main'){
		Config::getInstance()->set($item, $value, $filename);
	}
	
	/**
	 * 引入一个插件，事实上就是包含一个php文件进来
	 */
	public function loadPlugin($name){
		if(file_exists(APPLICATION_PATH . "plugins/{$name}.php")){
			require_once APPLICATION_PATH . "plugins/{$name}.php";
		}else if(file_exists(BACKEND_PATH . "plugins/{$name}.php")){
			require_once BACKEND_PATH . "plugins/{$name}.php";
		}
	}
	
}