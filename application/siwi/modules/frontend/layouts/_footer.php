<?php
use fayfox\helpers\String;
use fayfox\helpers\SqlHelper;
use fayfox\helpers\RequestHelper;
use fayfox\models\Option;
use fayfox\helpers\Html;
?>
<footer class="g-ft">
	<div class="w1190">
		<div class="box m-about">
			<h3>关于思唯</h3>
			<div class="box-content"><?php echo F::app()->widget->load('footer_about')?></div>
		</div>
		<div class="box m-msg">
			<h3>信息</h3>
			<div class="box-content">
				<ul>
					<li><a href="">版权申明</a></li>
					<li><a href="">关于隐私</a></li>
					<li><a href="">免责声明</a></li>
					<li><a href="">网站地图</a></li>
					<li><a href="">常见问题</a></li>
				</ul>
			</div>
		</div>
		<div class="box m-contact">
			<h3>联系</h3>
			<div class="box-content">
				<ul>
					<li><a href="">在线留言</a></li>
					<li><a href="">联系我们</a></li>
					<li><a href="">关注我们</a></li>
				</ul>
			</div>
		</div>
		<div class="box m-weixin">
			<h3>我们的微信</h3>
			<div class="box-content">
				<img src="<?php echo $this->url()?>static/siwi/images/weixin.png" />
				<p>关注我们的微信公众号，每天都有新鲜的设计，最新的资讯，灵感由你掌握。</p>
			</div>
		</div>
	</div>
	<div class="g-fcp">
		<div class="w1190">
			<p class="tip">最佳分辨率1280*800，建议使用Chrome、Firefox、Safari、ie10版本浏览器</p>
			<p class="cp"><?php echo Option::get('copyright')?></p>
		</div>
	</div>
</footer>
<script type="text/javascript" src="<?php echo $this->staticFile('js/common.js')?>"></script>
<script>common.init();</script>
<script type="text/javascript" src="<?php echo $this->url()?>js/custom/analyst-min.js"></script>
<script>_fa.init();</script>
<?php echo \F::app()->flash->get()?>
<?php if(F::app()->config->get('debug')){?>
<div id="debug-container">
	<div class="tabbable">
		<ul class="nav-tabs">
			<li class="active"><a href="#debug-tab-1">Sql Log</a></li>
			<li><a href="#debug-tab-2">Backtrace</a></li>
		</ul>
		<div class="tab-content">
			<div id="debug-tab-1" class="tab-pane p5">
				数据库操作:<?php echo \F::app()->db->getCount()?>次
				|
				内存使用:<?php echo round(memory_get_usage()/1024, 2)?>KB
				|
				执行时间:<?php echo String::money((microtime(true) - START) * 1000)?>ms
				<table class="inbox-table">
				<?php 
					$total_db_time = 0;
					$sqls = \F::app()->db->getSqlLogs();
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