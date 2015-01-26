<?php
use fayfox\helpers\Html;
use fayfox\helpers\Date;
use fayfox\models\File;
?>
<div class="inner cf">
	<div class="breadcrumbs">
		<?php
		echo Html::link('网站首页', array('')),
			' &gt; ',
			Html::link($post['cat_title'], array('cat-'.$post['cat_id'])),
			' &gt; ',
			Html::encode($post['title']);
		?>
	</div>
	<div class="g-sd">
		<div class="cat-list">
			<h3><?php echo Html::encode($left_cats['title'])?></h3>
			<ul>
			<?php foreach($left_cats['children'] as $c){
				echo Html::link($c['title'], array('cat-'.$c['id']), array(
					'wrapper'=>'li',
					'class'=>$c['id'] == $post['cat_id'] ? 'crt' : false,
				));
			}?>
			</ul>
		</div>
	</div>
	<div class="g-mn">
		<h1 class="post-title"><?php echo Html::encode($post['title'])?></h1>
		<div class="post-meta">
			<span>发布时间：<?php echo Date::niceShort($post['publish_time'])?></span>
			<span>阅读数：<?php echo $post['views']?></span>
		</div>
		<div class="post-content"><?php echo $post['content']?></div>
		<?php if($post['files']){?>
		<div class="attachment">
			<h3>附件：</h3>
			<ul>
			<?php foreach($post['files'] as $f){
				echo Html::link($f['description'], $this->url('file/download', array('id'=>$f['file_id'])), array(
					'target'=>'_blank',
				));
			}?>
			</ul>
		</div>
		<?php }?>
	</div>
</div>