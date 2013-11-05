<?php
/**
 * Mageplace Facebook Connect
 *
 * @category	Mageplace_Facebook
 * @package		Mageplace_Facebook_Connect
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Facebook_Connect_LoginController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		if(!Mage::helper('facebookconnect')->isEnabled()) {
			$this->_forward('noRoute');
			return;
		}

		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->_redirectUrl(Mage::helper('customer')->getDashboardUrl());
			return;
		}
		
		$isPopup = $this->getRequest()->getParam(Mageplace_Facebook_Connect_Model_Connect::FBCONNECTTYPE) ? false : true;
		
		try {
			$facebook = Mage::helper('facebookconnect')->getFacebook();
			if(!$facebook->getUserInfo()) {
				$facebook = $facebook->getFacebookAfterLogin();
			}
			
			if(!$facebook->getUserInfo()) {
				$redirectUrl = $facebook->getConnectUrl(null);
				$this->_redirectUrl($redirectUrl);
				return;
			}

			$customer = Mage::getModel('facebookconnect/customer')
				->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
				->setFacebook($facebook)
				->loadByFacebookEmail();

			if(!$customer->getId()) {
				$customer->create();
			}

			if($customer->getId()) {
				$customer->login();
			}

			$return = base64_decode($this->getRequest()->getPost('return'));
			
			if($isPopup) {
				echo '<script>window.opener.location.reload();</script>';	
			} else {
				$url = $return ? $return : Mage::helper('customer')->getDashboardUrl();
				$this->_redirectUrl($url);
			}
			
		} catch(Exception $e){
			Mage::logException($e);
			$message = Mage::helper('facebookconnect')->__('Problem with login via Facebook!'); 
//			Mage::getSingleton('customer/session')->addError($message);
			if($isPopup) {
				echo '<script>window.opener.location.reload();</script>';	
			} else {
				$this->_redirectUrl( Mage::helper('customer')->getDashboardUrl());
			}
		}
		
		if($isPopup) {
			echo "<script>window.close();</script>";
		}
	}
}
