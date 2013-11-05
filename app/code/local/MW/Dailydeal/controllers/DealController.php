<?php
class MW_Dailydeal_DealController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }
	public function ajaxdealAction()
	{
//		phuong phap ghep chuoi		
//		$day = explode('-', $_GET['currenttime']); 
//		$dayselect = implode(array(date('Y'),'-',$day[1],'-',$day[0],date(' H:i:s',Mage::getModel('core/date')->timestamp(time()))));
		$tblCatalogStockItem = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item');
		$dayselect = $this->getRequest()->getParam('currenttime');  
		$Y = date('Y',Mage::getModel('core/date')->timestamp(time()));
		$startday = implode(array($dayselect,' 00:00:00')); 
		$startdaytime = strtotime($startday); //echo $startdaytime;
		$endday = implode(array($dayselect,' 23:59:59')); 
		$enddaytime = strtotime($endday);
		$deals = Mage::getModel('dailydeal/dailydeal')->getCollection()
														->addFieldToFilter('status','1');	
		$deals->getSelect()->where("deal_qty > sold_qty");			
		$deals->getSelect()->joinLeft(      
	       array('stock'=>$tblCatalogStockItem),     
	       'stock.product_id = main_table.product_id',      
	       array('stock.qty', 'stock.is_in_stock')      
     	);						
		//$deals->getSelect()->where("stock.qty > 0");        
		$deals->getSelect()->where("stock.is_in_stock = 1");
		$currenttime = 	Mage::getModel('core/date')->timestamp(time());							
														
		//echo count($deals);die;												
		//Zend_Debug::dump($deals);die;;
		$names = array();
		$producturls = array();
		$images = array();
		$oldprices = array();
		$dealprices = array(); 		
		$i = 0;				
		if ($deals){
			$_taxHelper  = Mage::helper('tax');		
			$_coreHelper = Mage::helper('core');						
			foreach ($deals as $deal){			
								//Zend_Debug::dump($deal);die;
				$dealtimestart = strtotime($deal->getStartDateTime()); //echo $dealtimestart;die;
				$dealtimeend = strtotime($deal->getEndDateTime());
				
				if (($dealtimestart < $startdaytime && $dealtimeend > $startdaytime)
						|| ($dealtimestart > $startdaytime && $dealtimestart < $enddaytime)
						 ){	
					if ($currenttime <$dealtimeend){				
						$_product = Mage::getModel('catalog/product')->load($deal->getProductId());
						$_regularPrice = $_taxHelper->getPrice($_product, $_product->getPrice(), $_simplePricesTax);
						$names[$i] = $deal->getCurProduct();
						$producturls[$i] = $_product->getProductUrl();				
						$images[$i] = $_product->getImageUrl();			
						$oldprices[$i] = $_coreHelper->currency($_regularPrice,true,false);
						$dealprices[$i] = $_coreHelper->currency($deal['dailydeal_price'],true,false); 
						$i++;			
				
					}/*if ($currenttime <$dealtimeend){*/
				}
			}
		}/*if ($deals){*/
		echo "[".json_encode($producturls).",".json_encode($names).",".json_encode($images).",".json_encode($oldprices).",".json_encode($dealprices)."]";//die;

	}
}