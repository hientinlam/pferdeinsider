<?php
/**
 * Mageplace Facebook Connect
 *
 * @category	Mageplace_Facebook
 * @package		Mageplace_Facebook_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Facebook_Connect_Model_System_Config_Source_Size
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return array(
			array('value' => 'small',	'label' => 'small'),
			array('value' => 'medium',	'label' => 'medium'),
			array('value' => 'large',	'label' => 'large'),
			array('value' => 'xlarge',	'label' => 'xlarge'),
			/*array('value' => 'icon',	'label' => 'icon'),*/
		);
	}
}