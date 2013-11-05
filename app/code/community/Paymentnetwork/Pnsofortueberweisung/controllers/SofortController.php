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
 * @version	$Id: SofortController.php 3844 2012-04-18 07:37:02Z dehn $
 */
require_once dirname(__FILE__).'/../Helper/library/sofortLib.php';

class Paymentnetwork_Pnsofortueberweisung_SofortController extends Mage_Core_Controller_Front_Action
{
	
	protected $_redirectBlockType = 'pnsofortueberweisung/pnsofortueberweisung';
	protected $mailAlreadySent = false;
		
	/**
	 * when customer selects payment method
	 */
	public function redirectAction()
	{
		$session = $this->getCheckout();
		Mage::log($session); 
		$session->setSofortQuoteId($session->getQuoteId());
		$order = Mage::getModel('sales/order');
		$order->loadByIncrementId($session->getLastRealOrderId());
		$order->addStatusHistoryComment(Mage::helper('pnsofortueberweisung')->__('Sofortueberweisung payment loaded'))->setIsVisibleOnFront(false);
		$order->save();

		$payment = $order->getPayment()->getMethodInstance();
		$url = $payment->getUrl();
		$this->getResponse()->setRedirect($url);
		
		$session->unsQuoteId();
	}
	
	/**
	 * when customer returns after transaction
	 */
	public function returnAction()
	{
		if (!$this->getRequest()->isGet()) {
			$this->norouteAction();
			return;
		}
		$response = $this->getRequest()->getParams();	

		$session = $this->getCheckout();	
		$session->setQuoteId($session->getSofortQuoteId(true));
		$session->getQuote()->setIsActive(false)->save();
		$session->setData('sofort_aborted', 0);
		
		if(!$response['orderId']) {
			$this->_redirect('pnsofortueberweisung/pnsofortueberweisung/errornotice');
		} else {
			$this->_redirect('checkout/onepage/success', array('_secure'=>true));
		}
	}
	
