<?php
namespace fay\models;

use fay\core\Model;
use fay\models\tables\Users;
use fay\models\tables\Notifications;
use fay\models\tables\UserNotifications;

class Notification extends Model{
	/**
	 * @return Notification
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	
	/**
	 * 发送一条notification
	 * @param int|array $to
	 * @param string $content
	 * @param int $from
	 * @param int|null $publish_time
	 * @return 消息id
	 */
	public function send($to, $content, $from = Users::ITEM_SYSTEM_NOTIFICATION, $cat_id = 0, $publish_time = null){
		if(!is_array($to)){
			$to = array($to);
		}
		
		$notification_id = Notifications::model()->insert(array(
			'content'=>$content,
			'create_time'=>\F::app()->current_time,
			'from'=>$from,
			'cat_id'=>$cat_id,
			'publish_time'=>$publish_time ? $publish_time : \F::app()->current_time,
		));
		foreach($to as $t){
			UserNotifications::model()->insert(array(
				'to'=>$t,
				'notification_id'=>$notification_id,
			));
		}
		return $notification_id;
	}
}