<?php
/**
 * Copyright (c) 2012 Payment Network AG
 *
 * $Date: 2012-02-21 14:04:17 +0100 (Di, 21. Feb 2012) $
 * @version $Id: class.invoice.inc.php 3368 2012-02-21 13:04:17Z poser $
 * @package sofortLib
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */

/**
 * Abstraction of an invoice
 * Helper class to ease usage of "Rechnung by sofort"
 * Encapsulates Multipay, TransactionData and ConfirmSr to handle everything there is about "Rechnung by sofort"
 * @see SofortLib_Multipay
 * @see SofortLib_TransactionData
 * @see SofortLib_ConfirmSr
 */

class PnagInvoice extends PnagAbstractDocument {

	const PENDING_CONFIRM_INVOICE = 4195329;
	const LOSS_CANCELED = 4194824;
	const LOSS_CONFIRMATION_PERIOD_EXPIRED = 4196360;

	const PENDING_NOT_CREDITED_YET_PENDING = 32785;
	const PENDING_NOT_CREDITED_YET_REMINDER_1 = 65553;
	const PENDING_NOT_CREDITED_YET_REMINDER_2 = 131089;
	const PENDING_NOT_CREDITED_YET_REMINDER_3 = 262161;
	const PENDING_NOT_CREDITED_YET_DELCREDERE = 524305;
	
	const RECEIVED_CREDITED_PENDING = 33026;
	const RECEIVED_CREDITED_REMINDER_1 = 65794;
	const RECEIVED_CREDITED_REMINDER_2 = 131330;
	const RECEIVED_CREDITED_REMINDER_3 = 262402;
	const RECEIVED_CREDITED_DELCREDERE = 524546;
	
	/* im normalfall nicht möglich */
	const REFUNDED_REFUNDED_PENDING = 32836;
	const REFUNDED_REFUNDED_RECEIVED = 2097220;
	const REFUNDED_REFUNDED_REMINDER_1 = 65604;
	const REFUNDED_REFUNDED_REMINDER_2 = 131140;
	const REFUNDED_REFUNDED_REMINDER_3 = 262212;
	const REFUNDED_REFUNDED_DELCREDERE = 524356;
	/* im normalfall nicht möglich */
	
	const REFUNDED_REFUNDED_REFUNDED = 1048644;
	const PENDING_NOT_CREDITED_YET_RECEIVED = 2097169;
	const RECEIVED_CREDITED_RECEIVED = 2097410;
	
	
	/**
	 *
	 * Multipay-Object to handle API calls
	 * @var object
	 * @private
	 */
	var $multipay = null;

	/**
	 * Object TransactionData to handle information about transactions
	 * @var object
	 * @private
	 */
	var $transactionData = null;

	/**
	 * Object Confirm_SR to handle sofortrechnung/rechnung by sofort items
	 * Handling of Sofortrechung
	 * @var object
	 * @private
	 */
	var $confirmSr = null;

	/**
	 *
	 * Some kind of a bitmask to represent every possible state of Rechnung by sofort
	 * Every combination must be unique to represent a unique state
	 * @var array
	 * @private
	 */
	var $statusMask = array(
		'status'=> 
			array(
				'pending' => 1,
				'received' => 2,
				'refunded' => 4,
				'loss' => 8,
			),
		'status_reason' =>
			array(
				'not_credited_yet' => 16,
				'not_credited' => 32,
				'refunded' => 64,
				'compensation' => 128,
				'credited' => 256,
				'canceled' => 512,
				'confirm_invoice' => 1024,
				'confirmation_period_expired' => 2048,
				'wait_for_money' => 4096,
				'reversed' => 8192,
				'rejected' => 16384,
			),
		'invoice_status' =>
			array(
				'pending' => 32768,
				'reminder_1' => 65536,
				'reminder_2' => 131072,
				'reminder_3' => 262144,
				'delcredere' => 524288,
				'refunded' => 1048576,
				'received' => 2097152,
				'empty' => 4194304
		)
	);

	/**
	 *
	 * @see $statusMask
	 * @var string
	 * @private
	 */
	var $status = '';

