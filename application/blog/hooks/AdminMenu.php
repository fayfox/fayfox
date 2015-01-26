<?php
namespace blog\hooks;

use fayfox\core\FBase;

class AdminMenu extends FBase{
	public function run(){
		if(method_exists(\F::app(), 'addMenuTeam')){
			\F::app()->addMenuTeam(array(
				'label'=>'记账',
				'directory'=>'bill',
				'sub'=>array(
					array('label'=>'账单','router'=>'admin/bill/index',),
					array('label'=>'分类','router'=>'admin/bill/cat',),
				),
				'icon'=>'icon-rmb',
			));
		}
	}
}