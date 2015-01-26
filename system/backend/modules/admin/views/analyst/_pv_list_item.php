<?php
use fayfox\helpers\Html;
use fayfox\helpers\String;
?>
<tr>
	<td><a href="<?php echo $data['url']?>" target="_blank">
		<span class="abbr" title="<?php echo urldecode(Html::encode($data['url']))?>">
			<?php echo String::niceShort(urldecode(Html::encode($data['url'])), 100)?>
		</span>
	</a></td>
	<td><?php echo $data['pv']?></td>
	<td><?php echo $data['uv']?></td>
	<td><?php echo $data['ip']?></td>
	<td><span><?php echo Html::encode($data['site_title'])?></span></td>
</tr>