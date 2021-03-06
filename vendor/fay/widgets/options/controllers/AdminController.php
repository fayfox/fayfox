<?php
namespace fay\widgets\options\controllers;

use fay\core\Widget;

class AdminController extends Widget{
	
	public $title = '属性集';
	public $author = 'fayfox';
	public $author_link = 'http://www.fayfox.com';
	public $description = '以键值对的方式，存放任意对属性，并通过设定的模板进行渲染。';
	
	public function index($data){
		$this->view->data = $data;
		$this->view->render();
	}
	
	public function onPost(){
		$keys = $this->input->post('keys', null, array());
		$values = $this->input->post('values');
		$data = array(
			'data'=>array(),
			'template'=>$this->input->post('template'),
		);
		
		foreach($keys as $i=>$k){
			$data['data'][] = array(
				'key'=>$k,
				'value'=>$values[$i],
			);
		}
		$this->saveData($data);
		$this->flash->set('编辑成功', 'success');
	}
}