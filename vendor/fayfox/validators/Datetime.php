<?php
namespace fayfox\validators;

use fayfox\core\Validator;

class Datetime extends Validator{
	public $pattern = '/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$/';
	
	public $message = '{$attribute}日期格式不正确';
	
	/**
	 * 因为datetime类型很有可能先被strtotime过
	 * 用户直接输入数字一定是无效的，因为用户提交数据为string类型
	 */
	public $int = false;
	
	public function validate($value){
		if($this->int){
			if(!is_int($value)){
				return $this->message;
			}
		}else{
			if(!preg_match($this->pattern, $value)){
				return $this->message;
			}
		}
		return true;
	}
}