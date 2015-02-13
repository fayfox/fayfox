<?php
namespace doc\modules\frontend\controllers;

use doc\library\FrontController;
use fayfox\models\Option;

class IndexController extends FrontController{
	public function __construct(){
		parent::__construct();
		
		$this->layout->title = Option::get('seo_index_keywords');
		$this->layout->keywords = Option::get('seo_index_keywords');
		$this->layout->description = Option::get('seo_index_description');
		
		$this->layout->current_directory = 'home';
		
		$this->config->set('debug', true);
	}
	
	public function index(){
		$this->view->render();
	}
	
}