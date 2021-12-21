<h4>baserCMS ブログ記事 インポート</h4>


<?php echo $this->BcForm->create('BaserImporter', array('type' => 'file')) ?>

<table cellpadding="0" cellspacing="0" class="list-table">
	<tbody>
		<tr>
			<th class="col-head" width="25%"><?php echo $this->BcForm->label('BaserImporter.blog_content_id', 'ブログの指定') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BaserImporter.blog_content_id', array('type' => 'select', 'options' => $blogContents)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpBlogContentId', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('BaserImporter.blog_content_id') ?>
				<div id="helptextBlogContentId" class="helptext">
					<ul>
						<li>初期化するブログを指定できます。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head" width="25%"><?php echo $this->BcForm->label('BaserImporter.clear_data', 'ブログ記事の初期化') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BaserImporter.clear_data', array('type' => 'checkbox', 'label' => 'インポート前にブログ記事を初期化する')) ?>
				<br /><small>インポート前にインポート先のブログ記事を初期化する場合はチェックを入れてください。</small>
				<br /><small>初期化を指定すると、選択したブログの記事を削除した上で、AUTO_INCREMENTの値を、次のデータが最大値になるように調整します。</small>
			</td>
		</tr>
	</tbody>
</table>
<div class="submit bca-actions">
<?php echo $this->BcForm->submit('インポート', array('class' => 'button bca-btn bca-actions__item', 'data-bca-btn-type' => "save", 'data-bca-btn-size' => "lg")) ?>
</div>

<?php echo $this->BcForm->end() ?>
