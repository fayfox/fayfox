<?php
namespace cms\widgets\change_app\controllers;

use fay\core\Widget;
use fay\models\File;
use fay\core\Response;

class IndexController extends Widget{
	
	public function index($options){
		$apps = File::getFileList(APPLICATION_PATH.'..');
		$options = array();
		foreach($apps as $app){
			$options[$app['name']] = $app['name'];
		}
		$this->view->options = $options;
		$this->view->render();
	}
	
	public function change(){
		if($this->input->post('app')){
			$_SESSION['__app'] = $this->input->post('app');
			Response::redirect('admin/index/index');
		}
	}
}