	/**
	 *
	 * @see $statusMask
	 * @var string
	 * @private
	 */
	var $status_reason = '';
	
	
	/**
	 * 
	 * @see $statusMask
	 * @var string
	 */
	var $invoice_status = '';
	
	
	/**
	 * 
	 * Invoice's objection (Einrede)
	 * @var string
	 */
	var $invoice_objection = '';
	
	
	/**
	 *
	 * transaction id
	 * @var string
	 * @private
	 */
	var $transactionId = '';

	/**
	 * api key given in project setup in payment network backend
	 * @var string
	 * @private
	 */
	var $apiKey = '';

	/**
	 *
	 * api url
	 * @var string
	 * @private
	 */
	var $apiUrl = '';

	/**
	 * time
	 * @var string
	 * @private
	 */
	var $time = '';

	/**
	 * payment method
	 * @var string
	 * @private
	 */
	var $payment_method = '';

	/**
	 * The resulting url to the invoice (PDF)
	 * @var string
	 * @private
	 */
	var $invoiceUrl = '';

	/**
	 *
	 * Constructor
	 * @param string $apiKey
	 * @param string $transactionId
	 * @param string $apiUrl
	 */
	function PnagInvoice($apiKey, $transactionId = '') {
		$this->transactionId = $transactionId;
		$this->apiKey = $apiKey;
		$apiUrl = (getenv('sofortApiUrl') != '') ? getenv('sofortApiUrl') : 'https://api.sofort.com/api/xml';
		$this->apiUrl = $apiUrl;

		$this->multipay = new SofortLib_Multipay($this->apiKey, $this->apiUrl);
		if($transactionId != '') {
			$this->transactionData = $this->_setupTransactionData();
			$this->confirmSr = $this->_setupConfirmSr();
		}
		return $this;
	}
	
	
	/**
	 * Getter for a class constant
	 * @param int $id
	 * @return string
	 */
	function getConstantById($id) {
		$object = new ReflectionClass(__CLASS__); 
		$constants = array_flip($object->getConstants());
		return (array_key_exists($id, $constants)) ? $constants[$id] : 0;
	}
	
	
	/**
	 * 
	 * Getter for a class constant
	 * @param string $name
	 * @return int
	 */
	function getConstantByName($name) {
		$object = new ReflectionClass(__CLASS__); 
		$constants = $object->getConstants();
		return (array_key_exists($name, $constants)) ? $constants[$name] : 0;
	}
	
	
	/**
	 * 
	 * Set the bitmask to a specific state
	 * @param string $status
	 * @param string $status_reason
	 * @param string $invoice_status
	 * @return string pending - confirm_invoice - empty -> 4195329
	 */
	function setBitmask($status, $status_reason, $invoice_status) {
		$this->status = $status;
		$this->status_reason = $status_reason;
		$this->invoice_status = $invoice_status;
		$string = $this->status . ' - '.$this->status_reason . ' - ' . $this->invoice_status;
		return $string.' -> '.$this->_calcInvoiceStatusCode()."\n";
	}
	
	
	/**
	 * 
	 * Set the state
	 * An optional callback can be registered
	 * @param int $state
	 * @param function $callback
	 */
	function setState($state, $callback = '') {
		$this->state = $state;
		if($callback != '') {
			call_user_func($callback);
		}
		return $this;
	}
	
	
	/**
	 * Getter for the current state
	 * @return int $this->state 
	 */
	function getState() {
		return $this->state;
	}
	
	
	/**
	 * Setter for transactionId
	 * @param $transactionId
	 * @public
	 */
	function setTransactionId($transactionId) {
		$this->transactionId = $transactionId;
		$this->transactionData = $this->_setupTransactionData();
		$this->confirmSr = $this->_setupConfirmSr();
		return $this;
	}
	
	
	/**
	 * Construct the SofortLib_TransactionData object
	 * Collect every order's item and set it accordingly
	 * TransactionData is used encapsulated in this class to retrieve information about the order's details
	 * @return object SofortLib_TransactionData
	 * @private
	 */
	function _setupTransactionData() {
		$obj = new SofortLib_TransactionData($this->apiKey, $this->apiUrl);
		$response = $obj->setTransaction($this->transactionId);
		$response->sendRequest();
		if (!isset ($obj->response[0] ) ) {
			return false;
		} else {
			$transactionData = $obj->response[0];
		}
		
		$this->setStatus($transactionData['status']);
		$this->setStatusReason($transactionData['status_reason']);
		$this->setStatusOfInvoice($transactionData['invoice_status']);
		$this->setInvoiceObjection($transactionData['invoice_objection']);
		$this->setTransaction($this->transactionId);
		$this->setTime($transactionData['time']);
		$this->setPaymentMethod($transactionData['payment_method']);
		$this->setInvoiceUrl($transactionData['invoice_url']);
		
		$this->setAmount($transactionData['amount']);
		$this->setAmountRefunded($transactionData['amount_refunded']);
		
		if (empty($transactionData['items']) ||  empty($transactionData['item'])) {
			$itemArray = (!empty($transactionData['items'])) ? $transactionData['items'] : $transactionData['item'];
		} else {
			$itemArray = array();
		}
		// should there be any items, fetch them accordingly
		$this->items = array();
		if(is_array($itemArray) && !empty($itemArray)) {
			foreach($itemArray as $item) {
				$this->setItem($item['item_id'], $item['product_number'], $item['product_type'], $item['title'], $item['description'], $item['quantity'], $item['unit_price'], $item['tax']);
				$this->amount += ($item['unit_price'] * $item['quantity']);
			}
		}
		/* 
		 * set the state according to the state given by transaction information (status, status_reason, invoice_status)
		 * @see $statusMask
		 */
		$this->setState($this->_calcInvoiceStatusCode());
		return $obj;
	}
	
	
	/**
	 * Initialize SofortLib_ConfirmSR
	 * @private
	 * @return Object SofortLib_ConfirmSr
	 */
	function _setupConfirmSr() {
		$obj = new SofortLib_ConfirmSr($this->apiKey, $this->apiUrl);
		$obj->setTransaction($this->transactionId);
		
		return $obj;
	}
	
	
	/**
	 * Refreshes the TransactionData with the data directly from the pnag-server
	 * @return boolean
	 */
	function refreshTransactionData() {
		$this->transactionData = $this->_setupTransactionData();
		return true;
	}
	
	
	/**
	 * Wrapper function for cancelling this invoice via multipay (SofortLib)
	 * @return Ambigious boolean/Array
	 * @todo fix returned value array, empty array
	 * @public
	 */
	function cancelInvoice($transactionId = '') {
		if($transactionId != '') {
			$this->transactionId = $transactionId;
			$this->confirmSr = $this->_setupConfirmSr();
		}
		if($this->confirmSr != null) {
			
			unset($this->items);
			
			$this->confirmSr->cancelInvoice();
			$this->confirmSr->setComment('Vollstorno');
			$this->confirmSr->sendRequest();
			$this->transactionData = $this->_setupTransactionData();
			
			return $this->getErrors();
		}
		return false;
	}
	
	
	/**
	 * Wrapper function for confirming this invoice via multipay (SofortLib)
	 * @param $transactionId - optional parameter for confirming a transaction on the fly
	 * @return Ambigious boolean/Array
	 * @todo fix returned value array, empty array
	 * @public
	 */
	function confirmInvoice($transactionId = '') {
		if($transactionId != '') {
			$this->transactionId = $transactionId;
			$this->confirmSr = $this->_setupConfirmSr();
		}
		if($this->confirmSr != null) {
			$this->confirmSr->confirmInvoice();
			$this->confirmSr->setComment('Invoice confirmed');
			$this->confirmSr->sendRequest();
			$this->transactionData = $this->_setupTransactionData();
			return $this->getErrors();
		}
		return false;
	}
	
	
	/* ########################## WRAPPER FUNCTIONS MULTIPAY ########################## */
	/**
	 * Wrapper for SofortLib_Multipay::addSofortrechnungItem
	 * @see SofortLib_Multipay
	 * @public
	 * @param $itemId
	 * @param $productNumber
	 * @param $title
	 * @param $unit_price - float precision 2 @see multipay api
	 * @param $productType
	 * @param $description
	 * @param $quantity - int
	 * @param $tax
	 */
	function addItemToInvoice($itemId, $productNumber, $title, $unit_price, $productType = 0, $description = '', $quantity = 1, $tax = 19) {
		$unit_price = round($unit_price, 2);	// round all prices to two decimals
		$this->multipay->addSofortrechnungItem($itemId, $productNumber, $title, $unit_price, $productType, $description, $quantity, $tax);
		$this->setItem($itemId, $productNumber, $productType, $title, $description, $quantity, $unit_price, $tax);
		$this->amount += ($quantity * $unit_price);
		$this->setAmount($this->amount);
	}
	
	
	/**
	 * Remove an item from the invoice
	 * @public
	 * @param $itemId
	 * @return boolean
	 */
	function removeItemfromInvoice($itemId) {
		$return = false;
		$i = 0;
		foreach($this->items as $item) {
			if($item->item_id == $itemId) {
				// TODO: remove item
				//unset($this->items[$i]);
				$this->setAmount($this->getAmount() - $this->getItemAmount($itemId));
				$return = $this->multipay->removeSofortrechnungItem($itemId);
			}
			$i++;
		}
		return $return;
	}
	
	
	function updateInvoiceItem($itemId, $quantity, $unit_price) {
		$return = false;
		foreach($this->items as $item) {
			if($item->item_id == $itemId) {
				$oldPrice = $item->unit_price * $item->quantity;
				$item->unit_price = $unit_price;
				$item->quantity = $quantity;
				$newPrice = $unit_price * $quantity;
				$this->setAmount($this->getAmount() - $oldPrice + $newPrice);
				$return = $this->multipay->updateSofortrechnungItem($itemId, $quantity, $unit_price);
			}
		}
		return $return;
	}
	
	
	function getItemAmount($itemId) {
		return $this->multipay->getSofortrechnungItemAmount($itemId);
	}
	
	
	/**
	 * Wrapper for SofortLib_Multipay::setSofortrechnungShippingAddress
	 * @see SofortLib_Multipay
	 * @public
	 * @param $firstname
	 * @param $lastname
	 * @param $street
	 * @param $streetNumber
	 * @param $zipcode
	 * @param $city
	 * @param $salutation
	 * @param $country
	 */
	function addShippingAddresss($firstname, $lastname, $street, $streetNumber, $zipcode, $city, $salutation, $country = 'DE') {
		$this->multipay->setSofortrechnungShippingAddress($firstname, $lastname, $street, $streetNumber, $zipcode, $city, $salutation, $country);
	}
	
	
	/**
	 * Wrapper for SofortLib_Multipay::setSofortrechnungInvoiceAddress
	 * @see SofortLib_Multipay
	 * @public
	 * @param $firstname
	 * @param $lastname
	 * @param $street
	 * @param $streetNumber
	 * @param $zipcode
	 * @param $city
	 * @param $salutation
	 * @param $country
	 */
	function addInvoiceAddress($firstname, $lastname, $street, $streetNumber, $zipcode, $city, $salutation, $country = 'DE') {
		$this->multipay->setSofortrechnungInvoiceAddress($firstname, $lastname, $street, $streetNumber, $zipcode, $city, $salutation, $country = 'DE');
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::setSofortrechnungOrderId
	 * @see SofortLib_Multipay
	 * @public
	 * @param $arg
	 */
	function setOrderId($arg) {
		$this->multipay->setSofortrechnungOrderId($arg);
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::setSofortrechnungCustomerId
	 * @public
	 * @param $arg
	 */
	function setCustomerId($arg) {
		$this->multipay->setSofortrechnungCustomerId($arg);
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::setPhoneNumberCustomer
	 * @public
	 * @param $arg
	 */
	function setPhoneNumberCustomer($arg) {
		$this->multipay->setPhoneNumberCustomer($arg);
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::setEmailCustomer
	 * @public
	 * @param $arg
	 */
	function setEmailCustomer($arg) {
		$this->multipay->setEmailCustomer($arg);
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::addUserVariable
	 * @public
	 * @param $arg
	 */
	function addUserVariable($arg) {
		$this->multipay->addUserVariable($arg);
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::setNotificationUrl
	 * @public
	 * @param $arg
	 */
	function setNotificationUrl($arg) {
		$this->multipay->setNotificationUrl($arg);
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::setAbortUrl
	 * @public
	 * @param $arg
	 */
	function setAbortUrl($arg) {
		$this->multipay->setAbortUrl($arg);
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::setSuccessUrl
	 * @public
	 * @param $arg
	 */
	function setSuccessUrl($arg) {
		$this->multipay->setSuccessUrl($arg);
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::setTimeoutUrl
	 * @public
	 * @param $arg
	 */
	function setTimeoutUrl($arg) {
		$this->multipay->setTimeoutUrl($arg);
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::setReason
	 * @public
	 * @param $arg string
	 * @param $arg2 string
	 */
	function setReason($arg, $arg2 = '') {
		$this->multipay->setReason($arg, $arg2);
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::setAmount
	 * @public
	 * @param $arg float
	 * @param $currency string
	 */
	function setAmount($arg, $currency = 'EUR') {
		$this->multipay->setAmount($arg, $currency);
	}
	
	
	/**
	 * current total amount of the given order-articles
	 * @return float - sum (price, total) of all articles
	 */
	function getAmount() {
		if(isset($this->multipay->parameters['amount']) && $this->multipay->parameters['amount'] != 0.00) {
			return $this->multipay->parameters['amount'];
		} elseif(isset($this->amount) && $this->amount != 0.00) {
			return $this->amount;	// TODO: check
		}
		return 0.0;
	}
	
	
	function setAmountRefunded($arg) {
		$this->amountRefunded = $arg;
	}
	
	
	function getAmountRefunded() {
		return $this->amountRefunded;
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::setSofortrechnung
	 * @public
	 */
	function setSofortrechnung() {
		$this->multipay->setSofortrechnung();
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::getPaymentUrl
	 * @public
	 * @return url string
	 */
	function getPaymentUrl() {
		return $this->multipay->getPaymentUrl();
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::getPaymentUrl
	 * @public
	 * @return url string
	 */
	function getTransactionId() {
		return $this->multipay->getTransactionId();
	}
	
	
	/**
	 * Wrapper function for SofortLib_Multipay::toXml
	 * @public
	 * @return xml
	 */
	function toXml() {
		return $this->multipay->toXml();
	}
	
	
	/**
	 * Validate your parameters against API
	 * @return array - any validationerrors and -warnings
	 * @public
	 */
	/*
	function validateRequest() {
		$errorsAndWarnings = $this->multipay->validateRequest('sr');
		return $errorsAndWarnings;
	}
	*/
	
	
	/**
	 * send the order to pnag (-> buy your products)
	 * @return empty array if ok ELSE array with errors and/or warnings
	 * @public
	 */
	function checkout() {
		$this->multipay->sendRequest();
		$this->transactionId = $this->multipay->transactionId;	// set the resulting transaction id
		$this->transactionData = $this->_setupTransactionData();
		
		$errors = array();
		if ($this->isError()) {
			$errors = $this->getErrors();
		}
		
		$warnings = array();
		if ($this->isWarning()) {
			$warnings = $this->getWarnings();
		}
		
		if (!empty($errors) &&  !empty($warnings)) {
			return array(); //no errors or warnings found
		} else {
			$returnArray = array();
			$returnArray['errors'] = $errors;
			$returnArray['warnings'] = $warnings;
			return $returnArray;
		}
	}
	
	
	function getTransactionInfo() {
		if(is_a($this->transactionData, 'SofortLib')) {
			$this->transactionData->setTransaction($this->transactionId);
			$this->sendRequest();
			return $this->transactionData->response;
		} else {
			$this->transactionData = $this->_setupTransactionData();
		}
		return array();
	}
	/* ########################## WRAPPER FUNCTIONS MULTIPAY ########################## */
	
	
	/**
	 * Wrapper function for removing an article via multipay (SofortLib)
	 * Currently not implemented in PNAG API 06/2011
	 * @param $articleId int
	 * @public
	 * return array
	 */
	/*
	function removeArticle($articleId) {
		if($this->confirmSr != null) {
			$this->confirmSr->removeItem($articleId, -1);
			$this->confirmSr->confirmInvoice();
			$this->confirmSr->setComment('Article '.$articleId.' removed');
			$this->confirmSr->sendRequest();
			$this->_setupTransactionData();  //TODO --> $this->transactionData = $this->_setupTransactionData(); ???
			return $this->getErrors();
		}
		return array();
	}
	*/
	
	
	/**
	 * Wrapper function for changing the quantity of an article via multipay (SofortLib)
	 * Currently not implemented in PNAG API 06/2011
	 * @param $articleId int
	 * @param $quantity int
	 * @public
	 * @return array
	 */
	 /*
	function changeArticleQuantity($articleId, $quantity = 0) {
		if($this->confirmSr != null || $quantity < 1) {
			$this->confirmSr->removeItem($articleId, $quantity);
			$this->confirmSr->confirmInvoice();
			$this->confirmSr->setComment('Article '.$articleId.', changed quantity to: '.$quantity);
			$this->confirmSr->sendRequest();
			$this->_setupTransactionData();
			return $this->getErrors();
		}
		return array();
	}
	*/
	
	
	/**
	 * Output the resulting invoice as pdf
	 * @public
	 */
	function getInvoice() {
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="invoice.pdf"');
		echo file_get_contents($this->invoiceUrl);
		return true;
	}
	
	
	/**
	 * Getter for retrieving the invoice's url
	 * @public
	 * @return url string
	 */
	function getInvoiceUrl() {
		return $this->invoiceUrl;
	}
	
	
	/**
	 * Setter for status
	 * @public
	 * @param $status
	 * @return object
	 */
	function setStatus($status) {
		$this->status = $status;
		return $this;
	}
	
	
	/**
	 * Setter for status_reason
	 * @param $status_reason
	 * @return object
	 */
	function setStatusReason($status_reason) {
		$this->status_reason = $status_reason;
		return $this;
	}
	
	
	/**
	 * 
	 * Setter for invoice_status
	 * @param string $invoice_status | may be emtpy
	 * @return object
	 */
	function setStatusOfInvoice($invoice_status = '') {
		$this->invoice_status = !empty($invoice_status) ? $invoice_status : 'empty';
		return $this;
	}
	
	
	/**
	 * Setter for trasaction
	 * @param $transaction
	 * @return object
	 */
	function setTransaction($transaction) {
		$this->transaction = $transaction;
		return $this;
	}
	
	
	/**
	 * Setter for time
	 * @param $time
	 * @public
	 * return object
	 */
	function setTime($time) {
		$this->time = $time;
		return $this;
	}
	
	
	/**
	 * Setter for interface version
	 * Wrapper for class Multipay to set version according to shop module and it's interface version
	 * e.g. 'pn_xtc_5.0.0'
	 * @param $arg string 
	 */
	function setVersion($arg) {
		$this->multipay->setVersion($arg);
	}
	
	
	/**
	 * Setter for payment_method
	 * @param $paymentMethod
	 * @return object
	 */
	function setPaymentMethod($paymentMethod) {
		$this->payment_method = $paymentMethod;
		return $this;
	}
	
	
	/**
	 * Setter for invoiceUrl
	 * @public
	 * @param $invoiceUrl
	 * @return object
	 */
	function setInvoiceUrl($invoiceUrl) {
		$this->invoiceUrl = $invoiceUrl;
		return $this;
	}
	
	
	/**
	 * Sets the reason for objecting this invoice
	 * @param string $invoiceObjection (40-50 chars max.)
	 * @return object
	 */
	function setInvoiceObjection($invoiceObjection) {
		$this->invoice_objection = $invoiceObjection;
		return $this;
	}
	
	
	/**
	 * Sets the invoice status
	 * @public
	 * @param string $invoiceStatus
	 * @return object
	 */
	function setInvoiceStatus($invoiceStatus) {
		$this->invoice_status = $invoiceStatus;
		return $this;
	}
	
	
	/**
	 * Returns the reason for objecting this invoice
	 * @public
	 * @return string
	 */
	function getInvoiceObjection() {
		return $this->invoice_objection;
	}
	
	
	/**
	 * Instead of calculated status, this method returns the invoice's staus (string)
	 * @public
	 * @return string
	 */
	function getStatusOfInvoice() {
		return $this->invoice_status;
	}
	
	
	/**
	 * Uses the statusMask to "calculate" the current invoice's payment status
	 * @public
	 * @see Invoice::_calcInvoiceStatusCode
	 * @return int
	 */
	function getInvoiceStatus() {
		return $this->_calcInvoiceStatusCode();
	}
	
	
	/**
	 *
	 * Calculate the current invoice's payment status using bitwise OR
	 * @return int
	 * @private
	 */
	function _calcInvoiceStatusCode() {
		return $this->statusMask['status'][$this->status] 
				| $this->statusMask['status_reason'][$this->status_reason] 
				| $this->statusMask['invoice_status'][$this->invoice_status];
	}
	
	
	/**
	 * Getter for payment_method
	 * @public
	 * @return string
	 */
	function getPaymentMethod() {
		return $this->payment_method;
	}
	
	
	/**
	 * Getter for status_reason
	 * @public
	 * @return string
	 */
	function getStatusReason() {
		return $this->status_reason;
	}
	
	
	/**
	 * Getter for status
	 * @public
	 * @return string
	 */
	function getStatus() {
		return $this->status;
	}
	
	
	/**
	 * Getter for items
	 * @public
	 * @return array
	 */
	function getItems () {
		return $this->items;
	}
	
	
	/**
	 * return TransactionData, the invoice is working with
	 * NOTICE: if status changed (removeArticle, InvoiceConfirmed etc.) it returns always the FRESH TransactionData from pnag-server
	 * @return object
	 * @see $this->refreshTransactionData();
	 */
	function getTransactionData() {
		if ($this->transactionData) {
			return $this->transactionData;
		} else {
			return false;
		}
	}
	
	
	/**
	 * Check, if errors occured
	 * @public
	 * @return boolean
	 */
	function isError() {
		if ($this->multipay) {
			if ($this->multipay->isError('sr')) {
				return true;
			}
		} else if ($this->confirmSr) {
			if ($this->confirmSr->isError('sr')) {
				return true;
			}
		} else if ($this->transactionData) {
			if ($this->transactionData->isError('sr')) {
				return true;
			}
		}
		return false;
	}
	
	
	/**
	 * Check, if warnings occured
	 * @public
	 * @return boolean
	 */
	function isWarning() {
		if ($this->multipay) {
			if ($this->multipay->isWarning('sr')) {
				return true;
			}
		} else if ($this->confirmSr) {
			if ($this->confirmSr->isWarning('sr')) {
				return true;
			}
		} else if ($this->transactionData) {
			if ($this->transactionData->isWarning('sr')) {
				return true;
			}
		}
		return false;
	}
	
	
	/**
	 * returns one error (as String!)
	 */
	function getError() {
		if($this->multipay) {
			if ($this->multipay->isError('sr')) {
				return $this->multipay->getError('sr');
			}
		}
		if ($this->confirmSr) {
			if ($this->confirmSr->isError('sr')) {
				return $this->confirmSr->getError('sr');
			}
		}
		if ($this->transactionData) {
			if ($this->transactionData->isError('sr')) {
				return $this->transactionData->getError('sr');
			}
		}
		return '';
	}
	
	
	/**
	 * collect all errors and returns them
	 * @return array - all errors
	 * @public
	 */
	function getErrors() {
		$allErrors = array();
		if($this->multipay) {
			if ($this->multipay->isError('sr')) {
				$allErrors = array_merge ($this->multipay->getErrors('sr'), $allErrors);
			}
		}
		if ($this->confirmSr) {
			if ($this->confirmSr->isError('sr')) {
				$allErrors = array_merge ($this->confirmSr->getErrors('sr'), $allErrors);
			}
		}
		if ($this->transactionData) {
			if ($this->transactionData->isError('sr')) {
				$allErrors = array_merge ($this->transactionData->getErrors('sr'), $allErrors);
			}
		}
		return $allErrors;
	}
	
	
	/**
	 * @public
	 * collects all warnings and returns them
	 * @return array
	 */
	function getWarnings() {
		$allWarnings = array();
		if($this->multipay) {
			if ($this->multipay->isWarning('sr')) {
				$allWarnings = array_merge ($this->multipay->getWarnings('sr'), $allWarnings);
			}
		}
		if ($this->confirmSr) {
			if ($this->confirmSr->isWarning('sr')) {
				$allWarnings = array_merge ($this->confirmSr->getWarnings('sr'), $allWarnings);
			}
		}
		if ($this->transactionData) {
			if ($this->transactionData->isWarning('sr')) {
				$allWarnings = array_merge ($this->transactionData->getWarnings('sr'), $allWarnings);
			}
		}
		return $allWarnings;
	}
	
	
	/**
	 * Enabling logging for all encapsed SofortLib components
	 * @public
	 * @return boolean
	 */
	function enableLog() {
		(is_a($this->multipay, 'SofortLib')) ? $this->multipay->enableLog() : '';
		(is_a($this->transactionData, 'SofortLib')) ? $this->transactionData->enableLog() : '';
		(is_a($this->confirmSr, 'SofortLib')) ? $this->confirmSr->enableLog() : '';
		return true;
	}
	
	
	/**
	 * Disable logging for all encapsed SofortLib components
	 * @public
	 * @return boolean
	 */
	function disableLog() {
		(is_a($this->multipay, 'SofortLib')) ? $this->multipay->disableLog() : '';
		(is_a($this->transactionData, 'SofortLib')) ? $this->transactionData->disableLog() : '';
		(is_a($this->confirmSr, 'SofortLib')) ? $this->confirmSr->disableLog() : '';
		return true;
	}
	
	
	/**
	 * Log the given String into log.txt
	 * Notice: logging must be enabled -> use enableLog();
	 * @param string $msg - Message to log
	 * @return bool - true=logged ELSE false=logging failed
	 * @public
	 */
	function log($msg){
		if (is_a($this->multipay, 'SofortLib')) {
			$this->multipay->log($msg);
			return true;
		} else if (is_a($this->transactionData, 'SofortLib')) {
			$this->transactionData->log($msg);
			return true;
		} else if (is_a($this->confirmSr, 'SofortLib')) {
			$this->confirmSr->log($msg);
			return true;
		}
		return false;
	}
	
	
	/**
	 * Log the given String into error_log.txt
	 * Notice: logging must be enabled -> use enableLog();
	 * @param string $msg - Message to log
	 * @return bool - true=logged ELSE false=logging failed
	 * @public
	 */
	function logError($msg){
		if (is_a($this->multipay, 'SofortLib')) {
			$this->multipay->logError($msg);
			return true;
		} else if (is_a($this->transactionData, 'SofortLib')) {
			$this->transactionData->logError($msg);
			return true;
		} else if (is_a($this->confirmSr, 'SofortLib')) {
			$this->confirmSr->logError($msg);
			return true;
		}
		return false;
	}
	
	
	/**
	 * Log the given String into warning_log.txt
	 * Notice: logging must be enabled -> use enableLog();
	 * @param string $msg - Message to log
	 * @return bool - true=logged ELSE false=logging failed
	 * @public
	 */
	function logWarning($msg){
		if (is_a($this->multipay, 'SofortLib')) {
			$this->multipay->logWarning($msg);
			return true;
		} else if (is_a($this->transactionData, 'SofortLib')) {
			$this->transactionData->logWarning($msg);
			return true;
		} else if (is_a($this->confirmSr, 'SofortLib')) {
			$this->confirmSr->logWarning($msg);
			return true;
		}
		return false;
	}
	
	
	function __toString() {
		$string = '<pre>';
		$string .= print_r($this,1);
		$string .= '</pre>';
		return $string;
	}
}
?>