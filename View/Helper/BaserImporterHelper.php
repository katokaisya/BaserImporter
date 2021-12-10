<?php
class BaserImporterHelper extends AppHelper {
/**
 * ヘルパー
 *
 * @var		array
 * @access	public
 */
	public $helpers = array('Html');
/**
 * Before Render
 *
 * @return	void
 * @access	public
 */
	public function beforeRender($viewFile) {
		parent::beforeRender($viewFile);
	}
}

