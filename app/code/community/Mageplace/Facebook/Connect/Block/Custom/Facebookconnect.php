<?php
/**
 * Mageplace Facebook Connect
 *
 * @category    Mageplace_Facebook
 * @package     Mageplace_Facebook_Connect
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Facebook_Connect_Block_Custom_Facebookconnect extends Mageplace_Facebook_Connect_Block_Customer_Facebookconnect
{
	protected function _toHtml()
	{
		if(Mage::helper('facebookconnect')->isCustomButtonEnabled()) {
			return parent::_toHtml();
		} else {
			return '';
		}
    }	
}
