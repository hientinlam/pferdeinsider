<?php
class MW_Dailydeal_Model_Observer
{
    /**
     * 
     * Set gia cuoi cho collection product, product detail, sidebar,...
     * @param unknown_type $observer
     */
    /**************************************************************
     * Private/Protected  Methods
     **************************************************************/
    protected function _SetProductPriceAndWatermark($product, $needsWatermark)
    {
        // mark the start time
        $start_time           = MICROTIME(TRUE);
        $dailydeal_collection = Mage::getModel('dailydeal/dailydeal')->getCollection();
        $deal                 = $dailydeal_collection->loadcurrentdeal($product->getId());
        if ($deal) {
            if ($deal->getDealQty() > $deal->getSoldQty()) {
                $product->setSpecialPrice($product->getPrice());
                $product->setFinalPrice($deal->getDailydealPrice());
            }
        }
        // mark the stop time
        $stop_time = MICROTIME(TRUE);
        // get the difference in seconds
        $time      = $stop_time - $start_time;
    }
    
    protected function _SetProductPriceAndWatermark1($product, $dailyDealPrice)
    {
        if ($product) {
            $product->setSpecialPrice($product->getPrice());
            $product->setFinalPrice($dailyDealPrice);
        }
    }
    
    public function catalog_product_get_final_price($observer)
    {
        try {
            $product    = $observer->getProduct(); //Zend_Debug::dump($product);die();			
            $activedeal = Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), 'activedeal', "");
            
