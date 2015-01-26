<?php
namespace siwi\hooks;

use fayfox\core\FBase;

class AdminMenu extends FBase{
	public function run(){
		if(method_exists(\F::app(), 'removeMenuTeam')){
			\F::app()->removeMenuTeam('exam-question');
			\F::app()->removeMenuTeam('exam-paper');
		}
	}
}