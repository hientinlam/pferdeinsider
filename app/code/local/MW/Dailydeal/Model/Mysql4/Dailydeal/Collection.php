<?php

class MW_Dailydeal_Model_Mysql4_Dailydeal_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	
	/**
	* Intervals
	*
	* @var int
	*/
	protected $_intervals;
	/**
	* Array of store ids
	*
	* @var array
	*/
	protected $_storeIds;
	/**
	* Set store ids
	*
	* @param array $storeIds
	* @return Mage_Reports_Model_Resource_Report_Collection
	*/
	public function setStoreIds($storeIds)
	{
		$this->_storeIds = $storeIds;
		return $this;
	}
	/**
	* Get store ids
	*
	* @return arrays
	*/
	public function getStoreIds()
	{
		return $this->_storeIds;
	}
	/**
	* Resource initialization
	*
	*/
	
	/**
	* Get size, ham nay tam thoi chua dung den
	*
	* @return int
	*/
	public function getSigze()
	{
		return count($this->getIntervals());
	}
	/**
	* Set interval
	*
	* @param int $from
	* @param int $to
	* @return Mage_Reports_Model_Resource_Report_Collection
	*/
	public function setInterval($from, $to)
	{
		$this->_from = $from;
		$this->_to   = $to;
		
		return $this;
	}
	/**
	* Get intervals
	*
	* @return unknown
	*/
	public function getIntervals()
	{
		if (!$this->_intervals) {
			$this->_intervals = array();
			if (!$this->_from && !$this->_to) {
				return $this->_intervals;
			}
			$dateStart  = new Zend_Date($this->_from);
			$dateEnd    = new Zend_Date($this->_to);
			$t = array();
			while ($dateStart->compare($dateEnd) <= 0) {        
				$t['title'] = $dateStart->toString(Mage::app()->getLocale()->getDateFormat());
				$t['start'] = $dateStart->toString('yyyy-MM-dd HH:mm:ss');
				$t['end'] = $dateStart->toString('yyyy-MM-dd 23:59:59');
				$dateStart->addDay(1);                                     
				$this->_intervals[$t['title']] = $t;//Zend_Debug::dump($this->_intervals);die();
			}
		}
		return  $this->_intervals;
		}    
	/**
	* get report full
	*
	* @param int $from
	* @param int $to
	* @return unknown
	*/
	public function getReportFull($from, $to)
	{
		return $this->_model->getReportFull($this->timeShift($from), $this->timeShift($to));
	}
	
	/**
	* Get report
	*
	* @param int $from
	* @param int $to
	* @return Varien_Object
	*/
	public function getDeal($from, $to)
	{
		return $this->getDaily($this->timeShift($from), $this->timeShift($to))
			->addFieldToFilter('start_date_time',array('from' => $from, 'to' => $to));
	}
	public function getDaily($from, $to)
	{
		//		return $this->setDateRange($from, $to)
			//					->setPageSize($this->getPageSize())
			return 			$this->setStoreIds($this->getStoreIds());
	}
	/**
	* Retreive time shift
	*
	* @param string $datetime
	* @return string
	*/
	//    public function timeShift($datetime)
	//    {
		//        return Mage::app()->getLocale()
			//            ->utcDate(null, $datetime, true, Varien_Date::DATETIME_INTERNAL_FORMAT)
			//            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
	//    }
	public function _construct()
	{
		parent::_construct();
		$this->_init('dailydeal/dailydeal');
	}
	public function loadcurrentdeal($productid=null)
	{
		$now = date('Y-m-d H:i:s',Mage::getModel('core/date')->timestamp(time()));
		$query = $this->addFieldToFilter('status', 1)
			->addFieldToFilter('product_id', $productid)
			->addFieldToFilter('start_date_time', array('to' => $now))
			->addFieldToFilter('end_date_time', array('from' => $now));
		$query->getSelect()->where("deal_qty > sold_qty");		
		$query->load();
		$active_dailydeal = null;
		if (count($this->getItems()))
			{ 
			$active_dailydeal = $this->getFirstItem();
		}
		return $active_dailydeal;
	}
	
	public function loadmoredeal($productid=null)
	{
		$now = date('Y-m-d H:i:s',Mage::getModel('core/date')->timestamp(time()));
		$query = $this->addFieldToFilter('status', 1)
			->addFieldToFilter('product_id', $productid)
			//->addFieldToFilter('start_date_time', array('to' => $now))
			->addFieldToFilter('end_date_time', array('from' => $now));
		$query->getSelect()->where("deal_qty > sold_qty");		
		$query->load();
		$active_dailydeal = null;
		if (count($this->getItems()))
			{ 
			$active_dailydeal = $this->getFirstItem();
		}
		return $active_dailydeal;
	}
	
	public function loadDailydeal($product_sku=null, $dailydeal_id=null)
	{
		if (method_exists($this, 'addFieldToSelect'))
			$this->addFieldToSelect('*');
		$now = date('Y-m-d H:i:s',Mage::getModel('core/date')->timestamp(time())); 
		
		$query = $this->addFieldToFilter('start_date_time', array('to' => $now))
			->addFieldToFilter('end_date_time', array('from' => $now))
			->addFieldToFilter('status', 1);
		//Neu co product_sku thi loc theo sku
		if ($product_sku !== null)
			$query->addFieldToFilter('product_sku', $product_sku);
		//Neu co dailydeal _id thi loc tiep theo id cua deal
		if ($dailydeal_id !== null)
			$query->addFieldToFilter('dailydeal_id', $dailydeal_id);
		
		$query->load();//var_dump($query);
		$active_dailydeal = null;  
		//var_dump(count($this->getItems()));die();
		if (count($this->getItems()) )
			{ //echo 'dgdgd';
			$active_dailydeal = $this->getFirstItem();//var_dump($active_dailydeal);die();
			//			if (!$active_dailydeal->isValidForCurrentRequest())
				//				return null;
		}
		
		//return $active_dailydeal;
	}
	public function getActiveDailydeal($product = null)
	{
		if (gettype($product) != 'object') return null;
		if ($product->hasMW_Dailydeal()) 		
		return $product->getMW_Dailydeal();
		$active_dailydeal = $this->loadDailydeal($product->getSku());//Zend_Debug::dump($active_dailydeal);//die;
		$product->setMW_Dailydeal($active_dailydeal);//var_dump($active_dailydeal);die();
		return $active_dailydeal;
	}
	protected $_grid;
	
	public function setGrid($grid)
	{
		$this->_grid = $grid;
		return $this;
	}
	protected $_loadProducts;
	public function setLoadProducts($bool)
	{
		$this->_loadProducts = $bool ? true : false;
		return $this;
	}
	protected $_rawDates;
	public function setRawDates($bool)
	{
		$this->_rawDates = $bool ? true : false;
		return $this;
	}
	/**
	* Add attribute to sort order
	*
	* @param string $attribute
	* @param string $dir
	* @return Mage_Catalog_Model_Resource_Product_Collection
	*/
	public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
	{
		$this->getSelect()->order($attribute . " " . $dir);
		return $this;
	}
}