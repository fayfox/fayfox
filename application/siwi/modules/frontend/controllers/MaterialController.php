<?php
namespace siwi\modules\frontend\controllers;

use siwi\library\FrontController;
use fayfox\models\Category;
use fayfox\core\Sql;
use fayfox\models\tables\Posts;
use fayfox\common\ListView;
use fayfox\models\Post;
use fayfox\models\tables\Messages;
use fayfox\core\Response;
use siwi\helpers\FriendlyLink;

class MaterialController extends FrontController{
	public function __construct(){
		parent::__construct();
	
		$this->layout->title = '';
		$this->layout->keywords = '';
		$this->layout->description = '';
	
		$this->layout->current_header_menu = 'material';
	}
	
	public function index(){
		$params = FriendlyLink::getParams();
		$this->view->params = $params;
		if($params['cat_2']){
			$cat = Category::model()->get($params['cat_2']);
		}else if($params['cat_1']){
			$cat = Category::model()->get($params['cat_1']);
		}else{
			$cat = Category::model()->getByAlias('_material', '*');
		}
		$this->view->cat = $cat;
		$this->layout->title = $cat['seo_title'];
		$this->layout->keywords = $cat['seo_keywords'];
		$this->layout->description = $cat['seo_description'];
		
		$sql = new Sql();
		$sql->from('posts', 'p', 'id,title,abstract,publish_time,thumbnail,comments,user_id,cat_id')
			->joinLeft('users', 'u', 'p.user_id = u.id', 'nickname')
			->joinLeft('categories', 'c', 'p.cat_id = c.id', 'title AS cat_title, parent AS parent_cat_id')
			->joinLeft('categories', 'pc', 'c.parent = pc.id', 'title AS parent_cat_title')
			->order('is_top DESC, p.sort, p.publish_time DESC')
			->where(array(
				'c.left_value >= '.$cat['left_value'],
				'c.right_value <= '.$cat['right_value'],
				'p.deleted = 0',
				'p.status = '.Posts::STATUS_PUBLISH,
				'p.publish_time < '.$this->current_time,
			))
		;
		
		if($params['time']){
			$sql->where('p.publish_time > ' . ($this->current_time - 86400*$params['time']));
		}
		switch($params['time']){
			case 0:
				$this->view->time = '最新发表';
				break;
			case 3:
				$this->view->time = '三天内';
				break;
			case 7:
				$this->view->time = '一周内';
				break;
			case 30:
				$this->view->time = '一个月内';
				break;
			case 365:
				$this->view->time = '一年内';
				break;
		}
		
		$this->view->listview = new ListView($sql, array(
			'reload'=>$this->view->url('material'),
			'pageSize'=>2,
		));
		
		$this->view->cat_tree = Category::model()->getTree('_material');
	
		$this->view->render();
	}
	
	public function item(){
		$id = $this->input->get('id', 'intval');
		
		$post = Post::model()->get($id, 'nav,user');
		
		if(!$post){
			Response::showError('您访问的页面不存在', 404, '404');
		}
		Posts::model()->inc($post['id'], 'views', 1);//阅读数
		$this->view->post = $post;
		
		$this->layout->title = $post['seo_title'];
		$this->layout->keywords = $post['seo_keywords'];
		$this->layout->description = $post['seo_description'];
		
		$sql = new Sql();
		$sql->from('messages', 'm')
			->joinLeft('users', 'u', 'm.user_id = u.id', 'username,nickname,avatar')
			->joinLeft('messages', 'm2', 'm.parent = m2.id', 'content AS parent_content,user_id AS parent_user_id')
			->joinLeft('users', 'u2', 'm2.user_id = u2.id', 'nickname AS parent_nickname')
			->where(array(
				"m.target = {$id}",
				'm.type = '.Messages::TYPE_POST_COMMENT,
				'm.deleted = 0',
				'm.status = '.Messages::STATUS_APPROVED,
			))
			->order('create_time DESC');
		
		if(Post::model()->isLiked($id)){
			$this->view->liked = true;
		}else{
			$this->view->liked = false;
		}
		
		if(Post::model()->isFavored($id)){
			$this->view->favored = true;
		}else{
			$this->view->favored = false;
		}
		
		$this->view->listview = new ListView($sql, array(
			'reload'=>$this->view->url('material/'.$id),
			'itemView'=>'_comment_list_item',
			'pageSize'=>10,
		));
		
		$this->layout->canonical = $this->view->url('material/'.$post['id']);
		$this->view->render();
	}
}