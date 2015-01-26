<?php
namespace backend\modules\tools\controllers;

use backend\library\ToolsController;
use fayfox\helpers\RequestHelper;

class IndexController extends ToolsController{
	
	public function index(){
		$this->layout->subtitle = 'Tools';
		//引入IP地址库
		$this->plugin->load('IpLocation/IpLocation.class');
		$this->view->iplocation = new \IpLocation();
		
		//浏览器类型
		$this->view->browser = RequestHelper::getBrowser();
		
		$this->view->render();
	}
}