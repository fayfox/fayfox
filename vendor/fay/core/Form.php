<?php
namespace fay\core;

use fay\core\FBase;
use fay\helpers\Html;

class Form extends FBase{
	private static $_forms = array();
	private $_data = null;
	private $_rules = array();
	private $_labels = array();
	private $_filters = array();
	private $_errors = array();
	
	/**
	 * 场景（用于某些特殊的验证规则）
	 */
	private $scene = 'default';
	/**
	 * 默认设置模式
	 */
	private $js_model = 'poshytip';
	
	private function __construct(){}
	
	private function __clone(){}
	
	/**
	 * @param string $name
	 * @return Form
	 */
	public static function getInstance($name = 'default'){
		if(empty(self::$_forms[$name])){
			self::$_forms[$name] = new self();
			self::$_forms[$name]->setScene($name);
		}
		return self::$_forms[$name];
	}
	
	/**
	 * 获取首个实例化的表单实例<br>
	 * （一般一个页面就一个表单）
	 */
	public static function getFirstForm(){
		if(self::$_forms){
			return current(self::$_forms);
		}else{
			return self::getInstance();
		}
	}
	
	/**
	 * 获取所有表单实例
	 */
	public static function getForms(){
		return self::$_forms;
	}
	
	/**
	 * 设置场景
	 */
	public function setScene($scene){
		$this->scene = $scene;
		return $this;
	}
	
	/**
	 * 获取场景
	 * @return string
	 */
	public function getScene(){
		return $this->scene;
	}
	
	/**
	 * 设置js配置模式（得在js中做相关配置才有效果，可参考common.js的validformParams属性）
	 * @param string $js_model
	 * @return \fay\core\Form
	 */
	public function setJsModel($js_model){
		$this->js_model = $js_model;
		return $this;
	}
	
	/**
	 * 获取js配置模式
	 * @return string
	 */
	public function getJsModel(){
		return $this->js_model;
	}
	
	public function setErrors($errors, $cover = false){
		if($cover){
			$this->_errors = $errors;
		}else{
			$this->_errors = array_merge($this->_errors, $errors);
		}
	}
	
	public function getErrors(){
		return $this->_errors;
	}
	
	/**
	 * 传入一个model实例，用于设置验证规则，过滤器和标签
	 * @param \fay\core\Model $model
	 * @return \fay\core\Form
	 */
	public function setModel($model){
		if($model && $model instanceof Model){
			$this->setRules($model->rules())
				->setLabels($model->labels())
				->setFilters($model->filters());
		}
		return $this;
	}
	
	/**
	 * @param array $data
	 * @param bool $cover 如果key已存在，是否覆盖
	 * @return \fay\core\Form
	 */
	public function setData($data, $cover = false){
		if(!is_array($data)){
			//数据格式非法，抛出异常
			throw new Exception('fay\core\Form::setData第一个参数必须是数组', '<code>'.var_export($data, true).'</code>');
		}
		if($this->_data === null){
			$this->_data = array();
		}
		if($cover){
			$this->_data = $data + $this->_data;
		}else{
			$this->_data = $this->_data + $data;
		}
		
		return $this;
	}
	
	/**
	 * 根据key获取单个表单数据
	 * @param string $key
	 * @param mix $default 若key不存在，返回默认值
	 * @param bool $filter 若为true且$this->_filter中有设置过滤器，则进行过滤
	 */
	public function getData($key, $default = null, $filter = true){
		if($this->_data === null){
			$this->setData(Input::getInstance()->request());
		}
		if(isset($this->_data[$key])){
			if($filter && isset($this->_filters[$key])){
				return \F::filter($this->_filters[$key], $this->_data[$key]);
			}else{
				return $this->_data[$key];
			}
		}else{
			return $default;
		}
	}
	
	/**
	 * 获取所有表单数据
	 * 若$filter为true且有对应的$this->_filter，则对数据进行过滤
	 * 若$filter为true但没有对应的$this->_filter，也会返回数据
	 * @param bool $filter
	 */
	public function getAllData($filter = true){
		if($this->_data === null){
			$this->setData(Input::getInstance()->request());
		}
		if($filter){
			$data = array();
			foreach($this->_data as $k => $v){
				if(isset($this->_filters[$k])){
					$data[$k] = \F::filter($this->_filters[$k], $v);
				}else{
					$data[$k] = $v;
				}
			}
			return $data;
		}else{
			return $this->_data;
		}
	}
	
	/**
	 * 返回过滤后的所有数据
	 * 若$this->_filter中没有设置过滤器，则不返回该数据
	 */
	public function getFilteredData(){
		if($this->_data === null){
			$this->setData(Input::getInstance()->request());
		}
		$data = array();
		foreach($this->_data as $k => $v){
			if(isset($this->_filters[$k])){
				$data[$k] = \F::filter($this->_filters[$k], $v);
			}
		}
		return $data;
	}
	
	/**
	 * @param array $filters
	 * @return \fay\core\Form
	 */
	public function setFilters($filters){
		$this->_filters = $filters + $this->_filters;
		return $this;
	}
	
	/**
	 * @param array $labels
	 * @return \fay\core\Form
	 */
	public function setLabels($labels){
		$this->_labels = $labels + $this->_labels;
		return $this;
	}
	
	public function getLabels(){
		return $this->_labels;
	}
	
	/**
	 * 追加规则，不会删除原有规则
	 * @param array $rules
	 * @return \fay\core\Form
	 */
	public function setRules($rules){
		foreach($rules as $r){
			if(isset($r[2]['on']) && $r[2]['on'] != $this->scene){
				continue;
			}
			$this->_rules[] = $r;
		}
		return $this;
	}
	
