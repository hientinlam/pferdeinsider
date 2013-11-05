<?php

/**
 * Mageplace Facebook Connect
 *
 * @category    Mageplace_Facebook
 * @package     Mageplace_Facebook_Connect
 * @copyright   Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */
class Mageplace_Facebook_Connect_Model_Observer
{

    public function processCoreBlockAbstractToHtmlAfter($observer)
    {
        if (!$this->_isEnabled()) {
            return;
        }

        $nameInLayout = $observer->getBlock()->getNameInLayout();

        if ($nameInLayout == 'root') {
            if ($facebook = Mage::helper('facebookconnect')->getFacebook()) {
                $html = $facebook->insertXmlnsParams($observer->getTransport()->getHtml());
                $observer->getTransport()->setHtml($html);
            }

            return $this;
        }

        static $connect_block_displayed = false;
        if ($connect_block_displayed) {
            return;
        }

        if (($nameInLayout == 'customer_form_login') && $this->_isLoginPageButtonEnabled()) {
            $html = $this->_getLoginButtonHtml($observer);
            $observer->getTransport()->setHtml($html);
            $connect_block_displayed = true;
            return;
        }

        if (($nameInLayout == 'checkout.onepage.login') && $this->_isCheckoutPageButtonEnabled()) {
            $html = $this->_getCheckoutButtonHtml($observer);
            $observer->getTransport()->setHtml($html);
            $connect_block_displayed = true;
            return;
        }

        return $this;
    }

    public function processCoreBlockAbstractToHtmlBefore($observer)
    {
        $block = $observer->getBlock();
        if(($block instanceof Mage_Customer_Block_Account_Navigation) && Mage::helper('facebookconnect')->getPublishOrder())	{
			$block->addLink(
				'belitsoft_survey',
				'facebookconnect/manage/',
				Mage::helper('facebookconnect')->__('Facebook Connect')
			);
		}
     
     
     
     
    }
    
    
    protected function _isEnabled()
    {
        static $is_enabled = null;

        if (is_null($is_enabled)) {
            $is_enabled = Mage::helper('facebookconnect')->isEnabled();
        }

        return $is_enabled;
    }

    protected function _isLoginPageButtonEnabled()
    {
        static $login_page_enabled = null;

        if (is_null($login_page_enabled)) {
            $login_page_enabled = Mage::helper('facebookconnect')->isLoginPageButtonEnabled();
        }

        return $login_page_enabled;
    }

    public function _isCheckoutPageButtonEnabled()
    {
        static $checkout_page_enabled = null;

        if (is_null($checkout_page_enabled)) {
            $checkout_page_enabled = Mage::helper('facebookconnect')->isCheckoutPageButtonEnabled();
        }

        return $checkout_page_enabled;
    }

    protected function _getButtonHtmlCustom($observer)
    {
        $html = '';
        if ($block = $observer->getBlock()->getLayout()->getBlock('facebookconnect_button')) {
            $html .= $observer->getTransport()->getHtml() . $block->toHtml();
        }

        return $html;
    }

    protected function _getLoginButtonHtml($observer)
    {
        return $this->_getButtonHtmlCustom($observer);
    }

    protected function _getCheckoutButtonHtml($observer)
    {
        return $this->_getButtonHtmlCustom($observer);
    }

    public function processSalesOrderSaveAfter($observer)
    {			
	#		Mage::log('SAVE ORDER 1:'.$observer->getEvent()->getOrder()->getId());
			if (!Mage::helper('facebookconnect')->isEnabled()
					|| !($order = $observer->getEvent()->getOrder())
					|| !($order instanceof Mage_Sales_Model_Order)
					|| !Mage::helper('facebookconnect')->isPostEnabled($order)
					|| !$facebook = Mage::helper('facebookconnect')->getFacebook()){
				return $this;
			}
			
			
			if (!Mage::helper('facebookconnect')->getPublishOrder()){
				return $this;
			}

			#		Mage::log('SAVE ORDER 2:'.$order->getId());
			
			
			$order_state = $order->getStatus() ? $order->getStatus() : $order->getState();
			if (!$order_state) {
				$order_state = (string) $order->getConfig()->getStateDefaultStatus($order->getState());
			}

	#		Mage::log('ORDER('.$order->getId().') STATUS:'.$order_state);

			if ($order_state != Mage::helper('facebookconnect')->getPostOrderStatus()) {
				return $this;
			}

	#		Mage::log('SAVE ORDER 3:'.$order->getId());

			Mage::getModel('facebookconnect/order')->setFacebook(Mage::helper('facebookconnect')->getFacebook())->post($order);
	#		Mage::log('SAVE ORDER 4:'.$order->getId());

			return $this;
    }

}