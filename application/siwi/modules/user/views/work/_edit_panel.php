<?php
use fayfox\helpers\Html;
?>
<div class="clearfix">
	<div class="half-left">
	<?php
		$this->renderPartial('editor/_field_title');
		$this->renderPartial('editor/_field_cat');
		$this->renderPartial('editor/_field_tags');
		$this->renderPartial('editor/_field_copyright');
	?>
	</div>
	<div class="half-right">
		<?php
		$this->renderPartial('editor/_field_video');
		$this->renderPartial('editor/_field_file');
		$this->renderPartial('editor/_field_abstract');
	?>
	</div>
</div>
<div class="clearfix">
	<div class="gold-left">
		<?php $this->renderPartial('editor/_field_thumbnail');?>
	</div>
	<div class="gold-right">
		<fieldset class="form-field">
			<div class="title">
				<label>上传预览图</label>
			</div>
			<div class="files-container clearfix">
			<?php if(isset($files)){?>
				<?php foreach($files as $f){?>
				<div class="pic-item">
					<?php echo Html::inputHidden('files[]', $f['file_id'])?>
					<?php echo Html::img($f['file_id'], File::PIC_ZOOM, array(
						'dw'=>239,
						'dh'=>184,
					))?>
					<div class="remove-link-container hide">
						<a href="javascript:;" class="remove-link">
							<i class="icon-cross"></i>
							<span class="desc">删除图片</span>
						</a>
					</div>
				</div>
				<?php }?>
			<?php }?>
				<div id="upload-files-container" class="upload-panel">
					<a class="upload-link" id="upload-files-link" href="javascript:;">
						<i class="icon-plus"></i>
						<span class="click-to-upload">点击上传</span>
						<span class="desc">支持&nbsp;jpg,gif,png</span>
					</a>
				</div>
			</div>
		</fieldset>
	</div>
</div>