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
	1 => 2,
];

// 旧UserId => 新UserId
$config['Users'] = [];

// 旧BlogCategoryId => 新BlogCategoryId
$config['importBlogCategory'] = [
	1 => 6,

];
// 旧BlogTagId => 新BlogTagId
$config['importBlogTag'] = [];


$config['BaserImporterCategory'] = array(
	'category' => array(
		'お知らせ' => 'トピックス',
	),
	'tag' => array(),
);
// 記事idの開始値（これより大きいIDが存在する場合、上書きされる可能性があります。）
$config['startId'] = 100;
