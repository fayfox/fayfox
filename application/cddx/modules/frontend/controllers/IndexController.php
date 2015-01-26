<?php
namespace cddx\modules\frontend\controllers;

use cddx\library\FrontController;
use fayfox\models\Page;
use fayfox\models\Category;
use fayfox\models\Post;

class IndexController extends FrontController{
	public function __construct(){
		parent::__construct();
		
		$this->layout->title = '';
		$this->layout->keywords = '';
		$this->layout->description = '';
		
		$this->layout->current_header_menu = 'home';
	}
	
	public function index(){
		$page_about = Page::model()->getByAlias('about');
		$cat_news = Category::model()->getByAlias('news');
		$news = Post::model()->getByCat($cat_news, 7, 'id,title,abstract,publish_time', true);
		
		$this->view->assign(array(
			'about'=>$page_about,
			'cat_news'=>$cat_news,
			'news'=>$news,
		))->render();
	}
	
}