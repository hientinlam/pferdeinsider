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
class Belvg_Userprofile_Block_Rewrite_FrontPageHtmlHeader extends Mage_Page_Block_Html_Header{

    public function  _construct() {
        parent::_construct();
    }

    public function getWelcome()
    {
        if (empty($this->_data['welcome'])) {
            if (Mage::isInstalled() && Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->_data['welcome'] = $this->__('Welcome, %s!', $this->htmlEscape(Mage::getSingleton('customer/session')->getCustomer()->getName()));
            } else {
                $this->_data['welcome'] = Mage::getStoreConfig('design/header/welcome');
            }
        }
		$cid = Mage::getSingleton('customer/session')->getCustomer()->getId();        
		if ($cid){		
			$avatar = Mage::getModel('userprofile/avatars')->loadByCustomerId($cid);
                        if ($avatar){
                            $ava_src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'userprofile/avatar/'.$cid.'/'.$avatar->getValue();                
                            if ($avatar->getValue() != '')
                                $this->_data['welcome'].='<img src="'.$ava_src.'" width="50"/>';
                        }
			
			
                            
		}
			       
        return $this->_data['welcome'];
    }

}



