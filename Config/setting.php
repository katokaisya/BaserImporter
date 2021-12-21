<?php
/**
 * [Config] WpImporter
 *
 */
// データベーステーブル用のプレフィックス baser4系で変更していなければ、'mysite_';
$prefix = 'mysite_';

define('LOG_BASERIMPORTER', 'baser_importer');

CakeLog::config('baser_importer', array(
	'engine' => 'FileLog',
	'types' => array('baser_importer'),
	'file' => 'baser_importer',
));

// php arrayでエクスポートしたテーブルファイルを/config/data/フォルダに配置しておく
include __DIR__ . DS . 'data' . DS . $prefix . 'blog_posts.php';
include __DIR__ . DS . 'data' . DS . $prefix . 'blog_posts_blog_tags.php';
$config['mysite_blog_posts'] = ${$prefix.'blog_posts'};
$config['mysite_blog_posts_blog_tags'] = ${$prefix.'blog_posts_blog_tags'};

// 旧BlogContentId => 新BlogContentId
$config['importBlogContent'] = [
	1 => 1,
	2 => 2,
];

// 旧UserId => 新UserId
$config['Users'] = [];

// 旧BlogCategoryId => 新BlogCategoryId
$config['importBlogCategory'] = [
	1 => 2,
];
// 旧BlogTagId => 新BlogTagId
$config['importBlogTag'] = [
	1 => 2
];

// 記事idの開始値（これより大きいIDが存在する場合、上書きされる可能性があります。）
$config['startId'] = 10;

// 旧サイトで記事概要を使っていて、新サイトで概要を使用しない場合は falseにする。
$config['contentsUse'] = true;


// 旧サイトでBurgerEditorを使っておらず、新サイトでBurgerEditorを使用する場合は trueにする。
$config['burgerEditor'] = true;
