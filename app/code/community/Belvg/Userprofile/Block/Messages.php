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

class Belvg_Userprofile_Block_Messages  extends  Mage_Core_Block_Template
{

    protected function _getSession(){
        return Mage::getSingleton('customer/session');
    }

    protected function  _construct() {
        parent::_construct();
        
    }

    public function getInboxMessages(){
        $customer = $this->_getSession()->getCustomer();
        $messages = Mage::getResourceModel('userprofile/messages_collection')
         //  ->addCustomerFilter($customer->getId())
            ->addInboxFilter()
           ->newCustomFilter($customer->getId(),$customer->getId())
            ->load();  
       // echo "<pre>";print_r($messages->getData());
        return $messages;
    }

    public function getOutboxMessages(){
        $customer = $this->_getSession()->getCustomer();
        $messages = Mage::getResourceModel('userprofile/messages_collection')
            ->addCustomerFilter($customer->getId())
            ->addOutboxFilter()
            ->load();
        return $messages;
    }

    public function inRead($message){	
        if ($message->getStatus() == 0) return true;
        else            return false;
    }

    public function isMessageToAdmin()
    {
        return $this->getRequest()->getParam('admin', 0) == 1;
    }
}


