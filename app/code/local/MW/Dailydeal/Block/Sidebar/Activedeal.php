<?php
class MW_Dailydeal_Block_Sidebar_Activedeal extends Mage_Core_Block_Template
{
	public function getActivedeals()
	{
		$tblCatalogStockItem = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item');
		    $currenttime = date('Y-m-d H:i:s',Mage::getModel('core/date')->timestamp(time()));
		
		    $deals = Mage::getModel('dailydeal/dailydeal')
	    			->getCollection() 
    				->addFieldToFilter('status','1')   	
    				->addFieldToFilter('start_date_time',array('to' => $currenttime))
 					->addFieldToFilter('end_date_time',array('from' => $currenttime))					
    				->addAttributeToSort('start_date_time','ASC');
    		$deals->getSelect()->where("deal_qty > sold_qty");
			$deals->getSelect()->joinLeft(      
	       array('stock'=>$tblCatalogStockItem),     
	       'stock.product_id = main_table.product_id',      
	       array('stock.qty', 'stock.is_in_stock')      
     	);						
		//$deals->getSelect()->where("stock.qty > 0");        
		$deals->getSelect()->where("stock.is_in_stock = 1");
    		$deals->load();
			return $deals;
	}
}