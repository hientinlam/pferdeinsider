<?php
/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$installer->run("

ALTER TABLE {$this->getTable('forum_topic')}
	ADD `url_text_short` TEXT NOT NULL AFTER `url_text` ;
");

$c = Mage::getModel('forum/topic')->getCollection();
if($c->getSize())
{
	foreach($c as $val)
	{
		$id_path = Mage::helper('forum/topic')->buildIdForumPath($val->getId());
		$m = Mage::getModel('forum/topic')->load($val->getId());
		$m->setUrl_text_short($val->getUrl_text());
		$new_url_text = 'forum/' . $val->getUrl_text();
		$m->setUrl_text($new_url_text);
		$m->save();
		Mage::helper('forum/topic')->updateUrlRewriteRequestPath($id_path, $new_url_text);
	}
}


$installer->endSetup();
