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

class Belvg_Userprofile_Model_Avatars extends Mage_Core_Model_Abstract{
    
    protected $store_id = null;
    
    protected function _construct(){        
	$this->_init('userprofile/avatars');        
    }                  

    public function setStoreData($storeId){
        $this->store_id = $storeId;
        return $this;
    }
    
    public function loadByCustomerId($_customerId){


        if (!$this->store_id)
            $this->store_id = Mage::app()->getStore()->getId();
        $avatar = Mage::getModel('userprofile/avatars')
            ->getCollection()
            ->addFieldToFilter('store_id', $this->store_id)        
            ->addFieldToFilter('customer_id', $_customerId)
            ->fetchItem();


        if ($avatar)
            return $avatar;
        return $this;    
    }

    public function addAvatarToCustomer($attributeData,$customer_id){          
        $_avatar = $this->loadByCustomerId($customer_id);        
        if (isset($attributeData['avatar'])){
            $_data = $attributeData['avatar'];      
            $aDBInfo = array(
                'value' => $_data['name'],
                'customer_id' => $customer_id,
                'store_id' => Mage::app()->getStore()->getId()
            );
            $_avatar->addData($aDBInfo);
            try{
                $_avatar->save();
            }catch(Exception $e){
                Mage::getSingleton('customer/session')->addError($e->getMessage());
            }
        }else{
            foreach ($attributeData as $key=>$data){
                $_data = $data;      
                $aDBInfo = array(
                    'value' => $_data['name'],
                    'customer_id' => $customer_id,
                    'store_id' => str_replace('avatar_','',$key)
                );
                $_avatar->addData($aDBInfo);
                try{
                    $_avatar->save();
                }catch(Exception $e){
                    Mage::getSingleton('customer/session')->addError($e->getMessage());
                }
            }
        }
    }

    public function getAvatar($_customerId){

       if (!$_customerId) {return $this;}


        if (!$this->store_id)
            $this->store_id = Mage::app()->getStore()->getId();
            $avatar = Mage::getModel('userprofile/avatars')
            ->getCollection()
            ->addFieldToFilter('store_id', $this->store_id)
            ->addFieldToFilter('customer_id', $_customerId)
          ->fetchItem();

        //var_dump($avatar); die;
       // Mage::log(var_export($avatar, TRUE),NULL,'some_filename.log');

        if (!$avatar) return;
     $avatar = $avatar->getData();
     $avatar = $avatar['value'];

        if ($avatar)
            return $avatar;
        return $this;

    }
}


