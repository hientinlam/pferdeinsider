<?php
class MW_Dailydeal_Block_Showtabs_Past extends Mage_Core_Block_Template
{
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}
	    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }
  /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getPastdeals();

        // use sortable parameters
//        if ($orders = $this->getAvailableOrders()) {
//            $toolbar->setAvailableOrders($orders);
//        }
//        if ($sort = $this->getSortBy()) {
//            $toolbar->setDefaultOrder($sort);
//        }
//        if ($dir = $this->getDefaultDirection()) {
//            $toolbar->setDefaultDirection($dir);
//        }
//        if ($modes = $this->getModes()) {
//            $toolbar->setModes($modes);
//        }

        $toolbar->setAvailableOrders(array('dailydeal_id' => 'ID'));
        $toolbar->setDefaultOrder('dailydeal_id');
        $toolbar->setDefaultDirection('DESC');
        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
//        Mage::dispatchEvent('catalog_block_product_list_collection', array(
//            'collection' => $this->_getPastdealsCollection()
//        ));

        $this->getPastdeals()->load();
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
//        if ($blockName = $this->getToolbarBlockName()) {
//            if ($block = $this->getLayout()->getBlock($blockName)) {
//                return $block;
//            }
//        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }
    
 /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }
	/**
 	 * Pagination
 	 * */
 	public function getPastdeals()
 	{
 		$currenttime = date('Y-m-d H:i:s',Mage::getModel('core/date')->timestamp(time()));
 		$pastdeals_collection= Mage::getModel('dailydeal/dailydeal')->getCollection()
									->addFieldToFilter('status','1')
 									->addFieldToFilter('end_date_time',array('to' => $currenttime))
									//->addFieldToFilter('deal_qty',array('to' => 0))
 		->setOrder('dailydeal_id', 'DESC');	
		$pastdeals_collection->getSelect()->orwhere("deal_qty <= sold_qty");
		//print_r($pastdeals_collection->getSelect()->__toString());die;	
 		 $currentPage = (int)$this->getRequest()->getParam('page');
		    if(!$currentPage){
		        $currentPage = 1;
		    }		    	
		
		    $currentLimit = (int)$this->getRequest()->getParam('limit');	
		    //Dinh so tin duoc hien thi tren 1 trang
		    $pastdeals_collection->setPageSize($currentLimit);
		    //Hien thong tin theo chi so trang
		    $pastdeals_collection->setCurPage($currentPage);
					
		    return $pastdeals_collection;
 	}
   public function getPriceHtml($product)
    {
        $this->setTemplate('catalog/product/price.phtml');
        $this->setProduct($product);
        return $this->toHtml();
    }
}