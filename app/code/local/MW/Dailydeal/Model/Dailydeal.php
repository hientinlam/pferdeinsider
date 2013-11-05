<?php

class MW_Dailydeal_Model_Dailydeal extends Mage_Core_Model_Abstract
{

	
    public function _construct()
    {
        parent::_construct();
        $this->_init('dailydeal/dailydeal');
        
    }
	public function isPending()
	{
		return ($this->getPhase() == 1);
	}
	
	/**
	 * 
	 * Overrides the getPhase() property getter to "lazy calculate" the phase property
	 */
	public function getPhase()
	{
		if ($this->hasData('phase'))
			return $this->getData('phase');
		$phase = $this->_calculatePhase();
		$this->setPhase($phase);
		return $phase;
	}
	/**
	 * Caculates the correct phase based on the current date and time
	 */
	public function _calculatePhase()
	{ 
		try {
			//check if this is new daily deal
			if ($this->getStartDatetime() == null)
				return 1;//Pending
			$now 	= MW_Dailydeal_Helper_Data::DateTimeToStoreTZ();
			$start 	= MW_Dailydeal_Helper_Data::DateTimeToStoreTZ($this->getStartDatetime());
			$end 	= MW_Dailydeal_Helper_Data::DateTimeToStoreTZ($this->getEndDatetime());
			
			if ($start > $now){
				return 1; //Pending
			} elseif ($end < $now){
				return 4; //Expried
			} elseif (($start <= $now) && ($now <= $end)){
				if ($this->getStatus() == 1){
					return 2;//In Progress
				}
				else {
					return 3;//Paused
				}
			}
		}catch (Exception $ex){//end try
			MW_Dailydeal_Helper_Data::LogError($ex);
		}
	}
	
	/************************
	 * Process daily deal price
	 * 
	 * 
	 */
	//Dependent function 1
	public function calculateDailydealPrice($originalPrice = null, $discount = null)
	{
		if ($originalPrice === null) {
			$originalPrice = $this->getProduct()->getPrice();
		}
		if ($discount === null)
			$discount = $this->getDiscount();
		switch ($this->getDiscountType())
		{
			case 2:
				// by fixed amount from original price
				$price = round($originalPrice - $discount, 2);
				break;
			case 3:
				// to percentage of original price
				$price = round($originalPrice * ($discount/100), 2);//var_dump($price);die();
				break;
			case 4:
				// to new fixed price
				$price = round($discount, 2);
				break;
			case 1:
			default:
				// by percentage of original price
				$price = round($originalPrice * (1-$discount/100), 2);
		}
		return $price;
	}
	//Dependent function 2
	public function getOriginalPrice($website = null, $backend = false)
	{
		$tax_helper = Mage::helper('tax');
		// get groupsale store
		$store = $this->getStore($website);

		// get price by appropriate display type
		if ((!$backend && $tax_helper->getPriceDisplayType($store) == Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX) ||
			($backend && !Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX, $store)))
			$price = $this->GetProductPrice(false, $website);
		else
			$price = $this->GetProductPrice(true, $website);
		
		return $price;
	}
	//Dependent function 3
		/**
	 * Gets original product price including/excluding tax
	 *
	 * @param	null|bool $tax - null = default, true = including, false = excluding
	 * @param	mixed $store
	 * @return	float $price
	 */
	public function getProductPrice($tax = null, $website = null)
	{
		$tax_helper = Mage::helper('tax');
		$store = $this->getStore($website);
		$product = $this->getProduct(); 
		$price = $product->getPrice();
		$priceIncludesTax = $tax_helper->priceIncludesTax($store);
		$percent = $product->getTaxPercent();
		$includingPercent = null;
		$taxClassId = $product->getTaxClassId();

		if ($percent === null && $taxClassId)
		{
			$request = Mage::getSingleton('tax/calculation')->getRateRequest(null, null, null, $store);
			$percent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxClassId));
		}
		if ($priceIncludesTax && $taxClassId)
		{
			$request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false, $store);
			$includingPercent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxClassId));
		}
		if (($percent === false || $percent === null) && $priceIncludesTax && !$includingPercent)
			//return $store->roundPrice($price);
			return $price;

		if ($priceIncludesTax)
			$price = $this->_CalcProductPrice($price, $includingPercent, false);
		if ($tax || (($tax === null) && ($tax_helper->getPriceDisplayType($store) != Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX)))
			$price = $this->_CalcProductPrice($price, $percent, true);
		//return $store->roundPrice($price);
		return $price;
	}
	//Dependent function of getProductPrice function 1
	protected function _CalcProductPrice($price, $percent, $type)
	{
		if ($type)
			return $price * (1+($percent/100));
		else
			return $price - ($price/(100+$percent)*$percent);
	}
	//Dependent function of getProductPrice function 2
	public function getProduct()
	{//echo 'goi tu obser';die();
		if ($this->hasData('product'))
			return $this->getData('product');
		$product = Mage::getModel('catalog/product')->load($this->getProductId());//var_dump($product);die();
		$this->setProduct($product);
		return $product;
	}
	//Main process daily deal price
	public function getDailydealPrice($website = null, $backend = false)
	{//echo 'Zeus';die();
		// return cached result if present
		//if ($this->hasData('groupsale_price'))
		//	return $this->getData('groupsale_price');
		
		// get price by appropriate display type
		$price = $this->GetOriginalPrice($website, $backend);
		
		// calculate groupsale price
		$gs_price = $this->calculateDailydealPrice($price);

		// store cache
		//$this->setGroupsalePrice($gs_price);
		return $gs_price;
	}
	/************
	 * END Process daily deal price
	 */
	/*************************************************************************
	 * Event Handlers
	 *************************************************************************/
	
//	protected function _afterLoad()
//	{
//		parent::_afterLoad();
//
//		$this->_prepareDatesAfterLoad();
//		$this->_preparePromoAfterLoad();
//
//		$websiteIds = $this->_getData('website_ids');
//		if (is_string($websiteIds)) {
//			$this->setWebsiteIds(explode(',', $websiteIds));
//		}
//		$storeIds = $this->_getData('store_ids');
//		if (is_string($storeIds)) {
//			$this->setStoreIds(explode(',', $storeIds));
//		}
//		
//		$groupIds = $this->getCustomerGroupIds();
//		if (is_string($groupIds)) {
//			$this->setCustomerGroupIds(explode(',', $groupIds));
//		}
//	}
	protected function _beforeSave()
	{
		// Clear product cache (to ensure the observer adds watermark and special price etc.):
		$product = Mage::getModel('catalog/product')->load($this->getProductId());
		$product->cleanCache();
		//$this->_prepareDatesForSave();//Zend_Debug::dump($this);die();
		//$this->setStatus(1);
		//$this->setProductId(1000); Sau khi set dong nay thi chay dc, chang hiu vi sao :))
		//$this->setDiscount(20);
		//$this->setDiscountType(1);
		parent::_beforeSave();
	}

	
}