	/**
	 * 追加一条规则
	 * @param array $rule
	 * @return \fay\core\Form
	 */
	public function addRule($rule){
		if(empty($rule[2]['on']) || $rule[2]['on'] == $this->scene){
			$this->_rules[] = $rule;
		}
		return $this;
	}
	
	/**
	 * 生成<form>标签
	 * @param null|array $action 传入一个路由+参数的数组
	 * @param string $method post|get
	 * @param array $html_options
	 * @return string
	 */
	public function open($action = null, $method = 'post', $html_options = array()){
		if($action == null){
			$action = '';
		}else{
			$action = \F::app()->view->url($action[0], isset($action[1]) ? $action[1] : array());
		}
		if(isset($html_options['id'])){
			$id = $html_options['id'];
			unset($html_options['id']);
		}else if($this->scene == 'default'){
			$id = 'form';
		}else{
			$id = $this->scene . '-form';
		}
		$html = '<form id="'.$id.'" action="'.$action.'" method="'.$method.'" ';
		$class = '';
		if(isset($html_options['class'])){
			$class .= $html_options['class'];
			unset($html_options['class']);}
		if(!empty($this->_rules)){$class .= ' validform';}
		if(!empty($class)){$html .= 'class="'.$class.'" ';}
		
		foreach($html_options as $key => $val){
			$html .= " {$key}=\"{$val}\"";
		}
		$html .= '>';
		return $html;
	}
	
	public function close(){
		return '</form>';
	}
	
	/**
	 * 生成一个input框
	 * @param string $name name属性
	 * @param string $type type属性
	 * @param array $html_options 其它html属性，可以是自定义属性或者html标准属性
	 * @return string
	 */
	public function input($name, $type = 'text', $html_options = array()){
		return Html::input($name, $this->getData($name, ''), $type, $html_options);
	}
	
	public function inputText($name, $html_options = array(), $default = ''){
		return Html::inputText($name, $this->getData($name, $default), $html_options);
	}
	
	public function inputPassword($name, $html_options = array()){
		return Html::inputPassword($name, '', $html_options);
	}
	
	public function inputHidden($name, $html_options = array(), $default = ''){
		return Html::inputHidden($name, $this->getData($name, $default), $html_options);
	}
	
	public function inputCheckbox($name, $value, $html_options = array(), $default = false){
		$name1 = rtrim($name, '[]');
		$checked = false;
		$data = $this->getData($name1);
		if($data !== null){
			if(is_array($data) && in_array($value, $data)){
				$checked = true;
			}else if($data == $value){
				$checked = true;
			}
		}else if($default){
			$checked = true;
		}
		return Html::inputCheckbox($name, $value, $checked, $html_options);
	}
	
	public function inputRadio($name, $value, $html_options = array(), $default = false){
		$name1 = rtrim($name, '[]');
		$data = $this->getData($name1);
		if($data !== null && $value == $data){
			$checked = true;
		}else if($data === null && $default){
			$checked = true;
		}else{
			$checked = false;
		}
		return Html::inputRadio($name, $value, $checked, $html_options);
	}
	
	public function textarea($name, $html_options = array()){
		return Html::textArea($name, $this->getData($name, ''), $html_options);
	}
	
	/*
	 * 生成一个下拉菜单
	 */
	public function select($name = '', $options = array(), $html_options = array(), $default = ''){
		$name1 = rtrim($name, '[]');
		$data = $this->getData($name1);
		$data !== null ? $selected = $data : $selected = $default;
		return Html::select($name, $options, $selected, $html_options);
	}
	
	public function submitLink($text, $html_options = array()){
		if($this->scene == 'default'){
			$html_options['id'] = 'form-submit';
		}else{
			$html_options['id'] = $this->scene . '-form-submit';
		}
		return Html::link($text, 'javascript:;', $html_options);
	}
	
	/**
	 * 格式化rules为以字段为单位的结构，方便js操作
	 */
	public function getJsRules($rules = null){
		$rules === null && $rules = $this->_rules;
		$js_rules = array();
		foreach($rules as $r){
			if(!is_array($r[0])){
				$r[0] = array($r[0]);
			}
			foreach($r[0] as $field){
				if(empty($js_rules[$field])){
					$js_rules[$field] = array(
						'required'=>false,
						'validators'=>array(),
						'ajax'=>'',
					);
				}
				if($r[1] == 'required'){
					$js_rules[$field]['required'] = true;
					if(isset($r[2]['message'])){
						$js_rules[$field]['requiredMsg'] = $r[2]['message'];
					}
				}else if($r[1] == 'unique' || $r[1] == 'exist'){
					if(isset($r[2]['ajax'])){
						$params = isset($r[2]['ajax'][1]) ? $r[2]['ajax'][1] : array();
						if(isset($r[2]['except'])){
							$params[$r[2]['except']] = \F::app()->input->request($r[2]['except']);
						}
						$js_rules[$field]['ajax'] = \F::app()->view->url($r[2]['ajax'][0], $params);
					}
				}else if($r[1] == 'ajax'){
					$js_rules[$field]['ajax'] = \F::app()->view->url($r[2]['url'][0], isset($r[2]['url'][1]) ? $r[2]['url'][1] : array());
				}else{
					$js_rules[$field]['validators'][] = array(
						'name'=>$r[1],
						'params'=>isset($r[2]) ? $r[2] : array(),
					);
				}
			}
		}
		return $js_rules;
	}
	
	/**
	 * 数据校验
	 * @param string $filter 是否先用过滤器过滤再进行验证，默认为true
	 */
	public function check($filter = true){
		$validator = new Validator();
		$check =  $validator->check($this->_rules, $this->_labels, $this->getAllData($filter));
		if($check === true){
			return true;
		}else{
			$this->_errors = $check;
			return false;
		}
	}
}