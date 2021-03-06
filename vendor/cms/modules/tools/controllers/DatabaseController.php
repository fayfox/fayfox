<?php
namespace cms\modules\tools\controllers;

use cms\library\ToolsController;
use fay\helpers\String;
use fay\core\Db;

class DatabaseController extends ToolsController{
	/**
	 * @var Db
	 */
	public $db;
	
	public function __construct(){
		parent::__construct();
		$this->layout->current_directory = 'database';
		
		//登陆检查，仅超级管理员可访问本模块
		$this->isLogin();
		
		$this->db = Db::getInstance();
	}
	
	public function model(){
		$this->layout->subtitle = 'Models';
		
		$this->view->tables = $this->db->fetchAll('SHOW TABLES');
		$this->view->prefix = $this->config->get('db.table_prefix');
		
		$this->view->current_table = $this->input->get('t');
		
		$this->view->apps = $this->getApps();
		
		$this->view->render();
	}
	
	public function dd(){
		$this->layout->subtitle = 'Data Dictionary';
		
		$this->view->tables = $this->db->fetchAll('SHOW TABLES');
		$this->view->prefix = $this->config->get('db.table_prefix');
		
		if($this->input->get('t')){
			$table_name = $this->view->prefix . $this->input->get('t');
		}else{
			$first_table = $this->view->tables[0];
			$table_name = array_shift($first_table);
		}
		$this->view->fields = $this->db->fetchAll("SHOW FULL FIELDS FROM {$table_name}");
		
		$this->view->ddl = $this->db->fetchRow("SHOW CREATE TABLE {$table_name}");
		
		$t_name = preg_replace("/^{$this->view->prefix}(.*)/", '$1', $table_name, 1);
		
		if(substr($t_name, 0, strpos($t_name, '_')) == APPLICATION){
			$class_name = APPLICATION.'\models\tables\\'.String::underscore2case(substr($t_name, strpos($t_name, '_')));
		}else{
			$class_name = 'fay\models\tables\\'.String::underscore2case($t_name);
		}
		
		$this->view->current_table = $t_name;
		
		$this->view->labels = \F::model($class_name)->labels();
		
		$this->view->apps = $this->getApps();
		
		$this->view->render();
	}
	
	public function getModel(){
		//表名，不带前缀
		$table_name = $this->input->get('t');
		$sql = "SHOW FULL FIELDS FROM {$this->db->{$table_name}}";
		$this->view->fields = $this->db->fetchAll($sql);
		$primary = array();
		
		//主键
		foreach($this->view->fields as $f){
			if(isset($f['Key']) && $f['Key'] == 'PRI'){
				$primary[] = $f['Field'];
			}
		}
		$this->view->primary = $primary;
		
		//类名和命名空间
		if(substr($table_name, 0, strpos($table_name, '_')) == APPLICATION){
			$this->view->class_name = String::underscore2case(substr($table_name, strpos($table_name, '_')));
			$this->view->namespace = APPLICATION.'\models\tables';
		}else{
			$this->view->class_name = String::underscore2case($table_name);
			$this->view->namespace = 'fay\models\tables';
		}
		
		$this->view->table_name = $table_name;
		
		$this->view->renderPartial();
	}
	
