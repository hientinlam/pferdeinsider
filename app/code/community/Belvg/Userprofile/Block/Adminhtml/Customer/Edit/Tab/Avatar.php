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
 */?>
<?php
class Belvg_Userprofile_Block_Adminhtml_Customer_Edit_Tab_Avatar  
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Columns, that should be removed from grid
     *
     * @var array
     */
    protected $_columnsToRemove = array('customer_email', 'customer_firstname', 'customer_lastname');

    /**
     * Disable filters and paging
     *
     */
    public function _construct()
    {
       
        parent::_construct();
        $this->setTemplate('userprofile/tabs/avatar.phtml');
        $this->setId('customer_edit_tab_avatar');
        
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Avatar');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Avatar');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        return (bool)$customer->getId();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    public function isAvatar($storeId){
       $customer_id = $this->getRequest()->getParam('id');
       $avatar = Mage::getModel('userprofile/avatars')->setStoreData($storeId)->loadByCustomerId($customer_id);
       if ($avatar && $avatar->getValue() != '')
           return true;
       return false;
    }
    
    public function getAvatarSrc($storeId){
        $customer_id = $this->getRequest()->getParam('id');
        $avatar = Mage::getModel('userprofile/avatars')->setStoreData($storeId)->loadByCustomerId($customer_id);
        if ($avatar && $avatar->getValue() != '')
            $ava_src = str_replace('index.php','',$this->getBaseUrl()).'media/userprofile/avatar/'.$customer_id.'/'.$avatar->getValue();
        else
            $ava_src = $this->getSkinUrl('images/catalog/product/placeholder/thumbnail.jpg','base/default');
        return $ava_src;
    }

    
}




