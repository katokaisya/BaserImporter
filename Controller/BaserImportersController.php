<?php
App::import('Controller', 'Plugins');
class BaserImportersController extends AppController {
/**
 * クラス名
 *
 * @var		string
 * @access	public
 */
	public $name = 'BaserImporters';
/**
 * モデル
 *
 * @var		array
 * @access	public
 */
	public $uses = array(
		'Blog.BlogContent',
		'Blog.BlogPost',
		'Blog.BlogCategory',
		'Blog.BlogComment',
		'Blog.BlogPostsBlogTag',
		'Content',
		'SiteConfig',
	);
/**
 * コンポーネント
 *
 * @var		array
 * @access	public
 */
	public $components = array(
		'BcAuth',
		'Cookie',
		'BcAuthConfigure'
	);
/**
 * サブメニューエレメント
 *
 * @var 	array
 * @access 	public
 */
	public $subMenuElements = array('baser_importer');
/**
 * ぱんくずナビ
 *
 * @var string
 */
	public $crumbs = array(
		array(
			'name' => 'baser Importer',
			'url' => array(
				'plugin' => 'baser_importer',
				'controller' => 'baser_importers',
				'action' => 'index'
			)
		)
	);
/**
 * ページタイトル
 *
 * @var string
 */
	public $pageTitle = 'baser Importer';

/**
 * 各種設定値
 */
	public $blogContent;
	public $userList;

/**
 * [ADMIN]
 *
 * @return	void
 * @access	public
 */
	public function admin_index() {
		if($this->request->data) {
				$blogContentId = $this->request->data['BaserImporter']['blog_content_id'];
				$clearData = $this->request->data['BaserImporter']['clear_data'];

				if ($this->blog_import($blogContentId, $clearData)) {
					$this->setMessage('ファイルの読み込みに成功しました。');
				} else {
					$this->setMessage('ファイルの読み込みに失敗しました。', true);
				}
		}

		$contents = $this->Content->find('all', array(
			'conditions' => array(
				'Content.plugin' => 'Blog',
				'Content.type' => 'BlogContent',
				'OR' => array(
					array('Content.alias_id' => ''),
					array('Content.alias_id' => NULL),
				),
			),
			'order' => array(
				'Content.entity_id'
			),
		));
		// 名サイトとサブサイトで同じ名称のブログが有る可能性があるので、
		// プルダウンにサイト名を付加
		foreach($contents as $content) {
			if ($content['Site']['id']) {
				$siteName = $content['Site']['name'];
			} else {
				$bcSite = Configure::read('BcSite');
				$siteName = $bcSite['main_site_display_name'];
			}
			$blogContents[$content['Content']['entity_id']] = sprintf(
				'%s : %s',
				$siteName,
				$content['Content']['title']
			);
		}

		$this->set('blogContents', $blogContents);

	}

	private function blog_import($blogContentId, $clearData = 1) {

		set_time_limit(0);
		ini_set('memory_limit', -1);
		ini_set("max_execution_time",0);
		ini_set("max_input_time",0);
		clearAllCache();


		if ($clearData) {
			$db = ConnectionManager::getDataSource($this->BlogPost->useDbConfig);
			$dbPrefix = $db->config['prefix'];
			$this->BlogPost->deleteAll(array('BlogPost.blog_content_id' => $blogContentId), false);
			$this->BlogComment->deleteAll(array('BlogComment.blog_content_id' => $blogContentId), false);
			$this->BlogPost->query('ALTER TABLE `' . $dbPrefix . 'blog_posts` AUTO_INCREMENT=1;');
			$this->BlogCategory->query('ALTER TABLE `' . $dbPrefix . 'blog_categories` AUTO_INCREMENT=1;');
			$this->BlogComment->query('ALTER TABLE `' . $dbPrefix . 'blog_comments` AUTO_INCREMENT=1;');
		}

		$ret = false;
		if (empty($blogContentId)) {
			return $ret;
		}
		$this->makePostData($blogContentId);

		$this->blogContent = $this->BlogContent->find('first', array(
			'conditions' => array(
				'BlogContent.id' => $blogContentId,
			),
			'recursive' => -1,
		));
		$this->userList = $this->User->find('list', array(
			'fields' => array('real_name_1', 'id'),
			'recursive' => -1,
		));
		$ret = true;
		clearAllCache();


		if (Configure::read('BaserImporter.redirect_text')) {
			// リダイレクト設定をファイルに書き出す
			$targetFilePath = TMP .'redirect_string.txt';
			if (file_exists($targetFilePath)) {
				$File = new File($targetFilePath);
				$File->delete();
				$File = new File($targetFilePath, true, 0777);
			} else {
				$File = new File($targetFilePath, true, 0777);
			}
			$File->append($this->redirectString);
		}

		return $ret;
	}