	public function downloadModel(){
		$table_name = $this->input->get('t');
		$sql = "SHOW FULL FIELDS FROM {$this->db->{$table_name}}";
		$this->view->fields = $this->db->fetchAll($sql);
		$primary = array();
		foreach($this->view->fields as $f){
			if(isset($f['Key']) && $f['Key'] == 'PRI'){
				$primary[] = $f['Field'];
			}
		}
		$this->view->primary = $primary;
		
		if(substr($table_name, 0, strpos($table_name, '_')) == APPLICATION){
			$filename = String::underscore2case(substr($table_name, strpos($table_name, '_')));
			$this->view->class_name = String::underscore2case(substr($table_name, strpos($table_name, '_')));
			$this->view->namespace = APPLICATION.'\models\tables';
		}else{
			$filename = String::underscore2case($table_name);
			$this->view->class_name = String::underscore2case($table_name);
			$this->view->namespace = 'fay\models\tables';
		}
		$this->view->table_name = $table_name;
		
		$content = $this->view->renderPartial('getmodel', array(), -1, true);
		
		if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE){
			header('Content-Type: "application/x-httpd-php"');
			header('Content-Disposition: attachment; filename="'.$filename.'.php"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".strlen($content));
		}else{
			header('Content-Type: "application/x-httpd-php"');
			header('Content-Disposition: attachment; filename="'.$filename.'.php"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".strlen($content));
		}
		echo $content;
	}
	
	/**
	 * 列出当期APP的所有表的ddl语句，非当前app的表不会被列出，定制表会在最后
	 */
	public function ddl(){
		$this->layout->subtitle = 'Data Definition Language';
		
		$tables = $this->db->fetchAll('SHOW TABLES');
		$prefix = $this->config->get('db.table_prefix');
		
		$ddls = array();
		$custom_tables = array();
		foreach($tables as $t){
			$table_name = array_shift($t);
			
			$t_name = preg_replace("/^{$prefix}(.*)/", '$1', $table_name, 1);
			
			$apps = $this->getApps();
			if(strpos($t_name, '_') && in_array(substr($t_name, 0, strpos($t_name, '_')), $apps)){
				//是某个app的定制表
				if(substr($t_name, 0, strpos($t_name, '_')) == APPLICATION){
					$custom_tables[] = $t_name;
				}
				continue;
			}
			
			$sql = "SHOW CREATE TABLE {$table_name}";
			$ddl = $this->db->fetchRow($sql);
			$ddl = $ddl['Create Table'];
			$ddl = 'DROP TABLE IF EXISTS `'.str_replace($this->config->get('db.table_prefix'), '{{$prefix}}', $table_name).'`;'."\n".$ddl;
			
			$ddl = str_replace('CREATE TABLE `'.$this->config->get('db.table_prefix'), 'CREATE TABLE `{{$prefix}}', $ddl);
			$ddl = preg_replace('/AUTO_INCREMENT=\d+/', '', $ddl);//删除自递增
			$ddl = preg_replace("/ COMMENT '.+'/", '', $ddl);//删除注释
			$ddls[] = $ddl.';';
		}
		
		foreach($custom_tables as $t){
			$table_name = $prefix . $t;
				
			$sql = "SHOW CREATE TABLE {$table_name}";
			$ddl = $this->db->fetchRow($sql);
			$ddl = $ddl['Create Table'];
			$ddl = 'DROP TABLE IF EXISTS `'.str_replace($this->config->get('db.table_prefix'), '{{$prefix}}', $table_name).'`;'."\n".$ddl;
				
			$ddl = str_replace('CREATE TABLE `'.$this->config->get('db.table_prefix'), 'CREATE TABLE `{{$prefix}}', $ddl);
			$ddl = preg_replace('/AUTO_INCREMENT=\d+/', '', $ddl);//删除自递增
			$ddl = preg_replace("/ COMMENT '.+'/", '', $ddl);//删除注释
			$ddls[] = $ddl.';';
		}
		
		$this->view->ddls = implode("\n\n", $ddls);
		$this->view->render();
	}
	
	/**
	 * 导出一张表的insert语句
	 */
	public function export(){
		$this->layout->subtitle = 'Data Export';
		
		$this->view->tables = $this->db->fetchAll('SHOW TABLES');
		$this->view->prefix = $this->config->get('db.table_prefix');
		$this->view->apps = $this->getApps();
		
		//当前显示的表
		if($this->input->get('t')){
			$table_name = $this->view->prefix . $this->input->get('t');
		}else{
			$first_table = $this->view->tables[0];
			$table_name = array_shift($first_table);
		}
		$t_name = substr($table_name, strlen($this->view->prefix));
		
		//当前表所有列
		$sql = "SHOW FULL FIELDS FROM {$table_name}";
		$this->view->fields = $this->db->fetchAll($sql);
		
		$insert = '';
		if($fields = $this->input->get('fields')){
			//需要显示的列
			$fields = '`'.implode('`, `', $fields).'`';
			$sql = "SELECT {$fields} FROM {$table_name}";
			
			if($this->input->get('order')){
				$sql .= " ORDER BY {$this->input->get('order')} {$this->input->get('sort')}";
			}
			$data = $this->db->fetchAll($sql);
			
			foreach($data as $d){
				$d = \F::filter('addslashes', $d);
				$d = str_replace(array(
					"\r", "\n",
				), array(
					'\r', '\n',
				), $d);
				$values = "'".implode("', '", $d)."'";
				$insert .= "INSERT INTO `{{\$prefix}}{$t_name}` ({$fields}) VALUES ({$values});\r\n";
			}
		}
		$this->view->insert = $insert;
		
		$this->view->render();
	}
}