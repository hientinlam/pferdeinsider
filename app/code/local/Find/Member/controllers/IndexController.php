<?php
class Find_Member_IndexController extends Mage_Core_Controller_Front_Action
{
     protected function _getSession()
    {       
        return Mage::getSingleton('customer/session');
    }
    public function indexAction()
    {
       $firstname=$this->getRequest()->getParam('name');
       $this->loadLayout();     
        $this->getLayout()->getBlock('head')->setTitle('Expert-'.$firstname);
        $this->renderLayout();
   }
   
 
}