<?php
use fay\helpers\Html;
use fay\models\tables\Users;
use fay\models\tables\Menus;
?>
<div class="box" id="box-abstract" data-name="abstract">
	<div class="box-title">
		<h4>配置参数</h4>
	</div>
	<div class="box-content">
		<div class="form-field">
			<label class="title">顶级菜单</label>
			<?php echo Html::select('top', Html::getSelectOptions($menu), isset($data['top']) ? $data['top'] : Menus::ITEM_USER_MENU)?>
			<p class="color-grey">仅显示所选菜单的子菜单（不包含所选菜单本身）</p>
		</div>
		<div class="form-field">
			<a href="javascript:;" class="toggle-advance" style="text-decoration:underline;">高级设置</a>
		</div>
		<div class="advance <?php if(F::app()->session->get('role') != Users::ROLE_SUPERADMIN)echo 'hide';?>">
			<div class="form-field">
				<label class="title">渲染模版<span class="color-red">（若非开发人员，请不要修改此配置）</span></label>
				<?php echo Html::textarea('template', isset($data['template']) ? $data['template'] : '', array(
					'class'=>'wp90 h200 autosize',
				))?>
				<p class="color-grey">
					若模版内容符合正则<code>/^[\w_-]+\/[\w_-]+\/[\w_-]+$/</code>，
					即类似<code>frontend/widget/template</code><br />
					则会调用当前application下符合该相对路径的view文件。<br />
					否则视为php代码<code>eval</code>执行。若留空，会调用默认模版。
				</p>
			</div>
		</div>
	</div>
</div>
<script>
$(function(){
	$('.toggle-advance').on('click', function(){
		$(".advance").toggle();
	});
});
</script>