<?php
namespace fay\models\tables;

use fay\core\db\Table;

class RoleActions extends Table{
	protected $_name = 'role_actions';
	
	/**
	 * @return RoleActions
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	
	public function rules(){
		return array(
			array(array('id'), 'int', array('min'=>0, 'max'=>16777215)),
			array(array('role_id', 'action_id'), 'int', array('min'=>0, 'max'=>65535)),
		);
	}

	public function labels(){
		return array(
			'id'=>'Id',
			'role_id'=>'Role Id',
			'action_id'=>'Action Id',
		);
	}

	public function filters(){
		return array(
			'role_id'=>'intval',
			'action_id'=>'intval',
		);
	}
}