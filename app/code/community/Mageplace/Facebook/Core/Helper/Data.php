<?php

/**
 * Mageplace Facebook Core
 *
 * @category	Mageplace_Facebook
 * @package		Mageplace_Facebook_Core
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */
class Mageplace_Facebook_Core_Helper_Data extends Mage_Core_Helper_Abstract
{

	const EXTENSION_CONNECT = 'facebookconnect';
	const EXTENSION_LIKE = 'facebookilike';
	const EXTENSION_SHARE = 'facebookshare';
	const EXTENSION_COMMENTS = 'facebookcomments';
	const EXTENSION_SUBSCRIBE = 'facebooksubscribe';
	const TAB_CONTENT = 'content';
	const TAB_GENERAL = 'general';
	const VAR_APP_ID = 'app_id';
	const VAR_APP_SECRET = 'app_secret';
	const VAR_SHOW_IN_CMS = 'show_in_cms';
	const VAR_SHOW_IN_HOME = 'show_in_home';
	const VAR_SHOW_IN_CATEGORY = 'show_in_category';
	const VAR_SHOW_IN_PRODUCT = 'show_in_product';
	const VAR_SHOW_CUSTOM = 'show_custom';
	const VAR_SHOW_WIDGET = 'show_widget';
	const VAR_HREF = 'href';
	const VAR_URL = 'url';
	const VAR_LOCAL = 'local';
	const VAR_WIDTH = 'width';
	const VAR_HEIGHT = 'height';
	const VAR_SIZE = 'size';
	const LANGUAGE = 'language';

	static protected $EXTENSIONS = array(
		self::EXTENSION_CONNECT,
		self::EXTENSION_LIKE,
		self::EXTENSION_COMMENTS,
		self::EXTENSION_SHARE,
		self::EXTENSION_SUBSCRIBE,
	);

	public function getCfg($config, $default = null, $tab = null, $extension = null)
	{
		if (is_null($extension)) {
			$extension = $this->getExtension();
		}

		if (is_null($tab)) {
			$tab = self::TAB_GENERAL;
		}

		switch ($config) {
			case (self::VAR_APP_SECRET):
				$extension = self::EXTENSION_CONNECT;
				break;

			case (self::VAR_SHOW_IN_CMS):
			case (self::VAR_SHOW_IN_HOME):
			case (self::VAR_SHOW_IN_CATEGORY):
			case (self::VAR_SHOW_IN_PRODUCT):
			case (self::VAR_SHOW_CUSTOM):
			case (self::VAR_SHOW_WIDGET):
				$tab = self::TAB_CONTENT;
				break;
		}

		$value = Mage::getStoreConfig($extension . '/' . $tab . '/' . $config, Mage::app()->getStore());




		switch ($config) {
			case (self::VAR_HREF):
				$value = $value ? $value : Mage::helper('core/url')->getCurrentUrl();
				$value = rawurlencode($value);
				break;

			case (self::VAR_URL):
				$value = $value ? $value : Mage::helper('core/url')->getCurrentUrl();
				break;

			case (self::VAR_LOCAL):
				$value = $value ? $value : Mage::app()->getLocale()->getDefaultLocale();
				break;

			case (self::VAR_SHOW_IN_CMS):
			case (self::VAR_SHOW_IN_HOME):
			case (self::VAR_SHOW_IN_CATEGORY):
			case (self::VAR_SHOW_IN_PRODUCT):
			case (self::VAR_SHOW_CUSTOM):
			case (self::VAR_SHOW_WIDGET):
				$value = intval($value);
				break;

			case (self::VAR_APP_ID):
				if (!$value) {
					foreach (self::$EXTENSIONS as $ext) {
						if (($ext != $extension) && ($value = Mage::getStoreConfig($ext . '/' . self::TAB_GENERAL . '/' . $config, Mage::app()->getStore()))) {
							break;
						}
					}
				}
				break;
		}

		if (!$value && !is_null($default)) {
			$value = $default;
		}

		if (is_null($value)) {
			$value = '';
		}
		return $value;
	}

	public function getFacebook()
	{
		$facebook = Mage::registry('facebookcore_facebook_object');
		if (is_null($facebook)) {
			$facebook = Mage::getModel('facebookcore/facebook')
					->setAppId($this->getCfg(Mageplace_Facebook_Core_Helper_Data::VAR_APP_ID))
					->setSecret($this->getCfg(Mageplace_Facebook_Core_Helper_Data::VAR_APP_SECRET))
					->setCookie(true)
					->init();

			Mage::register('facebookcore_facebook_object', $facebook);
		}

		return $facebook;
	}

	public function setExtension($extension)
	{
		Mage::unregister('facebookcore_facebook_extension');
		Mage::register('facebookcore_facebook_extension', $extension);
	}

	public function getExtension()
	{
		static $extension = null;

		if (is_null($extension)) {
			$extension = Mage::registry('facebookcore_facebook_extension');
		}

		return $extension;
	}

	public function checkMetaTag($meta)
	{
		return $this->getFacebook()->isMetaAdded($meta);
	}

	public function addMetaTag($meta)
	{
		$this->getFacebook()->addMeta($meta);
	}

	function isHttps()
	{
		return strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? true : false; /* Mage::getModel('core/url')->getSecure(); */
	}

}
