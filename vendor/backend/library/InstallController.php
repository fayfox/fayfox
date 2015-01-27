<?php
namespace backend\library;

use fayfox\core\Controller;

class InstallController extends Controller{
	public function __construct(){
		parent::__construct();
		
		$this->layout_template = 'default';
	}
}