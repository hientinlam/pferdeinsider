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

class Simple_Forum_Block_Head extends Mage_Core_Block_Template
{
	
	protected function _prepareLayout()
	{
	    parent::_prepareLayout();
	}
	
	public function getTitle()
	{
		return Mage::getStoreConfig('forum/forumconfiguration/forum_title');
		//return Mage::helper('forum/topic')->__('Forum');		
	}
	
	public function isModerator()
	{
		return Mage::helper('forum/topic')->isModerator();
	}
	
	public function getDisplayRSS()
	{
		$action = $this->getRequest()->getActionName();
		if($action != 'edit')
		{
			return Mage::getStoreConfig('forum/forumconfiguration/allowrssfeeds');
		}
	}
	
	public function getRSSUrl()
	{
		return Mage::helper('forum/rss')->__getRSSUrl(); 
	}
	
	public function getShowTopBanner()
	{
		return Mage::getStoreConfig( 'forum/designsettings/showtopbanner' );
	}
	
	public function getTopBannerSrc()
	{
		return Mage::getStoreConfig( 'forum/designsettings/topbannerimage' );
	}
	
	public function getTopBannerIsLink()
	{
		if(Mage::getStoreConfig( 'forum/designsettings/topbannerlink' ) && Mage::getStoreConfig( 'forum/designsettings/topbannerlink' ) != '')
		{
			return true;
		}
	}
	
	public function getLinkTopBanner()
	{
		return Mage::getStoreConfig( 'forum/designsettings/topbannerlink' );
	}
	
	public function getTopLinkBannerBlank()
	{
		return Mage::getStoreConfig( 'forum/designsettings/topbannerlinkblank' );
	}
}
?>