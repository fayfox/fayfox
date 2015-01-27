<?php
namespace fayfox\validators;

use fayfox\core\Validator;
use fayfox\core\Sql;

/**
 * 该验证器必须传入table, field参数
 * additional可选
 */
class Unique extends Validator{
	public $message = '{$attribute}已存在';
	
	public $table;
	
	public $field;
	
	/**
	 * 若设置了此参数，且传入该参数，则会在where条件中添加不等于该参数值的条件。
	 * 这需要传参和和数据库字段同名
	 */
	public $except;
	
	/**
	 * 附加条件，
	 * 若except字段不够用，则可以用此方法传入更复杂的条件
	 */
	public $additional = array();
	
	public function validate($value){
		$field = $this->field ? $this->field : $this->_field;
		
		$sql = new Sql();
		$sql->from($this->table, $field)
			->where(array(
				"`{$field}` = ?"=>$value,
			) + $this->additional);
		if($this->except && \F::app()->input->request($this->except)){
			$sql->where(array("{$this->except} != ?"=>\F::app()->input->request($this->except)));
		}
		$result = $sql->fetchRow();
		if($result){
			return $this->message;
		}else{
			return true;
		}
	}
}