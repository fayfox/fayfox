<?php
use fayfox\models\Option;
?>
<footer id="footer">
	<div class="copy-right">
		<div class="w1000">
			<p class="cp"><?php echo Option::get('shine_color_copyright')?></p>
			<p class="beian"><?php echo Option::get('shine_color_beian')?>  技术支持：<a href="http://www.siwi.me" target="_blank">Siwi.Me</a></p>
		</div>
	</div>
</footer>
<script type="text/javascript" src="<?php echo $this->url()?>js/custom/analyst-min.js"></script>
<script>_fa.init();</script>