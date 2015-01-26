<?php
namespace blog\modules\frontend\controllers;

use blog\library\FrontController;
use fayfox\models\tables\Pages;
use fayfox\core\Response;
use fayfox\core\Validator;

class PageController extends FrontController{
	public function __construct(){
		parent::__construct();
	
		$this->layout->title = '';
		$this->layout->keywords = '';
		$this->layout->description = '';
	
		$this->layout->current_directory = 'home';
	}
	
	public function item(){
		$validator = new Validator();
		$check = $validator->check(array(
			array(array('alias'), 'required'),
		));
		
		if($check === true){
			$page = Pages::model()->fetchRow(array(
				'alias = ?'=>$this->input->get('alias'),
			));
			if($page){
				$this->view->page = $page;
				$this->layout->title = $page['seo_title'] ? $page['seo_title'] : $page['title'];
				$this->layout->keywords = $page['seo_keywords'];
				$this->layout->description = $page['seo_description'];
				$this->layout->current_directory = $page['alias'];
				$this->view->render();
			}else{
				Response::showError('页面不存在', 404);
			}
		}else{
			Response::showError('异常的请求');
		}
	}
}