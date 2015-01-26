<?php
namespace siwi\modules\user\controllers;

use siwi\library\UserController;
use fayfox\models\tables\Posts;
use fayfox\models\Post;
use fayfox\models\tables\Files;
use fayfox\models\tables\PostFiles;
use fayfox\models\Tag;
use fayfox\models\Category;
use fayfox\core\Sql;
use fayfox\core\Response;
use fayfox\core\Validator;

class WorkController extends UserController{
	private $rules = array(
		array(array('title', 'abstract'), 'string', array('max'=>500)),
		array(array('title', 'cat_id'), 'require'),
		array(array('cat_id', 'file', 'thumbnail'), 'int'),
		array(array('cat_id'), 'exist', array('table'=>'categories', 'field'=>'id')),
		array(array('video'), 'url', true),
	);
	
	public function __construct(){
		parent::__construct();
	
		$this->layout->current_directory = 'work';
	}
	
	public function create(){
		$this->layout->title = '发布作品';
		
		$this->form()->setRules($this->rules);
		if($this->input->post()){
			if($this->form()->check()){
				$abstract = $this->input->post('abstract');
				$content = $this->input->post('content');
				$abstract || $abstract = mb_substr(strip_tags($content), 0, 100);
				$post_id = Posts::model()->insert(array(
					'title'=>$this->input->post('title'),
					'cat_id'=>$this->input->post('cat_id', 'intval'),
					'thumbnail'=>$this->input->post('thumbnail', 'intval', 0),
					'abstract'=>$abstract,
					'create_time'=>$this->current_time,
					'user_id'=>$this->current_user,
					'publish_time'=>$this->current_time,
					'status'=>Posts::STATUS_PUBLISH,
				));
	
				Post::model()->setPropValueByAlias('siwi_work_video', $this->input->post('video'), $post_id);
				Post::model()->setPropValueByAlias('siwi_work_copyright', $this->input->post('copyright'), $post_id);
				
				if($f = $this->input->post('file', 'intval', 0)){
					$file = Files::model()->find($f, 'client_name,is_image');
					if($file){
						PostFiles::model()->insert(array(
							'file_id'=>$f,
							'post_id'=>$post_id,
							'desc'=>$file['client_name'],
							'is_image'=>$file['is_image'],
							'sort'=>1,
						));
					}
				}
				
				//多张预览图
				$files = $this->input->post('files', 'intval', array());
				$i = 1;
				foreach($files as $f){
					$i++;
					$file = Files::model()->find($f, 'is_image,client_name');
					if(!$file['is_image'])continue;
					PostFiles::model()->insert(array(
						'file_id'=>$f,
						'post_id'=>$post_id,
						'desc'=>$file['client_name'],
						'is_image'=>1,
						'sort'=>$i,
					));
				}
				
				
				Tag::model()->set($this->input->post('tags'), $post_id);
	
				Response::output('success', '作品发布成功', array('user/work/edit', array(
					'id'=>$post_id,
				)));
			}else{
				$this->flash->set('参数异常');
			}
		}
		$this->view->cats = Category::model()->getNextLevel('_work');
		
		$this->view->render();
	}
	
