<?php
use fayfox\helpers\Html;
?>
<div class="w1000 clearfix col-2">
	<div class="col-2-left">
		<nav class="left-menu">
			<ul>
				<li><a href="<?php echo $this->url('product')?>">产品展示</a></li>
			<?php foreach($pages as $p){?>
				<li><a href="<?php echo $this->url('service/'.$p['alias'])?>"><?php echo Html::encode($p['title'])?></a></li>
			<?php }?>
			</ul>
		</nav>
	</div>
	<div class="col-2-right">
		<div class="page-item">
			<header>
				<span class="title">产品展示</span>
				<span class="dashed"></span>
			</header>
			<div class="product-list">
				<?php $listview->showData();?>
				<div class="clear"></div>
			</div>
			<?php $listview->showPage();?>
		</div>
	</div>
</div>
<script src="<?php echo $this->url()?>js/custom/fayfox.fixcontent.js"></script>
<script>
$(function(){
	$(".left-menu").fixcontent();
});
</script>