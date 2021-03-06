<?php
namespace cms\modules\admin\controllers;

use cms\library\AdminController;
use fay\core\Sql;
use fay\common\ListView;
use fay\models\tables\Roles;
use fay\models\tables\RoleActions;
use fay\models\tables\Actionlogs;
use fay\core\Response;
use fay\helpers\Html;

class RoleController extends AdminController{
	public function __construct(){
		parent::__construct();
		$this->layout->current_directory = 'role';
	}
	
	public function index(){
		$this->layout->subtitle = '角色';
		
		$sql = new Sql();
		$sql->from('roles', 'r')
			->where(array(
				'deleted = 0',
			))
			->order('id DESC');
		$listview = new ListView($sql);
		$listview->pageSize = 15;
		$this->view->listview = $listview;
		$this->view->render();
	}
	
	public function create(){
		$this->layout->subtitle = '添加角色';
		
		$this->form()->setModel(Roles::model());
		if($this->input->post()){
			if($this->form()->check()){
				$role_id = Roles::model()->insert($this->form()->getFilteredData());
				$actions = $this->input->post('actions', 'intval', array());
				foreach($actions as $a){
					RoleActions::model()->insert(array(
						'role_id'=>$role_id,
						'action_id'=>$a,
					));
				}
				$this->actionlog(Actionlogs::TYPE_ROLE, '添加了一个角色', $role_id);
				Response::output('success', '角色添加成功', array('admin/role/edit', array(
					'id'=>$role_id,
				)));
			}else{
				$this->showDataCheckError($this->form()->getErrors());
			}
		}
		$sql = new Sql();
		$actions = $sql->from('actions', 'a')
			->joinLeft('categories', 'c', 'a.cat_id = c.id', 'title AS cat_title')
			->fetchAll();
		
		$actions_group = array();
		foreach($actions as $a){
			$actions_group[$a['cat_title']][] = $a;
		}
		$this->view->actions = $actions_group;
		
		$this->view->render();
	}
	
	public function edit(){
		$this->layout->subtitle = '编辑角色';
		$this->layout->sublink = array(
			'uri'=>array('admin/role/create'),
			'text'=>'添加角色',
		);
		$role_id = $this->input->get('id', 'intval');
		
		$this->form()->setModel(Roles::model());
		if($this->input->post()){
			if($this->form()->check()){
				Roles::model()->update($this->form()->getFilteredData(), $role_id, true);
				
				$actions = $this->input->post('actions', 'intval', array(0));
				RoleActions::model()->delete(array(
					'role_id = ?'=>$role_id,
					'action_id NOT IN (?)'=>$actions,
				));
				$old_actions = RoleActions::model()->fetchCol('action_id', array(
					'role_id = ?'=>$role_id,
				));
				
				foreach($actions as $a){
					if(!in_array($a, $old_actions)){
						RoleActions::model()->insert(array(
							'role_id'=>$role_id,
							'action_id'=>$a,
						));
					}
				}

				$this->actionlog(Actionlogs::TYPE_ROLE, '编辑了一个角色', $role_id);
				$this->flash->set('一个角色被编辑', 'success');
			}else{
				$this->showDataCheckError($this->form()->getErrors());
			}
		}
		$role = Roles::model()->find($role_id);
		$this->form()->setData($role);
		
		$this->form()->setData(array(
			'actions'=>RoleActions::model()->fetchCol('action_id', array('role_id = ?'=>$role_id)),
		));
		
		$sql = new Sql();
		$actions = $sql->from('actions', 'a')
			->joinLeft('categories', 'c', 'a.cat_id = c.id', 'title AS cat_title')
			->fetchAll();
		
		$actions_group = array();
		foreach($actions as $a){
			$actions_group[$a['cat_title']][] = $a;
		}
		$this->view->actions = $actions_group;
		
		$this->view->render();
	}
	
	public function delete(){
		$role_id = $this->input->get('id', 'intval');
		Roles::model()->update(array(
			'deleted'=>1,
		), $role_id);
		$this->actionlog(Actionlogs::TYPE_ROLE, '删除了一个角色', $role_id);

		Response::output('success', array(
			'message'=>'一个角色被删除 - '.Html::link('撤销', array('admin/role/undelete', array(
				'id'=>$role_id,
			))),
			'id'=>$role_id,
		));
	}
	
	public function undelete(){
		$role_id = $this->input->get('id', 'intval');
		Roles::model()->update(array(
			'deleted'=>0,
		), $role_id);
		$this->actionlog(Actionlogs::TYPE_ROLE, '还原了一个角色', $role_id);

		Response::output('success', array(
			'message'=>'一个角色被还原',
			'id'=>$role_id,
		));
	}
	
	public function isTitleNotExist(){
		if(Roles::model()->fetchRow(array(
			'title = ?'=>$value = $this->input->post('value', 'trim'),
			'id != ?'=>$this->input->get('id', 'intval', 0),
		))){
			echo json_encode(array(
				'status'=>0,
				'message'=>'角色已存在',
			));
		}else{
			echo json_encode(array(
				'status'=>1,
			));
		}
	}
}