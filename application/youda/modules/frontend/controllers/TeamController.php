<?php
namespace youda\modules\frontend\controllers;

use youda\library\FrontController;
use fayfox\models\Category;
use fayfox\core\Sql;
use fayfox\models\tables\Posts;
use fayfox\models\Option;
use fayfox\core\Response;

class TeamController extends FrontController{
	public $layout_template = 'inner';
	
	public function index(){
		$this->layout->subtitle = '团队简介';
		$this->layout->banner = 'team-banner.jpg';
		$this->layout->current_directory = 'team';
		
		//团队
		$cat_team = Category::model()->getByAlias('_youdao_team', '*');
		//SEO
		$this->layout->title = $cat_team['seo_title'];
		$this->layout->keywords = $cat_team['seo_keywords'];
		$this->layout->description = $cat_team['seo_description'];
		
		$this->layout->submenu = array(
			array(
				'title'=>'团队简介',
				'link'=>$this->view->url('team'),
				'class'=>'sel',
			),
		);
		
		$this->layout->breadcrumbs = array(
			array(
				'title'=>'首页',
				'link'=>$this->view->url(),
			),
			array(
				'title'=>'团队介绍',
				'link'=>$this->view->url('team'),
			),
		);

		$sql = new Sql();
		$this->view->team_members = $sql->from('posts', 'p')
			->order('p.is_top DESC, p.sort, p.publish_time DESC')
			->where(array(
				'p.cat_id = '.$cat_team['id'],
				'p.deleted = 0',
				"p.publish_time < {$this->current_time}",
				'p.status = '.Posts::STATUS_PUBLISH,
			))
			->fetchAll();
		;
	
		$this->view->render();
	}
	
	public function item(){
		if($this->input->get('id')){
			$member = Posts::model()->find($this->input->get('id', 'intval'));
			if($member){
				$this->view->member = $member;
				
				//SEO
				$this->layout->title = $member['seo_title'] ? $member['seo_title'] : $member['title'] . ' | ' . Option::get('seo_team_title');
				$this->layout->keywords = $member['seo_keywords'] ? $member['seo_keywords'] : $member['title'];
				$this->layout->description = $member['seo_description'] ? $member['seo_description'] : $member['abstract'];
			}else{
				Response::showError('您请求的页面不存在', 404, '页面不存在');
			}
		}else{
			Response::showError('您请求的页面不存在', 404, '页面不存在');
		}
		$this->layout->submenu = array(
			array(
				'title'=>'团队简介',
				'link'=>$this->view->url('team'),
				'class'=>'sel',
			),
		);
		$this->layout->subtitle = '团队简介';
		$this->layout->breadcrumbs = array(
			array(
				'title'=>'首页',
				'link'=>$this->view->url(),
			),
			array(
				'title'=>'团队介绍',
				'link'=>$this->view->url('team'),
			),
			array(
				'title'=>$member['title'],
			),
		);
		$this->layout->current_directory = 'team';
		
		$this->view->render();
	}
}