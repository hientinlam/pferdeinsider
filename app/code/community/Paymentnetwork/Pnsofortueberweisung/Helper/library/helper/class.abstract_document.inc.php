<?php
/// \cond
/**
 * Copyright (c) 2012 Payment Network AG
 *
 * $Date: 2012-02-21 14:04:17 +0100 (Di, 21. Feb 2012) $
 * @version SofortLib 1.3.0  $Id: class.abstract_document.inc.php 3368 2012-02-21 13:04:17Z poser $
 * @author Payment Network AG http://www.payment-network.com (integration@sofort.com)
 * @internal
 *
 */
class PnagAbstractDocument {
	
	/**
	 * Holds all items associated with this kind of document (instance might be invoice, bank transfer, ...)
	 * @var array
	 */
	var $items = array();
	
	/**
	 * 
	 * Holds the instance of PnagCustomer associated with this kind of document
	 * @var object
	 */
	var $customer = null;
	
	/**
	 * 
	 * Holds the currency associated with this kind of document
	 * @var String
	 */
	var $currency = '';
	
	/**
	 * Holds the amount/total of this kind of document
	 * @var float
	 */
	var $amount = 0.00;
	
	/**
	 * 
	 * Holds the refunded amount/total 
	 * @var float
	 */
	var $amountRefunded = 0.00;
	
	/**
	 * puts the given article into $this->items
	 * should only be used for the articles from the shopsystem
	 * @todo change VAT according to legislation
	 */
	function setItem($item_id, $product_number = 0, $product_type = '-', $title = '', $description = '', $quantity = 0, $unit_price = '', $tax = '19') {
		array_push($this->items, new PnagArticle($item_id, $product_number, $product_type, $title, $description, $quantity, $unit_price, $tax));
		return $this;
	}
	
	
	/**
	 * Getter for items
	 * @return array $this->items
	 */
	function getItems() {
		return $this->items;
	}
	
	
	/**
	 * searches in the before given shoparticles for the highest tax and returns it
	 * @return int/float - highest found taxvalue e.g. 0 or 7 or 19...
	 */
	function getHighestShoparticleTax() {
		$highestTax = 0;
		foreach ($this->items as $item) {
			if ($item->getTax() > $highestTax) {
				$highestTax = $item->getTax();
			}
		}
		return $highestTax;
	}
	
	
	/**
	 * Set the customer's credentials
	 * @param $name	string
	 * @param $lastname string
	 * @param $firstname string
	 * @param $company string
	 * @param $csID string customer id in shop
	 * @param $vat_id string - customer's VAT ID
	 * @param $shop_id - shop's ID
	 * @param $ID
	 * @param $cIP
	 * @param $street_address string
	 * @param $suburb string
	 * @param $city string
	 * @param $postcode string
	 * @param $state string
	 * @param $country	string
	 * @param $format_id string
	 * @param $telephone string
	 * @param $email_address string
	 */
	function setCustomer($name = '', $lastname = '', $firstname = '', $company = '', $csID = '', $vat_id = '', $shop_id = '', $ID = '', $cIP = '', $street_address = '', $suburb = '', $city = '', $postcode = '', $state = '', $country = '', $format_id = '', $telephone = '', $email_address = '') {
		$this->customer = new PnagCustomer($name, $lastname, $firstname, $company, $csID, $vat_id, $shop_id, $ID, $cIP, $street_address, $suburb, $city, $postcode, $state, $country, $format_id, $telephone, $email_address);
		return $this;
	}
	
	
	/**
	 *
	 * Setter for currency
	 * @param $currency string
	 */
	function setCurrency($currency) {
		$this->currency = $currency;
		return $this;
	}
	
	
	/**
	 * Calculate the total amount
	 * @private
	 * @return $object
	 */
	function _calcAmount() {
		$this->amount = 0.0;
		foreach($this->items as $item) {
			$this->amount += $item->unit_price * $item->quantity;
		}
		return $this;
	}
	
	
	/**
	 * get the total amount
	 */
	function getAmount() {
		return $this->amount;
	}
}