	public function edit(){
		$this->layout->title = '编辑作品';
		
		$id = $this->input->get('id', 'intval');
		if(!$id){
			Response::showError('不完整的请求');
		}
		
		$post = Posts::model()->find($id);
		if(!$post){
			Response::showError('作品编号不存在');
		}
		if($post['user_id'] != $this->current_user){
			Response::showError('您无权限编辑此作品');
		}
		
		$this->form()->setRules($this->rules);
		if($this->input->post()){
			if($this->form()->check()){
				$abstract = $this->input->post('abstract');
				$content = $this->input->post('content');
				$abstract || $abstract = mb_substr(strip_tags($content), 0, 100);
				Posts::model()->update(array(
					'title'=>$this->input->post('title'),
					'cat_id'=>$this->input->post('cat_id', 'intval'),
					'thumbnail'=>$this->input->post('thumbnail', 'intval', 0),
					'abstract'=>$abstract,
					'create_time'=>$this->current_time,
					'user_id'=>$this->current_user,
					'publish_time'=>$this->current_time,
					'status'=>Posts::STATUS_PUBLISH,
				), $id);
				
				Post::model()->setPropValueByAlias('siwi_work_video', $this->input->post('video'), $id);
				Post::model()->setPropValueByAlias('siwi_work_copyright', $this->input->post('copyright'), $id);
				
				$f = $this->input->post('file', 'intval', 0);
				if($f){
					$file = PostFiles::model()->fetchRow(array(
						'post_id = '.$post['id'],
						'is_image = 0',
					), 'file_id');
					if($f != $file['file_id']){
						PostFiles::model()->delete(array(
							'post_id = '.$post['id'],
							'is_image = 0',
						));
						$file = Files::model()->find($f, 'client_name,is_image');
						if($file){
							PostFiles::model()->insert(array(
								'file_id'=>$f,
								'post_id'=>$id,
								'desc'=>$file['client_name'],
								'is_image'=>$file['is_image'],
								'sort'=>1,
							));
						}
					}
				}else{
					PostFiles::model()->delete(array(
						'post_id = '.$post['id'],
						'is_image = 0',
					));
				}
				
				$files = $this->input->post('files', 'intval', array());
				//删除已被删除的图片
				if($files){
					PostFiles::model()->delete(array(
						'post_id = ?'=>$post['id'],
						'file_id NOT IN ('.implode(',', $files).')',
						'is_image = 1',
					));
				}else{
					PostFiles::model()->delete(array(
						'post_id = ?'=>$post['id'],
						'is_image = 1',
					));
				}
				//获取已存在的图片
				$old_files_ids = PostFiles::model()->fetchCol('file_id', array(
					'post_id = ?'=>$post['id'],
					'is_image = 1',
				));
				$i = 1;
				foreach($files as $f){
					$i++;
					if(in_array($f, $old_files_ids)){
						PostFiles::model()->update(array(
							'sort'=>$i,
						), array(
							'post_id = ?'=>$post['id'],
							'file_id = ?'=>$f,
						));
					}else{
						$file = Files::model()->find($f, 'is_image,client_name');
						if(!$file['is_image'])continue;
						PostFiles::model()->insert(array(
							'file_id'=>$f,
							'post_id'=>$post['id'],
							'desc'=>$file['client_name'],
							'sort'=>$i,
							'is_image'=>1,
						));
					}
				}
	
				Tag::model()->set($this->input->post('tags'), $post['id']);
				
				$this->flash->set('作品编辑成功', 'success');
				
				$post = Posts::model()->find($id);
			}else{
				$this->flash->set('参数异常');
			}
		}
		
		$this->form()->setData($post);
		
		//parent cat
		$cat = Category::model()->get($post['cat_id'], 'parent');
		$this->form()->setData(array('parent_cat'=>$cat['parent']));
		
		//tags
		$sql = new Sql();
		$tags = $sql->from('posts_tags', 'pt', '')
			->joinLeft('tags', 't', 'pt.tag_id = t.id', 'title')
			->where('pt.post_id = '.$post['id'])
			->fetchAll();
		$tag_titles = array();
		foreach($tags as $t){
			$tag_titles[] = $t['title'];
		}
		$this->form()->setData(array('tags'=>implode(',', $tag_titles)));
		
		//file
		$file = PostFiles::model()->fetchRow(array(
			'post_id = '.$post['id'],
			'is_image = 0',
		), 'file_id,desc');
		$this->view->file = $file;
		$this->form()->setData(array('file'=>isset($file['file_id']) ? $file['file_id'] : ''));
		
		//files
		$files = PostFiles::model()->fetchAll(array(
			'post_id = '.$post['id'],
			'is_image = 1',
		), 'file_id,desc', 'sort');
		$this->view->files = $files;
		
		//copyright
		$this->form()->setData(array('copyright'=>Post::model()->getPropValueByAlias('siwi_work_copyright', $post['id'])));
		
		//video
		$this->form()->setData(array('video'=>Post::model()->getPropValueByAlias('siwi_work_video', $post['id'])));
		
		$this->view->cats = Category::model()->getNextLevel('_work');
		$this->view->render();
	}
}