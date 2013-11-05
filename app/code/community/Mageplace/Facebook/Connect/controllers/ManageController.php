<?php
/**
 * Mageplace Facebook Connect
 *
 * @category    Mageplace_Facebook
 * @package     Mageplace_Facebook_Connect
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Facebook_Connect_ManageController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Action predispatch
	 *
	 * Check customer authentication for some actions
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		if (!Mage::getSingleton('customer/session')->authenticate($this)) {
			$this->setFlag('', 'no-dispatch', true);
		}
	}

	public function indexAction()
	{
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');

		if ($block = $this->getLayout()->getBlock('customer_facebookconnect')) {
			$block->setRefererUrl($this->_getRefererUrl());
		}
		$this->getLayout()->getBlock('head')->setTitle($this->__('Facebook Connect'));
		$this->renderLayout();
	}

	public function saveAction()
	{
		if (!$this->_validateFormKey()) {
			return $this->_redirect('customer/account/');
		}
		try {
			$fb_wall_post = (boolean)$this->getRequest()->getParam('fb_wall_post', false);
			Mage::getSingleton('customer/session')->getCustomer()
			->setStoreId(Mage::app()->getStore()->getId())
			->setData('fb_wall_post', $fb_wall_post)
			->save();
			if ($fb_wall_post) {
				Mage::getSingleton('customer/session')->addSuccess($this->__('The parameter has been saved.'));
			} else {
				Mage::getSingleton('customer/session')->addSuccess($this->__('The parameter has been removed.'));
			}

		} catch (Exception $e) {
			Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving your parameter.'));
		}
		$this->_redirect('customer/account/');
	}
}
