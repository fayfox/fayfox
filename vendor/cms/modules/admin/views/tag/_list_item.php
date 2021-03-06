<?php
use fay\helpers\Html;
?>
<tr valign="top" id="tag-<?php echo $data['id']?>">
	<td>
		<strong><?php echo Html::encode($data['title'])?></strong>
		<div class="row-actions"><?php
			echo Html::link('查看文章', array('admin/post/index', array(
				'tag_id'=>$data['id'],
			)));
			echo Html::link('编辑', array('admin/tag/edit', array(
				'id'=>$data['id'],
			)), array(), true);
			echo Html::link('永久删除', array('admin/tag/remove', array(
				'id'=>$data['id'],
			)), array(
				'class'=>'color-red remove-link',
			), true);
		?>
		</div>
	</td>
	<td><?php echo $data['count']?></td>
	<td><?php echo Html::inputText("sort[{$data['id']}]", $data['sort'], array(
		'size'=>3,
		'maxlength'=>3,
		'data-id'=>$data['id'],
		'class'=>'tag-sort w30',
	))?></td>
</tr>