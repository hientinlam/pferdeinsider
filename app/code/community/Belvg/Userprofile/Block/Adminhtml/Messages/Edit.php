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
class Belvg_Userprofile_Block_Adminhtml_Messages_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
      public function __construct()
    {
         
        parent::__construct();
            
        $this->setTemplate('system/design/edit.phtml');
        $this->setId('design_new');
    }

    protected function _prepareLayout()
    { 
        parent::_prepareLayout();
                $this->setChild('back_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label'     => Mage::helper('catalog')->__('Back'),
                            'onclick'   => 'setLocation(\''.$this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
                            'class' => 'back'
                        ))
                );
                $this->setChild('save_button',
                        $this->getLayout()->createBlock('adminhtml/widget_button')
                            ->setData(array(
                                'label'     => Mage::helper('catalog')->__('Reply'),
                                'onclick'   => 'setLocation(\''.$this->getUrl('*/*/reply/', array('id'=>$this->getRequest()->getParam('id'),'store'=>$this->getRequest()->getParam('store', 0))).'\')',
                                'class' => 'save'
                            ))
                 );
          
                $this->setChild('delete_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label'     => Mage::helper('catalog')->__('Delete'),
                            'onclick'   => 'confirmSetLocation(\''.Mage::helper('catalog')->__('Are you sure?').'\', \''.$this->getDeleteUrl().'\')',
                            'class'  => 'delete'
                        ))
                 );

        
        return $this;
         
       
       
    }

    public function getDesignChangeId()
    {
	return 1;
        //return Mage::registry('design')->getId();
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getHeader()
    {
        $header = '';

            $header = Mage::helper('core')->__('Message');

        return $header;
    }  
}
?>

