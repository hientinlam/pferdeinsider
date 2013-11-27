<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento community edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento community edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Affiliate
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Affiliate_Model_Transaction_Profit extends Mage_Core_Model_Abstract
{
    const XML_PATH_RATIO_PFERDE = 'brst_experts/earnings_ratio/pferde';
    const XML_PATH_RATIO_AFF_NEW = 'brst_experts/earnings_ratio/affiliate_newcustomer';
    const XML_PATH_RATIO_AFF_SPECIAL = 'brst_experts/earnings_ratio/affiliate_special';
    const XML_PATH_RATIO_AFF_REPEAT = 'brst_experts/earnings_ratio/affiliate_repeatcustomer';

    const XML_PATH_TAX_RATE_SMALL_BUSINESS = 'brst_experts/tax_rate/small_business';
    const XML_PATH_TAX_RATE_BIG_BUSINESS = 'brst_experts/tax_rate/big_business';

    public function _construct()
    {
        $this->_init('awaffiliate/transaction_profit');
    }

    public function createTransaction($order = null)
    {
        $isDirectPurchase = true;

        if ($this->_isValidForCreation()) {
            $order = $this->getLinkedEntityOrder();
            $isDirectPurchase = false;
        }

        /* @var $order Mage_Sales_Model_Order */
        if ($order == null || !$order->getId()) {
            Mage::throwException($this->__('Not valid for creating transaction'));
        }

        // Check whether customer is new (only has this order)
        $orderCollection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('customer_id', $order->getCustomerId())
            ->addFieldToFilter('state', Mage_Sales_Model_Order::STATE_COMPLETE);
        $isNewCustomer = ($orderCollection->count() == 1); //This order only.

        foreach ($order->getAllItems() as $orderItem) {
            /* @var $orderItem Mage_Sales_Model_Order_Item */
            $product = $orderItem->getProduct();
            $expertModel = $this->_getExpertModel($product);
            $affiliateModel = $this->_getAffiliateModel($isDirectPurchase);
            $customerModel = $this->_getCustomerModel($order->getCustomerId());

            $this->_saveToDiscountTable($customerModel, $product);
            $this->_saveToPaymentTable();
            $this->_saveToAmountTable();
        }
    }

    protected function _saveToDiscountTable(Mage_Customer_Model_Customer $expertModel, Mage_Catalog_Model_Product $product)
    {
        $modelToSave = Mage::getModel('brst_calculate/discount')->setId(null) //New blank model
            ->setMemberId($expertModel->getId())
            ->setMemberName($this->_getExpertName($product))
            ->setTotalOrder(0)
            ->setGrossEarned(0)
            ->setAmountEarned(0)
            ->setTaxPaid(0)
            ->setAdminAmount(0)
            ->setBalance(0)
            ;

        $modelCollection = Mage::getModel('brst_calculate/discount')->getCollection()->addFieldToFilter('member_id', $expertModel->getId());
        if($modelCollection->count() > 0) { // Has previous data
            foreach($modelCollection as $model) {
                $modelToSave = $model;
                break;
            }
        }

        $modelToSave->setTotalOrder(intval($modelToSave->getTotalOrder()) + 1)
            ->setGrossEarned()
            ->setAmountEarned(0)
            ->setTaxPaid(0)
            ->setAdminAmount(0)
            ->setBalance(0)
        ;



    }

    protected function _saveToPaymentTable()
    {

    }

    protected function _saveToAmountTable()
    {

    }

    protected function _getAdminShareRatio()
    {
        return $this->_getConfigPercentage(self::XML_PATH_RATIO_PFERDE);
    }

    protected function _getAffiliateEarningTypeAndRatio(Mage_Catalog_Model_Product $product, $isNewCustomer = false, $isDirectPurchase = false)
    {
        if($isDirectPurchase) {
            return array(
                'type' => 'direct',
                'ratio' => 0
            );
        }

        //TODO: Use YesNo source model to remove hardcoded value
        if($product->getSpecialProduct() == '48') { // Special product
            return array(
                'type' => 'special',
                'ratio' => $this->_getConfigPercentage(self::XML_PATH_RATIO_AFF_SPECIAL)
            );
        }

        if($isNewCustomer) {
            return array(
                'type' => 'new',
                'ratio' => $this->_getConfigPercentage(self::XML_PATH_RATIO_AFF_NEW)
            );
        }

        return array(
            'type' => 'repeat',
            'ratio' => $this->_getConfigPercentage(self::XML_PATH_RATIO_AFF_REPEAT)
        );
    }

    protected function _getExpertTaxRate(Mage_Customer_Model_Customer $expertModel)
    {
        $bankModelCollection = Mage::getModel('bank/bank')->getCollection()->addFieldToFilter('customer_id', $expertModel->getId());
        if($bankModelCollection->count() == 0) {
            Mage::throwException($this->__('There is no bank information for expert id = %s', $expertModel->getId()));
        }

        foreach($bankModelCollection as $bankModel) {
            if($bankModel->getBusinesstype() == null || $bankModel->getBusinesstype() == 'small') {
                return $this->_getConfigPercentage(self::XML_PATH_TAX_RATE_SMALL_BUSINESS);
            } else {
                return $this->_getConfigPercentage(self::XML_PATH_TAX_RATE_BIG_BUSINESS);
            }
        }
    }

    protected function _getConfigPercentage($configPath)
    {
        return floatval(Mage::getStoreConfig($configPath))/100;
    }

    protected function _getExpertModel(Mage_Catalog_Model_Product $product)
    {
        //TODO: Should improve the way we filter expert, should be id. Filter by name is bad.
        $expertName = $this->_getExpertName($product);
        $adminUserCollection = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('username', $expertName);
        if($adminUserCollection->count() == 0) {
            Mage::throwException($this->__('There is no expert with name: %s', $expertName));
        }

        foreach($adminUserCollection as $adminUser) {
            $expertModel = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail($adminUser->getEmail());
            if(!$expertModel->getId()) {
                Mage::throwException($this->__('There is no expert with email: %s', $adminUser->getEmail()));
            }

            //TODO: Should cache expertModel per product.
            return $expertModel;
        }
    }

    protected function _getExpertName(Mage_Catalog_Model_Product $product)
    {
        //TODO: Change the source model for member_list attribute: experts are customers having groupId of 4 (Member)
        return $product->getResource()->getAttribute('member_list')->getFrontend()->getValue($product);
    }

    protected function _getAffiliateModel($directPurchase)
    {
        $affiliateModel = Mage::getModel('awaffiliate/affiliate');
        if($directPurchase) {
            $originalAffiliate = $this->_getOriginalAffiliatePurchaseFrom();
            if($originalAffiliate && $originalAffiliate->getId()) { // Repeat customer, original affiliate will get the share
                $affiliateModel = $originalAffiliate;
            } else { // Completely new customer, only Expert get the share
                //TODO: set zero amount for affiliate
            }
        } else { // Purchase via affiliate link
            $affiliateModel->load($this->getAffiliateId());
            if(!$affiliateModel->getId()) {
                Mage::throwException($this->__('Affiliate not existed. Id = %s', $this->getAffiliateId()));
            }
        }

        return $affiliateModel;
    }

    protected function _getOriginalAffiliatePurchaseFrom()
    {
        //TODO: Implement.
    }

    protected function _getCustomerModel($customerId)
    {
        $customerModel = Mage::getModel('customer/customer')->load($customerId);
        if(!$customerModel->getId()) {
            Mage::throwException($this->__('Customer does not exist. Id = %s', $customerId));
        }

        return $customerModel;
    }




    public function createTransaction1($isaffiliate=Null,$oid=Null)
    {
       
        if ($this->_isValidForCreation()) {
            $affiliatedetail =  Mage::getModel('awaffiliate/affiliate')->load($this->getAffiliateId())->getData();

            /* For experts collection */
            /* @var $order Mage_Sales_Model_Order */
            $order = $this->getLinkedEntityOrder();
            $orderItemCollection = $order->getAllItems();
            foreach ($orderItemCollection as $orderItem)
            {
//                $name[] = $item->getName();
//                $unitPrice[]=$item->getPrice();
//                $sku[]=$item->getSku();
//                $ids[]=$item->getProductId();
//                $qty[]=$item->getQtyToInvoice();
//                $created_date[] = substr($item->getCreatedAt(), 0, 10);
//            }
//            foreach($ids as $key=>$id)
//            {
                /* @var $orderItem Mage_Sales_Model_Order_Item */
//                $id = $orderItem->getProductId();
                $item_name = $orderItem->getName();
                $product_price = $orderItem->getPrice();

                $_product = Mage::getModel('catalog/product')->load($orderItem->getProductId());
                $specialproduct=$_product['special_product'];
                $expertname = $_product->getResource()->getAttribute('member_list')->getFrontend()->getValue($_product);
                $adminmodel = Mage::getModel('admin/user')->getCollection()->getData();
                foreach($adminmodel as $admininfo)
                    {
                        if($admininfo['username']==$expertname)
                        {
                           $expertemail = $admininfo['email'];
                           $expertinfo = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail($expertemail);
                           $expertid = $expertinfo['entity_id'];
                           break;
                        }
                    }
                
                /** Get affiliate data **/
                $customerEntity = Mage::getModel('customer/customer')->load($affiliatedetail['customer_id']);
                $created_at = date('d/m/Y');

                /** Get order collection of a current customer **/

                // HiepHM: check whether customer is new (only has this order)
                $orderCollection = Mage::getModel('sales/order')->getCollection()
                    ->addFieldToFilter('customer_id', $order->getCustomerId())
                    ->addFieldToFilter('state', Mage_Sales_Model_Order::STATE_COMPLETE);
                $isNewCustomer = ($orderCollection->count() == 1); //HiepHM: This order only.

                /** calculate shareratio for admin **/
                $configdata = Mage::getStoreConfig('brst_experts/specific');
                $specialShareRatio = '40';
                $specificShareRatio = Mage::getStoreConfig('brst_experts/specific/specific');
                $globalShareRatio = Mage::getStoreConfig('brst_experts/global/global');
                
                foreach ($configdata as $value) {
                     if($expertname==$value && $specialproduct=='48')
                      {
                          if($specialShareRatio > $specificShareRatio)
                          {
                              $sharetype='special';
                              $shareratioadmin=$specialShareRatio;
                              $adminshareratiovalue=$product_price*$shareratioadmin/100;
                          }
                          else
                          {
                              $sharetype='specific';
                              $shareratioadmin=$specificShareRatio;
                              $adminshareratiovalue=$product_price*$shareratioadmin/100;
                          }
                      }
                      else if($expertname==$value)
                      {
                          $sharetype='specific';
                          $shareratioadmin=$specificShareRatio;
                          $adminshareratiovalue=$product_price*$shareratioadmin/100;
                      }
                      else{
                           $sharetype='global';
                          $shareratioadmin=$globalShareRatio;
                          $adminshareratiovalue=$product_price*$shareratioadmin/100;
                      }
                }
                
                /** set Affiliate Ratio ***/
                if($specialproduct=='48'){
                    $affiliateratio='40';
                }
                else if($isNewCustomer){
                    $affiliateratio='20';
                }
                else{
                    $affiliateratio='10';
                }
                $affiliatepay=$product_price*$affiliateratio/100; // HiepHM: must calcualte base on deduced total
               
                $expertratio=100-$shareratioadmin;
                $expertprice=$product_price-$affiliatepay;





                /** Tax apply or not */
//                $customerModel1 = Mage::getModel('customer/session')->getCustomer()->getCustometId();
                $bankdetail=Mage::getModel('bank/bank')->getCollection()->getData();
                foreach($bankdetail as $bankdata)
                 {
                     if($expertid == $bankdata['customer_id'])
                     {
                        $businesstype=$bankdata['businesstype'];
                        if($businesstype=='small' || $businesstype==NULL)
                         {
                             $taxpay='0';
                             $taxratio='0';
                             /***Earnings of Expert **/
                             $expertearning=$expertprice*$expertratio/100;
                             $getyoupaid=$expertearning;
                             break;
                         }
                         else
                         {
                             $taxratio='19';
                             $expertearning=($expertprice*$expertratio/100);
                             $taxpay=$expertearning*$taxratio/100;
                             $getyoupaid=$expertearning-$taxpay;
                             break;
                         }
                     }
                     else
                     {
                             $taxpay='0';
                             $taxratio='0';
                             /***Earnings of Expert **/
                             $expertearning=$expertprice*$expertratio/100;
                             $getyoupaid=$expertearning;
                     }
                 }
                
                /* for member total discount */
                $check_model = Mage::getModel('brst_calculate/discount')->getCollection()->getData();
                $ifexist=0;
                $adminid='';
                $no_order='';
                $totalamount='';
                $member_amount='';
                $tax_paid='';
                $admin_amount='';
                $balance='';
                foreach($check_model as $colldta)
                {
                    if($colldta['member_name'] == $expertname)
                    {
                        $ifexist=1;
                        $adminid=$colldta['id'];
                        $no_order=$colldta['total_order'];
                        $totalamount=$colldta['gross_earned'];
                        $member_amount=$colldta['amount_earned'];
                        $tax_paid=$colldta['tax_paid'];
                        $admin_amount=$colldta['admin_amount'];
                        $balance=$colldta['balance'];
                        break;
                    }
                }
                if($ifexist==1)
                {
                    $model = Mage::getModel('brst_calculate/discount')->load($adminid);
                    $model->setmember_name($expertname);
                    $model->settotal_order($no_order + 1);
                    $model->setgross_earned($totalamount + $product_price);
                    $model->setamount_earned($member_amount + $getyoupaid);
                    $model->settax_paid($tax_paid + $taxpay);
                    $model->setadmin_amount($admin_amount + $adminshareratiovalue);
                    $model->setbalance($member_amount + $getyoupaid);
                    $model->save();
                }
                else
                {
                    $model = Mage::getModel('brst_calculate/discount');
                    $model->setmember_id($expertid);
                    $model->setmember_name($expertname);
                    $model->settotal_order(1);
                    $model->setgross_earned($product_price);
                    $model->setamount_earned($getyoupaid);
                    $model->settax_paid($taxpay);
                    $model->setadmin_amount($adminshareratiovalue);
                    $model->setbalance($getyoupaid);
                    $model->save();
                }
                /* end member total discount */
                /* insert data into 'brst_member_payment' */
                    $member_model = Mage::getModel('brst_member/payment')->getCollection()->getData();
                    $exist=0;
                    $memberid='';
                    $total_earned='';
                    $totalpaid='';
                    $pending_amount='';
                    $inprogress='';
                    $invoice_date='';
                    $status='';
                    $sendinvoice='';
                    foreach($member_model as $colldata)
                    {
                        if($colldata['member_name'] == $expertname)
                        {
                            $exist=1;
                            $memberid=$colldata['id'];
                            $total_earned=$colldata['total_earned'];
                            $totalpaid=$colldata['total_paid'];
                            $pending_amount=$colldata['pending_amount'];
                            $inprogress=$colldata['inprogress'];
                            $invoice_date=$colldata['last_invoice_date'];
                            $status=$colldata['status'];
                            $sendinvoice=$colldata['send_invoice'];
                            break;
                        }
                    }
                    if($exist==1)
                    {
                        $models = Mage::getModel('brst_member/payment')->load($memberid);
                        $models->settotal_earned($total_earned + $getyoupaid);
                        $models->setpending_amount(($total_earned + $getyoupaid) - $totalpaid);
                        if((($total_earned + $getyoupaid) - $totalpaid) > 0)
                        {
                           $models->setstatus('unpaid');  
                        }
                        else
                        {
                             $models->setstatus('paid');  
                        }
                        $models->setlast_invoice_date($created_at);
                        //$models->setlast_invoice_date($created_at);
                        $models->save();
                    }
                    else
                    {
                        $models = Mage::getModel('brst_member/payment');
                        $models->setmember_name($expertname);
                        $models->settotal_earned($getyoupaid);
                        //$models->settotal_paid();
                        $models->setpending_amount($getyoupaid);
                        //$models->setinprogress();
                        $models->setlast_invoice_date($created_at);
                       
                        //$models->setsend_invoice();
                        $models->save();
                    }
                /* end for member payment */
                /*** Insert data into into table 'brst_experts_amount' **/
                $expertmodel = Mage::getModel('brst_experts/amount');
                $expertmodel->setorder_id($order->getIncrementId());
                $expertmodel->setproduct_id($item_name);
                $expertmodel->setexpert_name($expertname);
                $expertmodel->setexpert_ratio($expertratio);
                $expertmodel->setshare_ratio($shareratioadmin);
                $expertmodel->setshare_type($sharetype);
                $expertmodel->setaffiliate_name($customerEntity->getFirstname());
                $expertmodel->setaffiliate_ratio($affiliateratio);
                $expertmodel->settax($taxratio);
                $expertmodel->setgross_price($product_price);
                $expertmodel->setaffiliate_pay($affiliatepay);
                $expertmodel->setadmin_pay($adminshareratiovalue);
                $expertmodel->settax_pay($taxpay);
                $expertmodel->setgetyoupaid($getyoupaid);
                $expertmodel->setcreated_at($created_at);
                $expertmodel->setordertimestamp(time());
                $expertmodel->setcustomer_id($order->getCustomerId());
                $expertmodel->setaffiliate_id($customerEntity->getId());
              
               //echo "<pre>";print_r($expertmodel);die('helo');
                $expertmodel->save();
               //  echo "<pre>";print_r($expertmodel);die('helo');
                /* insert every item of order into table 'brst_member_orders' */
//                $connection=Mage::getSingleton("core/resource")->getConnection("core_write");
//                $qry="insert into brst_member_orders(member_id,member_name,product_amount,invoice_date) values ('$expertid','$expertname','$product_price','$created_at')";
//                $connection->query($qry);
            }
            //$customdiscount->save();
        }
        else if($isaffiliate=='customer')
        {
                  
            $order = Mage::getModel('sales/order')->loadByIncrementId($oid);
            $clientdata=Mage::getModel('awaffiliate/client')->getCollection()->addFieldToFilter('customer_id', array('eq' => array($order->getCustomerId())));
            $clientinfo=$clientdata->getData();
            
            
            /*** Get order collection of a current customer**/
            $orderCollection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id', array('eq' => array($order->getCustomerId())));
            $orderData=$orderCollection->getData();
            $orderId=$orderData[0]['entity_id'];
           // echo "<pre>";print_r($orderData);die('helo');
            
            $countorder=count($clientinfo);
            if($countorder>1 || $countorder==1)
            {
                $affiliateId=$clientinfo[0]['affiliate_id'];
              // die('djgdfgdfgfdgdfgdfs');
                $affiliatedata =  Mage::getModel('awaffiliate/affiliate')->load($affiliateId)->getData();
                $customerEntity = Mage::getModel('customer/customer')->load($affiliatedata['customer_id']);
                $orderItemCollection = $order->getAllItems();
                foreach ($orderItemCollection as $itemId => $orderItem)
                {
                    $name[] = $orderItem->getName();
                    $unitPrice[]=$orderItem->getPrice();
                    $sku[]=$orderItem->getSku();
                    $ids[]=$orderItem->getProductId();
                    $qty[]=$orderItem->getQtyToInvoice();
                    $created_date[] = substr($orderItem->getCreatedAt(), 0, 10);
                }
                foreach($ids as $key=>$id)
                {
                    
                    $id=$ids[$key];
                    $item_name=$name[$key];
                    $product_price=$unitPrice[$key];
                    $admin_price = $product_price * 20/100;
//                    $total_discount = $admin_price + $rate;
//                    $expert_price = ($product_price - $total_discount);
                    
                    $_product = Mage::getModel('catalog/product')->load($id);
                    $specialproduct=$_product['special_product'];
                    $expertname = $_product->getResource()->getAttribute('member_list')->getFrontend()->getValue($_product);
                    $adminmodel=Mage::getModel('admin/user')->getCollection()->getData();
                    
                    foreach($adminmodel as $admininfo)
                    {
                        if($admininfo['username']==$expertname)
                        {
                           $expertemail=$admininfo['email'];
                           $expertinfo = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail($expertemail);
                           $expertid=$expertinfo['entity_id'];
                           break;
                        }
                    }
                    
                  
                    
                    /** Get affiliate data**/
                    $created_at=date('d/m/Y');
                    
                    
                    /** calculate shareratio for admin  **/
                    $configdata=Mage::getStoreConfig('brst_experts/specific');
                    $specialShareRatio='40';
                    $specificShareRatio=Mage::getStoreConfig('brst_experts/specific/specific');
                    $globalShareRatio=Mage::getStoreConfig('brst_experts/global/global');

                    foreach ($configdata as $key => $value) {
                         if($expertname==$value && $specialproduct=='48')
                          {
                              if($specialShareRatio > $specificShareRatio)
                              {
                                  $sharetype='special';
                                  $shareratioadmin=$specialShareRatio;
                                  $adminshareratiovalue=$product_price*$shareratioadmin/100;
                              }
                              else
                              {
                                  $sharetype='specific';
                                  $shareratioadmin=$specificShareRatio;
                                  $adminshareratiovalue=$product_price*$shareratioadmin/100;
                              }

                          }
                          else if($expertname==$value)
                          {
                              $sharetype='specific';
                              $shareratioadmin=$specificShareRatio;
                              $adminshareratiovalue=$product_price*$shareratioadmin/100;
                          }
                          else{
                               $sharetype='global';
                              $shareratioadmin=$globalShareRatio;
                              $adminshareratiovalue=$product_price*$shareratioadmin/100;
                          }
                    }
                    
                         /** set Affiliate Ratio ***/
                        if($specialproduct=='48'){
                            $affiliateratio='40';
                        }
                        else if($orderId==''){
                            $affiliateratio='20';
                        }
                        else{
                            $affiliateratio='10';
                        }
                       //$affiliateprice=$product_price-$adminshareratiovalue;
                      $affiliatepay=$product_price*$affiliateratio/100;
                      $expertratio=100-$shareratioadmin;
                      $expertprice=$product_price-$affiliatepay;
                    
                    /** Tax apply or not */
                    $customerModel1 = Mage::getModel('customer/session')->getCustomer()->getCustometId();
                    $bankdetail=Mage::getModel('bank/bank')->getCollection()->getData();
                    foreach($bankdetail as $bankdata)
                     {
                         if($expertid == $bankdata['customer_id'])
                         {
                            $businesstype=$bankdata['businesstype'];
                            if($businesstype=='small')
                             {
                                 $taxpay='0';
                                 $taxratio='0';
                                 /***Earnings of Expert **/
                                 $expertearning=$expertprice*$expertratio/100;
                                 $getyoupaid=$expertearning;
                                 break;
                             }
                             else
                             {
                                 $taxratio='19';
                                 $expertearning=$expertprice*$expertratio/100;
                                 $taxpay=$expertearning*$taxratio/100;
                                 $getyoupaid=$expertearning-$taxpay;
                                 break;
                             }
                         }
                         else {
                                 $taxpay='0';
                                 $taxratio='0';
                                /*** Earnings of Expert **/
                                 $expertearning=$expertprice*$expertratio/100;
                                 $getyoupaid=$expertearning;
                        }
                     }

                    /* for member total discount */
                    $check_model = Mage::getModel('brst_calculate/discount')->getCollection()->getData();
                    $ifexist=0;
                    $adminid='';
                    $no_order='';
                    $totalamount='';
                    $member_amount='';
                    $tax_paid='';
                    $admin_amount='';
                    $balance='';
                    foreach($check_model as $colldta)
                    {
                        if($colldta['member_name'] == $expertname)
                        {
                            $ifexist=1;
                            $adminid=$colldta['id'];
                            $no_order=$colldta['total_order'];
                            $totalamount=$colldta['gross_earned'];
                            $member_amount=$colldta['amount_earned'];
                            $tax_paid=$colldta['tax_paid'];
                            $admin_amount=$colldta['admin_amount'];
                            $balance=$colldta['balance'];
                            break;
                        }
                    }
                    if($ifexist==1)
                    {
                        $model = Mage::getModel('brst_calculate/discount')->load($adminid);
                        $model->setmember_name($expertname);
                        $model->settotal_order($no_order + 1);
                        $model->setgross_earned($totalamount + $product_price);
                        $model->setamount_earned($member_amount + $getyoupaid);
                        $model->settax_paid($tax_paid + $taxpay);
                        $model->setadmin_amount($admin_amount + $adminshareratiovalue);
                        $model->setbalance($member_amount + $getyoupaid);
                        $model->save();
                    }
                    else
                    {
                        $model = Mage::getModel('brst_calculate/discount');
                        $model->setmember_id($expertid);
                        $model->setmember_name($expertname);
                        $model->settotal_order(1);
                        $model->setgross_earned($product_price);
                        $model->setamount_earned($getyoupaid);
                        $model->settax_paid($taxpay);
                        $model->setadmin_amount($adminshareratiovalue);
                        $model->setbalance($getyoupaid);
                        $model->save();
                    }
                    /* end member total discount */
                    /* insert data into 'brst_member_payment' */
                    $member_model = Mage::getModel('brst_member/payment')->getCollection()->getData();
                    $exist=0;
                    $memberid='';
                    $total_earned='';
                    $totalpaid='';
                    $pending_amount='';
                    $inprogress='';
                    $invoice_date='';
                    $status='';
                    $sendinvoice='';
                    foreach($member_model as $colldata)
                    {
                        if($colldata['member_name'] == $expertname)
                        {
                            $exist=1;
                            $memberid=$colldata['id'];
                            $total_earned=$colldata['total_earned'];
                            $totalpaid=$colldata['total_paid'];
                            $pending_amount=$colldata['pending_amount'];
                            $inprogress=$colldata['inprogress'];
                            $invoice_date=$colldata['last_invoice_date'];
                            $status=$colldata['status'];
                            $sendinvoice=$colldata['send_invoice'];
                            break;
                        }
                    }
                    if($exist==1)
                    {
                        $models = Mage::getModel('brst_member/payment')->load($memberid);
                        $models->settotal_earned($total_earned + $getyoupaid);
                        $models->setpending_amount($pending_amount + $getyoupaid);
                        $models->setlast_invoice_date($created_at);
                        //$models->setlast_invoice_date($created_at);
                        $models->save();
                    }
                    else
                    {
                        $models = Mage::getModel('brst_member/payment');
                        $models->setmember_name($expertname);
                        $models->settotal_earned($getyoupaid);
                        //$models->settotal_paid();
                        $models->setpending_amount($getyoupaid);
                        //$models->setinprogress();
                        $models->setlast_invoice_date($created_at);
                        //$models->setstatus();
                        //$models->setsend_invoice();
                        $models->save();
                    }
                    /* end for member payment */
                    /** Insert data into into table 'brst_experts_amount' **/
                    $expertmodel = Mage::getModel('brst_experts/amount');
                    $expertmodel->setorder_id($oid);
                    $expertmodel->setproduct_id($item_name);
                    $expertmodel->setexpert_name($expertname);
                    $expertmodel->setexpert_ratio($expertratio);
                    $expertmodel->setshare_ratio($shareratioadmin);
                    $expertmodel->setshare_type($sharetype);
                    $expertmodel->setaffiliate_name($customerEntity->getFirstname());
                    $expertmodel->setaffiliate_ratio($affiliateratio);
                    $expertmodel->settax($taxratio);
                    $expertmodel->setgross_price($product_price);
                    $expertmodel->setaffiliate_pay($affiliatepay);
                    $expertmodel->setadmin_pay($adminshareratiovalue);
                    $expertmodel->settax_pay($taxpay);
                    $expertmodel->setgetyoupaid($getyoupaid);
                    $expertmodel->setcreated_at($created_at);
                    $expertmodel->setordertimestamp(time());
                    $expertmodel->setcustomer_id($order->getCustomerId());
                    $expertmodel->setaffiliate_id($customerEntity->getId());
                    $expertmodel->save();
                    
                    /* insert every item of order into table 'brst_member_orders' */
                    $connection=Mage::getSingleton("core/resource")->getConnection("core_write");
                    $qry="insert into brst_member_orders(member_id,member_name,product_amount,invoice_date) values ('$expertid','$expertname','$product_price','$created_at')";
                    $connection->query($qry);
                }
           }
           else
           {
            $order = Mage::getModel('sales/order')->loadByIncrementId($oid);
            $clientdata=Mage::getModel('awaffiliate/client')->getCollection()->addFieldToFilter('customer_id', array('eq' => array($order->getCustomerId())));
            $clientinfo=$clientdata->getData();
           }
            if($countorder==0)
            {
                $affiliateId='';
                $affiliatedata =  Mage::getModel('awaffiliate/affiliate')->load($affiliateId)->getData();
                $customerEntity = Mage::getModel('customer/customer')->load($affiliatedata['customer_id']);
                $orderItemCollection = $order->getAllItems();
                foreach ($orderItemCollection as $itemId => $orderItem)
                {
                    $name[] = $orderItem->getName();
                    $unitPrice[]=$orderItem->getPrice();
                    $sku[]=$orderItem->getSku();
                    $ids[]=$orderItem->getProductId();
                    $qty[]=$orderItem->getQtyToInvoice();
                    $created_date[] = substr($orderItem->getCreatedAt(), 0, 10);
                }
                foreach($ids as $key=>$id)
                {
                   
                    $id=$ids[$key];
                 
                    $item_name=$name[$key];
                    $product_price=$unitPrice[$key];
                    $admin_price = $product_price * 20/100;
//                    $total_discount = $admin_price + $rate;
//                    $expert_price = ($product_price - $total_discount);
                    
                    $_product = Mage::getModel('catalog/product')->load($id);
                  
                    $specialproduct=$_product['special_product'];
                    $expertname = $_product->getResource()->getAttribute('member_list')->getFrontend()->getValue($_product);
                    $adminmodel=Mage::getModel('admin/user')->getCollection()->getData();
                    
                    foreach($adminmodel as $admininfo)
                    {
                        if($admininfo['username']==$expertname)
                        {
                           $expertemail=$admininfo['email'];
                           $expertinfo = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail($expertemail);
                           $expertid=$expertinfo['entity_id'];
                           break;
                        }
                    }
                    
                    /** Get affiliate data**/
                    $created_at=date('d/m/Y');
                    
                    /*** Get order collection of a current customer**/
                    $orderCollection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id', array('eq' => array($order->getCustomerId())));
                    $orderData=$orderCollection->getData();
                    $orderId=$orderData[0]['entity_id'];
                    
                    /** calculate shareratio for admin  **/
                    $configdata=Mage::getStoreConfig('brst_experts/specific');
                    $specialShareRatio='40';
                    $specificShareRatio=Mage::getStoreConfig('brst_experts/specific/specific');
                    $globalShareRatio=Mage::getStoreConfig('brst_experts/global/global');

                    foreach ($configdata as $key => $value) {
                         if($expertname==$value && $specialproduct=='48')
                          {
                              if($specialShareRatio > $specificShareRatio)
                              {
                                  $sharetype='special';
                                  $shareratioadmin=$specialShareRatio;
                                  $adminshareratiovalue=$product_price*$shareratioadmin/100;
                              }
                              else
                              {
                                  $sharetype='specific';
                                  $shareratioadmin=$specificShareRatio;
                                  $adminshareratiovalue=$product_price*$shareratioadmin/100;
                              }

                          }
                          else if($expertname==$value)
                          {
                              $sharetype='specific';
                              $shareratioadmin=$specificShareRatio;
                              $adminshareratiovalue=$product_price*$shareratioadmin/100;
                          }
                          else{
                               $sharetype='global';
                              $shareratioadmin=$globalShareRatio;
                              $adminshareratiovalue=$product_price*$shareratioadmin/100;
                          }
                    }
                    
                         /** set Affiliate Ratio ***/
                        if($specialproduct=='48'){
                            $affiliateratio='0';
                        }
                        else if($orderId==''){
                            $affiliateratio='0';
                        }
                        else{
                            $affiliateratio='0';
                        }
                       //$affiliateprice=$product_price-$adminshareratiovalue;
                      $affiliatepay=$product_price*$affiliateratio/100;
                      $expertratio=100-$shareratioadmin;
                      $expertprice=$product_price-$affiliatepay;
                    
                    /** Tax apply or not */
                    $customerModel1 = Mage::getModel('customer/session')->getCustomer()->getCustometId();
                    $bankdetail=Mage::getModel('bank/bank')->getCollection()->getData();
                    foreach($bankdetail as $bankdata)
                     {
                         if($expertid == $bankdata['customer_id'])
                         {
                            $businesstype=$bankdata['businesstype'];
                            if($businesstype=='small')
                             {
                                 $taxpay='0';
                                 $taxratio='0';
                                 /***Earnings of Expert **/
                                 $expertearning=$expertprice*$expertratio/100;
                                 $getyoupaid=$expertearning;
                                 break;
                             }
                             else
                             {
                                 $taxratio='19';
                                 $expertearning=$expertprice*$expertratio/100;
                                 $taxpay=$expertearning*$taxratio/100;
                                 $getyoupaid=$expertearning-$taxpay;
                                 break;
                             }
                         }
                         else {
                                 $taxpay='0';
                                 $taxratio='0';
                                /*** Earnings of Expert **/
                                 $expertearning=$expertprice*$expertratio/100;
                                 $getyoupaid=$expertearning;
                        }
                     }

                    /* for member total discount */
                    $check_model = Mage::getModel('brst_calculate/discount')->getCollection()->getData();
                    $ifexist=0;
                    $adminid='';
                    $no_order='';
                    $totalamount='';
                    $member_amount='';
                    $tax_paid='';
                    $admin_amount='';
                    $balance='';
                    foreach($check_model as $colldta)
                    {
                        if($colldta['member_name'] == $expertname)
                        {
                            $ifexist=1;
                            $adminid=$colldta['id'];
                            $no_order=$colldta['total_order'];
                            $totalamount=$colldta['gross_earned'];
                            $member_amount=$colldta['amount_earned'];
                            $tax_paid=$colldta['tax_paid'];
                            $admin_amount=$colldta['admin_amount'];
                            $balance=$colldta['balance'];
                            break;
                        }
                    }
                    if($ifexist==1)
                    {
                        $model = Mage::getModel('brst_calculate/discount')->load($adminid);
                        $model->setmember_name($expertname);
                        $model->settotal_order($no_order + 1);
                        $model->setgross_earned($totalamount + $product_price);
                        $model->setamount_earned($member_amount + $getyoupaid);
                        $model->settax_paid($tax_paid + $taxpay);
                        $model->setadmin_amount($admin_amount + $adminshareratiovalue);
                        $model->setbalance($member_amount + $getyoupaid);
                        $model->save();
                    }
                    else
                    {
                        $model = Mage::getModel('brst_calculate/discount');
                        $model->setmember_id($expertid);
                        $model->setmember_name($expertname);
                        $model->settotal_order(1);
                        $model->setgross_earned($product_price);
                        $model->setamount_earned($getyoupaid);
                        $model->settax_paid($taxpay);
                        $model->setadmin_amount($adminshareratiovalue);
                        $model->setbalance($getyoupaid);
                        $model->save();
                    }
                    /* end member total discount */
                    /* insert data into 'brst_member_payment' */
                    $member_model = Mage::getModel('brst_member/payment')->getCollection()->getData();
                    $exist=0;
                    $memberid='';
                    $total_earned='';
                    $totalpaid='';
                    $pending_amount='';
                    $inprogress='';
                    $invoice_date='';
                    $status='';
                    $sendinvoice='';
                    foreach($member_model as $colldata)
                    {
                        if($colldata['member_name'] == $expertname)
                        {
                            $exist=1;
                            $memberid=$colldata['id'];
                            $total_earned=$colldata['total_earned'];
                            $totalpaid=$colldata['total_paid'];
                            $pending_amount=$colldata['pending_amount'];
                            $inprogress=$colldata['inprogress'];
                            $invoice_date=$colldata['last_invoice_date'];
                            $status=$colldata['status'];
                            $sendinvoice=$colldata['send_invoice'];
                            break;
                        }
                    }
                    if($exist==1)
                    {
                        $models = Mage::getModel('brst_member/payment')->load($memberid);
                        $models->settotal_earned($total_earned + $getyoupaid);
                        $models->setpending_amount($pending_amount + $getyoupaid);
                        $models->setlast_invoice_date($created_at);
                        //$models->setlast_invoice_date($created_at);
                        $models->save();
                    }
                    else
                    {
                        $models = Mage::getModel('brst_member/payment');
                        $models->setmember_name($expertname);
                        $models->settotal_earned($getyoupaid);
                        //$models->settotal_paid();
                        $models->setpending_amount($getyoupaid);
                        //$models->setinprogress();
                        $models->setlast_invoice_date($created_at);
                        //$models->setstatus();
                        //$models->setsend_invoice();
                        $models->save();
                    }
                    /* end for member payment */
                    /** Insert data into into table 'brst_experts_amount' **/
                    $expertmodel = Mage::getModel('brst_experts/amount');
                    $expertmodel->setorder_id($oid);
                    $expertmodel->setproduct_id($item_name);
                    $expertmodel->setexpert_name($expertname);
                    $expertmodel->setexpert_ratio($expertratio);
                    $expertmodel->setshare_ratio($shareratioadmin);
                    $expertmodel->setshare_type($sharetype);
                    $expertmodel->setaffiliate_name('');
                    $expertmodel->setaffiliate_ratio($affiliateratio);
                    $expertmodel->settax($taxratio);
                    $expertmodel->setgross_price($product_price);
                    $expertmodel->setaffiliate_pay($affiliatepay);
                    $expertmodel->setadmin_pay($adminshareratiovalue);
                    $expertmodel->settax_pay($taxpay);
                    $expertmodel->setgetyoupaid($getyoupaid);
                    $expertmodel->setcreated_at($created_at);
                    $expertmodel->setordertimestamp(time());
                    $expertmodel->setcustomer_id($order->getCustomerId());
                    $expertmodel->setaffiliate_id('');
                    $expertmodel->save();
                    
                    /* insert every item of order into table 'brst_member_orders' */
                    $connection=Mage::getSingleton("core/resource")->getConnection("core_write");
                    $qry="insert into brst_member_orders(member_id,member_name,product_amount,invoice_date) values ('$expertid','$expertname','$product_price','$created_at')";
                    $connection->query($qry);
                }
           }
        }
        else {
            Mage::throwException($this->__('Not valid for create transaction'));
        }
        return $this;
    }
    
    protected function _canBeApplied($profitModel)
    {
        switch ($profitModel->getType()) {
            case AW_Affiliate_Model_Source_Profit_Type::TIER_CUR:
            case AW_Affiliate_Model_Source_Profit_Type::FIXED_CUR:
                /** @var $order Mage_Sales_Model_Order */
                if ($order = $this->getData('linked_entity_order')) {
                    return $order->getInvoiceCollection()->getSize() == 0;
                } else {
                    return false;
                }
                break;
        }
        return true;
    }

    public function getAffiliate()
    {
        if (!is_null($this->getData('affiliate_id')) && intval($this->getData('affiliate_id')) > 0) {
            $_affiliate = Mage::getModel('awaffiliate/affiliate')->load($this->getData('affiliate_id'));
            if (!is_null($_affiliate->getId())) {
                return $_affiliate;
            }
        }
        return null;
    }

    public function getCampaign()
    {
        if (!is_null($this->getData('campaign_id')) && intval($this->getData('campaign_id')) > 0) {
            $_campaign = Mage::getModel('awaffiliate/campaign')->load($this->getData('campaign_id'));
            if (!is_null($_campaign->getId())) {
                return $_campaign;
            }
        }
        return null;
    }

    public function getLinkedEntity()
    {
        switch ($this->getData('linked_entity_type')) {
            case AW_Affiliate_Model_Source_Transaction_Profit_Linked::INVOICE_ITEM:
                $entityObject = $this->getData('linked_entity_invoice');
                break;
            case AW_Affiliate_Model_Source_Transaction_Profit_Linked::ORDER_ITEM :
                $entityObject = Mage::getModel('sales/order_item')->load($this->getData('linked_entity_id'));
                break;
            default:
                return null;
        }
        return (isset($entityObject) && $entityObject) ? $entityObject : null;
    }

    protected function _isValidForCreation()
    {
     
        if (is_null($this->getAffiliate())) {
             
            return false;
           
        }
        if (is_null($this->getCampaign())) {
            return false;
          
        }
        if (is_null($this->getLinkedEntity())) {
            return false;
         
        }
        $type = Mage::getModel('awaffiliate/source_transaction_profit_type')->getOption($this->getData('type'));
        if ($type === FALSE) {
            return false;
            
        }
        
        return true;
    }

    protected function _getAttractedAmount()
    {
        $linkedEntity = $this->getLinkedEntity();
        if (!$linkedEntity) {
            return null;
        }
        $amount = null;
        switch ($this->getData('linked_entity_type')) {
            case AW_Affiliate_Model_Source_Transaction_Profit_Linked::INVOICE_ITEM:
                if (Mage::helper('awaffiliate/config')->isConsiderTax($linkedEntity->getData('store_id'))) {
                    $amount = $linkedEntity->getData('subtotal_incl_tax');
                } else {
                    $amount = $linkedEntity->getData('subtotal');
                }
                if ($_discountAmount = $linkedEntity->getData('discount_amount')) {
                    $amount = max(0, $amount - $_discountAmount);
                }
                break;
            case AW_Affiliate_Model_Source_Transaction_Profit_Linked::ORDER_ITEM :
                if (Mage::helper('awaffiliate/config')->isConsiderTax($linkedEntity->getData('store_id'))) {
                    $amount = $linkedEntity->getData('row_total_incl_tax');
                } else {
                    $amount = $linkedEntity->getData('row_total');
                }
                break;
            default:
                return null;
        }
        return $amount;
    }

    protected function _getCurrencyCode()
    {
        $linkedEntity = $this->getLinkedEntity();
        if (!$linkedEntity) {
            return null;
        }
        $code = null;
        switch ($this->getData('linked_entity_type')) {
            case AW_Affiliate_Model_Source_Transaction_Profit_Linked::INVOICE_ITEM:
            case AW_Affiliate_Model_Source_Transaction_Profit_Linked::ORDER_ITEM:
                $code = $linkedEntity->getOrder()->getData('base_currency_code');
                break;
            default:
                return null;
        }
        return $code;
    }

    protected function _afterLoad()
    {
        if (is_string($this->getData('withdrawal_transaction_ids')))
            $this->setData('withdrawal_transaction_ids', @explode(',', $this->getData('withdrawal_transaction_ids')));
        return parent::_afterLoad();
    }

    protected function _beforeSave()
    {
        $res = parent::_beforeSave();
        if (is_null($this->getData('rate'))) {
            Mage::throwException(Mage::helper('awaffiliate')->__('Rate is not specified'));
        }
        if (is_null($this->getData('currency_code'))) {
            Mage::throwException(Mage::helper('awaffiliate')->__('Currency code is not specified'));
        }
        if ($this->getData('withdrawal_transaction_ids') !== null && is_array($this->getData('withdrawal_transaction_ids')))
            $this->setData('withdrawal_transaction_ids', @implode(',', $this->getData('withdrawal_transaction_ids')));
        return $res;
    }

    protected function _afterSave()
    {
        $res = parent::_afterSave();
        $affiliate = $this->getAffiliate();
        if (!is_null($affiliate)) {
            $affiliate->recollectBalances();
        }
        return $res;
    }
}
