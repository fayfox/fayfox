<?php
use fayfox\models\File;
use fayfox\helpers\Html;
use fayfox\core\Uri;

$_backtrace = $exception->getTrace();
$level = $exception->getLevel();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title><?php echo $level, ' - ', $exception->getMessage()?></title>
<script type="text/javascript" src="<?php echo $this->url()?>js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="<?php echo $this->url()?>js/prettify.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo $this->url()?>css/debug.css" />
</head>
<body>
<div class="header">
	<h1><?php echo $level, ' - ', $exception->getMessage()?></h1>
	<i class="icon"></i>
</div>
<div class="backtrace">
	<div id="backtrace-container">
	<?php foreach($_backtrace as $k => $b){?>
		<div <?php if(!$k)echo 'class="act"'?>>
			<div class="element-wrap">
				<p class="function"><span class="index"><?php echo $k+1?>.</span><?php
					if(isset($b['class'])){
						echo "{$b['class']}{$b['type']}{$b['function']}()";
					}else{
						echo "{$b['function']}()";
					}
				?></p>
				<p class="file"><?php if(isset($b['file'])){
					echo $b['file'], ':(', $b['line'], ')';
				}?></p>
			</div>
			<?php if(isset($b['file'])){?>
			<div class="code-wrap" <?php if(!$k)echo 'style="display:block"'?>>
				<pre class="prettyprint linenums:<?php echo $b['line'] - 10 < 1 ? 1 : $b['line'] - 10?>" data-line="<?php echo $b['line']?>"><?php
					$source = File::getFileLine($b['file'], $b['line'], 10);
					$source || $source = '无相关文件';
					echo Html::encode(str_replace("\t", '    ', $source));//tab转四个空格，免得缩进太多不好看
				?></pre>
			</div>
			<?php }?>
		</div>
	<?php }?>
	</div>
</div>
<div class="system-data">
	<h3>System Data</h3>
	<table class="data-table">
		<tr>
			<th>Error File</th>
			<td><?php echo __FILE__?></td>
		</tr>
		<tr>
			<th>APPLICATION</th>
			<td><?php echo APPLICATION?></td>
		</tr>
		<tr>
			<th>Router</th>
			<td><?php echo Uri::getInstance()->router?></td>
		</tr>
		<tr>
			<th>BASEPATH</th>
			<td><?php echo BASEPATH?></td>
		</tr>
		<tr>
			<th>SYSTEM_PATH</th>
			<td><?php echo SYSTEM_PATH?></td>
		</tr>
		<tr>
			<th>PHP_VERSION</th>
			<td><?php echo PHP_VERSION?></td>
		</tr>
	</table>
	<h3>SERVER</h3>
	<?php $server_keys = array(
		'OS', 'SERVER_SOFTWARE', 'DOCUMENT_ROOT', 'DOCUMENT_URI', 'REQUEST_URI', 'SCRIPT_NAME', 'REQUEST_METHOD',
		'SCRIPT_FILENAME', 'FCGI_ROLE', 'PHP_SELF', 'SERVER_PORT', 'SERVER_NAME', 'SERVER_ADDR', 'HTTP_HOST',
		'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR', 'HTTP_ACCEPT_ENCODING', 'Path', 'PROCESSOR_ARCHITECTURE',
		'PROCESSOR_ARCHITEW6432', 'PROCESSOR_IDENTIFIER', 'REMOTE_PORT', 'REQUEST_TIME',
	);//选择性显示一些SERVER数据?>
	<table class="data-table">
	<?php foreach($server_keys as $k){?>
		<tr>
			<th><?php echo $k?></th>
			<td><?php isset($_SERVER[$k]) ? print_r($_SERVER[$k]) : ''?></td>
		</tr>
	<?php }?>
	</table>
	<h3>Cookies</h3>
	<table class="data-table">
	<?php foreach($_COOKIE as $k => $v){?>
		<tr>
			<th><?php echo $k?></th>
			<td><?php print_r($v)?></td>
		</tr>
	<?php }?>
	</table>
</div>
<script>
$(function(){
	prettyPrint();

	function highlightCurrentLine(){
		$('.prettyprinted').each(function(){
			var firstLine = $(this).find('li:first').attr('value');
			var currentLine = $(this).attr('data-line');
			var offset = parseInt(currentLine) - parseInt(firstLine);
			$(this).find('li:eq('+offset+')').addClass('crt');
		});
	}
	highlightCurrentLine();
	
	$('.backtrace').on('click', '.element-wrap', function(){
		var $parent = $(this).parent();
		if($parent.hasClass('act')){
			$parent.removeClass('act').find('.code-wrap').slideUp();
			return false;
		}
		//$('.backtrace').find('.code-wrap').slideUp();
		//$('#backtrace-container > div').removeClass('act');
		$parent.addClass('act').find('.code-wrap').slideDown();
	});
})
</script>
</body>
</html>