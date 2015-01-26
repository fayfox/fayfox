<?php
namespace siwi\hooks;

use fayfox\core\FBase;

class HideBoxes extends FBase{
	public function run(){
		//移除分类选择，该系统不需要多分类体系
		\F::app()->removeBox('category');
	}
}