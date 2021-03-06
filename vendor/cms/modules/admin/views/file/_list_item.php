<?php
use fay\models\File;
use fay\helpers\Html;
use fay\helpers\Date;
use fay\models\Qiniu;
use fay\models\tables\Files;

$full_file_path = File::model()->getUrl($data);
?>
<tr valign="top" id="file-<?php echo $data['id']?>" data-qiniu="<?php echo $data['qiniu']?>">
	<td><?php echo Html::inputCheckbox('ids[]', $data['id'], false, array(
		'class'=>'batch-ids',
	));?></td>
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
			echo Html::link('物理删除', array('admin/file/remove', array(
				'id'=>$data['id'],
			)), array(
				'class'=>'delete-file color-red',
				'data-id'=>$data['id'],
			));
			echo Html::link('下载', array('admin/file/download', array(
				'id'=>$data['id'],
			)), array(
				'class'=>'download-file',
			), true);
		?>
		</div>
	</td>
	<?php if(in_array('qiniu', $cols)){?>
	<td>
		<div class="qiniu-status qiniu-uploaded <?php if(!$data['qiniu']){echo 'hide';}?>">
			<span class="color-green">已上传</span>
			<div class="row-actions"><?php
				echo Html::link('查看', Qiniu::model()->getUrl($data), array(
					'target'=>'_blank',
					'class'=>'show-qiniu-file',
				));
				echo Html::link('删除', array('admin/qiniu/delete', array(
					'id'=>$data['id'],
				)), array(
					'data-id'=>$data['id'],
					'class'=>'qiniu-delete color-red',
					'title'=>'从七牛删除，本地图片会保留',
				));
			?></div>
		</div>
		<div class="qiniu-status qiniu-not-upload <?php if($data['qiniu']){echo 'hide';}?>">
			<span class="color-orange">未上传</span>
			<div class="row-actions"><?php
				echo Html::link('上传', array('admin/qiniu/put', array(
					'id'=>$data['id'],
				)), array(
					'data-id'=>$data['id'],
					'class'=>'qiniu-put',
				));
			?></div>
		</div>
		<div class="loading hide">
			<img src="<?php echo $this->url()?>images/throbber.gif" />操作中...
		</div>
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
	<?php if(in_array('type', $cols)){?>
	<td><?php switch($data['type']){
		case Files::TYPE_AVATAR:
			echo '头像';
		break;
		case Files::TYPE_CAT:
			echo '分类插图';
		break;
		case Files::TYPE_EXAM:
			echo '考试系统';
		break;
		case Files::TYPE_GOODS:
			echo '商品图片';
		break;
		case Files::TYPE_PAGE:
			echo '静态页';
		break;
		case Files::TYPE_POST:
			echo '文章';
		break;
		case Files::TYPE_WIDGET:
			echo '小工具';
		break;
		default:
			echo '其它';
	}?></td>
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