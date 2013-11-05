<?php

/**
 * The base class for cancelling SofortDauerauftrag
 *
 * Copyright (c) 2012 Payment Network AG
 *
 * $Date: 2012-02-21 14:04:17 +0100 (Di, 21. Feb 2012) $
 * @version SofortLib 1.5.0  $Id: sofortLib_cancel_sa.inc.php 3368 2012-02-21 13:04:17Z poser $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 *
 */

class SofortLib_CancelSa extends SofortLib_Abstract {
	
	var $parameters;
	var $file;
	
	var $cancelUrl = '';


	/**
	 * create new cancel object
	 *
	 * @param String $apikey your API-key
	 */
	function SofortLib_CancelSa($apikey='') {
		list($userid, $projectId, $apikey) = explode(':', $apikey);
		$apiUrl = (getenv('sofortApiUrl') != '') ? getenv('sofortApiUrl') : 'https://api.sofort.com/api/xml';
		$this->SofortLib($userid, $apikey, $apiUrl);
	}


	/**
	 * generate XML message
	 * @return string
	 */
	function toXml() {
		$msg = '<?xml version="1.0" encoding="UTF-8"?>';
		$msg .= $this->_arrayToXml($this->parameters, 'cancel_sa');

		return $msg;
	}
	
	/**
	 * 
	 * remove SofortDauerauftrag
	 * @param String $transaction Transaction ID
	 * @return SofortLib_CancelSa
	 */
	function removeSofortDauerauftrag($transaction) {
		if(empty($transaction) && array_key_exists('transaction', $this->parameters)) {
			$transaction = $this->parameters['transaction'];
		}

		if(!empty($transaction)) {
			$this->parameters = NULL;
			$this->parameters['transaction'] = $transaction;
		}

		return $this;
	}
	
	
	/**
	 * the customer will be redirected to this url after a successful
	 * transaction, this should be a page where a short confirmation is
	 * displayed
	 *
	 * @param string $arg the url after a successful transaction
	 * @return SofortLib_Multipay
	 */
	function setSuccessUrl($arg) {
		$this->parameters['success_url'] = $arg;
		return $this;
	}
	
	
	/**
	 * the customer will be redirected to this url if he uses the
	 * abort link on the payment form, should redirect him back to
	 * his cart or to the payment selection page
	 *
	 * @param string $arg url for aborting the transaction
	 * @return SofortLib_Multipay
	 */
	function setAbortUrl($arg) {
		$this->parameters['abort_url'] = $arg;
		return $this;
	}
	
	
	/**
	 * if the customer takes too much time or if your timeout is set too short
	 * he will be redirected to this page
	 *
	 * @param string $arg url
	 * @return SofortLib_Multipay
	 */
	function setTimeoutUrl($arg) {
		$this->parameters['timeout_url'] = $arg;
		return $this;
	}
	
	
	/**
	 * Set the transaction you want to confirm/change
	 * @param String $arg Transaction Id
	 * @return SofortLib_CancelSa
	 */
	function setTransaction($arg) {
		$this->parameters['transaction'] = $arg;
		return $this;
	}
	
	
	function getCancelUrl() {
		return $this->cancelUrl;
	}
	
	/**
	 * Parser for response from server
	 * this callback will be called for every closing xml-tag
	 * @private
	 */
	function onParseTag($data, $tag){
			switch($tag) {
			case 'cancel_url':
				$this->cancelUrl = $data;
				break;
			default:
			break;
		}
	}
	
}