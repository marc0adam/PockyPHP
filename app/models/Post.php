<?php
class Post extends ModModel {
	var $belongsTo = array(
		'Poster' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		)
	);
	
	var $hasMany = array(
		'Comments' => array(
			'className' => 'Comment',
			'foreignKey' => 'post_id',
			'order' => 'time_stamp'
		)
	);
	
	var $hasAndBelongsToMany = array(
		'Categories' => array(
			'className' => 'Category',
			'joinTable' => 'categories_posts',
			'foreignKey' => 'post_id',
			'associationForeignKey' => 'category_id',
			'order' => 'name'
		)
	);
}
