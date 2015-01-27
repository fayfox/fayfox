<?php
namespace fayfox\core;

use fayfox\core\FBase;
use fayfox\core\Uri;

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
		
		if($uri->router == 404){
			Response::showError('您请求的页面不存在<br />router:'.$uri->router, 404, '页面不存在');
		}
		
		//根据router来读取缓存
		if(!\F::input()->get('__r')){//强制跳过缓存，主要用于测试
			$cache_routers = $this->config('*', 'cache');
			$cache_routers_keys = array_keys($cache_routers);
			if(!Input::getInstance()->post() && in_array($uri->router, $cache_routers_keys)){
				$filename = md5(json_encode(Input::getInstance()->get($cache_routers[$uri->router]['params'])));
				$filepath = APPLICATION_PATH.'runtimes/cache/pages/'.$uri->router;
				if(file_exists($filepath.'/'.$filename)){
					if($cache_routers[$uri->router]['ttl'] == 0 || filemtime($filepath.'/'.$filename) + $cache_routers[$uri->router]['ttl'] > time()){
						if(!empty($cache_routers[$uri->router]['eval'])){
							eval($cache_routers[$uri->router]['eval']);
						}
						readfile($filepath.'/'.$filename);
						die;
					}
				}
			}
		}
		
		$file = $this->getControllerAndAction($uri);
		if($file){
			$controller = new $file['controller'];
			if($this->config('hook')){
				Hook::getInstance()->call('after_controller_constructor');
			}
			$controller->{$file['action']}();
		}else{
			Response::showError('您请求的页面不存在<br />router:'.$uri->router, 404, '页面不存在');
		}
		
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
			}
		}
		
		//无直接对应的类文件或者类文件中无此Action，查找后台
		if(file_exists(BACKEND_PATH . 'modules/'. $uri->module . '/controllers/'. ucfirst($uri->controller) . 'Controller.php')){
			$class_name = '\backend\modules\\'.$uri->module.'\controllers\\'.$uri->controller.'Controller';
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
			}
		}
		
		//访问地址不存在
		return false;
	}
}