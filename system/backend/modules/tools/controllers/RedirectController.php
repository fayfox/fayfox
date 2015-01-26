<?php
namespace backend\modules\tools\controllers;

use backend\library\ToolsController;
use fayfox\core\Response;

class RedirectController extends ToolsController{
	/**
	 * 跳转到指定url
	 */
	public function index(){
		$url = base64_decode($this->input->get('url', 'trim'));
		if($this->form()->setRules(array(
			array('url', 'required'),
			array('url', 'url'),
		))->setFilters(array(
			'url'=>'trim|base64_decode',
		))->check()){
			header('location:'.$this->form()->getData('url'));
			die;
		}else{
			Response::showError('您访问的页面不存在', 404, '404');
		}
	}
}