<?php
class FME_Events_Block_Events extends Mage_Catalog_Block_Product_Abstract
{
	public function __construct()
	{
	       $date = Mage::registry('eventDate');
             //  echo "<pre>";print_r($date);
		$store = Mage::app()->getStore()->getId();
       
		$collection = Mage::getModel('events/events')->getCollection();
		$collection->addFieldToFilter('event_id',array('in' => Mage::helper('events')->getSameIdsFromStores($store)));
               // echo "<pre>";print_r($collection->getData());die('ajaj');
		if ($date)
		{
			$collection->getSelect()->where('DATE(event_start_date) <= (?)',$date)
									->where('DATE(event_end_date) >= (?)', $date);
		} 	//echo (string) $collection->getSelect();exit;
		$this->setCollection($collection);
	}
	
	public function _prepareLayout()
    {
		parent::_prepareLayout();

		if ($headBlock = $this->getLayout()->getBlock('head'))
		{
			if ($title = Mage::helper('events')->getSeoInfo('title'))
			{
				$headBlock->setTitle($title);
			}
			
			if ($description = Mage::helper('events')->getSeoInfo('description'))
			{
				$headBlock->setDescription($description);
			}
			
			if ($keywords = Mage::helper('events')->getSeoInfo('keywords')) {
				$headBlock->setKeywords($keywords);
			}
		}
		
			$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
			$breadcrumbs->addCrumb('events', array(
				'label' => Mage::helper('cms')->__(Mage::helper('events')->linkTitleHeader()),
				'title' => Mage::helper('cms')->__(Mage::helper('events')->linkTitleHeader()),
				'link' => false
			));
		
		$this->setTitle(Mage::helper('events')->getSeoInfo('title'));
		$toolbar = $this->getToolbarBlock();
		// called prepare sortable parameters
		$collection = $this->getCollection();
		
		// use sortable parameters
		if ($orders = $this->getAvailableOrders())
		{
			$toolbar->setAvailableOrders($orders);
		}
		
		if ($sort = $this->getSortBy())
		{
			$toolbar->setDefaultOrder($sort);
		}
		
		if ($dir = $this->getDefaultDirection())
		{
			$toolbar->setDefaultDirection($dir);
		}
		
		$toolbar->setCollection($collection);

		$this->setChild('toolbar', $toolbar);
		//echo '<pre>';print_r($this->getCollection());exit;
		$this->getCollection()->distinct('event_id')->load();
		
		return $this;
    }
    
	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
	
	public function getMode()
	{
		return $this->getChild('toolbar')->getCurrentMode();
	}
	
	public function getDefaultDirection()
	{
		return 'asc';
	}
	
	public function getAvailableOrders()
	{
		return array('event_title'=> 'Name','event_venu'=>'Venue','event_start_date'=>'Start Date','event_end_date'=>'End Date');
	}
	
	public function getSortBy()
	{
		return 'event_title';
	}
	
	public function getToolbarBlock()
	{
		$block = $this->getLayout()->createBlock('events/toolbar', microtime());
		
		return $block;
	}

	public function getToolbarHtml()
	{
		return $this->getChildHtml('toolbar');
	}
	
    public function getEvents()     
    { 
       if (!$this->hasData('events'))
	   {
           $this->setData('events', Mage::registry('events'));
       }
		
       return $this->getData('events');
    }
	
	
}
