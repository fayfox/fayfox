<?php
use fayfox\helpers\Html;
use fayfox\models\File;
use ncp\helpers\FriendlyLink;
use fayfox\helpers\Date;
?>
<li>
	<div class="news_title">
		<h2><?php echo Html::link($data['title'], FriendlyLink::getNewsLink(array(
			'id'=>$data['id'],
		)))?></h2>
		<span><?php echo Date::niceShort($data['publish_time'])?></span>
	</div>     
	<div class="news_info"><?php echo Html::encode($data['abstract'])?></div>
	<div class="news_view"><?php echo Html::link('详情', FriendlyLink::getNewsLink(array(
		'id'=>$data['id'],
	)))?></div>
</li>