<?php
class MW_Dailydeal_Block_Showtabs_Active extends Mage_Core_Block_Template
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
    protected $_defaultToolbarBlock = 'dailydeal/showtabs_product_list_toolbar';
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
        $collection = $this->getActivedeals();

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

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
//        Mage::dispatchEvent('catalog_block_product_list_collection', array(
//            'collection' => $this->_getActivedealsCollection()
//        ));

        $this->getActivedeals()->load();
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
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
 	public function getActivedeals()
 	{
		$tblCatalogStockItem = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item');
 		$currenttime = date('Y-m-d H:i:s',Mage::getModel('core/date')->timestamp(time()));
 		$activedeals_collection= Mage::getModel('dailydeal/dailydeal')->getCollection()
 									->addFieldToFilter('status','1')
 									->addFieldToFilter('start_date_time',array('to' => $currenttime))
 									->addFieldToFilter('end_date_time',array('from' => $currenttime)) 
 									->setOrder('dailydeal_id', 'DESC');
		$activedeals_collection->getSelect()->where("deal_qty > sold_qty"); 	
		$activedeals_collection->getSelect()->joinLeft(      
	       array('stock'=>$tblCatalogStockItem),     
	       'stock.product_id = main_table.product_id',      
	       array('stock.qty', 'stock.is_in_stock')      
     	);						
		//$activedeals_collection->getSelect()->where("stock.qty > 0");        
		$activedeals_collection->getSelect()->where("stock.is_in_stock = 1");     		
		//print_r($activedeals_collection->getSelect()->__toString())		;die;
 		 $currentPage = (int)$this->getRequest()->getParam('page');
		    if(!$currentPage){
		        $currentPage = 1;
		    }		    	
		
		    $currentLimit = (int)$this->getRequest()->getParam('limit');	
		    //Dinh so tin duoc hien thi tren 1 trang
		    $activedeals_collection->setPageSize($currentLimit);
		    //Hien thong tin theo chi so trang
		    $activedeals_collection->setCurPage($currentPage);
		    return $activedeals_collection;
 	}
   public function getPriceHtml($product)
    {
        $this->setTemplate('catalog/product/price.phtml');
        $this->setProduct($product);
        return $this->toHtml();
    }
    /**
     * Retrieve url for direct adding product to cart
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array())
    {
        if ($this->hasCustomAddToCartUrl()) {
            return $this->getCustomAddToCartUrl();
        }

        if ($this->getRequest()->getParam('wishlist_next')){
            $additional['wishlist_next'] = 1;
        }

        $addUrlKey = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $addUrlValue = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => false));
        $additional[$addUrlKey] = Mage::helper('core')->urlEncode($addUrlValue);

        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }
}