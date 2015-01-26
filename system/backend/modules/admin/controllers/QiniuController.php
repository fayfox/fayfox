<?php
namespace backend\modules\admin\controllers;

use backend\library\AdminController;
use fayfox\models\Qiniu;
use fayfox\core\Response;

class QiniuController extends AdminController{
	public function put(){
		$result = Qiniu::model()->put($this->input->get('id', 'intval'));
		
		if($result['status']){
			Response::output('success', array(
				'message'=>'文件已被上传至七牛',
				'data'=>$result['data'],
			));
		}else{
			Response::output('error', array(
				'message'=>'上传七牛出错'.$result['message']->Err,
			));
		}
	}
	
	public function delete(){
		$result = Qiniu::model()->delete($this->input->get('id', 'intval'));
		
		if($result !== true){
			Response::output('error', array(
				'message'=>'从七牛删除文件出错'.$result->Err,
			));
		}else{
			Response::output('success', array(
				'message'=>'文件从七牛删除',
			));
		}
	}
}