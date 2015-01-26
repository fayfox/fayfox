<?php
namespace siwi\hooks;

use fayfox\core\FBase;

class Dashboard extends FBase{
	public function run(){
		//操作dashboard，测试用途
		\F::app()->addBox(array(
			'name'=>'test',
			'title'=>'第三方widget测试',
		));
	}
}