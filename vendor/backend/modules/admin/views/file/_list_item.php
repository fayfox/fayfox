<?php
use fayfox\models\File;
use fayfox\helpers\Html;
use fayfox\helpers\Date;
use fayfox\models\Qiniu;

$full_file_path = File::model()->getUrl($data);
?>
<tr valign="top" id="file-<?php echo $data['id']?>">
	<td class="align-center">
	<?php if($data['is_image']){?>
		<?php echo Html::link(Html::img($data['id'], File::PIC_THUMBNAIL, array(
			'width'=>60,
			'height'=>60,
		)), $full_file_path, array(
			'class'=>'file-image fancybox-image',
			'encode'=>false,
			'title'=>$data['client_name'],
		))?>
	<?php }else{?>
		<img src="<?php echo File::model()->getThumbnailUrl($data)?>" />
	<?php }?>
	</td>
	<td>
		<strong>
			<?php echo Html::link($data['client_name'], $full_file_path, array(
				'class'=>'row-title fancybox-image',
			))?>
		</strong>
		<div class="row-actions">
		<?php
			if($data['is_image'] == 1){
				echo Html::link('查看', $full_file_path, array(
					'class'=>'file-image',
					'target'=>'_blank',
				));
			}
			echo Html::link('永久删除', 'javascript:;', array(
				'class'=>'delete-file color-red',
				'data-id'=>$data['id'],
			));
			echo Html::link('下载', array('admin/file/download', array(
				'id'=>$data['id'],
			)), array(
				'class'=>'download-file',
			));
		?>
		</div>
	</td>
	<?php if(in_array('qiniu', $cols)){?>
	<td>
		<?php
			if($data['qiniu']){
				echo '<span class="color-green">已上传</span>';
			}else{
				echo '<span class="color-orange">未上传</span>';
			}
		?>
		<div class="row-actions"><?php if($data['qiniu']){
			echo Html::link('查看', Qiniu::model()->getUrl($data), array(
				'target'=>'_blank',
			));
			echo Html::link('删除', array('admin/qiniu/delete', array(
				'id'=>$data['id'],
			)), array(
				'class'=>'qiniu-delete color-red',
			));
		}else{
			echo Html::link('上传', array('admin/qiniu/put', array(
				'id'=>$data['id'],
			)), array(
				'class'=>'qiniu-put',
			));
		}?></div>
	</td>
	<?php }?>
	<?php if(in_array('file_type', $cols)){?>
	<td><?php echo $data['file_type']?></td>
	<?php }?>
	<?php if(in_array('file_path', $cols)){?>
	<td><?php echo $data['file_path']?></td>
	<?php }?>
	<?php if(in_array('file_size', $cols)){?>
	<td><?php echo number_format($data['file_size']/1024, 2, '.', ',')?>KB</td>
	<?php }?>
	<?php if(in_array('user', $cols)){?>
	<td><?php echo $data[$display_name]?></td>
	<?php }?>
	<?php if(in_array('downloads', $cols)){?>
	<td><?php echo $data['downloads']?></td>
	<?php }?>
	<?php if(in_array('upload_time', $cols)){?>
	<td><span class="time abbr" title="<?php echo Date::format($data['upload_time'])?>">
		<?php if(F::form('setting')->getData('display_time', 'short') == 'short'){
			echo Date::niceShort($data['upload_time']);
		}else{
			echo Date::format($data['upload_time']);
		}?>
	</span></td>
	<?php }?>
</tr>