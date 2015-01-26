<?php
namespace jxsj\modules\frontend\controllers;

use jxsj\library\FrontController;
use fayfox\models\Page;
use fayfox\models\tables\Pages;
use fayfox\core\Response;

class PageController extends FrontController{
	public function item(){
		$page = Page::model()->get($this->input->get('id', 'intval'));
		
		if(!$page){
			Response::showError('您访问的页面不存在', 404, '404');
		}
		//阅读数
		Pages::model()->inc($page['id'], 'views', 1);
		
		//seo
		$this->layout->title = $page['seo_title'];
		$this->layout->keywords = $page['seo_keywords'];
		$this->layout->description = $page['seo_description'];
		
		$this->view->page = $page;
		$this->view->render();
	}
	
}









