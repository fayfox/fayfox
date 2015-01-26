<?php
use fayfox\helpers\String;
use fayfox\helpers\Html;
use fayfox\models\Option;
?>
<div id="banner">
	<?php \F::app()->widget->load('index-slides')?>
</div>
<div id="main">
	<section id="sec-1" class="box-1">
		<div class="box-1-title">
			<h3>公司简介</h3>
			<a href="<?php echo $this->url('about')?>" class="more">
				<img src="<?php echo $this->staticFile('images/more.png')?>" class="fixpng" />
			</a>
		</div>
		<div class="box-1-content">
			<a href="<?php echo $this->url('about')?>">
				<img src="<?php echo $this->staticFile('images/p1.jpg')?>" />
			</a>
			<p>
				<?php echo String::niceShort($about['abstract'], 70, true)?>
				<a href="<?php echo $this->url('about')?>" class="color-red">[详细]</a>
			</p>
		</div>
	</section>
	<section id="sec-2" class="box-1">
		<div class="box-1-title">
			<h3>最新资讯</h3>
			<a href="<?php echo $this->url('post')?>" class="more">
				<img src="<?php echo $this->staticFile('images/more.png')?>" class="fixpng" />
			</a>
		</div>
		<div class="box-1-content">
			<?php $top_news = array_shift($last_news);?>
			<div id="top-news">
				<img src="<?php echo $this->staticFile('images/p2.jpg')?>" class="f-left" />
				<h4><?php echo Html::link($top_news['title'], array('post/'.$top_news['id']))?></h4>
				<p>
					<?php echo String::niceShort($top_news['abstract'], 60, true)?>
					<?php echo Html::link('[详细]', array('post/'.$top_news['id']), array(
						'class'=>'color-red',
					))?>
				</p>
			</div>
			<div id="news-list">
				<ul>
				<?php foreach($last_news as $n){?>
					<li>
						<?php echo Html::link($n['cat_title'], array('c/'.$n['cat_id']), array(
							'class'=>'news-list-type f-left',
						))?>
						<a href="<?php echo $this->url("post/{$n['id']}")?>" class="news-list-title" title="<?php echo Html::encode($n['title'])?>">
							<em class="f-right"><?php echo date('Y-m-d', $n['publish_time'])?></em>
							<span>
								<?php echo String::niceShort($n['title'], 40, true)?>
								<?php if($n['publish_time'] > $this->current_time - 86400 * 30){?>
								<img src="<?php echo $this->staticFile('images/652264.png')?>" />
								<?php }?>
							</span>
						</a>
						<br class="clear" />
					</li>
				<?php }?>
				</ul>
			</div>
		</div>
	</section>
	<section id="sec-3" class="box-1">
		<div class="box-1-title">
			<h3>联系我们</h3>
			<a href="<?php echo $this->url('contact')?>" class="more">
				<img src="<?php echo $this->staticFile('images/more.png')?>" class="fixpng" />
			</a>
		</div>
		<div class="box-1-content">
			<a href="<?php echo $this->url('contact')?>">
				<img src="<?php echo $this->staticFile('images/contact.jpg')?>" id="call-me" />
			</a>
			<p id="call-me-text">
				量身定制解决方案
				<br />
				为企业发展保驾护航
			</p>
			<div id="contact">
				<p class="phone bold"><?php echo Option::get('youdao_phone')?></p>
				<p>电话：<?php echo Option::get('youdao_fax')?></p>
				<p>邮箱：<a href="mailto:<?php echo Option::get('youdao_email')?>"><?php echo Option::get('youdao_email')?></a></p>
				<p>地址：<?php echo Option::get('youdao_address')?></p>
			</div>
		</div>
	</section>
	<div class="clear"></div>
</div>
<script>
$(function(){
	//DD_belatedPNG.fix('.logos img');
	
	$("#csa").animate({"left":130,"top":57}, 800);
	$("#ul").animate({"left":251,"top":76}, 800);
	$("#vde").animate({"left":440,"top":55}, 800);
	$("#ccc").animate({"left":745,"top":114}, 800);
	$(".logos img").delay(801).animate({"bottom":10})
		.animate({"bottom":0})
		.animate({"bottom":10})
		.animate({"bottom":0})
		.animate({"bottom":10})
		.animate({"bottom":0});
	$(".logos img").live("mousemove", function(){
		$(this).animate({"bottom":10})
			.animate({"bottom":0});
	}).live("mouseout", function(){
		$(this).stop(true).animate({"bottom":0})
			.animate({"bottom":10})
			.animate({"bottom":0})
			.animate({"bottom":10})
			.animate({"bottom":0});
	});
});
</script>