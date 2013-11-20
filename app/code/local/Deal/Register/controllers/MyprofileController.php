<?php

 class Deal_Register_MyprofileController extends Mage_Core_Controller_Front_Action
 {
     public function indexAction()
     {
         $userId = Mage::getSingleton('customer/session')->getId();
         if ($userId) {
             $user = Mage::getModel('customer/customer')->load($userId);
             $username = Mage::getModel('admin/user')->load($user->getData('email'), 'email')->getData('username');
             $this->_forward('index', 'index', 'member', array('name' => $username));
         } else {
             $this->_redirect('customer/account/login');
         }
     }
 }
