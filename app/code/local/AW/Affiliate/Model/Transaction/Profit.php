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

//    const XML_PATH_TAX_RATE_SMALL_BUSINESS = 'brst_experts/tax_rate/small_business';
    const XML_PATH_TAX_RATE_VAT = 'brst_experts/tax_rate/vat';

    protected $_helper;

    public function _construct()
    {
        $this->_init('awaffiliate/transaction_profit');
        $this->_helper = Mage::helper('awaffiliate');
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
            Mage::throwException($this->_helper->$this->_helper->__('Not valid for creating transaction'));
        }

        // Check whether customer is new (only has this order)
        $orderCollection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('customer_id', $order->getCustomerId())
            ->addFieldToFilter('state', Mage_Sales_Model_Order::STATE_COMPLETE);
        $isNewCustomer = ($orderCollection->count() == 0); // Never complete any order

        $customerModel = $this->_getCustomerModel($order->getCustomerId());
        $affiliateModel = $this->_getAffiliateModel($customerModel, $isDirectPurchase);
        if($affiliateModel->getId()) { // Ensure the original affiliate (if exist) get the share
            $isDirectPurchase = false;
        }

        foreach ($order->getAllItems() as $orderItem) {
            /* @var $orderItem Mage_Sales_Model_Order_Item */
            $product = $orderItem->getProduct();
            $expertModel = $this->_getExpertModel($product);

            $sharedAmounts = $this->_calculateShareAmounts($orderItem, $expertModel, $isNewCustomer, $isDirectPurchase);

            $this->_saveToDiscountTable($expertModel, $orderItem, $sharedAmounts);
            $this->_saveToPaymentTable($expertModel, $orderItem, $sharedAmounts);
            $this->_saveToAmountTable($expertModel, $affiliateModel, $order, $orderItem, $sharedAmounts);
        }
    }

    protected function _calculateShareAmounts(Mage_Sales_Model_Order_Item $orderItem, $expertModel, $isNewCustomer, $isDirectPurchase)
    {
        $amount = array();
        // Ignore qty (not use $orderItem->getRowTotalInclTax()) since all products are downloadable
        $amount['raw'] = floatval($orderItem->getPriceInclTax());
        $amount['gross'] = round($amount['raw'] / (1 + $this->_getVatTaxRate($expertModel)), 2);

        $affliateEarningShare = $this->_getAffiliateEarningTypeAndRatio($orderItem->getProduct(), $isNewCustomer, $isDirectPurchase);
        $amount['affiliate_type'] = $affliateEarningShare['type'];
        $amount['affiliate_ratio'] = $affliateEarningShare['ratio'];
        $amount['affiliate_share'] = round($amount['gross'] * $affliateEarningShare['ratio'], 2);

        $remain = $amount['gross'] - $amount['affiliate_share'];

        $amount['pferde_ratio'] = $this->_getAdminShareRatio();
        $amount['pferde_share'] = $remain * $amount['pferde_ratio'];

        $amount['expert_tax_rate'] = 0;
        $amount['expert_tax'] = 0;
//        $amount['expert_ratio'] = 1 - $amount['affiliate_ratio'];
        $amount['expert_share'] = $remain - $amount['pferde_share'];

        return $amount;
    }

    protected function _saveToDiscountTable(Mage_Customer_Model_Customer $expertModel, Mage_Sales_Model_Order_Item $orderItem, $shareAmounts)
    {
        $product = $orderItem->getProduct();

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
            ->setGrossEarned(floatval($modelToSave->getGrossEarned()) + $shareAmounts['gross'])
            ->setAmountEarned(floatval($modelToSave->getAmountEarned()) + $shareAmounts['expert_share'])
            ->setTaxPaid(floatval($modelToSave->getTaxPaid()) + $shareAmounts['expert_tax'])
            ->setAdminAmount(floatval($modelToSave->getAdminAmount()) + $shareAmounts['pferde_share'])
            ->setBalance(floatval($modelToSave->getBalance()) + $shareAmounts['expert_share'])
            ;
        $modelToSave->save();
    }

    protected function _saveToPaymentTable(Mage_Customer_Model_Customer $expertModel, Mage_Sales_Model_Order_Item $orderItem, $shareAmounts)
    {
        $product = $orderItem->getProduct();
        $expertName = $this->_getExpertName($product);
        $modelToSave = Mage::getModel('brst_member/payment')->setId(null) //New blank model
            ->setMemberName($expertName)
            ->setTotalEarned(0)
            ->setTotalPaid(0)
            ->setPendingAmount(0)
            ->setInprogress(0)
            ;

        $modelCollection = Mage::getModel('brst_member/payment')->getCollection()->addFieldToFilter('member_name', $expertName);
        if($modelCollection->count() > 0) { // Has previous data
            foreach($modelCollection as $model) {
                $modelToSave = $model;
                break;
            }
        }

        $modelToSave->setTotalEarned(floatval($modelToSave->getTotalEarned() + $shareAmounts['expert_share']))
            ->setPendingAmount(floatval($modelToSave->getTotalEarned()) - floatval($modelToSave->getTotalPaid()))
            ->setStatus($modelToSave->getPendingAmount() > 0 ? 'unpaid' : 'paid')
            ->setLastInvoiceDate(date('d/m/Y'))
            ->save()
            ;
    }

    protected function _saveToAmountTable(
        Mage_Customer_Model_Customer $expertModel,
        AW_Affiliate_Model_Affiliate $affiliateModel,
        Mage_Sales_Model_Order $order,
        Mage_Sales_Model_Order_Item $orderItem,
        $shareAmounts)
    {
        $product = $orderItem->getProduct();
        $modelToSave = Mage::getModel('brst_experts/amount');
        $modelToSave->setOrderId($order->getIncrementId());
        $modelToSave->setProductId($product->getName());
        $modelToSave->setCustomerId($order->getCustomerId());
        $modelToSave->setAffiliateId(is_null($affiliateModel->getId()) ? null : $affiliateModel->getCustomer()->getId());

        $modelToSave->setRawPrice($shareAmounts['raw']);
        $modelToSave->setGrossPrice($shareAmounts['gross']);

        $modelToSave->setShareRatio($shareAmounts['pferde_ratio'] * 100);
        $modelToSave->setAdminPay($shareAmounts['pferde_share']);

        $modelToSave->setAffiliateName(is_null($affiliateModel->getId()) ? 'N/A' : $affiliateModel->getCustomer()->getName());
        $modelToSave->setShareType($shareAmounts['affiliate_type']);
        $modelToSave->setAffiliateRatio($shareAmounts['affiliate_ratio'] * 100);
        $modelToSave->setAffiliatePay($shareAmounts['affiliate_share']);

        $modelToSave->setExpertName($this->_getExpertName($product));
//        $modelToSave->setExpertRatio($shareAmounts['expert_ratio'] * 100);
//        $modelToSave->setTax($shareAmounts['expert_tax_rate'] * 100);
//        $modelToSave->setTaxPay($shareAmounts['expert_tax']);
        $modelToSave->setGetyoupaid($shareAmounts['expert_share']);

        $modelToSave->setCreatedAt(date('d/m/Y'));
        $modelToSave->setOrdertimestamp(time());
        $modelToSave->save();
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

    protected function _getVatTaxRate(Mage_Customer_Model_Customer $expertModel)
    {
        return $this->_getConfigPercentage(self::XML_PATH_TAX_RATE_VAT);
//        $bankModelCollection = Mage::getModel('bank/bank')->getCollection()->addFieldToFilter('customer_id', $expertModel->getId());
//        if($bankModelCollection->count() == 0) {
//            // Default to small business
//            return $this->_getConfigPercentage(self::XML_PATH_TAX_RATE_SMALL_BUSINESS);
//            //Mage::throwException($this->_helper->__('There is no bank information for expert id = %s', $expertModel->getId()));
//        }
//
//        foreach($bankModelCollection as $bankModel) {
//            if($bankModel->getBusinesstype() == null || $bankModel->getBusinesstype() == 'small') {
//                return $this->_getConfigPercentage(self::XML_PATH_TAX_RATE_SMALL_BUSINESS);
//            } else {
//                return $this->_getConfigPercentage(self::XML_PATH_TAX_RATE_BIG_BUSINESS);
//            }
//        }
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
            Mage::throwException($this->_helper->__('There is no expert with name: %s', $expertName));
        }

        foreach($adminUserCollection as $adminUser) {
            $expertModel = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail($adminUser->getEmail());
            if(!$expertModel->getId()) {
                Mage::throwException($this->_helper->__('There is no expert with email: %s', $adminUser->getEmail()));
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

    protected function _getAffiliateModel(Mage_Customer_Model_Customer $customerModel, $directPurchase)
    {
        $affiliateModel = Mage::getModel('awaffiliate/affiliate');
        if($directPurchase) {
            $originalAffiliate = $this->_getOriginalAffiliatePurchaseFrom($customerModel);
            if($originalAffiliate && $originalAffiliate->getId()) { // Repeat customer, original affiliate will get the share
                $affiliateModel = $originalAffiliate;
            } else {
                // Do nothing.
                // Completely new customer, only Expert get the share
            }
        } else { // Purchase via affiliate link
            $affiliateModel->load($this->getAffiliateId());
            if(!$affiliateModel->getId()) {
                Mage::throwException($this->_helper->__('Affiliate not existed. Id = %s', $this->getAffiliateId()));
            }
        }

        return $affiliateModel;
    }

    protected function _getOriginalAffiliatePurchaseFrom(Mage_Customer_Model_Customer $customerModel)
    {
        $affiliateClientCollection = Mage::getModel('awaffiliate/client')->getCollection()
            ->addFieldToFilter('customer_id', $customerModel->getId())
            ->addOrder('id', 'ASC');

        if($affiliateClientCollection->count() == 0) { // Completely new customer
            return null;
        }

        // Get first affiliate id
        return Mage::getModel('awaffiliate/affiliate')->load($affiliateClientCollection->getFirstItem()->getAffiliateId());
    }

    protected function _getCustomerModel($customerId)
    {
        $customerModel = Mage::getModel('customer/customer')->load($customerId);
        if(!$customerModel->getId()) {
            Mage::throwException($this->_helper->__('Customer does not exist. Id = %s', $customerId));
        }

        return $customerModel;
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
            Mage::throwException(Mage::helper('awaffiliate')->$this->_helper->__('Rate is not specified'));
        }
        if (is_null($this->getData('currency_code'))) {
            Mage::throwException(Mage::helper('awaffiliate')->$this->_helper->__('Currency code is not specified'));
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
