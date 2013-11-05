<?php
/**
 * Mageplace Facebook Connect
 *
 * @category    Mageplace_Facebook
 * @package     Mageplace_Facebook_Connect
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Facebook_Connect_Block_Widget_Facebookconnect
	extends Mageplace_Facebook_Connect_Block_Abstract
	implements Mage_Widget_Block_Interface
{
	public function getTitle()
	{
		$title = $this->getData('title');
		
		return $title ? $title : parent::getTitle();
	}
	
	protected function _toHtml()
	{
		if(Mage::helper('facebookconnect')->isWidgetButtonEnabled()) {
			return parent::_toHtml();
		} else {
			return '';
		}
    }	
}
