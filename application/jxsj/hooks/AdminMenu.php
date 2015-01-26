<?php
namespace jxsj\hooks;

use fayfox\core\FBase;

class AdminMenu extends FBase{
	public function run(){
		if(method_exists(\F::app(), 'removeMenuTeam')){
			\F::app()->removeMenuTeam('goods');
			\F::app()->removeMenuTeam('voucher');
			\F::app()->removeMenuTeam('notification');
			\F::app()->removeMenuTeam('bill');
			\F::app()->removeMenuTeam('menu');
			\F::app()->removeMenuTeam('template');
		}
	}
}