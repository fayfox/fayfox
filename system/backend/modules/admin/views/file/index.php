<?php $cols = F::form('setting')->getData('cols')?>
<div class="col-1">
	<table class="list-table">
		<thead>
			<tr>
				<th width="62"></th>
				<th>文件</th>
				<?php if(in_array('qiniu', $cols)){?>
				<th>七牛</th>
				<?php }?>
				<?php if(in_array('file_type', $cols)){?>
				<th>文件类型</th>
				<?php }?>
				<?php if(in_array('file_path', $cols)){?>
				<th>存储路径</th>
				<?php }?>
				<?php if(in_array('file_size', $cols)){?>
				<th>大小</th>
				<?php }?>
				<?php if(in_array('user', $cols)){?>
				<th>用户</th>
				<?php }?>
				<?php if(in_array('downloads', $cols)){?>
				<th>下载次数</th>
				<?php }?>
				<?php if(in_array('upload_time', $cols)){?>
				<th>上传时间</th>
				<?php }?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th></th>
				<th>文件</th>
				<?php if(in_array('qiniu', $cols)){?>
				<th>七牛</th>
				<?php }?>
				<?php if(in_array('file_type', $cols)){?>
				<th>文件类型</th>
				<?php }?>
				<?php if(in_array('file_path', $cols)){?>
				<th>存储路径</th>
				<?php }?>
				<?php if(in_array('file_size', $cols)){?>
				<th>大小</th>
				<?php }?>
				<?php if(in_array('user', $cols)){?>
				<th>用户</th>
				<?php }?>
				<?php if(in_array('downloads', $cols)){?>
				<th>下载次数</th>
				<?php }?>
				<?php if(in_array('upload_time', $cols)){?>
				<th>上传时间</th>
				<?php }?>
			</tr>
		</tfoot>
		<tbody>
			<?php $listview->showData(array(
				'cols'=>$cols,
				'display_name'=>F::form('setting')->getData('display_name'),
				'display_time'=>F::form('setting')->getData('display_time'),
			));?>
		</tbody>
	</table>
	<?php $listview->showPage();?>
	<div class="clear"></div>
</div>
<script>
$(function(){
	$(document).on("click", ".delete-file", function(){
		if(confirm('确定要物理删除该文件吗，删除后将无法恢复')){
			var file_id = $(this).attr("data-id");
			$.ajax({
				type: "GET",
				url: system.url("admin/file/ajax_delete"),
				data: {
					'id':file_id
				},
				dataType: 'json',
				success: function(data){
					if(data.status){
						$("#file-"+file_id+" td").addClass("bg-red").fadeOut("slow");
					}else{
						alert(data.message);
					}
				}
			});
		}
	}).on("click", ".qiniu-put", function(){
		$.ajax({
			type: "GET",
			url: system.url("admin/file/ajax_delete"),
			data: {
				'id':file_id
			},
			dataType: 'json',
			success: function(data){
				if(data.status){
					$("#file-"+file_id+" td").addClass("bg-red").fadeOut("slow");
				}else{
					alert(data.message);
				}
			}
		});
	});
});
</script>