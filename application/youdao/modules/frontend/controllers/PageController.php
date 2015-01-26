<?php
namespace youdao\modules\frontend\controllers;

use youdao\library\FrontController;
use fayfox\models\tables\Pages;
use fayfox\core\Response;

class PageController extends FrontController{
	public $layout_template = 'inner';
	
	public function __construct(){
		parent::__construct();
	}
	
	public function item(){
		if($this->input->get('alias')){
			$page = Pages::model()->fetchRow(array('alias = ?'=>$this->input->get('alias')));
		}else if($this->input->get('id')){
			$page = Pages::model()->fetchRow(array('id = ?'=>$this->input->get('id', 'intval')));
		}
		
		if(isset($page) && $page){
			Pages::model()->inc($page['id'], 'views', 1);
			$this->view->page = $page;
		}else{
			Response::showError('您请求的页面不存在', 404, '页面不存在');
		}

		$this->layout->submenu = array(
			array(
				'title'=>'关于有道',
				'link'=>'',
				'class'=>'sel',
			),
			array(
				'title'=>'公司动态',
				'link'=>'',
				'class'=>'',
			),
			array(
				'title'=>'项目动态',
				'link'=>'',
				'class'=>'',
			),
		);
		$this->layout->subtitle = '公司概况';
		$this->layout->breadcrumbs = array(
			array(
				'title'=>'首页',
				'link'=>$this->view->url(),
			),
			array(
				'title'=>'关于有道',
				'link'=>$this->view->url('about'),
			),
			array(
				'title'=>'企业简介',
			),
		);
		$this->layout->banner = $page['alias'].'-banner.jpg';
		$this->layout->current_directory = $page['alias'];
		$this->view->render();
	}
}