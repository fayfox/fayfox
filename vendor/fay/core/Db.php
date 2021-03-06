<?php
namespace fay\core;

use fay\core\db\Intact;
use fay\helpers\SqlHelper;

class Db extends FBase{
	private $_host;
	private $_user;
	private $_pwd;
	private $_port = 3306;
	private $_dbname;
	private $_charset;
	private $_conn;
	private $_table_prefix;
	private $_debug = false;
	private static $_instance;
	public $matches = array('=', '<>', '<', '<=', '>', '>=', 'like', 'not like', 'is null',
		'is not null', 'is between', 'is not between', 'is in list', 'is not in list');
	public $operators = array('AND', 'OR', 'AND NOT', 'OR NOT');
	
	/**
	 * sql执行总次数
	 * @var int
	 */
	private static $_count = 0;
	
	private static $_sqls = array();
	
	private function __construct(){}
	
	private function __clone(){}
	
	public static function getInstance($config = array()){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self();
			self::$_instance->init($config);
		}
		return self::$_instance;
	}
	
	/**
	 * 初始化
	 */
	public function init($config){
		$db_config = $this->config('db');
		$this->_host = isset($config['host']) ? $config['host'] : $db_config['host'];
		$this->_user = isset($config['user']) ? $config['user'] : $db_config['user'];
 		$this->_pwd = isset($config['password']) ? $config['password'] : $db_config['password'];
 		$this->_port = !empty($config['port']) ? $config['port'] : (!empty($db_config['port']) ? $db_config['port'] : 3306);
		$this->_dbname = isset($config['dbname']) ? $config['dbname'] : $db_config['dbname'];
		$this->_charset = isset($config['charset']) ? $config['charset'] : $db_config['charset'];
		$this->_table_prefix = isset($config['table_prefix']) ? $config['table_prefix'] : $db_config['table_prefix'];
		$this->_debug = isset($config['debug']) ? $config['debug'] : $this->config('debug');
		$this->getConn();
	}
	
	public function getConn(){
		if(!$this->_conn){
			$dsn = "mysql:host={$this->_host};port={$this->_port};dbname={$this->_dbname};charset={$this->_charset}";
			try {
				$this->_conn = new \PDO($dsn, $this->_user, $this->_pwd);
			}catch(\PDOException $e){
				throw new Exception($e->getMessage());
			}
			$this->_conn->exec("SET NAMES {$this->_charset}");
		}
		return $this->_conn;
	}
	
	/**
	 * 执行一条sql语句，若是insert语句，则返回插入后产生的自递增id号，
	 * 若是update或delete语句，则返回受影响的记录条数
	 * @param String $sql
	 */
	public function execute($sql, $params = array()){
		$start_time = microtime(true);
		$sth = $this->_conn->prepare($sql) or $this->error($this->_conn->errorInfo(), $sql, $params);
		$sth->execute($params) or $this->error($sth->errorInfo(), $sql, $params);
		$sqltype = strtolower(substr(trim($sql), 0, 6));
		self::$_count++;
		$this->logSql($sql, $params, microtime(true) - $start_time);
		if($sqltype == 'insert'){
			return $this->_conn->lastInsertId();
		}else{
			return $sth->rowCount();
		}
	}
	
	/**
	 * 返回所有查询数据，如果没有符合条件的数据，则返回空数组
	 * @param string $sql
	 * @param array $params
	 * @param string $style
	 */
	public function fetchAll($sql, $params = array(), $style = 'assoc'){
		if($style == 'num'){
			$result_style = \PDO::FETCH_NUM;
		}else if($style == 'both'){
			$result_style = \PDO::FETCH_BOTH;
		}else{
			$result_style = \PDO::FETCH_ASSOC;
		}
		$start_time = microtime(true);
		$sth = $this->_conn->prepare($sql) or $this->error($this->_conn->errorInfo(), $sql, $params);
		$sth->execute($params) or $this->error($sth->errorInfo(), $sql, $params);
		self::$_count++;
		$this->logSql($sql, $params, microtime(true) - $start_time);
		return $sth->fetchAll($result_style);
	}
	
	/**
	 * 以一维数组的方式，返回一列结果
	 * @param string $col
	 * @param string $sql
	 * @param array $params
	 */
	public function fetchCol($col, $sql, $params = array()){
		$start_time = microtime(true);
		$sth = $this->_conn->prepare($sql) or $this->error($this->_conn->errorInfo(), $sql, $params);
		$sth->execute($params) or $this->error($sth->errorInfo(), $sql, $params);
		self::$_count++;
		$result = $sth->fetchAll(\PDO::FETCH_ASSOC);
		$this->logSql($sql, $params, microtime(true) - $start_time);
		$return = array();
		foreach($result as $r){
			$return[] = $r[$col];
		}
		return $return;
	}
	
	/**
	 * 返回第一条查询数据，如果没有符合条件的数据，则返回false
	 * @param string $sql
	 * @param array $params
	 * @param string $style
	 */
	public function fetchRow($sql, $params = array(), $style = 'assoc'){
		if($style == 'num'){
			$result_style = \PDO::FETCH_NUM;
		}else if($style == 'both'){
			$result_style = \PDO::FETCH_BOTH;
		}else{
			$result_style = \PDO::FETCH_ASSOC;
		}
		$start_time = microtime(true);
		$sth = $this->_conn->prepare($sql) or $this->error($this->_conn->errorInfo(), $sql, $params);
		$sth->execute($params) or $this->error($sth->errorInfo(), $sql, $params);
		self::$_count++;
		$this->logSql($sql, $params, microtime(true) - $start_time);
		return $sth->fetch($result_style);
	}
	
	public function insert($table, $data){
		$fields = array();
		$pres = array();
		$values = array();
		foreach($data as $k => $v){
			if($v === false)continue;
			if($v instanceof Intact){
				$fields[] = "`{$k}`";
				$pres[] = $v->get();
			}else{
				$fields[] = "`{$k}`";
				$pres[] = '?';
				$values[] = $v;
			}
		}
		$sql = "INSERT INTO {$this->__get($table)} (".implode(',', $fields).') VALUES ('.implode(',', $pres).')';
		return $this->execute($sql, $values);
	}
	
	public function update($table, $data, $condition = false){
		if(empty($data)){
			throw new Exception('Db::update语句更新数据不能为空');
		}
		
		$set = array();
		$values = array();
		foreach($data as $k => $v){
			if($v === false)continue;
			if($v instanceof Intact){
				$set[] = "`{$k}` = {$v->get()}";
			}else{
				$set[] = "`{$k}` = ?";
				$values[] = $v;
			}
		}
		
		if($condition === false){
			$sql = "UPDATE {$this->__get($table)} SET ".implode(',', $set);
			return $this->execute($sql, $values);
		}else{
			$where = $this->getWhere($condition);
			$sql = "UPDATE {$this->__get($table)} SET ".implode(',', $set)." WHERE {$where['condition']}";
			return $this->execute($sql, array_merge($values, $where['params']));
		}
	}
	
	public function delete($table, $condition){
		$where = $this->getWhere($condition);
		$sql = "DELETE FROM {$this->__get($table)} WHERE {$where['condition']}";
		return $this->execute($sql, $where['params']);
	}
	
	public function inc($table, $condition, $field, $count){
		$where = $this->getWhere($condition);
		if($count >= 0){
			$count = '+'.$count;
		}
		$sql = "UPDATE {$this->__get($table)} SET `{$field}` = `{$field}` {$count} WHERE {$where['condition']}";
		return $this->execute($sql, $where['params']);
	}
	
	public function __get($table_name){
		return $this->_table_prefix . $table_name;
	}
	
	/**
	 * 构造一个where语句以及相关参数
	 * @param array $where
	 * @return array array('condition', 'params')
	 */
	public function getWhere($where){
		if(is_array($where)){
			$condition = '';
			$params = array();
			foreach($where as $key => $value){
				if($value === false)continue;
				if(in_array(strtoupper(trim($key)), $this->operators)){
					$op = ' ' . strtoupper($key) . ' ';
					$partial = $this->getPartialCondition($op, $value);
					if($condition != ''){$condition .= ' AND ';}
					$condition .= $partial['condition'];
					$params = array_merge($params, $partial['params']);
				}else{
					$op = ' AND ';
					if($condition != ''){$condition .= $op;}
					if(is_numeric($key)){//'id = 1'
						$condition .= $value;
					}else{//'id = ?'=>1
						if(is_array($value)){
							$params = array_merge($params, $value);
							if(substr_count($key, '?') == 1 && count($value) > 1){
								$key = str_replace('?', '?'.str_repeat(', ?', count($value) - 1), $key);
							}
						}else{
							$params[] = $value;
						}
						$condition .= $key;
					}
				}
			}
			return array(
				'condition'=>$condition,
				'params'=>$params,
			);
		}else{
			return array(
				'condition'=>$where,
				'params'=>array(),
			);
		}
	}
	
	private function getPartialCondition($op, $condition_arr){
		$partial_condition = array();
		$params = array();
		foreach($condition_arr as $key => $value){
			if(in_array(strtoupper($key), $this->operators)){
				$partial = $this->getPartialCondition(' ' . strtoupper($key) . ' ', $value);
				$partial_condition[] = $partial['condition'];
				$params = array_merge($params, $partial['params']);
			}else{
				if(is_numeric($key)){//'id = 1'
					$partial_condition[] = $value;
				}else{//'id = ?'=>1
					if(is_array($value)){
						$params = array_merge($params, $value);
						if(substr_count($key, '?') == 1 && count($value) > 1){
							$key = str_replace('?', '?'.str_repeat(', ?', count($value) - 1), $key);
						}
					}else{
						$params[] = $value;
					}
					$partial_condition[] = $key;
				}
			}
		}
		$condition = ' ( ' . implode($op, $partial_condition) . ' ) ';
		return array(
			'condition'=>$condition,
			'params'=>$params
		);
	}
	
	public function error($message, $sql = '', $params = array()){
		if(is_array($message)){
			$message = implode(' - ', $message);
		}
		throw new Exception($message, $sql ? '<code>'.SqlHelper::nice($sql, $params).'</code>' : '');
	}
	
	public function getCount(){
		return self::$_count;
	}
	
	private function logSql($sql, $params = array(), $time){
		self::$_sqls[] = array($sql, $params, $time);
	}
	
	public function getSqlLogs(){
		return self::$_sqls;
	}
}