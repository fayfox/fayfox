<?php
return array(
	/**
	 * 文章创建前执行
	 */
	'before_post_create'=>array(
		array(
			'file'=>'TourRoute',
			'action'=>'addBox',
		),
	),
	/**
	 * 文章创建完成后执行
	 */
	'after_post_created'=>array(
		array(
			'file'=>'TourRoute',
			'action'=>'save',
		),
	),
	/**
	 * 文章更新完成后执行
	 */
	'after_post_updated'=>array(
		array(
			'file'=>'TourRoute',
			'action'=>'save',
		),
	),
	/**
	 * 文章更新前执行
	 */
	'before_post_update'=>array(
		array(
			'file'=>'TourRoute',
			'action'=>'setRoutes',
		),
		array(
			'file'=>'TourRoute',
			'action'=>'addBox',
		),
	),
	'after_controller_constructor'=>array(
		//Controller实例化后执行
		array(
			'router'=>'/^(admin)\/.*$/i',
			'file'=>'AdminMenu',
		),
		array(
			'router'=>'/^admin\/post\/(create|edit|index).*$/i',
			'file'=>'HideBoxes',
		),
	),
);