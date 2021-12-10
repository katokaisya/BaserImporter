BaserImporter
==========
baserCMSのブログ記事データをブログへ読み込みます。

Documentation
-------

phpMyadmin ＞ エクスポート ＞ PHP array にて
DBプレフィックス_blog_posts と DBプレフィックス_blog_posts_blog_tags を選んでエクスポートしたphpファイルを準備してください。

出力されたphpファイルをプラグイン/Config/data/フォルダ内に配置して下さい。

/Config/setting.php にて　各種設定をして下さい。

$prefix = 'DBプレフィックス';   ※必須
$config['importBlogContent'] = [旧BlogContentId => 新BlogContentId];　※必須
$config['Users'] = [旧UserId => 新UserId];
$config['importBlogCategory'] = [ 旧BlogCategoryId => 新BlogCategoryId ];　※必須
$config['importBlogTag'] = [旧BlogTagId => 新BlogTagId];

TODO
-------
管理画面から設定できるようにしたい

License
-------

Lincensed under the MIT lincense since version 2.0
