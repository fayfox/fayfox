<?php
namespace backend\widgets\user_info\controllers;

use fayfox\core\Widget;

class IndexController extends Widget{
	
	public function index($options){
		//引入IP地址库
		$this->plugin->load('IpLocation/IpLocation.class');
		$this->view->iplocation = new \IpLocation();
		
		$this->view->render();
	}
	
	public function placeholder(){
		
		$this->view->render('placeholder');
	}
}