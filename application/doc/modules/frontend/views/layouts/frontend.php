<?php
use fay\helpers\Html;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php if(!empty($canonical)){?>
<link rel="canonical" href="<?php echo $canonical?>" />
<?php }?>
<title><?php echo $title ? $title : ''?></title>
<meta content="<?php if(isset($keywords))echo Html::encode($keywords);?>" name="keywords" />
<meta content="<?php if(isset($description))echo Html::encode($description);?>" name="description" />
<link type="text/css" rel="stylesheet" href="<?php echo $this->staticFile('css/style.css')?>" >
<?php echo $this->getCss()?>
<script type="text/javascript" src="<?php echo $this->url()?>js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="<?php echo $this->url()?>js/custom/system.min.js"></script>
<script>
system.base_url = '<?php echo $this->url()?>';
</script>
<!--[if lt IE 9]>
	<script type="text/javascript" src="<?php echo $this->url()?>js/html5.js"></script>
<![endif]-->
</head>
<body>
<div class="wrapper">
	<?php $this->renderPartial('layouts/_sidebar_menu')?>
	<div class="main-content">
		<div class="cf main-title">
			<h1 class="fl"><?php echo isset($page_title) ? $page_title : ''?></h1>
			<?php if(isset($breadcrumb)){?>
			<ol class="fr breadcrumb">
				<li>
					<a href="<?php echo $this->url()?>"><i class="icon-home"></i>主页</a>
				</li>
				<?php foreach($breadcrumb as $b){?>
				<li><?php echo Html::link($b['text'], $b['href'])?></li>
				<?php }?>
			</ol>
			<?php }?>
		</div>
		<div class="main-content-inner"><?php echo $content?></div>
		<?php $this->renderPartial('layouts/_footer')?>
	</div>
</div>
<script type="text/javascript" src="<?php echo $this->staticFile('js/common.js')?>"></script>
<script type="text/javascript" src="<?php echo $this->url()?>js/prefixfree.min.js"></script>
<script>
$(function(){
	common.init();
});
</script>
</body>
</html>