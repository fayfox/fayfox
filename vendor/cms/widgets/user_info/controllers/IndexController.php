<?php
namespace cms\widgets\user_info\controllers;

use fay\core\Widget;
use fay\core\Loader;

class IndexController extends Widget{
	
	public function index($options){
		//引入IP地址库
		Loader::vendor('IpLocation/IpLocation.class');
		$this->view->iplocation = new \IpLocation();
		
		$this->view->render();
	}
	
	public function placeholder(){
		
		$this->view->render('placeholder');
	}
}