<?php
class AW_Affiliate_Block_Campaign_Embeded extends Mage_Core_Block_Template
{

	public function __construct()
	{
        
		parent::__construct();
                
                $collection = Mage::getResourceModel('catalog/product_collection');     
                $this->setCollection($collection);
	}

	protected function _prepareLayout()
	{
		parent::_prepareLayout();

		$parent_id = Mage::app()->getStore()->getRootCategoryId();
		if($this->getRequest()->getParam('category_id',false)){
			$parent_id = $this->getRequest()->getParam('category_id');
		}
                
		$collection = $this->getCollection();
		$this->getCollection()->load();
		return $this;
	}
	
}