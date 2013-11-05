<?php
/**
 * Mage World
 *
 * NOTICE OF LICENSE

 * @category    MW
 * @package     MW_Dailydeal
 * @copyright   Copyright (c) 2012 Mage World (http://www.mageworld.com)
 
 */


/**
 * Product reports admin controller
 *
 * @category   MW
 * @package    MW_Dailydeal_Adminhtml
 * @author     Magento Developer <chinhbt@asiaconnect.com.vn>
 */
//Lay tu Mage_Adminhtml_Report_ProductController
class MW_Dailydeal_Adminhtml_DailyscheduleController extends Mage_Adminhtml_Controller_Action 
{
    /**
     * init
     */
    public function _initAction()
    {
    	Mage::getSingleton('adminhtml/session')->setFlag('dailyschedule');
//    	$act = $this->getRequest()->getActionName();
//        if(!$act)
//            $act = 'default';
        $this->loadLayout();
        	//->_setActiveMenu('dailydeal/items')    
        return $this;
    }
  
    /**
     * Daily Schedule Action
     */
    public function listAction()
    {
    	//return "{$this->getJsObjectName()}.doFilter();";
        $this->_title($this->__('Daily Deals Ordered'));
       // var_dump($this->getRequest()->getParam('month'));die();
//var_dump(Mage::getSingleton('adminhtml/session')->getMonth());die();
        $this->_initAction();
        $this->_setActiveMenu('');
            $this->_addBreadcrumb(Mage::helper('dailydeal')->__('Daily Deals Ordered'), Mage::helper('dailydeal')->__('Daily Deals Ordered'));
           
          // $this->_addContent($this->getLayout()->createBlock('dailydeal/adminhtml_dailyschedule_list'));
           
           $this->renderLayout();
    }

}