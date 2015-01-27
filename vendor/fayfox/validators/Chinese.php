<?php
namespace fayfox\validators;

use fayfox\core\Validator;

class Chinese extends Validator{
	public $pattern = '/^[\x{4e00}-\x{9fa5}]+$/u';//PHP中的中文正则跟js里写法不一样
	
	public $message = '{$attribute}必须是中文';
	
	public function validate($value){
		if(preg_match($this->pattern, $value)){
			return true;
		}else{
			return $this->message;
		}
	}
}