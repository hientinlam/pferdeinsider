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

class Belvg_Userprofile_Block_Group  extends  Mage_Core_Block_Template
{

    protected function  _construct() {
        parent::_construct();
        
    }

    public function getItemsHtml(){
        $group_id = Mage::registry('attr_group_id');
        $form = new Varien_Data_Form();
        $form->setAction('/boo/')
                 ->setId('design')
                 ->setName('design')
                 ->setMethod('POST')
                 ->setUseContainer(true);
        $fieldset = $form->addFieldset('general', array('legend'=>Mage::helper('core')->__($this->getGroupName())));
        $items = Mage::getResourceModel('userprofile/attributes_collection')
            ->addGroupFilter($group_id)
            ->addStoreFilter($this->helper('core')->getStoreId())
            ->addVisibleFilter()
            ->load();
        foreach ($items as $_item){            
            if ($_item->getType() != 'select'){
                $fieldset->addField($_item->getTitle(), $_item->getType(), array(
                    'label'    => Mage::helper('core')->__($_item->getTitle()),
                    'title'    => Mage::helper('core')->__($_item->getTitle()),
                    'name'     => $_item->getTitle(),
                    'width'    => '50px',
                ));
            }else{
                $fieldset->addField($_item->getTitle(), $_item->getType(), array(
                    'label'    => Mage::helper('core')->__($_item->getTitle()),
                    'title'    => Mage::helper('core')->__($_item->getTitle()),
                    'name'     => $_item->getTitle(),
                    'values'   => Mage::getModel('userprofile/attributes')->getOptions($_item),                    
                    'width'    => '50px',
                ));
            }
        }        
        return $form->getHtml();
    }

    public function getGroupName(){
        $group_id = $this->getRequest()->getParam('id');
        $group = Mage::getResourceModel('userprofile/groups_collection')
            ->load($group_id);
        $groupData = $group->getData();
        return $groupData['title'];
    }
    
}


