<?php
namespace siwi\models;

use fayfox\core\Model;
use fayfox\models\Category;

class Post extends Model{
	public $cats = array();
	
	/**
	 * @return Post
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getType($cat_id){
		$cat = Category::model()->get($cat_id, 'left_value,right_value');
		if(empty($this->cats)){
			$this->cats = Category::model()->getNextLevel('_system_post', 'alias,left_value,right_value');
		}
		
		foreach($this->cats as $c){
			if($cat['left_value'] > $c['left_value'] && $cat['right_value'] < $c['right_value'])
				return ltrim($c['alias'], '_');
		}
	}
}