	private function makePostData($blogContentId) {
		$blogPosts = Configure::read('mysite_blog_posts');
		$blogPostsBlogTags = Configure::read('mysite_blog_posts_blog_tags');
		$blogContents = Configure::read('importBlogContent');
		$blogCategory = Configure::read('importBlogCategory');
		$users = Configure::read('Users');
		$redirect = [];
		foreach ($blogPosts as $key => $blogPost) {
			$blogPosts[$key]['id'] += Configure::read('startId');
			switch ($blogPost['blog_content_id']) {
				case '1':
					$redirect[$key] = 'RewriteRule ^news/archives/'. $blogPost['no']. ' /joho/news/archives/'. $blogPost['no']. ' [R=302,L]';
					break;
				default:
					break;
			}
			// カテゴリ置換
			if (array_key_exists($blogPost['blog_category_id'], $blogCategory)) {
				$blogPosts[$key]['blog_category_id'] = $blogCategory[$blogPost['blog_category_id']];
			}

			// ユーザー置換
			if (array_key_exists($blogPost['user_id'], $users)) {
				$blogPosts[$key]['user_id'] = $users[$blogPost['user_id']];
			}
			// ブログエンティティID 置換
			$blogPosts[$key]['blog_content_id'] = $blogContents[$blogPost['blog_content_id']];

			// BurgerEditorを使っている場合は1カラムテキストに入れる。
			$contentsPrefix = Configure::read('burgerEditor') ? '<div data-bgb="wysiwyg" class="bgb-wysiwyg"><div data-bgt="ckeditor" data-bgt-ver="2.1.0" class="bgt-container bgt-ckeditor-container show"><div class="bge-ckeditor" data-bge="ckeditor">' : '';
			$contentsSafix = Configure::read('burgerEditor') ? '</div></div></div>' : '';

			// 記事詳細にコンテンツを集める
			if (Configure::read('contentsUse') == false) {
				$blogPosts[$key]['detail'] = $contentsPrefix. $blogPost['content']. $contentsSafix;
				$blogPosts[$key]['detail'] .= $contentsPrefix. $blogPost['detail']. $contentsSafix;
				$blogPosts[$key]['content'] = '';
			} else {
				$blogPosts[$key]['content'] = $blogPost['content'];
				$blogPosts[$key]['detail'] = $blogPost['detail'] ? $contentsPrefix. $blogPost['detail']. $contentsSafix : '';
			}

			// 保存処理
			$this->BlogPost->create();
			$ret = $this->BlogPost->saveAll($blogPosts[$key], array('callbacks' => false));

		}

		// タグの付け直し
		if (!empty($blogPostsBlogTags)) {
			foreach ($blogPostsBlogTags as $i => $blogPostsBlogTag) {
				$blogPostsBlogTags[$i]['blog_post_id'] += Configure::read('startId');
				$blogPostsBlogTags[$i]['blog_tag_id'] += 100;
				$this->BlogPostsBlogTag->create();
				$ret = $this->BlogPostsBlogTag->saveAll($blogPostsBlogTags[$i], array('callbacks' => false));
			}
		}
		$this->log(implode("\n", $redirect));
		return $ret;
	}



}
