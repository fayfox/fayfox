<?php
use fayfox\core\Bootstrap;
use fayfox\core\Hook;

session_start();//开启session
define('START', microtime(true));
define('BASEPATH', realpath(dirname(__FILE__)).'/');//定义程序根目录绝对路径
define('APPLICATION', isset($_SESSION['__app']) ? $_SESSION['__app'] : 'blog');

require '_init.php';

$bootstrap = new Bootstrap();
if($bootstrap->config('hook')){
	Hook::getInstance()->call('before_system');
}
$bootstrap->init();