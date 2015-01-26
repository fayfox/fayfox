<?php
/**
 * url重写（不要改以后设置，否则可能导致系统无法正常运行）
 * 当然你也可以在服务器商做这些设置，当服务器不方便设置的时候，这里更方便程序员掌控。
 */
return array(
	'/^a$/'=>'admin/login/index',
	'/^widget\/(\w+)\/(.*)$/'=>'widget/load/name/$1/action/$2',//widget加载
	'/^widget\/(\w+)$/'=>'widget/load/name/$1',//widget加载
	'/^admin\/widget\/load\/(\w+)$/'=>'admin/widget/load/name/$1',
	'/^tools$/'=>'tools/index/index',//工具
	
	'/^tools\/analyst$/'=>'tools/analyst/js',//访问统计
	
	//图片显示
	'/^file\/pic(.*)$/'=>'tools/file/pic$1',
	'/^file\/vcode(.*)$/'=>'tools/file/vcode$1',
	'/^file\/qrcode(.*)$/'=>'tools/file/qrcode$1',
	'/^file\/download(.*)$/'=>'tools/file/download$1',
	
	'/^redirect(.*)$/'=>'tools/redirect/index$1',
);