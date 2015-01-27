<?php 
use fayfox\models\Setting;
use fayfox\helpers\Html;
use fayfox\models\tables\Users;
use fayfox\helpers\String;
use fayfox\helpers\SqlHelper;
use fayfox\helpers\RequestHelper;
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="image/x-icon" href="<?php echo $this->url()?>favicon.ico" rel="shortcut icon" />
<?php echo $this->getCss()?>

<!--[if (!IE)|(gte IE 8)]><!-->
<link type="text/css" rel="stylesheet" href="<?php echo $this->url()?>css/admin/style-metro.css" />
<!--<![endif]-->

<!--[if lt IE 8]>
<link type="text/css" rel="stylesheet" href="<?php echo $this->url()?>css/admin/style.css" />
<![endif]-->

<!--[if IE 6]>
<link type="text/css" rel="stylesheet" href="<?php echo $this->url()?>css/admin/ie6.css" />
<![endif]-->
<script type="text/javascript" src="<?php echo $this->url()?>js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="<?php echo $this->url()?>js/custom/system.min.js"></script>
<script>
system.base_url = '<?php echo $this->url()?>';
system.user_id = '<?php echo F::app()->session->get('id', 0)?>';
</script>
<script type="text/javascript" src="<?php echo $this->url()?>js/custom/fayfox.block.js"></script>
<script type="text/javascript" src="<?php echo $this->url()?>js/custom/admin/common.min.js"></script>
<title><?php echo $subtitle?> | 后台</title>
</head>
<body class="<?php $admin_body_class = Setting::model()->get('admin_body_class');echo $admin_body_class['class']?>">
<div class="wrapper">
	<div class="adminbar">
		<ul class="adminbar-left">
		<?php
			foreach(F::app()->_top_nav as $nav){
				if(isset($nav['role'])){
					if(is_array($nav['role']) && !in_array(F::app()->session->get('role'), $nav['role'])){
						continue;
					}else if(F::app()->session->get('role') != $nav['role']){
						continue;
					}
				}
		?>
			<li class="toggle-hover">
				<?php echo Html::link('<i class="'.$nav['icon'].'"></i>'.$nav['label'], array($nav['router']), array(
					'class'=>'item',
					'target'=>'_blank',
					'target'=>isset($nav['target']) ? $nav['target'] : false,
					'encode'=>false,
					'title'=>$nav['label'],
				))?>
			</li>
		<?php }?>
		</ul>
		<ul class="adminbar-right">
			<li class="toggle-hover header-notification">
				<a href="<?php echo $this->url('admin/notification/my')?>" class="item">
					<i class="icon-comment"></i>
					系统通知（<span id="header-notification-count">0</span>）
				</a>
				<div class="clear"></div>
				<div class="sub-wrapper header-notification-list"></div>
			</li>
			<li class="toggle-hover">
				<a href="javascript:;" class="item"><i class="icon-user"></i>你好，<?php echo F::app()->session->get('username')?></a>
				<div class="clear"></div>
				<div class="sub-wrapper profile">
					<ul>
						<li><?php echo F::app()->session->get('role_title')?></li>
						<li><a href="<?php echo $this->url('admin/profile/index');?>">编辑我的个人信息</a></li>
						<li><a href="<?php echo $this->url('admin/login/logout');?>">退出</a></li>
					</ul>
				</div>
			</li>
		</ul>
	</div>
	<div class="menuback"></div>
	<div class="menuwrap">
		<?php include '_admin_left.php';?>
	</div>
	<div class="ffcontent" id="ffcontent">
		<div class="ffbody">
			<div class="ffbody-content">
				<div class="screen-meta">
				<?php if(isset($_help)){?>
					<div class="hide" id="ffhelp-content"><?php $this->renderPartial($_help);?></div>
				<?php }?>
				<?php if(isset($_setting_panel)){?>
					<div class="hide" id="ffsetting-content"><?php $this->renderPartial($_setting_panel);?></div>
				<?php }?>
				</div>
				<div class="screen-meta-links">
				<?php if(isset($_help)){?>
					<div class="ffhelp-link-wrap">
						<a href="#ffhelp-content" class="ffhelp-link">帮助</a>
					</div>
				<?php }?>
				<?php if(isset($_setting_panel)){?>
					<div class="ffsetting-link-wrap">
						<a href="#ffsetting-content" class="ffsetting-link">设置</a>
					</div>
				<?php }?>
				</div>
				<h2 class="sub-title">
					<?php echo isset($subtitle) ? $subtitle : '';?>
					<?php if(isset($sublink)){
						$htmlOptions = isset($sublink['htmlOptions']) ? $sublink['htmlOptions'] : array();
						if(isset($htmlOptions['class'])){
							$htmlOptions['class'] .= ' sub-link';
						}else{
							$htmlOptions['class'] = ' sub-link';
						}

						echo Html::link($sublink['text'], $sublink['uri'], $htmlOptions);
					}?>
				</h2>
				<div class="notification-wrap">
					<?php echo F::app()->flash->get();?>
				</div>
				<div class="notification-wrap-js"></div>
				<?php echo $content?>
			</div>
			<div class="clear"></div>
			<?php if(F::app()->session->get('role') == Users::ROLE_SUPERADMIN){?>
			<div style="position:absolute;bottom:10px;">
				数据库操作:<?php echo F::app()->db->getCount()?>次
				|
				内存使用:<?php echo round(memory_get_usage()/1024, 2)?>KB
				|
				执行时间:<?php echo String::money((microtime(true) - START) * 1000)?>ms
			</div>
			<?php }?>
		</div>
		<div class="clear"></div>
	</div>
</div>
<script>
$(function(){
	//系统消息提示
	common.headerNotification();
	setInterval(common.headerNotification, 30000);
	<?php
		$forms = F::forms();
		foreach($forms as $k=>$f){?>
			common.validformParams.forms['<?php echo $k?>'] = {
				'rules':<?php echo json_encode($f->getJsRules())?>,
				'labels':<?php echo json_encode($f->getLabels())?>,
				'model':'<?php echo $f->getJsModel()?>',
				'scene':'<?php echo $f->getScene()?>'
			};
	<?php }?>
	common.init();
});
</script>
<img src="<?php echo $this->url()?>images/throbber.gif" class="hide" />
<img src="<?php echo $this->url()?>images/ajax-loading.gif" class="hide" />
<?php if(F::app()->config->get('debug')){?>
<div id="debug-container">
	<div class="tabbable">
		<ul class="nav-tabs">
			<li class="active"><a href="#debug-tab-1">Sql Log</a></li>
			<li><a href="#debug-tab-2">Backtrace</a></li>
		</ul>
		<div class="tab-content">
			<div id="debug-tab-1" class="tab-pane p5">
				<table class="inbox-table">
				<?php 
					$total_db_time = 0;
					$sqls = F::app()->db->getSqlLogs();
					foreach($sqls as $k=>$s){
						$total_db_time += $s[2]?>
					<tr>
						<td><?php echo $k+1?></td>
						<td><?php echo SqlHelper::nice(Html::encode($s[0]), $s[1])?></td>
						<td><?php echo String::money($s[2] * 1000)?>ms</td>
					</tr>
				<?php }?>
					<tr>
						<td colspan="2" align="center">数据库耗时</td>
						<td><?php echo String::money($total_db_time * 1000)?>ms</td>
					</tr>
				</table>
			</div>
			<div id="debug-tab-2" class="tab-pane p5 hide">
				<?php RequestHelper::renderBacktrace()?>
			</div>
		</div>
	</div>
</div>
<?php }?>
</body>
</html>