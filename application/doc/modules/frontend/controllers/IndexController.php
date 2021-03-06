<?php
namespace doc\modules\frontend\controllers;

use doc\library\FrontController;
use fay\core\Sql;
use fay\models\Post;
use fay\models\Option;

class IndexController extends FrontController{
	public function index(){
		$this->layout->title = Option::get('sitename');
		$this->layout->page_title = Option::get('sitename');
		
		$sql = new Sql();
		$sql->from('posts', 'p', 'cat_id')
			->joinLeft('categories', 'c', 'p.cat_id = c.id', 'alias,title,description')
			->order('last_modified_time DESC')
			->limit(20)
			->group('p.cat_id')
		;
		$this->view->last_modified_cats = $sql->fetchAll();
		
		$this->view->assign(array(
			'posts'=>Post::model()->getByCatAlias('fayfox', 0, 'id,title,content,content_type', false, 'is_top DESC, sort, publish_time ASC'),
		))->render();
	}
	
}