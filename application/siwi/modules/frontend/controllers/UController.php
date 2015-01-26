<?php
namespace siwi\modules\frontend\controllers;

use siwi\library\FrontController;
use fayfox\models\tables\Users;
use fayfox\core\Sql;
use fayfox\models\Category;
use fayfox\models\tables\Posts;
use fayfox\models\tables\Messages;
use fayfox\common\ListView;
use fayfox\models\tables\Followers;
use fayfox\models\User;

class UController extends FrontController{
	/**
	 * 当前显示的用户，非当前登录用户
	 */
	public $user_id;
	
	public function __construct(){
		parent::__construct();
		$this->layout_template = 'home';
		
		$this->layout->title = '';
		$this->layout->keywords = '';
		$this->layout->description = '';
		
		$this->layout->current_directory = 'home';
		
		$this->user_id = $this->input->get('id', 'intval');
		$this->layout->user = Users::model()->find($this->user_id, 'avatar,nickname');
		
		if(Followers::model()->fetchRow(array(
			'follower = '.$this->current_user,
			'user_id'=>$this->user_id,
		))){
			$this->layout->is_follow = true;
		}else{
			$this->layout->is_follow = false;
		}

		$this->layout->popularity = intval(User::model()->getPropValueByAlias('popularity', $this->user_id));
		$this->layout->creativity = intval(User::model()->getPropValueByAlias('creativity', $this->user_id));
		$this->layout->fans = intval(User::model()->getPropValueByAlias('fans', $this->user_id));
		$this->layout->follow = intval(User::model()->getPropValueByAlias('follow', $this->user_id));
	}
	
	public function index(){
		
		$sql = new Sql();
		
		//素材
		$cat_work = Category::model()->getByAlias('_material', 'left_value,right_value');
		$this->view->works = $sql->from('posts', 'p', 'id,title,abstract,publish_time,thumbnail,comments,user_id,cat_id')
			->joinLeft('users', 'u', 'p.user_id = u.id', 'nickname')
			->joinLeft('categories', 'c', 'p.cat_id = c.id', 'title AS cat_title, parent AS parent_cat_id')
			->joinLeft('categories', 'pc', 'c.parent = pc.id', 'title AS parent_cat_title')
			->order('is_top DESC, p.sort, publish_time DESC')
			->where(array(
				'p.user_id = ?'=>$this->user_id,
				'c.left_value > '.$cat_work['left_value'],
				'c.right_value < '.$cat_work['right_value'],
				'p.deleted = 0',
				'p.status = '.Posts::STATUS_PUBLISH,
				'p.publish_time < '.$this->current_time,
			))
			->fetchAll()
		;
		
		//博文
		$cat_blog = Category::model()->getByAlias('_blog', 'left_value,right_value');
		$this->view->posts = $sql->from('posts', 'p', 'id,title,abstract,publish_time,thumbnail,comments,user_id')
			->joinLeft('users', 'u', 'p.user_id = u.id', 'nickname')
			->joinLeft('categories', 'c', 'p.cat_id = c.id')
			->order('is_top DESC, p.sort, publish_time DESC')
			->where(array(
				'p.user_id = ?'=>$this->user_id,
				'c.left_value > '.$cat_blog['left_value'],
				'c.right_value < '.$cat_blog['right_value'],
				'p.deleted = 0',
				'p.status = '.Posts::STATUS_PUBLISH,
				'p.publish_time < '.$this->current_time,
			))
			->fetchAll()
		;
		
		//留言
		$sql->from('messages', 'm')
			->joinLeft('users', 'u', 'm.user_id = u.id', 'nickname,avatar')
			->where(array(
				'm.target = '.$this->user_id,
				'm.parent = 0',
				'm.type = '.Messages::TYPE_USER_MESSAGE,
				'm.status = '.Messages::STATUS_APPROVED,
				'm.deleted = 0',
			))
			->order('id DESC');
		$this->view->listview = new ListView($sql, array(
			'itemView'=>'_message_list_item',
		));
		$this->view->render();
	}
	
}