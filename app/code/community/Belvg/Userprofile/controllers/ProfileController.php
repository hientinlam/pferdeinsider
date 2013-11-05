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

class Belvg_Userprofile_ProfileController extends Mage_Core_Controller_Front_Action
{

	protected function _getSession()
        {
            return Mage::getSingleton('customer/session');
        }

	protected function _initAction() {		
		$this->loadLayout();			
		return $this;
	}   
 
	public function indexAction() {		
		$this->_initAction()
		->renderLayout();
	}
        
	public function groupAction(){
                $this->_initAction(); 
                $group_id = $this->getRequest()->getParam('id');
                if ($group_id){
                    Mage::unregister('attr_group_id');
                    Mage::register('attr_group_id', $id);
                    $this->renderLayout();
                }
                else{
                    $this->_redirect('not-found');
                }

        }

        public function editPostAction(){
               if (!$this->_validateFormKey()) {
                    return $this->_redirect('*/*/edit');
                }                
                if ($this->getRequest()->isPost()) {
                    /* @var $customer Mage_Customer_Model_Customer */
                    $customer = $this->_getSession()->getCustomer();
                    $profileModel = Mage::getModel('userprofile/avatars');
                    if ($_FILES) {
                        
                        if($_FILES['avatar']['size'] < 4194304)
                        {
                            $this->upload($_FILES);
                         }
                        else
                        {
                           Mage::getSingleton('core/session')->addsuccess('You can upload image of size upto 4 Mb');
                             $this->_redirect('customer/account/index');
                             return; 
                        }
                    }
                    /* @var $customerForm Mage_Customer_Model_Form */
                    $customerForm = Mage::getModel('customer/form');
                    $customerForm->setFormCode('customer_account_edit')
                        ->setEntity($customer);

                    $customerData = $customerForm->extractData($this->getRequest());

                    $errors = array();
                    $customerErrors = $customerForm->validateData($customerData);
                    if ($customerErrors !== true) {
                        $errors = array_merge($customerErrors, $errors);
                    } else {
                        $customerForm->compactData($customerData);
                        $errors = array();

                        // If password change was requested then add it to common validation scheme
                        if ($this->getRequest()->getParam('change_password')) {
                            $currPass   = $this->getRequest()->getPost('current_password');
                            $newPass    = $this->getRequest()->getPost('password');
                            $confPass   = $this->getRequest()->getPost('confirmation');

                            $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
                            if (Mage::helper('core/string')->strpos($oldPass, ':')) {
                                list($_salt, $salt) = explode(':', $oldPass);
                            } else {
                                $salt = false;
                            }

                            if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                                if (strlen($newPass)) {
                                    // Set entered password and its confirmation - they will be validated later to match each other and be of right length
                                    $customer->setPassword($newPass);
                                    $customer->setConfirmation($confPass);
                                } else {
                                    $errors[] = $this->__('New password field cannot be empty.');
                                }
                            } else {
                                $errors[] = $this->__('Invalid current password');
                            }
                        }

                        // Validate account and compose list of errors if any
                        $customerErrors = $customer->validate();
                        if (is_array($customerErrors)) {
                            $errors = array_merge($errors, $customerErrors);
                        }
                    }

                    if (!empty($errors)) {
                        $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
                        foreach ($errors as $message) {
                            $this->_getSession()->addError($message);
                        }
                        $this->_redirect('*/*/edit');
                        return $this;
                    }

                    try {
                        $customer->setConfirmation(null);
                        $customer->save();
                        $profileModel->addAvatarToCustomer($_FILES,$customer->getId());                        
                        $this->_getSession()->setCustomer($customer)
                            ->addSuccess($this->__('The account information has been saved.'));                        
                        $this->_redirect('customer/account');
                        return;
                    } catch (Mage_Core_Exception $e) {
                        $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                            ->addError($e->getMessage());
                    } catch (Exception $e) {
                        $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                            ->addException($e, $this->__('Cannot save the customer.'));
                    }
                }

                $this->_redirect('*/*/edit');
        }

        protected function upload($data){
           
            $customer = $this->_getSession()->getCustomer();
            $upload_dir = getcwd().'/media/userprofile/';
            foreach ($data as $key=>$value){
                if (is_dir($upload_dir.$key.'/'.$customer->getId().'/')){                    
                    if (move_uploaded_file ($value['tmp_name'], $upload_dir.$key.'/'.$customer->getId().'/'.$value['name']))
                            return true;
                }
                else{
                    
                    mkdir($upload_dir.$key.'/', '0755');
					exec('chmod '.$upload_dir.$key.'/'.' 0755');
                    mkdir($upload_dir.$key.'/'.$customer->getId().'/', '0755');     			
					exec('chmod '.$upload_dir.$key.'/'.$customer->getId().'/'.' 0755');
                    if (move_uploaded_file ($value['tmp_name'], $upload_dir.$key.'/'.$customer->getId().'/'.$value['name']))
                            return true;
                }

            }
            return false;
            

        }
	

}
