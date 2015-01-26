<?php
namespace cddx\hooks;

use fayfox\core\FBase;

class HideBoxes extends FBase{
	public function run(){
		\F::app()->removeBox('alias');
		\F::app()->removeBox('tags');
		\F::app()->removeBox('keywords');
		\F::app()->removeBox('category');
		\F::app()->removeBox('main-category');
		\F::app()->removeBox('likes');
	}
}