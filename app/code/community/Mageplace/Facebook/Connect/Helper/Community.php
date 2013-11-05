<?php
/**
 * Mageplace Facebook Connect
 *
 * @category    Mageplace_Facebook
 * @package     Mageplace_Facebook_Connect
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Facebook_Connect_Helper_Community extends Mageplace_Facebook_Core_Helper_Data
{
	const ENABLE_EXTENSION = 'enable_extension';
	const TAB_SINGUP = 'singup';
	const TAB_CREATE_ACCOUNT = 'create_account';
	const VAR_ENABLE_MESSAGE = 'singup_enable_message';
	const VAR_IMAGE = 'singup_image';
	const VAR_MESSAGE = 'singup_message';
	const VAR_NAME = 'singup_name';
	const VAR_DESCRIPTION = 'singup_description';
	const VAR_SHOW_IN_LOGIN = 'show_in_login';
	const VAR_SHOW_IN_CHECKOUT = 'show_in_checkout';
	const VAR_SHOW_CUSTOM = 'show_custom';
	const VAR_SHOW_WIDGET = 'show_widget';
	const VAR_SHOW_FACES = 'show_faces';
	const VAR_MAX_ROWS = 'max_rows';
	const VAR_ENABLE_NOTIFY = 'enable_notify';
	const VAR_EMAIL_TEMPLATE = 'email_template';
	const VAR_EMAIL_IDENTITY = 'email_identity';

	public function __construct()
	{
		$this->setExtension(Mageplace_Facebook_Core_Helper_Data::EXTENSION_CONNECT);
	}

	public function isEnabled()
	{
		static $is_enabled = null;

		if (is_null($is_enabled)) {
			$is_enabled = $this->getCfg(self::ENABLE_EXTENSION, false);
		}

		return $is_enabled;
	}

	public function isLoginPageButtonEnabled()
	{
		static $is_enabled = null;

		if (is_null($is_enabled)) {
			$is_enabled = $this->isEnabled() && $this->getCfg(self::VAR_SHOW_IN_LOGIN, false);
		}

		return $is_enabled;
	}

	public function isCheckoutPageButtonEnabled()
	{
		static $is_enabled = null;

		if (is_null($is_enabled)) {
			$is_enabled = $this->isEnabled() && $this->getCfg(self::VAR_SHOW_IN_CHECKOUT, false);
		}

		return $is_enabled;
	}

	public function isCustomButtonEnabled()
	{
		static $is_enabled = null;

		if (is_null($is_enabled)) {
			$is_enabled = $this->isEnabled() && $this->getCfg(self::VAR_SHOW_CUSTOM, false);
		}

		return $is_enabled;
	}

	public function isWidgetButtonEnabled()
	{
		static $is_enabled = null;

		if (is_null($is_enabled)) {
			$is_enabled = $this->isEnabled() && $this->getCfg(self::VAR_SHOW_WIDGET, false);
		}

		return $is_enabled;
	}

	public function isPostEnabled($order = null)
	{
		if (!Mage::app()->getStore()->isAdmin()) {
			return (Mage::getSingleton('customer/session')->getCustomer()->getData('fb_wall_post') ? true : false);
		} else {
			if (!($customerId = $order->getCustomerId())
					|| !($customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->load($customerId))
					|| !($customer instanceof Mage_Customer_Model_Customer)) {
				return false;
			}

			return ($customer->getData('fb_wall_post') ? true : false);
		}
	}

	public function getPostOrderStatus()
	{
		if (!$orderstatus = $this->getCfg('orderstatus')) {
			return Mage_Sales_Model_Order::STATE_COMPLETE;
		}

		return $orderstatus;
	}
	
	public function getPublishOrder()
	{
		return $this->getCfg('publish_order');
	}
	
	

	public function getCfg($config, $default = null, $tab = null, $extension = null)
	{
		switch ($config) {
			case self::VAR_ENABLE_MESSAGE:
			case self::VAR_IMAGE:
			case self::VAR_MESSAGE:
			case self::VAR_NAME:
			case self::VAR_DESCRIPTION:
				$tab = self::TAB_SINGUP;
				break;

			case self::VAR_EMAIL_IDENTITY:
			case self::VAR_EMAIL_TEMPLATE:
			case self::VAR_ENABLE_NOTIFY:
				$tab = self::TAB_CREATE_ACCOUNT;
				break;


			case self::VAR_SHOW_IN_LOGIN:
			case self::VAR_SHOW_IN_CHECKOUT:
			case self::VAR_SHOW_CUSTOM:
			case self::VAR_SHOW_WIDGET:
				$tab = parent::TAB_CONTENT;
				break;

			case self::ENABLE_EXTENSION:
				$tab = parent::TAB_GENERAL;
				break;
		}

		$value = parent::getCfg($config, $default, $tab, $extension);

		switch ($config) {
			case self::VAR_SHOW_IN_LOGIN:
			case self::VAR_SHOW_IN_CHECKOUT:
			case self::VAR_SHOW_CUSTOM:
			case self::VAR_SHOW_WIDGET:
			case self::VAR_ENABLE_MESSAGE:
			case self::ENABLE_EXTENSION:
				$value = (bool) $value;
				break;

			case self::VAR_IMAGE:
			case self::VAR_MESSAGE:
			case self::VAR_NAME:
			case self::VAR_DESCRIPTION:
				$value = trim(strval($value));
				break;
		}


		return $value;
	}

	public function setFacebookHtmlDisplayed($set = true)
	{
		Mage::register('isFacebookHtmlGenerated', $set);
	}

	public function isFacebookHtmlDisplayed()
	{
		return Mage::registry('isFacebookHtmlGenerated');
	}

	public function getFacebookHtml()
	{
		if ($facebook = $this->getFacebook()) {
			return $facebook->getFacebookHtml() . $facebook->getFacebookLoginEventHtml();
		}

		return null;
	}

	public function getFacebook($facebook_reset=null)
	{
		static $facebook = null;
		
		if($facebook_reset instanceof Mageplace_Facebook_Connect_Model_Connect) {
			$facebook = $facebook_reset;
		} else if (is_null($facebook)) {
			$facebook = Mage::getModel('facebookconnect/connect')
				->setAppId(Mage::helper('facebookconnect')->getCfg('app_id'))
				->setSecret(Mage::helper('facebookconnect')->getCfg('app_secret'))
				->setCookie(true)
				->init();
		}

		return $facebook;
	}

}
