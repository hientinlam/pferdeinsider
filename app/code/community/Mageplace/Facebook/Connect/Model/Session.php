<?php
/**
 * Mageplace Facebook Connect
 *
 * @category	Mageplace_Facebook
 * @package	 Mageplace_Facebook_Connect
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license	 http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Facebook_Connect_Model_Session extends Mage_Core_Model_Session_Abstract
{
	public function __construct()
	{
		$namespace = 'mageplace_facebook_connect_' . (Mage::app()->getStore()->getWebsite()->getCode());
		$this->init($namespace);
	}
	
	public function checkPost($order_id)
	{
		$orders = (array) $this->getData('facebookconnect_orders');
		if(array_key_exists($order_id, $orders)) {
			return true;
		}
		$orders[$order_id] = true;
		$this->setData('facebookconnect_orders', $orders);
		return false;
	}
	
	public function getConnectUrlState()
	{
		if (!$this->getData('connect_state_key')) {
			$this->setData('connect_state_key', Mage::helper('core')->getRandomString(16));
		}
		
		return $this->getData('connect_state_key');
	}
	
	public function validateConnectUrlState()
	{
		if (!($stateKey = Mage::app()->getFrontController()->getRequest()->getParam('state', null)) || $stateKey != $this->getConnectUrlState()) {
			return false;
		}
		
		return true;
	}
}
