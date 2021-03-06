<?php
use fay\models\tables\Users;
use fay\helpers\Date;
use fay\helpers\Html;
use fay\models\File;
?>
<tr valign="top" id="user-<?php echo $data['id']?>">
	<?php if(in_array('avatar', $cols)){?>
	<td><?php echo Html::link(Html::img($data['avatar'], File::PIC_THUMBNAIL, array(
		'width'=>40,
		'height'=>40,
		'class'=>'circle',
		'spare'=>'avatar',
	)), array('admin/user/item', array(
		'id'=>$data['id'],
	)), array(
		'title'=>false,
		'encode'=>false,
	))?></td>
	<?php }?>
	
	<td>
		<strong>
			<?php if($data['username'])
				echo Html::encode($data['username']);
			else
				echo '&nbsp;';?>
		</strong>
		<div class="row-actions">
			<?php
				echo Html::link('查看', array('admin/user/item', array(
					'id'=>$data['id'],
				)), array(), true);
				echo Html::link('编辑', array('admin/user/edit', array(
					'id'=>$data['id'],
				)), array(), true);
			?>
		</div>
	</td>
	
	<?php if(in_array('role', $cols)){?>
	<td><?php echo Html::encode($data['role_title'])?></td>
	<?php }?>
	
	<?php if(in_array('cellphone', $cols)){?>
	<td><?php echo Html::encode($data['cellphone'])?></td>
	<?php }?>
	
	<?php if(in_array('email', $cols)){?>
	<td>
		<a href="mailto:<?php echo Html::encode($data['email'])?>"><?php echo Html::encode($data['email'])?></a>
	</td>
	<?php }?>
	
	<?php if(in_array('nickname', $cols)){?>
	<td><?php echo Html::encode($data['nickname'])?></td>
	<?php }?>
	
	<?php if(in_array('realname', $cols)){?>
	<td><?php echo Html::encode($data['realname'])?></td>
	<?php }?>
	
	<?php if(in_array('status', $cols)){?>
	<td>
		<?php if($data['status'] == Users::STATUS_PENDING){?>
			<span class="color-orange">未审核</span>
		<?php }else if($data['status'] == Users::STATUS_VERIFIED){?>
			<span class="color-green">通过审核</span>
		<?php }else if($data['status'] == Users::STATUS_VERIFY_FAILED){?>
			<span class="color-red">未通过审核</span>
		<?php }else if($data['status'] == Users::STATUS_UNCOMPLETED){?>
			<span class="color-orange">信息不完整</span>
		<?php }else if($data['status'] == Users::STATUS_NOT_VERIFIED){?>
			<span class="color-orange">未验证邮箱</span>
		<?php }?>
	</td>
	<?php }?>
	
	<?php if(in_array('block', $cols)){?>
	<td><?php if($data['block']){
		echo '<span class="color-red">阻塞</span>';
	}else{
		echo '<span class="color-green">正常</span>';
	}?></td>
	<?php }?>
	
	<?php if(in_array('reg_time', $cols)){?>
	<td>
		<span class="time abbr" title="<?php echo Date::format($data['reg_time'])?>">
			<?php echo Date::niceShort($data['reg_time'])?>
		</span>
	</td>
	<?php }?>
	
	<?php if(in_array('reg_ip', $cols)){?>
	<td>
		<span class="ip abbr" title="<?php echo long2ip($data['reg_ip'])?>">
			<?php echo $iplocation->getCountryAndArea(long2ip($data['reg_ip']))?>
		</span>
	</td>
	<?php }?>
	
	<?php if(in_array('last_login_time', $cols)){?>
	<td>
		<span class="time abbr" title="<?php echo Date::format($data['last_login_time'])?>">
			<?php echo Date::niceShort($data['last_login_time'])?>
		</span>
	</td>
	<?php }?>
	
	<?php if(in_array('last_login_ip', $cols)){?>
	<td>
		<span class="ip abbr" title="<?php echo long2ip($data['last_login_ip'])?>">
			<?php echo $iplocation->getCountryAndArea(long2ip($data['last_login_ip']))?>
		</span>
	</td>
	<?php }?>
	
	<?php if(in_array('last_time_online', $cols)){?>
	<td>
		<span class="time abbr" title="<?php echo Date::format($data['last_time_online'])?>">
			<?php echo Date::niceShort($data['last_time_online'])?>
		</span>
	</td>
	<?php }?>
	
	<?php if(in_array('trackid', $cols)){?>
	<td><?php echo $data['trackid']?></td>
	<?php }?>
</tr>