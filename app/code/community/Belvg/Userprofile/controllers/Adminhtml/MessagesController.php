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

class Belvg_Userprofile_Adminhtml_MessagesController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct(){

        
    }

	protected function _initAction() {
		
		$this->loadLayout()
			->_setActiveMenu('customer')
			->_addBreadcrumb($this->__('User Profile'), $this->__('Messages'));
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) { 
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true); 
		}
		return $this;
	}  
    public function indexAction(){        
	$this->_initAction();
        $this->_setActiveMenu('customer');
        $this->_addBreadcrumb($this->__('User Profile'), $this->__('Messages'));
        $this->_addContent($this->getLayout()->createBlock('userprofile/adminhtml_messages'));
 	$this->renderLayout();
    }

    public function replyAction(){
        $this->_initAction()
		->renderLayout();
    }
	
    public function newAction(){
        $this->_initAction()
		->renderLayout();
    }

    public function editAction(){
        $this->_initAction()
		->renderLayout();
    }
    public function deleteAction(){
        $msg_id = $this->getRequest()->getParam('id');
        Mage::getModel('userprofile/messages')->remove($msg_id);
        $this->_redirect('*/*/');
    }
	
	
	public function saveAction(){
		$_data = $this->getRequest()->getPost('design');
		if ($_data)
            Mage::getModel('userprofile/messages')->addFromAdminToCustomers($_data);
        else
            $this->_redirect('*/*/');
        $this->_redirect('*/*/');
	}
	
    public function sendAction(){
        $_data = $this->getRequest()->getPost('design');
        $msg_id = $this->getRequest()->getParam('id');
        if ($_data)
            Mage::getModel('userprofile/messages')->addFromAdmin($_data,$msg_id);
        else
            $this->_redirect('*/*/');
        $this->_redirect('*/*/');
    }


}

