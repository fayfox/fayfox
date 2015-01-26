<?php
namespace jxsj\modules\frontend\controllers;

use jxsj\library\FrontController;
use fayfox\models\Post;
use fayfox\models\tables\Posts;
use fayfox\core\Response;

class PostController extends FrontController{
	public function item(){
		$post = Post::model()->get($this->input->get('id', 'intval'), 'nav,files');
		
		if(!$post){
			Response::showError('您访问的页面不存在', 404, '404');
		}
		//阅读数
		Posts::model()->inc($post['id'], 'views', 1);
		
		//seo
		$this->layout->title = $post['seo_title'];
		$this->layout->keywords = $post['seo_keywords'];
		$this->layout->description = $post['seo_description'];
		
		$this->view->post = $post;
		$this->view->render();
	}
	
}