            if ($product && $activedeal == 1 /*product is active deal*/ ) {
                $this->_SetProductPriceAndWatermark($product, true);
            }
        }
        catch (Exception $ex) {
            MW_Dailydeal_Helper_Data::LogError($ex);
        }
    }
    
    public function catalogproduct_collectionload_after($observer)
    {
        try {
            // Go over all of the products:
            foreach ($observer->getCollection()->getItems() as $product) {
                $activedeal = Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), 'activedeal', "");
                if ($activedeal == 1 /*product is active deal*/ )
                    $this->_SetProductPriceAndWatermark($product, true);
            }
        }
        catch (Exception $ex) {
            MW_Dailydeal_Helper_Data::LogError($ex);
        }
    }
    
    /**
     * 
     * Kt quantity trc khi add vao shopping cart
     * type product: simple, configurable
     * @param unknown_type $observer
     */
    public function checkoutcart_productadd_after($observer)
    {
        $event       = $observer->getEvent();
        //        $product = $event->getProduct();
        $quoteItem   = $event->getQuoteItem();
        $productItem = $event->getProduct();
        //Zend_Debug::dump($event->getQuoteItem()->getProduct()->debug());die;
        // also tried this:
        //$quoteItem['product']['cart_qty'] *= 2;
        
        //$quoteItem->getQuote()->collectTotals();
        //$quoteItem->getQuote()->save();
        
        //Neu so luong s/p nhap vao lon hon deal thi thong bao loi~ va set qty ve 1.
        //if ($p = $observer->getQuoteItem()->getParentItem()){
        
        /**
         * Kiem soat tong product
         * Hien tai dang co deal nen it nhat se qty left se con lai it nhat la 1
         * Simple product: nhap tong lon hon thi se set ve 1, luc nay deal se het qty va ko con deal nua
         * Configurable product: neu deal qty left con 1 thi chi cho phep add to cart 1 option. Dieu nay lam update
         * item se lam viec chuan hon.
         */
        if ($quoteItem) {
            if ($productItem->getTypeId() == "configurable") {
                $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($quoteItem->getProductId());
                //Mage::log($event->getProduct()->getTypeId(),null,'type[rpduct.log');
                $deal      = Mage::getModel('dailydeal/dailydeal')->getCollection()->loadcurrentdeal($parentIds[0]);
                if ($deal) {
                    $dealqty = $deal->getDealQty();
                    $soldqty = $deal->getSoldQty();
                    
                    if ($productItem->getQty() > ($dealqty - $soldqty)) {
                        $p = $observer->getQuoteItem()->getParentItem();
                        $p->setQty(0); //Mage::log($quoteItem->getQty(),null,'type[rpduct.log');
                        //					$message = $product->getName();
                        $this->_getSession()->addError('The quantity that you have inserted is over deal quantity left. Please reinsert another one!');
                    }
                }
            } else {
                $deal = Mage::getModel('dailydeal/dailydeal')->getCollection()->loadcurrentdeal($quoteItem->getProductId());
                if ($deal) {
                    $dealqty = $deal->getDealQty();
                    $soldqty = $deal->getSoldQty();
                    
                    if ($productItem->getQty() > ($dealqty - $soldqty)) {
                        $quoteItem->setQty(0);
                        $message = $productItem->getName();
                        $this->_getSession()->addError('The quantity that you have inserted is over deal quantity left. Please reinsert another one!');
                    }
                }
            }
        }
        /*if ($quoteItem){*/
        return $this;
    }
    /**
     * 
     * Update lai quantity trong shopping cart, xem co vuot qua so luong deal con lai ko?
     * type product: simple, configurable
     * @param unknown_type $observer
     */
    
    public function checkoutcart_updateitems_after($observer)
    {
        $_this        = $observer->cart;		
        $billingemail = $_this->getQuote()->getBillingAddress()->getEmail(); //->getEmail();
        $datas        = $observer->info;
        foreach ($datas as $dataId => $dataInfo) {
            $item = $_this->getQuote()->getItemById($dataId); //var_dump($item->getQty());die;
            if (!$item) {
                continue;
            }
            //	        Load deal, trong event nay, id se dc tu dong nhan dang la thuoc simple hay configurable
            //			nen ko can load parentID
            //	        Simple product: 
            //	        Configurable product: 
            $deal = Mage::getModel('dailydeal/dailydeal')->getCollection()->loadcurrentdeal($item->getProductId());            
            if ($deal) {
                $dealqty = $deal->getDealQty();
                $soldqty = $deal->getSoldQty();
                //	  		  	Voi Configruable, neu chi con 1 item left thi du cho bat cu option nao cua configruable product
                //	  		  	duoc them vao cung ko thoa man, vi so luong se + thanh 2
                //	  		  	KT theo quantity left
                if ($dataInfo['qty'] > ($dealqty - $soldqty)) {
                    //Dung de xoa item ko thoa man
                    //	        		if (!empty($dataInfo['remove']) || (isset($dataInfo['qty']) && $dataInfo['qty']=='0')) {
                    //	            	$this->removeItem($dataId);
                    //	            	continue;
                    //	        		}			
                    $item->setQty($dataInfo['qty']);
                    $message = $item->getName();
					$qty_left = $dealqty - $soldqty;
                    $this->_getSession()->addError("The quantity that you have inserted is over deal quantity left ($qty_left). Please reinsert another one!");
                } else {
                    /*if ($item->getQty() > ($dealqty-$soldqty)) {*/
                    //        		KT theo quantity per customer
                    $totalcount = 0;
                    $totallimit = (int) Mage::getStoreConfig('dailydeal/general/limitpercustomer');
                    if ($totallimit > 0) {
                        //$customerarray = array();
                        $customerarray = explode(',', $deal['customer_group_ids']);
                        $i             = 0;
                        $totalcount    = 0;
                        while ($i < count($customerarray)) {
                            Mage::log($customerarray[$i], null, 'customer.log');
                            $customer       = explode(':', $customerarray[$i]);
                            $emailcustomer  = $customer[0]; //var_dump($customer);
                            $buyqtycustomer = $customer[1]; //var_dump($buyqtycustomer);die;
                            //var_dump($quote->getEmail());
                            if ($emailcustomer != null && $emailcustomer == $billingemail || $emailcustomer == $customeremail) {
                                $totalcount = $buyqtycustomer;
                                break;
                            }
                            $i++;
                        }
                        /*while ($customerarray[$i] != null){*/
                        
                        //		        	var_dump($item->getQty());die;	        	
                        $totalcount += $dataInfo['qty']; //break;
                        //		        	var_dump($totalcount);die;
                        if ($totalcount > $totallimit) {
                            //Zend_Debug::dump($item->getParentItem());die;											
                            $this->_getSession()->addError("Quantity you chose exceed the deal quantity ($totallimit) that you are allowed to buy!");
                            //;
                            $item->setQty($dataInfo['qty']);
                            break;
                        }
                        /*if($totalcount != $totallimit){*/
                    }
                    /*if ($totallimit > 0){*/
                }
            }
        }		
        //Dung de tra lai ket qua da setQty
        return $this;
    }
    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    /**
     * 
     * save order
     * @param unknown_type $observer
     */
    
    
    public function salesorder_placeafter($observer)
    {
        //		$getOrder = $observer->getEvent()->getOrder();//var_dump($getOrder->getId());die;
        //		$getOrder->setId(0);
        ////		$getOrder->save();
        ////		$order = Mage::getModel('sales/order')->load($getOrder->getId());
        ////$order->setState(Mage_Sales_Model_Order::STATE_CANCELED); //or whatever distinct order status you'd like
        ////$order->save();
        //		
        //Mage::getSingleton('checkout/session')->addError('Sorry, either of your card information (billing address or card validation digits) dint match. Please try again');                 
        ////
        //            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('checkout/onepage/failure'))->sendResponse();
        //		Mage::app()->getResponse()->sendHeaders();
        //		return $this;
        //					Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('checkout/cart'))->sendResponse();		//echo 'test';die;	        					        		
        //					Mage::app()->getResponse()->sendHeaders();exit();
        // check if groupsale extension is enabled and yet to be treated
        if (($observer == null) || (MW_Dailydeal_Helper_Data::IsZizioGroupsaleEnabled() == false))
            return $this;
        // get order instance
        $order        = $observer->getEvent()->getOrder();
        $billingemail = $order->getBillingAddress()->getEmail();
        //Lay tat ca cac item trong shopping cart
        $items        = $order->getAllVisibleItems(); //var_dump(count($order->getAllItems()));die;
        foreach ($items as $item) {
            $productid = $item->getProductId();
            if ($productid) {
                $deal = Mage::getModel('dailydeal/dailydeal')->getCollection()->loadcurrentdeal($productid); //Zend_Debug::dump($deal);die;
                if ($deal) {
                    //					
                    //Lay tat ca item da order
                    //check quantity have ordered, 
                    $orderitems  = Mage::getModel('sales/order_item')->getCollection(); //Zend_Debug::dump($orderitems);die;
                    $soldlattest = 0;
                    foreach ($orderitems as $orderitem) { //Mage::log($orderitem->getProductId(),null,'orderitems.log');
                        $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($orderitem->getProductId()); //echo $parentIds[0].'-'.$productid.' ';
                        if (($orderitem->getSku() == $deal->getProductSku()) || ($parentIds[0] == $productid)) { //echo $orderitem->getQtyOrdered();die;
                            $createdat = Mage::getModel('core/date')->timestamp(strtotime($orderitem->getCreatedAt()));
                            $dealstart = strtotime($deal->getStartDateTime());
                            $dealend   = strtotime($deal->getEndDateTime());
                            
                            if (($createdat > $dealstart) && ($createdat < $dealend)) {
                                $soldlattest += round($orderitem->getQtyOrdered()); //Mage::log($orderitem->getCreatedAt(),null,'saleorder.log');
                            }
                        }
                    }
                    /*foreach ($orderitems as $orderitem){*/
                    //If quantity of ordered product more than or equal quantity of product in deal, 
                    //then change status of that deal to "disabled"
                    //					var_dump($soldlattest);die;		
                    // Update information about customer and qty buy, chuoi ttin se co dang nhu sau:
                    //	aa:a,bb:b,cc:c
                    //	aa,bb,bb: dia chi email
                    //	a,b,c: so luong da mua
                    $listcustomer = $deal->getCustomerGroupIds();
                    
                    $explodecomma = explode(",", $listcustomer);
                    if ($explodecomma[0] != null) {
                        //						Mage::log($listcustomer,null,'listcustomer.log');
                        $updatelist = '';
                        $exist      = false;
                        for ($i = 0; $i < count($explodecomma) - 1; $i++) {
                            //							var_dump($billingemail);							
                            $explodecotton = explode(":", $explodecomma[$i]);
                            if ($explodecotton[0] != null && $billingemail == $explodecotton[0]) {
                                $updateqty         = $explodecotton[1] + $item->getQtyOrdered();
                                $updateinformation = implode(array(
                                    $explodecotton[0],
                                    ":",
                                    $updateqty
                                ));
                                $updatelist        = implode(array(
                                    $updateinformation,
                                    ",",
                                    $updatelist
                                ));
                                $exist             = true;
                            } else {
                                $updatelist = implode(array(
                                    $explodecomma[$i],
                                    ",",
                                    $updatelist
                                ));
                            }
                        }
                        if ($exist == false) {
                            $updateinformation = implode(array(
                                $billingemail,
                                ":",
                                $item->getQtyOrdered()
                            ));
                            $updatelist        = implode(array(
                                $updateinformation,
                                ",",
                                $listcustomer
                            ));
                        }
                    } else {
                        /*if ($listcustomer){*/
                        $updatelist = implode(array(
                            $billingemail,
                            ":",
                            $item->getQtyOrdered(),
                            ","
                        ));
                    }
                    //						var_dump($updatelist);die;						
                    
                    //Mage::log($updatelist,null,'updatelist.log');
                    //					var_dump($updatelist);die;
                    $deal->setCustomerGroupIds($updatelist); //Zend_Debug::dump($latestlistcustomer);die;					
                    $deal->setSoldQty($soldlattest)->save();
                    
                    //					var_dump($deal->getCustomerGroupIds());		die;
                }
                /*if ($deal){*/
            }
            /*if	($productid){*/
        }
        /*foreach ($items as $item){*/
        return $this;
    }
    
    protected function _getProductDeal($product)
    {
        // mark the start time
        $start_time           = MICROTIME(TRUE);
        $dailydeal_collection = Mage::getModel('dailydeal/dailydeal')->getCollection();
        $deal                 = $dailydeal_collection->loadcurrentdeal($product->getId());
        if ($deal) {
            if ($deal->getDealQty() > $deal->getSoldQty()) {
                return $deal;
            }
        }
        return null;
    }
    
    public function _getProductId($product)
    {
        // mark the start time
        $start_time           = MICROTIME(TRUE);
        $dailydeal_collection = Mage::getModel('dailydeal/dailydeal')->getCollection();
        $deal                 = $dailydeal_collection->loadmoredeal($product->getId());
        if ($deal) {
            if ($deal->getDealQty() > $deal->getSoldQty()) {
                return $deal;
            }
        }
        return null;
    }
    
    
    public function runCron()
    {
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );
        
        $storeId = Mage::app()->getStore()->getId();
        
        $_productCollection = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*')->addAttributeToFilter('visibility', $visibility)->setStoreId($storeId)->addStoreFilter($storeId);
        foreach ($_productCollection as $product) {
            $activedeal = Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), 'activedeal', "");
            if ($activedeal == 1) {				
                $productDeal = $this->_getProductDeal($product, true);
                if ($productDeal == null) {
                    $_product = Mage::getModel('catalog/product')->load($product->getId());
                    $_product->setData("activedeal", 0);
                    $_product->save();
                }
            }           
        }
    }
	
	public function checkQuoteItemQty($observer){
	
		$quoteItem = $observer->getEvent()->getItem();
		$result = new Varien_Object();
		$result->setHasError(false);
		
        /* @var $quoteItem Mage_Sales_Model_Quote_Item */
        if (!$quoteItem || !$quoteItem->getProductId() || !$quoteItem->getQuote()
            || $quoteItem->getQuote()->getIsSuperMode()) {
            return $this;
        }

        /**
         * Get Qty
         */
        $qty = $quoteItem->getQty();
		
		$_product = Mage::getModel('catalog/product')->load($quoteItem->getProductId());
		$productDeal = $this->_getProductDeal($_product, true);
		
		if($productDeal != null){		
			$currentQty = $productDeal->getData('deal_qty')-$productDeal->getData('sold_qty');	
			if($qty > $currentQty){
				$message = Mage::helper('cataloginventory')->__("The requested quantity for '%s' not available in Deal. Deal quantity left: $currentQty", $productDeal->getData('cur_product'));
            	$result->setHasError(true)
                ->setMessage($message)
                ->setQuoteMessage($message)
                ->setQuoteMessageIndex('qty');
			}	
			$totallimit = (int) Mage::getStoreConfig('dailydeal/general/limitpercustomer');
			if($totallimit>0){
			if($qty >$totallimit){						
				$message = Mage::helper('cataloginventory')->__("Quantity you chose exceed the deal quantity (%s) that you are allowed to buy!", $totallimit);
            	$result->setHasError(true)
                ->setMessage($message)
                ->setQuoteMessage($message)
                ->setQuoteMessageIndex('qty');
			}
			}
		}					
     	if ($result->getHasError()) {
            $quoteItem->addErrorInfo(
                'cataloginventory',								
                Mage_CatalogInventory_Helper_Data::ERROR_QTY,
                $result->getMessage()
            );

            $quoteItem->getQuote()->addErrorInfo(
                $result->getQuoteMessageIndex(),
                'cataloginventory',
                Mage_CatalogInventory_Helper_Data::ERROR_QTY,
                $result->getQuoteMessage()
            );
        }
		return $this;
	}
}