/**
 *
 * Data object that encapsulates user's data
 * $Date: 2012-02-21 14:04:17 +0100 (Di, 21. Feb 2012) $
 * $ID$
 *
 */
class PnagCustomer {
	var $name = '';
	var $lastname = '';
	var $firstname = '';
	var $company = '';
	var $csID = '';
	var $vat_id = '';
	var $shop_id = '';
	var $ID = '';
	var $cIP = '';
	var $street_address = '';
	var $suburb = '';
	var $city = '';
	var $postcode = '';
	var $state = '';
	var $country = '';
	var $format_id = '';
	var $telephone = '';
	var $email_address = '';
	
	
	/**
	 * Set the customer's credentials
	 * @param $name	string
	 * @param $lastname string
	 * @param $firstname string
	 * @param $company string
	 * @param $csID string customer id in shop
	 * @param $vat_id string - customer's VAT ID
	 * @param $shop_id - shop's ID
	 * @param $ID
	 * @param $cIP
	 * @param $street_address string
	 * @param $suburb string
	 * @param $city string
	 * @param $postcode string
	 * @param $state string
	 * @param $country	string
	 * @param $format_id string
	 * @param $telephone string
	 * @param $email_address string
	 */
	function PnagCustomer($name = '', $lastname = '', $firstname = '', $company = '', $csID = '', $vat_id = '', $shop_id = '', $ID = '', $cIP = '', $street_address = '', $suburb = '', $city = '', $postcode = '', $state = '', $country = '', $format_id = '', $telephone = '', $email_address = '') {
		$this->name = $name;
		$this->lastname = $lastname;
		$this->firstname = $firstname;
		$this->company = $company;
		$this->csID = $csID;
		$this->vat_id = $vat_id;
		$this->shop_id = $shop_id;
		$this->ID = $ID;
		$this->cIP = $cIP;
		$this->street_address = $street_address;
		$this->suburb = $suburb;
		$this->city = $city;
		$this->postcode = $postcode;
		$this->state = $state;
		$this->country = $country;
		$this->format_id = $format_id;
		$this->telephone = $telephone;
		$this->email_address = $email_address;
	}
}


/**
 *
 * Data object that encapsulates article's data
 * $Date: 2012-02-21 14:04:17 +0100 (Di, 21. Feb 2012) $
 * $ID$
 *
 */
class PnagArticle {

	var $item_id = '';
	var $product_number = '';
	var $product_type = '';
	var $title = '';
	var $description = '';
	var $quantity = '';
	var $unit_price = '';
	var $tax = '';
	
	
	/**
	 * Constructor
	 * @param $item_id int
	 * @param $product_number string
	 * @param $product_type string
	 * @param $title string
	 * @param $description string
	 * @param $quantity int
	 * @param $unit_price float
	 * @param $tax float
	 */
	function PnagArticle($item_id, $product_number, $product_type, $title, $description, $quantity, $unit_price, $tax) {
		$this->item_id = $item_id;
		$this->product_number = $product_number;
		$this->product_type = $product_type;
		$this->title = $title;
		$this->description = $description;
		$this->quantity = $quantity;
		$this->unit_price = $unit_price;
		$this->tax = $tax;
	}
	
	
	function getItemId () {
		return $this->item_id;
	}
	
	
	function getQuantity () {
		return $this->quantity;
	}
	
	
	function setQuantity ($quantity) {
		$this->quantity = $quantity;
	}
	
	
	function getUnitPrice () {
		return $this->unit_price;
	}
	
	
	function setUnitPrice ($unitPrice) {
		$this->unit_price = $unitPrice;
	}
	
	
	function getTitle() {
		return $this->title;
	}
	
	
	function getTax() {
		return $this->tax;
	}
	
	
	function setTax ($value) {
		$this->tax = $value;
	}
	
	
	function setProductNumber ($productNumber) {
		$this->product_number = $productNumber;
	}
	
	
	function getProductNumber () {
		return $this->product_number;
	}
}
/// \endcond
?>