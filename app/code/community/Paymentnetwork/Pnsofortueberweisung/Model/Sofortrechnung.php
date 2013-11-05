<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Paymentnetwork
 * @package	Paymentnetwork_Sofortueberweisung
 * @copyright  Copyright (c) 2011 Payment Network AG
 * @author Payment Network AG http://www.payment-network.com (integration@payment-network.com)
 * @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version	$Id: Sofortrechnung.php 3844 2012-04-18 07:37:02Z dehn $
 */
require_once dirname(__FILE__).'/../Helper/library/sofortLib.php';

class Paymentnetwork_Pnsofortueberweisung_Model_Sofortrechnung extends Paymentnetwork_Pnsofortueberweisung_Model_Abstract
{
	
	/**
	* Availability options
	*/
	protected $_code = 'sofortrechnung'; 
	protected $_formBlockType = 'pnsofortueberweisung/form_sofortrechnung';
	protected $_infoBlockType = 'pnsofortueberweisung/info_sofortrechnung';
	protected $_canCapture = true;
	protected $_canCancelInvoice = true;
	protected $_canCapturePartial = true;
	protected $_canVoid = false;
	protected $_canRefund = true;
	protected $_isGateway = true;
	

	
	
	public function getUrl(){
		$order 		= $this->getOrder();
		$amount		= number_format($order->getGrandTotal(),2,'.','');
		$billing	= $order->getBillingAddress();
		$security 	= $this->getSecurityKey();
		$reason1 	= Mage::helper('pnsofortueberweisung')->__('Order No.: ').$order->getRealOrderId();
		$reason1 = preg_replace('#[^a-zA-Z0-9+-\.,]#', ' ', $reason1);
		$reason2 	= Mage::getStoreConfig('general/store_information/name');
		$reason2 = preg_replace('#[^a-zA-Z0-9+-\.,]#', ' ', $reason2);
		$success_url = Mage::getUrl('pnsofortueberweisung/sofort/return',array('orderId'=>$order->getRealOrderId(), '_secure'=>true));
		$cancel_url = Mage::getUrl('pnsofortueberweisung/sofort/error',array('orderId'=>$order->getRealOrderId()));
		$notification_url = Mage::getUrl('pnsofortueberweisung/sofort/notification',array('orderId'=>$order->getRealOrderId(), 'secret' =>$security));
	
		$sObj = new SofortLib_Multipay(Mage::getStoreConfig('payment/sofort/configkey'));
		$sObj->setVersion(self::MODULE_VERSION);
		$sObj->setAmount($amount, $order->getOrderCurrencyCode());
		$sObj->setReason($reason1, $reason2);
		$sObj->setSuccessUrl($success_url);
		$sObj->setAbortUrl($cancel_url);
		$sObj->setNotificationUrl($notification_url);
		$sObj->addUserVariable($order->getRealOrderId());
		$sObj->setEmailCustomer($order->getCustomerEmail());
		//$sObj->setPhoneNumberCustomer($order->getCustomerTelephone());
		

		$sObj->setSofortrechnung();
		
		$sObj->setSofortrechnungCustomerId($order->getCustomerId());
		$sObj->setSofortrechnungOrderId($order->getRealOrderId());
		
		$address = $order->getBillingAddress();
		$sObj->setSofortrechnungInvoiceAddress($address->getFirstname(), $this->_getLastname($address),
						$this->_getStreet($address), $this->_getNumber($address), $address->getPostcode(), $address->getCity(), $this->_getSalutation($address), $address->getCountryId());
						
		$address = $order->getShippingAddress();
		$sObj->setSofortrechnungShippingAddress($address->getFirstname(), $this->_getLastname($address),
						$this->_getStreet($address), $this->_getNumber($address), $address->getPostcode(), $address->getCity(), $this->_getSalutation($address), $address->getCountryId());


		//items
		$discountTax = 19;
		foreach ($order->getAllVisibleItems() as $item) {
			$sObj->addSofortrechnungItem(md5($item->getSku()), $item->getSku(), $item->getName(), $this->_getPriceInclTax($item), 0, $item->getDescription(), $item->getQtyOrdered(), $item->getTaxPercent());
			if($item->getTaxPercent() > 0)
				$discountTax = min($item->getTaxPercent(), $discountTax); //tax of discount is min of cart-items
		}	

		//shipping
		if($order->getShippingAmount() != 0) 
			$shippingTax = round($order->getShippingTaxAmount()/$order->getShippingAmount()*100);
		else 
			$shippingTax = 0;
		$sObj->addSofortrechnungItem(1, 1, $order->getShippingDescription(), $this->_getShippingInclTax($order), 1, '', 1, $shippingTax);
		
		//discount
		if($order->getDiscountAmount() != 0) {
			$sObj->addSofortrechnungItem(2, 2, Mage::helper('sales')->__('Discount'), $order->getDiscountAmount(), 2, '', 1, $discountTax);
		}

		$sObj->sendRequest();
		if(!$sObj->isError()) {
			$url = $sObj->getPaymentUrl();
			$tid = $sObj->getTransactionId();
			$order->getPayment()->setTransactionId($tid)->setIsTransactionClosed(0);			
			$order->getPayment()->setAdditionalInformation('sofort_transaction', $tid);
			$order->getPayment()->setAdditionalInformation('sofort_lastchanged', 0);
			$order->getPayment()->setAdditionalInformation('sofort_secret', $security)->save();
			Mage::getSingleton('checkout/session')->setData('sofort_aborted', 1);
			
			return $url; 
		} else {	
			$errors = $sObj->getErrors();
			foreach($errors as $error) 
				Mage::getSingleton('checkout/session')->addError(Mage::helper('pnsofortueberweisung')->localizeXmlError($error));

			return $cancel_url;
		}
	}

