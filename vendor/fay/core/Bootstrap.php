<?php
namespace fay\core;

use fay\core\FBase;
use fay\core\Uri;

class Bootstrap extends FBase{
	public function init(){
		//默认时区
		$date = $this->config('date');
		$default_timezone = $date['default_timezone'];
		date_default_timezone_set($default_timezone);
		
		//报错级别
		switch ($this->config('environment')){
			case 'development':
				error_reporting(E_ALL);
				break;
			case 'production':
				error_reporting(0);
				break;
		}
		
		//路由
		$uri = new Uri();
		
		if($this->config('hook')){
			Hook::getInstance()->call('after_uri');
		}
		
		if(!$uri->router){
			//路由解析失败
			throw new HttpException('Routing format illegal');
		}
		
		//根据router来读取缓存
		if(!\F::input()->get('__r')){//强制跳过缓存，主要用于测试
			$cache_routers = $this->config('*', 'cache');
			$cache_routers_keys = array_keys($cache_routers);
			if(!Input::getInstance()->post() && in_array($uri->router, $cache_routers_keys)){
				$filepath = APPLICATION_PATH.'runtimes/cache/pages/'.$uri->router;
				$cache_file = $filepath . '/' . md5(json_encode(Input::getInstance()->get(isset($cache_routers[$uri->router]['params']) ? $cache_routers[$uri->router]['params'] : array())));
				if(file_exists($cache_file) && ($cache_routers[$uri->router]['ttl'] == 0 || filemtime($cache_file) + $cache_routers[$uri->router]['ttl'] > time())){
					if(!empty($cache_routers[$uri->router]['function'])){
						$cache_routers[$uri->router]['function']();
					}
					readfile($cache_file);
					die;
				}
			}
		}
		
		$file = $this->getControllerAndAction($uri);
		$controller = new $file['controller'];
		if($this->config('hook')){
			Hook::getInstance()->call('after_controller_constructor');
		}
		$controller->{$file['action']}();
		
	}
	
	/**
	 * 查找对应的controller文件和action方法
	 */
	private function getControllerAndAction($uri){
		//先找当前app目录
		if(file_exists(MODULE_PATH . $uri->module  . '/controllers/' . ucfirst($uri->controller) . 'Controller.php')){
			$class_name = '\\'.APPLICATION.'\modules\\'.$uri->module.'\controllers\\'.$uri->controller.'Controller';
			if(method_exists($class_name, $uri->action)){
				//直接对应的action
				return array(
					'controller'=>$class_name,
					'action'=>$uri->action,
				);
			}else if(method_exists($class_name, $uri->action.'Action')){
				//特殊关键词可能需要添加Action后缀
				return array(
					'controller'=>$class_name,
					'action'=>$uri->action.'Action',
				);
			}else{
				throw new HttpException("Action \"{$uri->action}\" Not Found IN Controller \"{$class_name}\"");
			}
		}
		
		//无直接对应的类文件或者类文件中无此Action，查找后台
		if(file_exists(BACKEND_PATH . 'modules/'. $uri->module . '/controllers/'. ucfirst($uri->controller) . 'Controller.php')){
			$class_name = '\cms\modules\\'.$uri->module.'\controllers\\'.$uri->controller.'Controller';
			if(method_exists($class_name, $uri->action)){
				//直接对应的action
				return array(
					'controller'=>$class_name,
					'action'=>$uri->action,
				);
			}else if(method_exists($class_name, $uri->action.'Action')){
				//特殊关键词可能需要添加Action后缀
				return array(
					'controller'=>$class_name,
					'action'=>$uri->action.'Action',
				);
			}else{
				throw new HttpException("Action \"{$uri->action}\" Not Found IN Controller \"{$class_name}\"");
			}
		}
		
		//访问地址不存在
		throw new HttpException("Controller \"{$uri->controller}\" Not Found");
	}
}