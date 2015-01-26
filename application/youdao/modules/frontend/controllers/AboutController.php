<?php
namespace youdao\modules\frontend\controllers;

use youdao\library\FrontController;
use fayfox\models\tables\Pages;
use fayfox\models\Option;
use fayfox\core\Response;

class AboutController extends FrontController{
	public $layout_template = 'inner';
	
	public function index(){
		$page = Pages::model()->fetchRow(array('alias = ?'=>$this->input->get('alias', 'about')));
		if(!$page){
			Response::showError('您请求的页面不存在', 404, '页面不存在');
		}
		$this->view->page = $page;
		//SEO
		$this->layout->title = $page['seo_title'] ? $page['seo_title'] : $page['title'] . ' | ' . Option::get('seo_index_title');
		$this->layout->keywords = $page['seo_keywords'] ? $page['seo_keywords'] : $page['title'];
		$this->layout->description = $page['seo_description'] ? $page['seo_description'] : $page['abstract'];
		
		Pages::model()->inc($page['id'], 'views', 1);
		
		$this->layout->banner = 'about-banner.jpg';
		$this->layout->current_directory = 'about';
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
				'title'=>$page['title'],
			),
		);
		$this->layout->submenu = array(
			array(
				'title'=>'关于有道',
				'link'=>$this->view->url('about'),
				'class'=>'sel',
			),
		);
		$this->layout->subtitle = $page['title'];
		
		$this->view->render();
	}
	
	public function abstractModel(){
		
	}
	
	public function culture(){
		
	}
}