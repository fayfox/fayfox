<?php
namespace shinecolor\modules\frontend\controllers;

use shinecolor\library\FrontController;
use fayfox\models\tables\Links;

class ClientController extends FrontController{
	public function __construct(){
		parent::__construct();
	
		$this->layout->title = '';
		$this->layout->keywords = '';
		$this->layout->description = '';
	
		$this->layout->current_header_menu = 'client';
	}
	
	public function index(){
		$this->view->links = Links::model()->fetchAll(array(
			'visiable = 1',
		), '*', 'sort');
		
		$this->layout->breadcrumbs = array(
			array(
				'label'=>'首页',
				'link'=>$this->view->url(),
			),
			array(
				'label'=>'合作客户',
			),
		);
		
		$this->view->render();
	}
}