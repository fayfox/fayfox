<?php
namespace fay\models;

use fay\core\Model;
use fay\models\tables\Files;
use fay\core\Loader;

class Qiniu extends Model{
	/**
	 * @return Qiniu
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	/**
	 * 根据本地文件ID，将本地文件上传至七牛云空间
	 * @param int $file_id 本地文件ID
	 */
	public function put($file){
		if(is_numeric($file)){
			$file = Files::model()->find($file);
		}
		
		Loader::vendor('qiniu/io');
		Loader::vendor('qiniu/rs');
		
		$qiniu = $this->config('*', 'qiniu');
		
		Qiniu_SetKeys($qiniu['accessKey'], $qiniu['secretKey']);
		$putPolicy = new \Qiniu_RS_PutPolicy($qiniu['bucket']);
		$upToken = $putPolicy->Token(null);
		$putExtra = new \Qiniu_PutExtra();
		$putExtra->Crc32 = 1;
		list($ret, $err) = Qiniu_PutFile($upToken, $this->getKey($file), File::model()->getPath($file), $putExtra);
		
		if($err !== null){
			return array(
				'status'=>0,
				'message'=>$err,
			);
		}else{
			Files::model()->update(array(
				'qiniu'=>1,
			), $file['id']);
			return array(
				'status'=>1,
				'data'=>$ret,
			);
		}
	}
	
	/**
	 * 根据本地文件ID，删除对应七牛空间的文件
	 * @param int $file_id 本地文件ID
	 */
	public function delete($file){
		if(is_numeric($file)){
			$file = Files::model()->find($file, 'id,raw_name,file_ext,file_path');
		}
		
		Loader::vendor('qiniu/rs');
		
		$qiniu = $this->config('*', 'qiniu');
		
		Qiniu_SetKeys($qiniu['accessKey'], $qiniu['secretKey']);
		$client = new \Qiniu_MacHttpClient(null);
		
		$err = Qiniu_RS_Delete($client, $qiniu['bucket'], $this->getKey($file));
		
		//一般来说，出错是因为远程文件已被删除，所以此处直接将本地标记为未上传
		Files::model()->update(array(
			'qiniu'=>0,
		), $file['id']);
		
		if($err !== null){
			return $err;
		}else{
			return true;
		}
	}
	
	/**
	 * 根据本地文件信息，获取七牛对应的文件路径
	 * 若文件未被上传，返回false
	 * 若传入宽高参数，则会调用七牛相应接口进行处理
	 * 
	 * @param $file 若为数字，视为files表ID；若为数组，直接使用
	 * @param $options 包含宽高参数，若文件非图片，宽高参数无效
	 */
	public function getUrl($file, $options = array()){
		if(is_numeric($file)){
			$file = Files::model()->find($file, 'raw_name,file_ext,file_path,is_image,qiniu');
		}
		
		if(!$file['qiniu']){
			return '';
		}
		$domain = \F::app()->config->get('domain', 'qiniu');
		$domain || $domain = 'http://'.\F::app()->config->get('bucket', 'qiniu').'.qiniudn.com/';
		$src = $domain . $this->getKey($file);
		
		if($file['is_image'] && (isset($options['dw']) || isset($options['dh']))){
			if(!empty($options['dw']) && !empty($options['dh'])){
				$src .= '?imageView2/1';//裁剪
			}else{
				$src .= '?imageView2/0';//等比缩放
			}
			if(!empty($options['dw'])){
				$src .= '/w/'.$options['dw'];
			}
			if(!empty($options['dh'])){
				$src .= '/h/'.$options['dh'];
			}
		}
		
		return $src;
	}
	
	/**
	 * 获取一个七牛上的key
	 * @param array $file 必须包含file_path, raw_name, file_ext三项
	 * @return string
	 */
	private function getKey($file){
		if(substr($file['file_path'], 0, 4) == './..'){
			return 'pri-'.str_replace('/', '-', substr($file['file_path'], strpos($file['file_path'], '/', 3)+1)).$file['raw_name'].$file['file_ext'];
		}else{
			return str_replace('/', '-', substr($file['file_path'], strpos($file['file_path'], '/', 2)+1)).$file['raw_name'].$file['file_ext'];
		}
	}
}