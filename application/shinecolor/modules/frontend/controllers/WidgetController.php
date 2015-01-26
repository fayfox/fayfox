<?php
namespace shinecolor\modules\frontend\controllers;

use shinecolor\library\FrontController;
use fayfox\core\Response;

class WidgetController extends FrontController{
	//加载一个widget
	public function load(){
		if($this->input->get('name')){
			$this->widget->get($this->input->get('name', 'trim'))->{$this->input->get('action', 'trim', 'index')}($this->input->get());
		}else{
			Response::showError('不完整的请求');
		}
	}
}