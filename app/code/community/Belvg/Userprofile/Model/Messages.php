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

class Belvg_Userprofile_Model_Messages extends Mage_Core_Model_Abstract{
    
        protected function _construct(){        
            $this->_init('userprofile/messages');
        }

        public function add($msgData){
            $msgData['date'] = date('Y-m-d',time());
            $msgData['customer_id'] = Mage::getSingleton('customer/session')->getCustomer()->getId();
            //$msgData['status'] = 1;
            $msgData['type'] = 1;
            try{
                $item = $this
                    ->setData($msgData)
                    ->save();
                Mage::getSingleton('customer/session')->addSuccess('Message was succesfully send');
            }  catch (Exception $e){
                Mage::getSingleton('customer/session')->addError($e->getMessage());
            }
        }

        public function addFromAdmin($msgData,$id){
            $messageData = $this->getMessageDataAsArray($id);
            $msgData['date'] = date('Y-m-d',time());
            $msgData['customer_id'] = $messageData['customer_id'];
            $msgData['status'] = 0;
            $msgData['type'] = 0;
            try{
                    $item = $this
                        ->setId($id)
                        ->setData($msgData)
                        ->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess('Message was succesfully send');
                }  catch (Exception $e){
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
	
	
	public function addFromAdminToCustomers($msgData){      
            $msgData['date'] = date('Y-m-d',time());
	    try{
		   foreach ($msgData['customer_id'] as $cust_id){
			   $msgData['customer_id'] = $cust_id;
			   $msgData['status'] = 0;
			   $msgData['type'] = 0;
			   $item = $this
					->setData($msgData)
					->save();
		   }                      
                Mage::getSingleton('adminhtml/session')->addSuccess('Message was succesfully send');
            }  catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        public function remove($messageId){
            try{
                $this->setId($messageId)
                        ->delete();
                    Mage::getSingleton('customer/session')->addSuccess('Message was succesfully deleted');
            }  catch (Exception $e){
                    Mage::getSingleton('customer/session')->addError($e->getMessage());
            }
        }

        public function getMessageDataAsArray($id){
            $message = $this->load($id);
            return $message->_data;
        }

	public function isRead($id){
            try{
                $this->load($id);
                $this->setStatus(1);
                $this->save();
            }  catch (Exception $e){ 
                Mage::getSingleton('customer/session')->addError($e->getMessage());
            }             
        }
	
}


