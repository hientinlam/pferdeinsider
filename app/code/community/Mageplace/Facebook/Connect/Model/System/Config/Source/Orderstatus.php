<?php
/**
 * Mageplace Facebook Connect
 *
 * @category	Mageplace_Facebook
 * @package		Mageplace_Facebook_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Facebook_Connect_Model_System_Config_Source_Orderstatus
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return Mage::getSingleton('sales/order_config')->getStatuses();
	}

}