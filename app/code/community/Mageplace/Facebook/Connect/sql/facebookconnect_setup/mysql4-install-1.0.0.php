<?php
/**
 * Mageplace Facebook Connect
 *
 * @category    Mageplace_Facebook
 * @package     Mageplace_Facebook_Connect
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

$installer = $this;

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute(
	'customer',
	'fb_wall_post',
	array(
		'type'		=> 'int',
		'label'		=> 'Post order details to Facebook Wall',
		'input'		=> 'select',
		'source'	=> 'eav/entity_attribute_source_boolean',
		'default'  	=> '1',
		'visible'	=> true,
		'required' 	=> false,
	)
);

$eavConfig = Mage::getSingleton('eav/config');
$attribute = $eavConfig->getAttribute('customer', 'fb_wall_post');
$attribute->setData('used_in_forms', array('customer_account_edit','customer_account_create','adminhtml_customer'));
$attribute->save();