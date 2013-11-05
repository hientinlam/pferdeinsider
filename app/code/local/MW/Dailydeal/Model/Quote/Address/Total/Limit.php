<?php 
class MW_Dailydeal_Model_Quote_Address_Total_Limit extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	public function collect(Mage_Sales_Model_Quote_Address $address)
	{

	    $quote = $address->getQuote(); //Mage::log($quote->getBillingAddress()->getEmail(),null,'quote.log');
	    $billingemail = $quote->getBillingAddress()->getEmail();
	    $customeremail = $quote->getCustomerEmail();
	 //   Mage::log($billingemail,null,'billingaddress.log');	
        $items = $address->getAllVisibleItems();
//		$items = $quote->getAllItems();
		if (!count($items)) {
            return $this;
        }
        $totalcount = 0;
        $totallimit = (int)Mage::getStoreConfig('dailydeal/general/limitpercustomer');
//        Voi moi 1 item kt trong deal xem tai thoi diem nay, co KH nao da mua
//        neu co thilay ra so luong, + them so luong trong checkout card.
//        neu tong so qua lon thi xuat ra bao loi
//        Luu y: Viec kt thoi diem dc thuc hien luc sales order
        foreach ($items as $item){
        		$parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')
    						->getParentIdsByChild($item->getProduct()->getId());
    			 
    			if ($parentIds[0]){
    				$deal = Mage::getModel('dailydeal/dailydeal')->getCollection()
	        			->loadcurrentdeal($parentIds[0]);
    			}else {

    				$deal = Mage::getModel('dailydeal/dailydeal')->getCollection()
	        			->loadcurrentdeal($item->getProduct()->getId());
    			}
	        			//Zend_Debug::dump($deal);die;
	        	if ($deal){
	        		//$customerarray = array();
		        	$customerarray = explode(',', $deal['customer_group_ids']);
		        	$i = 0;
		        	$totalcount = 0;
		        	while ($i < count($customerarray)){//Mage::log($customerarray[$i],null,'customer.log');
		        		$customer = explode(':', $customerarray[$i]);
		        		$emailcustomer = $customer[0]; //var_dump($customer);
		        		$buyqtycustomer = $customer[1];//var_dump($buyqtycustomer);//die;
		        		//var_dump($quote->getEmail());
			        		if ($emailcustomer != null && $emailcustomer == $billingemail || $emailcustomer == $customeremail){
			        			$totalcount = $buyqtycustomer;
			        			break;	
			        		}
		        		$i++;
		        	}/*while ($customerarray[$i] != null){*/		
//		        	var_dump($item->getQty());die;	        	
		        	$totalcount += $item->getQty();//break;
//		        	var_dump($totalcount);die;
		        	if($totalcount > $totallimit){	      	
			        	//Zend_Debug::dump($item->getParentItem());die;
			        	$item->setQty(0);
			        	$this->_getSession()->addError('Quantity you chose exceed the deal quantity that you are allowed to buy!');
			        	break;
        			}/*if($totalcount != $totallimit){*/      
	        	}/*if ($deal){*/
  	
        }/*foreach ($items as $item){*/
       //var_dump($totalcount);die;
        parent::collect($address);
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
}