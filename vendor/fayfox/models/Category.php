<?php
namespace fayfox\models;

use fayfox\core\Model;
use fayfox\models\tables\Categories;

class Category extends Model{
	/**
	 * @return Category
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	/**
	 * 索引记录
	 * @param int $parent
	 * @param int $start_num
	 */
	public function buildIndex($parent = 0, $start_num = 1){
		Tree::model()->buildIndex('fayfox\models\tables\Categories', $parent, $start_num);
	}
	
	/**
	 * 根据分类别名获取一个分类信息
	 * @param string $alias
	 * @param string $fields
	 * @return array
	 */
	public function getByAlias($alias, $fields = '*'){
		return Categories::model()->fetchRow(array(
			'alias = ?'=>$alias,
		), $fields);
	}
	
	/**
	 * 根据父节点别名，获取其所有子节点，返回二维数组（非树形）<br>
	 * 若不指定别名，返回整张表
	 * @param string $alias
	 * @param string $fields
	 * @return array
	 */
	public function getAll($alias = null, $fields = 'id,parent,alias,title,sort'){
		if($alias === null){
			return Categories::model()->fetchAll(array(), $fields, 'sort');
		}else{
			$node = $this->getByAlias($alias, 'left_value,right_value');
			if($node){
				return Categories::model()->fetchAll(array(
					'left_value > '.$node['left_value'],
					'right_value < '.$node['right_value'],
				), $fields, 'sort');
			}else{
				return array();
			}
		}
	}
	
	/**
	 * 根据父节点ID，获取其所有子节点，返回二维数组（非树形）<br>
	 * 若不指定别名，返回整张表
	 * @param string $alias
	 * @param string $fields
	 * @return array
	 */
	public function getAllByParentId($id = 0, $fields = 'id,parent,alias,title,sort'){
		if($id == 0){
			return Categories::model()->fetchAll(array(), $fields, 'sort');
		}else{
			$node = $this->get($id, 'left_value,right_value');
			if($node){
				return Categories::model()->fetchAll(array(
					'left_value > '.$node['left_value'],
					'right_value < '.$node['right_value'],
				), $fields, 'sort');
			}else{
				return array();
			}
		}
	}
	
	/**
	 * 根据父节点别名，获取所有子节点的ID，以一维数组方式返回
	 * 若不指定别名，返回整张表
	 * @param string $alias
	 */
	public function getAllIds($alias = null){
		if($alias === null){
			return Categories::model()->fetchCol('id');
		}else{
			$node = $this->getByAlias($alias, 'left_value,right_value');
			if($node){
				return Categories::model()->fetchCol('id', array(
					'left_value > '.$node['left_value'],
					'right_value < '.$node['right_value'],
				));
			}else{
				return array();
			}
		}
	}
	
	/**
	 * 根据父节点ID，获取所有子节点的ID，以一维数组方式返回
	 * @param int $parent_id
	 * @return array
	 */
	public function getAllIdsByParentId($parent_id){
		$node = $this->get($parent_id, 'left_value,right_value');
		if($node){
			return Categories::model()->fetchCol('id', array(
				'left_value > '.$node['left_value'],
				'right_value < '.$node['right_value'],
			));
		}else{
			return array();
		}
	}
	
	/**
	 * 根据父节点别名，获取分类树<br>
	 * 若不指定别名，返回整张表
	 * @param string $alias
	 * @return array
	 */
	public function getTree($alias = null){
		if($alias === null){
			return Tree::model()->getTree('fayfox\models\tables\Categories');
		}else{
			$node = $this->getByAlias($alias, 'id');
			if($node){
				return Tree::model()->getTree('fayfox\models\tables\Categories', $node['id']);
			}else{
				return array();
			}
		}
	}
	
	/**
	 * 根据父节点ID，获取分类树
	 * @param int $id
	 * @param string $fields 返回的字段
	 * @return array
	 */
	public function getTreeByParentId($id = 0, $fields = '!seo_title,seo_keywords,seo_description,is_system'){
		return Tree::model()->getTree('fayfox\models\tables\Categories', $id, $fields);
	}
	
	/**
	 * 根据父节点别名，获取其下一级节点
	 * @param string $alias
	 * @param string $fields
	 * @param string $order
	 */
	public function getNextLevel($alias, $fields = '*', $order = 'sort, id'){
		$node = $this->getByAlias($alias, 'id');
		if($node){
			return Categories::model()->fetchAll(array(
				'parent = ?'=>$node['id'],
			), $fields, $order);
		}else{
			return array();
		}
	}
	
	/**
	 * 根据父节点ID，获取其下一级节点
	 * @param int $id
	 * @param string $fields
	 * @param string $order
	 */
	public function getNextLevelByParentId($id, $fields = '*', $order = 'sort, id'){
		return Categories::model()->fetchAll(array(
			'parent = ?'=>$id,
		), $fields, $order);
	}
	
	/**
	 * 根据ID获取分类，可一次传入多个ID
	 * @param int|string $ids
	 * @param string $fields
	 * @return array
	 */
	public function get($ids, $fields = '*'){
		$ids = explode(',', $ids);
		if(count($ids) > 1){
			$cats = Categories::model()->fetchAll(array(
				'id IN (?)'=>$ids,
			), $fields);
			$return = array();
			foreach($ids as $i){
				foreach($cats as $k => $c){
					if($c['id'] == $i){
						$return[] = $c;
						unset($cats[$k]);
						break;
					}
				}
			}
			return $return;
		}else{
			return Categories::model()->find($ids[0], $fields);
		}
	}
	
	/**
	 * 修改一条记录的sort值，并修改左右值
	 * @param int $id
	 * @param int $sort
	 */
	public function sort($id, $sort){
		Tree::model()->sort('fayfox\models\tables\Categories', $id, $sort);
	}
	
	/**
	 * 创建一个节点
	 * @param int $parent
	 * @param int $sort
	 * @param array $data
	 */
	public function create($parent, $sort = 100, $data = array()){
		return Tree::model()->create('fayfox\models\tables\Categories', $parent, $sort, $data);
	}
	
	/**
	 * 删除一个节点，其子节点将被挂载到父节点
	 * @param int $id
	 */
	public function remove($id){
		return Tree::model()->remove('fayfox\models\tables\Categories', $id);
	}
	
	/**
	 * 删除一个节点，及其所有子节点
	 * @param int $id
	 */
	public function removeAll($id){
		return Tree::model()->removeAll('fayfox\models\tables\Categories', $id);
	}
	
	/**
	 * 更新一个节点
	 * @param int $parent
	 * @param int $sort
	 * @param array $data
	 */
	public function update($id, $data, $sort = null, $parent = null){
		return Tree::model()->edit('fayfox\models\tables\Categories', $id, $data, $sort, $parent);
	}
	
	/**
	 * 判断$cat1是否为$cat2的子节点
	 * @param int|array $cat1 可以是cat_id或者数组，若为数组则必须传入left_value和right_value
	 * @param int|array $cat2
	 */
	public function isChild($cat1, $cat2){
		if(!is_array($cat1)){
			$cat1 = $this->get($cat1, 'left_value,right_value');
		}
		if(!is_array($cat2)){
			$cat2 = $this->get($cat2, 'left_value,right_value');
		}
		
		if($cat1['left_value'] > $cat2['left_value'] && $cat1['right_value'] < $cat2['right_value']){
			return true;
		}else{
			return false;
		}
	}
}