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

class Belvg_Userprofile_Model_Observer extends Mage_Core_Model_Abstract{
    
   public function customObserverAction(){
       $customer_id = Mage::app()->getRequest()->getPost('customer_id');
       $profileModel = Mage::getModel('userprofile/avatars');
       if ($_FILES){
           if ($this->upload($_FILES, $customer_id))
                   $profileModel->addAvatarToCustomer($_FILES,$customer_id);
       }       
   }

   protected function upload($data,$customer_id){        
        $upload_dir = getcwd().'/media/userprofile/';
        foreach ($data as $key=>$value){
            if (is_dir($upload_dir.$key.'/'.$customer_id.'/')){
                if (move_uploaded_file ($value['tmp_name'], $upload_dir.$key.'/'.$customer_id.'/'.$value['name']))
                        return true;
            }
            else{

                mkdir($upload_dir.$key.'/', '0755');
                mkdir($upload_dir.$key.'/'.$customer_id.'/', '0755');
                if (move_uploaded_file ($value['tmp_name'], $upload_dir.$key.'/'.$customer_id.'/'.$value['name']))
                        return true;
            }

        }
        return false;


    }
}


