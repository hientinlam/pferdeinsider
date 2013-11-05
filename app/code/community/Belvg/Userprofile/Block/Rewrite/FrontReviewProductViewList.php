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
class Belvg_Userprofile_Block_Rewrite_FrontReviewProductViewList extends Mage_Review_Block_Product_View_List{

    public function  _construct() {
        parent::_construct();
    }

    public function getAvatarSrc($customer_id){
//        return '11111';
//        $avatar =  Mage::getModel('userprofile/attributes')->getAvatar($customer_id);

        $avatar =  Mage::getModel('userprofile/avatars')->getAvatar($customer_id);
        
        if ($avatar)
            $ava_src = str_replace('index.php','',$this->getBaseUrl()).'media/userprofile/avatar/'.$customer_id.'/'.$avatar;
        else
            $ava_src = $this->getSkinUrl('images/catalog/product/placeholder/thumbnail.jpg','base/default');
		if($customer_id==''){
           $ava_src = str_replace('index.php','',$this->getBaseUrl()).'media/userprofile/avatar/default/default.jpg';
          }
        return $ava_src;
    }

}



