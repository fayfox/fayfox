<?php
namespace backend\modules\tools\controllers;

use backend\library\ToolsController;
use fayfox\helpers\String;
use fayfox\core\Response;
use fayfox\models\tables\Widgets;

class WidgetController extends ToolsController{
	//加载一个widget
	public function render(){
		if($this->input->get('name')){
			$widget_obj = $this->widget->get($this->input->get('name', 'trim'));
			if($widget_obj == null){
				if($this->input->isAjaxRequest()){
					echo json_encode(array(
						'status'=>0,
						'message'=>'Widget不存在或已被删除',
					));
					die;
				}else{
					Response::showError('Widget不存在或已被删除', 404);
				}
			}
			$action = String::hyphen2case($this->input->get('action', 'trim', 'index'), false);
			if(method_exists($widget_obj, $action)){
				$widget_obj->{$action}($this->input->get());
			}else if(method_exists($widget_obj, $action.'Action')){
				$widget_obj->{$action.'Action'}($this->input->get());
			}else{
				if($this->input->isAjaxRequest()){
					echo json_encode(array(
						'status'=>0,
						'message'=>'Widget方法不存在',
					));
				}else{
					Response::showError('Widget方法不存在', 404);
				}
			}
		}else{
			if($this->input->isAjaxRequest()){
				echo json_encode(array(
					'status'=>0,
					'message'=>'不完整的请求',
				));
			}else{
				Response::showError('不完整的请求');
			}
		}
	}
	
	public function load(){
		if($alias = $this->input->get('alias')){
			$widget_config = Widgets::model()->fetchRow(array(
				'alias = ?'=>$alias,
			));
			if($widget_config['enabled']){
				$widget_obj = $this->widget->get($widget_config['widget_name']);
				if($widget_obj == null){
					if($this->input->isAjaxRequest()){
						echo json_encode(array(
							'status'=>0,
							'message'=>'Widget不存在或已被删除',
						));
						die;
					}else{
						Response::showError('Widget不存在或已被删除', 404);
					}
				}
				$widget_obj->index(json_decode($widget_config['options'], true));
			}
		}else{
			if($this->input->isAjaxRequest()){
				echo json_encode(array(
					'status'=>0,
					'message'=>'不完整的请求',
				));
			}else{
				Response::showError('不完整的请求');
			}
		}
	}
}