	/**
	 * Retrieve information from payment configuration
	 *
	 * @param   string $field
	 * @return  mixed
	 */
	public function getConfigData($field, $storeId = null)
	{

		return parent::getConfigData($field, $storeId);
	}
	
	private function _getStreet($address) {
		$street = trim(str_replace("\n", " ", $address->getStreetFull()));
		$matched = preg_match('#(.+)[ \.,](.+)#i', trim($street), $matches);
		if($matched && !empty($matches[1]) && !empty($matches[2]))
			return $matches[1];
		
		return $street;		
	}

	private function _getNumber($address) {
		$street = trim(str_replace("\n", " ", $address->getStreetFull()));
		$matched = preg_match('#(.+)[ \.,](.+)#i', trim($street), $matches);
		if($matched && !empty($matches[1]) && !empty($matches[2]))
			return $matches[2];
		
		return '';
	}
	
	private function _getLastname($address) {
		if($address->getCompany())
			return $address->getLastname() . " " . $address->getCompany();
		
		return $address->getLastname();
	}
	
	private function _getSalutation($address) {
		if($address->getCompany())
			return 1;

		return '';
	}
	
	/**
	 * check billing country is allowed for the payment method
	 *
	 * @return bool
	 */
	public function canUseForCountry($country)
	{
		//we only support DE right now
		return strtolower($country) == 'de' && parent::canUseForCountry($country);
	}	
	
	/**
	 * we deactivate this payment method if it was aborted before
	 * 
	 * @return bool
	 */
	public function canUseCheckout() {
		$aborted = Mage::getSingleton('checkout/session')->getData('sofort_aborted') == 1;
		
		return !$aborted && parent::canUseCheckout();
	}
	
	 /**
	 * Capture payment
	 *
	 * @param Mage_Sales_Model_Order_Payment $payment
	 * @return Mage_Paypal_Model_Payflowpro
	 */
	public function capture(Varien_Object $payment, $amount)
	{
		$tid = $payment->getAdditionalInformation('sofort_transaction');
		$payment->setTransactionId($tid);
		return $this;
	}	
	
	/**
	 * Refund money
	 *
	 * @param   Varien_Object $invoicePayment
	 * @return  Mage_GoogleCheckout_Model_Payment
	 */
	public function refund(Varien_Object $payment, $amount) {
		
		$tid = $payment->getAdditionalInformation('sofort_transaction');
		$order = $payment->getOrder();
		if(!empty($tid)) {
			$sObj = new SofortLib_ConfirmSr(Mage::getStoreConfig('payment/sofort/configkey'));
			$sObj->cancelInvoice($tid)->setComment('refund')->sendRequest();
			if($sObj->isError()) {
				Mage::throwException($sObj->getError());
			} else {
				$payment->setTransactionId($tid.'-refund')
					->setShouldCloseParentTransaction(true)
					->setIsTransactionClosed(0);		
			
				$order->addStatusHistoryComment(Mage::helper('pnsofortueberweisung')->__('The invoice has been canceled.'))->setIsVisibleOnFront(true);
				$order->save();
					
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pnsofortueberweisung')->__('Successfully canceled invoice. Credit memo created: %s', $tid));
				return $this;
			}		
		}
		
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pnsofortueberweisung')->__('Could not cancel invoice.'));	  	
		

		return $this;
	}
	
   
	/*
	 * workaround for magento < 1.4.1
	 */
	private function _getPriceInclTax($item)
	{
		if ($item->getPriceInclTax()) {
			return $item->getPriceInclTax();
		}
		$qty = ($item->getQty() ? $item->getQty() : ($item->getQtyOrdered() ? $item->getQtyOrdered() : 1));
		$price = (floatval($qty)) ? ($item->getRowTotal() + $item->getTaxAmount())/$qty : 0;
		return Mage::app()->getStore()->roundPrice($price);
	}

	/*
	 * workaround for magento < 1.4.1
	 */
	private function _getShippingInclTax($order) 
	{
		if($order->getShippingInclTax()) {
			return $order->getShippingInclTax();
		}
		
		$price = $order->getShippingTaxAmount()+$order->getShippingAmount();
		return Mage::app()->getStore()->roundPrice($price);
	}
}