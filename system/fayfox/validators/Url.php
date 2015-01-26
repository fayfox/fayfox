<?php
namespace fayfox\validators;

use fayfox\core\Validator;

class Url extends Validator{
	public $pattern = '/^(http|https):\/\/\w+.*$/';
	
	public $message = '{$attribute}格式不正确';
	
	public function validate($value){
		if(preg_match($this->pattern, $value)){
			return true;
		}else{
			return $this->message;
		}
	}
}