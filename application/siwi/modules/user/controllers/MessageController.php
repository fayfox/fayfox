<?php
namespace siwi\modules\user\controllers;

use siwi\library\UserController;
use fayfox\models\tables\Messages;
use fayfox\models\Message;
use fayfox\helpers\Date;
use fayfox\core\Validator;

class MessageController extends UserController{
	public function create(){
		$rules = array(
			array(array('target', 'content'), 'require'),
			array(array('target', 'parent'), 'int'),
		);
		$validator = new Validator();
		$check = $validator->check($this->rules);
		
		if($check === true){
			//插入留言
			$target = $this->input->post('target', 'intval');
			$content = $this->input->post('content');
			$type = Messages::TYPE_USER_MESSAGE;
			$parent = $this->input->post('parent', 'intval', 0);
			$message_id = Message::model()->create($target, $content, $type, $parent);
			
			$message = Message::model()->get($message_id);
			$message['date'] = Date::niceShort($message['create_time']);
			if($this->input->isAjaxRequest()){
				echo json_encode(array('status'=>1, 'data'=>$message));
			}
		}else{
			if($this->input->isAjaxRequest()){
				echo json_encode(array('status'=>0, 'message'=>'参数异常'));
			}
		}
	}
}