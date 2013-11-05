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

class Belvg_Userprofile_Block_Adminhtml_Messages_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form		= new Varien_Data_Form();
        $message_id	= $this->getRequest()->getParam('id');
        $fieldset	= $form->addFieldset('general', array('legend'=>Mage::helper('core')->__('General Settings')));
        $wysiwygConfig	= Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array('tab_id' => 'page_tabs')
        );
        $loaddata=Mage::getModel('userprofile/messages')->load($message_id);
            if($loaddata['send_to']!=0)
            {
                $customer_data=Mage::getModel('customer/customer')->load($loaddata['send_to']);
                $custemail=$customer_data->getEmail();

                $fieldset->addField('sendername', 'label', array(
                     'label'    => Mage::helper('core')->__('Send To:'),
                     'value' => $custemail
                            ));
            }
            $fieldset->addField('title', 'text', array(
                'label'    => Mage::helper('core')->__('Title'),
                'title'    => Mage::helper('core')->__('Title'),
                'name'     => 'title',
                'width'    => '50px',
                'disabled' => true
            ));
             $fieldset->addField('message', 'editor', array(
                'label'    => Mage::helper('core')->__('Message'),
                'title'    => Mage::helper('core')->__('Message'),
                'name'     => 'message',
                'wysiwyg' => true,
                'disabled' => true,
                'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            ));
    
        $formData = Mage::getModel('userprofile/messages')->getMessageDataAsArray($message_id);

        $form->addValues($formData);
        $form->setFieldNameSuffix('design');
        $this->setForm($form);
    }

}

?>

