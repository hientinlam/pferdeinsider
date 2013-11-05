<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_Userprofile
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */
class Belvg_Userprofile_Block_Rewrite_FrontCustomerAccountHello extends Mage_Customer_Block_Account_Dashboard_Hello{

    protected function  _construct() {
        parent::_construct();
    }

    public function getAvatar(){
        $cid = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $avatar = Mage::getModel('userprofile/avatars')->loadByCustomerId($cid);
        $ava_src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'userprofile/avatar/'.$cid.'/'.$avatar->getValue();        
        return $ava_src;
    }
    
     public function hasAvatar(){
        $cid = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $avatar = Mage::getModel('userprofile/avatars')->loadByCustomerId($cid);
        return ($avatar->getValue() != '')?true:false;
    }

}



