<?php
/**
 * Mageplace Facebook Connect
 *
 * @category    Mageplace_Facebook
 * @package     Mageplace_Facebook_Connect
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Facebook_Connect_Block_Abstract extends Mage_Core_Block_Template
{
	public function getAttribute($name)
	{
		$value = Mage::helper('facebookconnect')->getCfg($name);

		return $value;
	}
	
	public function getLinkUrl()
	{
		return Mage::helper('facebookconnect')->getFacebook()->getConnectUrl();
	}

	public function getTitle()
	{
		$title = $this->getAttribute('title');

		return $title ? $title : Mage::helper('facebookconnect')->__('Sign in with Facebook');
	}

	protected function _toHtml()
	{
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			return '';
		}
		
		$html = parent::_toHtml();

		if(Mage::helper('facebookconnect')->isFacebookHtmlDisplayed()) {
			return $html;
		}
		
		Mage::helper('facebookconnect')->setFacebookHtmlDisplayed();
		
		$html = strval(Mage::helper('facebookconnect')->getFacebookHtml()) . $html;
		
		
		return $html;
	}
}