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
 
class Simple_Forum_Helper_Sitemap extends Mage_Core_Helper_Abstract
{
	
	const PAGE_VAR_NAME             = 'p';
	const SORT_VAR_NAME             = 'sort';
	const LIMIT_VAR_NAME            = 'limit';
		
	public function __getUrl($o, $limit, $page)
	{
		return $this->_getUrl($o->getUrl_text(), array( '_current'=>false, '_escape'=>false, '_use_rewrite'=>false,'_query'=>array(self::PAGE_VAR_NAME => $page, self::SORT_VAR_NAME => 1, self::LIMIT_VAR_NAME => $limit)));
	}
}