<?php
namespace fayfox\core;

use fayfox\core\FBase;

class Plugin extends FBase{
	public function load($name){
		$this->loadPlugin($name);
	}
}