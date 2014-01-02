<?php

class Brst_Createproduct_Block_Create extends Mage_Core_Block_Template
{
    public function getExpertsProducts()
    {
        $expertname = $this->_getCurrentExpertName();
        $productModel = Mage::getModel('catalog/product');
        $attr = $productModel->getResource()->getAttribute("member_list");
        if ($attr->usesSource()) {
            $id = $attr->getSource()->getOptionId($expertname[0]);
        }

        $collection = Mage::getResourceModel('catalog/product_collection');
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        $collection->addAttributeToSelect($attributes)
            ->addStoreFilter();
        $collection->addFieldToFilter(array(array('attribute'=>'member_list',array('eq'=>$id))));
        return $collection;
    }

    protected function _getCurrentExpertName()
    {
        $expertname = false;
        $customersession=Mage::getModel('customer/session')->getCustomer();
        $customerEmail=$customersession->getEmail();
        $adminmodel=Mage::getModel('admin/user')->getCollection()->getData();
        foreach($adminmodel as $admininfo)
        {
            if($admininfo['email']==$customerEmail)
            {
                $expertname =explode('-',$admininfo['username']);
                break;
            }
        }
        if ($expertname == false) {
            throw new Exception('Unable to fetch expert name.');
        }
        return $expertname;
    }
}