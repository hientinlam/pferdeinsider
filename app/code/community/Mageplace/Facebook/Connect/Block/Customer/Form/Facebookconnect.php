<?php
/**
 * Mageplace Facebook Connect
 *
 * @category    Mageplace_Facebook
 * @package     Mageplace_Facebook_Connect
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Facebook_Connect_Block_Customer_Form_Facebookconnect extends Mage_Customer_Block_Account_Dashboard // Mage_Core_Block_Template
{

	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('facebookconnect/customer/form/facebookconnect.phtml');
	}

	public function getIsPostToFBWall()
	{
		return Mage::helper('facebookconnect')->isPostEnabled();
	}

	public function getAction()
	{
		return $this->getUrl('*/*/save', array('_secure' => Mage::helper('facebookconnect')->isHttps()));
	}

}
