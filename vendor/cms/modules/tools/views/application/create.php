<?php
use fay\helpers\Html;
?>
<div class="col-2-1">
	<form method="post" action="" id="form" class="validform">
		<div class="col-left">
			<div class="form-field">
				<label class="title">项目名称<em class="color-red">*</em></label>
				<?php echo Html::inputText('name', '', array(
					'data-required'=>'required',
					'data-rule'=>'string',
					'data-params'=>'{format:\'alias\'}',
					'data-label'=>'项目名称',
					'data-ajax'=>$this->url('tools/application/is-app-not-exist'),
					'class'=>'w300',
				))?>
			</div>
			<div class="form-field">
				<label class="title">主机<em class="color-red">*</em></label>
				<?php echo Html::inputText('host', 'localhost', array(
					'data-required'=>'required',
					'data-label'=>"主机",
					'class'=>'w300',
				))?>
			</div>
			<div class="form-field">
				<label class="title">用户名<em class="color-red">*</em></label>
				<?php echo Html::inputText('user', 'root', array(
					'data-required'=>'required',
					'data-label'=>'用户名',
					'class'=>'w300',
				))?>
			</div>
			<div class="form-field">
				<label class="title">密码</label>
				<?php echo Html::inputText('password', '', array(
					'class'=>'w300',
				))?>
			</div>
			<div class="form-field">
				<label class="title">端口</label>
				<?php echo Html::inputText('port', 3306, array(
					'class'=>'w300',
					'data-rule'=>'int',
					'data-label'=>'端口',
				))?>
			</div>
			<div class="form-field">
				<label class="title">数据库名<em class="color-red">*</em></label>
				<?php echo Html::inputText('dbname', '', array(
					'data-required'=>'required',
					'data-label'=>'数据库名',
					'class'=>'w300',
				))?>
			</div>
			<div class="form-field">
				<label class="title">表前缀</label>
				<?php echo Html::inputText('table_prefix', 'faycms_', array(
					'class'=>'w300',
				))?>
			</div>
			<div class="form-field">
				<label class="title">是否创建数据库</label>
				<?php
					echo Html::inputRadio('database', '1', true, array(
						'label'=>'现在创建',
					));
					echo Html::inputRadio('database', '0', false, array(
						'label'=>'以后创建',
					));
				?>
				<p class="color-grey">若不创建，后期可以通过install创建</p>
			</div>
			<div id="install-db">
				<div class="form-field">
					<label class="title">站点名称<em class="color-red">*</em></label>
					<?php echo Html::inputText('sitename', '', array(
						'data-required'=>'required',
						'data-label'=>'站点名称',
						'class'=>'w300',
					))?>
				</div>
				<div class="form-field">
					<label class="title">超级管理员:用户名<em class="color-red">*</em></label>
					<?php echo Html::inputText('user_username', '', array(
						'data-required'=>'required',
						'data-label'=>'用户名',
						'class'=>'w300',
					))?>
				</div>
				<div class="form-field">
					<label class="title">超级管理员:密码<em class="color-red">*</em></label>
					<?php echo Html::inputText('user_password', '', array(
						'data-required'=>'required',
						'data-label'=>'密码',
						'class'=>'w300',
					))?>
				</div>
			</div>
			<div class="form-field">
				<a href="javascript:;" class="btn-1" id="form-submit">Submit</a>
			</div>
		</div>
	</form>
</div>
<script>
$(function(){
	$('[name="database"]').on('change', function(){
		if($(this).val() == '1'){
			$('#install-db').show();
		}else{
			$('#install-db').hide();
			$('#install-db').find('input').each(function(){
				$(this).poshytip('hide');
			});
		}
	});
});
</script>