	/**
	 * Customer returns after sofortvorkasse transaction
	 */
	public function returnSofortvorkasseAction() 
	{
		$response = $this->getRequest()->getParams();
		$order = Mage::getModel('sales/order')->loadByIncrementId($response['orderId']);
		//$order->sendNewOrderEmail();
		$session = $this->getCheckout();	
		$session->setQuoteId($session->getSofortQuoteId(true));
		$session->getQuote()->setIsActive(false)->save();

		$this->loadLayout();
		$this->getLayout()->getBlock('sofortvorkassesuccess');		
		Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($order->getId())));
		$this->renderLayout();		
	}
	
	/**
	 * 
	 * customer canceled payment
	 */
	public function errorAction()
	{
		$session = $this->getCheckout();	
		$session->setQuoteId($session->getSofortQuoteId(true));
		$session->getQuote()->setIsActive(true)->save();
		
		$order = Mage::getModel('sales/order');
		$order->load($this->getCheckout()->getLastOrderId());		
		$order->cancel();
		$order->addStatusToHistory($order->getStatus(), Mage::helper('pnsofortueberweisung')->__('Cancelation of payment')); 
		$order->save();

		if(!($session->getData('sofort_aborted') == 1))
			$session->setData('sofort_aborted', 0);
			
		$session->addNotice(Mage::helper('pnsofortueberweisung')->__('Cancelation of payment'));
		$this->_redirect('checkout/cart');		
		return;	
	}	
	
	
	/**
	 * notification about status change
	 */
	public function notificationAction()
	{
		$response = $this->getRequest()->getParams();	
		$orderId = $response['orderId'];
		$secret = $response['secret'];

		$sofort = new SofortLib_Notification();
		$transaction = $sofort->getNotification(); 

		//no valid parameters/xml
		if(empty($orderId) || empty($transaction) || $sofort->isError()) {
			return;
		}

		$s = new SofortLib_TransactionData(Mage::getStoreConfig('payment/sofort/configkey'));
		$s->setTransaction($transaction)->sendRequest();
		
		if($s->isError()) {
			Mage::log('Notification invalid: '.__CLASS__ . ' ' . __LINE__ . $s->getError());
			return;
		}
		
		$order = Mage::getModel('sales/order');
		$order->loadByIncrementId($orderId);
		$paymentObj = $order->getPayment()->getMethodInstance();		
		$payment = $order->getPayment();
		
		//data of transaction doesn't match order
		if($payment->getAdditionalInformation('sofort_transaction') != $transaction 
		|| $payment->getAdditionalInformation('sofort_secret') != $secret 
		|| $payment->getAdditionalInformation('sofort_lastchanged') === $this->_getLastChanged($s)) {
			Mage::log('Notification invalid: '.__CLASS__ . ' ' . __LINE__ );
			return;
		}

		$payment->setAdditionalInformation('sofort_lastchanged', $this->_getLastChanged($s))->save();
		
		if($s->isLoss())
			$this->_transactionLoss($s, $order);
		elseif($s->isPending() && $s->isSofortvorkasse())
			$this->_transactionUnconfirmed($s, $order);	
		elseif($s->isPending() && $s->isSofortrechnung() && $s->getStatusReason() == 'confirm_invoice')
			$this->_transactionUnconfirmed($s, $order);	
		elseif($s->isPending()) 
			$this->_transactionConfirmed($s, $order);
		elseif($s->isReceived() && $s->isSofortvorkasse()) 
			$this->_transactionConfirmed($s, $order);
		elseif($s->isReceived())
			$this->_transactionReceived($s, $order);
		elseif($s->isRefunded())
			$this->_transactionRefunded($s, $order);
		else //uups
			$order->addStatusToHistory($order->getStatus(), " " . $s->getStatus() . " " . $s->getStatusReason());

		$order->save();
	}
	
	private function _transactionLoss($s, $order) {
		$payment = $order->getPayment();
		
		if($s->isSofortlastschrift() || $s->isLastschrift()) {
			//$order->cancel();
			$payment->setParentTransactionId($s->getTransaction())
				->setShouldCloseParentTransaction(true)
				->setIsTransactionClosed(0)
				->registerRefundNotification($s->getAmount());			

			$order->addStatusHistoryComment(Mage::helper('pnsofortueberweisung')->__('Customer returned payment'))->setIsVisibleOnFront(true);
			$order->save();
		} elseif($s->isSofortrechnung()) {
			$order->cancel();
			$order->addStatusHistoryComment(Mage::helper('pnsofortueberweisung')->__('Successfully canceled invoice: %s', $s->getTransaction()))->setIsVisibleOnFront(true);
		} else {
			$order->cancel();
			$order->addStatusHistoryComment(Mage::helper('pnsofortueberweisung')->__('Customer canceled payment'))->setIsVisibleOnFront(true);
		}
		$order->save();
	}
	
	private function _transactionUnconfirmed($s, $order) {
		$payment = $order->getPayment();
		$transaction = $s->getTransaction();
		$statusReason = $s->getStatusReason();
		
		if ($s->isPending() && $s->isSofortvorkasse() ) {
			$order->setState('sofort');
			$order->addStatusToHistory($order->getStatus(), Mage::helper('pnsofortueberweisung')->__('Waiting for money'), true);
			$order->sendNewOrderEmail();
		} elseif ($s->isPending() && $s->isSofortrechnung() && $statusReason == 'confirm_invoice') {
			$order->setState('sofort');

			//customer may have changed the address during payment process
			$address = $s->getInvoiceAddress();
			$order->getBillingAddress()
				->setStreet($address['street'] . ' ' . $address['street_number'])
				->setFirstname($address['firstname'])
				->setLastname($address['lastname'])
				->setPostcode($address['zipcode'])
				->setCity($address['city'])
				->setCountryId($address['country_code']);

			$address = $s->getShippingAddress();
			$order->getShippingAddress()
				->setStreet($address['street'] . ' ' . $address['street_number'])
				->setFirstname($address['firstname'])
				->setLastname($address['lastname'])
				->setPostcode($address['zipcode'])
				->setCity($address['city'])
				->setCountryId($address['country_code']);

			$order->save();
			
			$order->addStatusHistoryComment(Mage::helper('pnsofortueberweisung')->__('Payment successfull. Invoice needs to be confirmed.', $transaction))
					->setIsVisibleOnFront(true)
					->setIsCustomerNotified(true);
					
			$order->sendNewOrderEmail();
		}
		$order->save();	
	}
	
	private function _transactionConfirmed($s, $order) {
		$payment = $order->getPayment();
		$paymentObj = $order->getPayment()->getMethodInstance();
		$amount = $s->getAmount();
		$currency = $s->getCurrency();
		$statusReason = $s->getStatusReason();
		$transaction = $s->getTransaction();
		
		if($s->isReceived() && $s->isSofortvorkasse()) {
			$notifyCustomer = false;
		} elseif($s->isSofortrechnung() && $statusReason == 'not_credited_yet' && $s->getInvoiceStatus() == 'pending') { 
			$notifyCustomer = false;
			$invoice = array(
							'number' => $s->getInvoiceNumber(),
							'bank_holder' => $s->getInvoiceBankHolder(),
							'bank_account_number' => $s->getInvoiceBankAccountNumber(),
							'bank_code' => $s->getInvoiceBankCode(),
							'bank_name' => $s->getInvoiceBankName(),
							'reason' => $s->getInvoiceReason(1). ' '.$s->getInvoiceReason(2),
							'date' => $s->getInvoiceDate(),
							'due_date' => $s->getInvoiceDueDate(),
							'debitor_text' => $s->getInvoiceDebitorText()
			);
			$order->getPayment()->setAdditionalInformation('sofort_invoice', serialize($invoice));
		} elseif($s->isSofortrechnung()) {
			return;		
		} else { 
			$notifyCustomer = true;
		}
		
			
		$payment->setStatus(Paymentnetwork_Pnsofortueberweisung_Model_Pnsofortueberweisung::STATUS_SUCCESS);
		$payment->setStatusDescription(Mage::helper('pnsofortueberweisung')->__('Payment was successful.', $transaction));
		$order->setPayment($payment);

		
		if($order->getPayment()->canCapture() && $order->canInvoice()) {
			$payment->setTransactionId($transaction)
					->setIsTransactionClosed(0)
					->registerCaptureNotification($amount);
		} elseif(method_exists($payment, 'addTransaction')) {  //transaction overview in magento > 1.5
			$payment->setTransactionId($transaction)
					->setIsTransactionClosed(0)
					->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE); 
		}

		$order->setPayment($payment);
		$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING);			
		$order->addStatusHistoryComment(Mage::helper('pnsofortueberweisung')->__('Payment was successful.', $transaction), $paymentObj->getConfigData('order_status'))
					->setIsVisibleOnFront(true)
					->setIsCustomerNotified($notifyCustomer);
		
		if($notifyCustomer) {
			$order->save();
			$order->sendNewOrderEmail();
		}
		
		$order->save();
	}
	
	private function _transactionReceived($s, $order) {
		$payment = $order->getPayment();
		if($s->isReceived() && ($s->isSofortrechnung() || $s->isLastschrift() || $s->isSofortlastschrift()) ) {
			//don't do anything
			//$order->addStatusToHistory($order->getStatus(), Mage::helper('pnsofortueberweisung')->__('Money received.'));
		} elseif($s->isReceived()) { // su,sl,ls
			$order->addStatusHistoryComment(Mage::helper('pnsofortueberweisung')->__('Money received.'))->setIsVisibleOnFront(false);
		}
		
		$order->save();
	}
	
	private function _transactionRefunded($s, $order) {
		$payment = $order->getPayment();

		if(!$payment->getTransaction($s->getTransaction().'-refund')) {
			$payment->setParentTransactionId($s->getTransaction())
				->setShouldCloseParentTransaction(true)
				->setIsTransactionClosed(0)
				->registerRefundNotification($s->getAmountRefunded());

			$order->addStatusHistoryComment(Mage::helper('pnsofortueberweisung')->__('The invoice has been canceled.'))->setIsVisibleOnFront(true);
			$order->save();
		}
	}
	
	private function _getLastChanged($s) {
		return sha1($s->getStatus() . $s->getStatusReason());
	}
	
	/**
	* Get singleton of Checkout Session Model
	*
	* @return Mage_Checkout_Model_Session
	*/
	public function getCheckout()
	{
		return Mage::getSingleton('checkout/session');
	}

}