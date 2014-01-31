<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

require_once("Mage/Customer/controllers/AccountController.php");
class Deal_Register_AccountController extends Mage_Customer_AccountController
{
       public function createAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }
     public function createPostAction()
         { 
          
         if ($this->_getSession()->isLoggedIn()) {
             $this->_redirect('*/*/');
             return;
         }
         $radiovalue=$this->getRequest()->getPost();
         if ($this->getRequest()->isPost()) {
             $errors = array();

             if (!$customer = Mage::registry('current_customer')) {
                 $customer = Mage::getModel('customer/customer')->setId(null);
             }
 
             foreach (Mage::getConfig()->getFieldset('customer_account') as $code=>$node) {
                 if ($node->is('create') && ($value = $this->getRequest()->getParam($code)) !== null) {
                     $customer->setData($code, $value);
                 }
             }
 
             if ($this->getRequest()->getParam('is_subscribed', false)) {
                 $customer->setIsSubscribed(1);
             }
 
             /**
              * Initialize customer group id
              */
            
             if ($radiovalue['user_role']=='Professionals')
		        $customer->setGroupId('4');
             else
                $customer->getGroupId();

             try {
                 $validationCustomer = $customer->validate();
                 if (is_array($validationCustomer)) {
                     $errors = array_merge($validationCustomer, $errors);
                 }
                 $validationResult = count($errors) == 0;
 
                 if (true === $validationResult) {
                      $customer->save();
              
                      $arrData=$this->getRequest()->getPost();
                      $group_id=$customer->getGroupId();
                      $cid=$customer->getId();
                      $nickname=$arrData['nickname'];
                      $owner=$arrData['radio1'];
                      $type=$arrData['user_role'];
                      $aboutme=$arrData['about-me'];
                      $status=$arrData['status'];
                      $vision=$arrData['vision'];
                      $gender=$arrData['gender'];
                      $dateofbirth=$arrData['year']."/".$arrData['month']."/".$arrData['day'];
                      $date=$dateofbirth;
                      //$affialiate1=$arrData['is_affialiate'];
                
                      if(in_array($arrData['user_role'],array('Professionals','Affialiates'))){
                          $data1['customer_id'] =$cid ;
                          $data1['email'] = $arrData['email'];
                          $data1['status'] = 'pending';

                          $affialiate_model=Mage::getModel('brst_approve/affialiaterequest');
                          $affialiate_model->setData($data1);
                          $affialiate_model->save(); 


                          $sender_name=Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name Admin
                          $sender_email=Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email Admin
                          $emailid=$customer->getEmail();

                          $mailTemplate  = Mage::getModel('core/email_template');
                          $translate     = Mage::getSingleton('core/translate');
                          $templateId    = 1; 
                          $template_collection =  $mailTemplate->load($templateId);
                          $template_data       = $template_collection->getData();
                          $tem_text            = $template_data['template_text'];
                          $subject             = $template_data['template_code']; 
                          $logo1               = $customer->getFirstname();
                          $mail_text           = str_replace("{{var firstname}}", $logo1, $tem_text);
                          //die('hello die here');
                          $to        = $emailid;
                          $headers   = 'MIME-Version: 1.0' . "\r\n";
                          $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                              // Additional headers
                          $headers .= "To: $emailid" . "\r\n";
                          $headers .= 'From:'.$sender_name.'<'.$sender_email .'>' . "\r\n";
                          mail($to, $subject, $mail_text, $headers);


                          /*
                           * Send Email to ADMIN
                           */
                          $adminid        = Mage::getStoreConfig('trans_email/ident_general/email');
                          $adminname      = Mage::getStoreConfig('trans_email/ident_general/name');
                          $mailTemplate1  = Mage::getModel('core/email_template');
                          $translate1     = Mage::getSingleton('core/translate');
                          $templateId1    = 2; 
                          $template_collection1 =  $mailTemplate1->load($templateId1);
                          $template_data1      = $template_collection1->getData();
                          $tem_text1            = $template_data1['template_text'];
                          $subject             = $template_data1['template_code']; 
                          $logo2               = $adminname;
                          $sender_name1         = $customer->getFirstname();
                          $sender_email1        = $customer->getEmail();

                          $mail_text1          = str_replace("{{var firstname}}", $logo1, $tem_text1);
                          $mail_text2          = str_replace("{{var admin}}", $logo2, $mail_text1);
                          //die('hello die here');
                          $to1        = $adminid;
                          $headers   = 'MIME-Version: 1.0' . "\r\n";
                          $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                              // Additional headers
                          $headers .= "To: $adminid" . "\r\n";
                          $headers .= 'From:'.$sender_name1.'<'.$sender_email1 .'>' . "\r\n";
                          mail($to1, $subject, $mail_text2, $headers);
                      }
                      
                      $affdata['customer_id']=$customer->getId();
                      $affdata['rate']=0;
                      $affdata['status']='inactive';
                      $affdata['current_balance']=0;
                      $affdata['active_balance']=0;
                      $madeaffiliate = Mage::getModel('awaffiliate/affiliate');
                      $madeaffiliate->setData($affdata);
                      $madeaffiliate->save(); 
                      
                        $address = Mage::getModel("customer/address");
                        $address->setCustomerId($customer->getId());
                        $address->setFirstname($customer->getFirstname());
                        $address->setLastname($customer->getLastname());
                        $address->setStreet($arrData['address']);
                        $address->setPostcode($arrData['zipcode']);
                        $address->setregion($arrData['state']);
                        $address->setCity($arrData['city']);
                        $address->setCountryId($arrData['country']); //Country code here
                        $address->setIsDefaultBilling('1');
                        $address->setIsDefaultShipping('1');
                        $address->setSaveInAddressBook('1');
                        //  echo "<pre>";print_r($address);die('sdshds');
                        $address->save();
                  
                      $register_model=Mage::getModel('register/register');
                      $register_model->setcustomer_id($cid);
                      $register_model->sethorse_owner($owner);
                      $register_model->setnickname($nickname);
                      $register_model->settype($type);
                      $register_model->setstatus($status);
                      $register_model->setvision($vision);
                      $register_model->setaboutme($aboutme);
                      $register_model->setgender($gender);
                      $register_model->setbirthdate("$dateofbirth");
                      $register_model->setdateofbirth($dateofbirth);
                      $register_model->save();

                      $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
                      $query1="update tbl_brstcustomer set birthdate='$dateofbirth' where id=".$register_model->getId();            
                      $result1=$connection->query($query1);

                     if($group_id=='4'){
                        $arg_attribute = 'member_list';
                        $arg_value=  $arrData['firstname'];
                        $manufacturers = array($arg_value);

                        $attr_model = Mage::getModel('catalog/resource_eav_attribute');
                        $attr = $attr_model->loadByCode('catalog_product', $arg_attribute);
                        $attr_id = $attr->getAttributeId();

                        $option['attribute_id'] = $attr_id;
                        foreach ($manufacturers as $key=>$manufacturer) {
                            $option['value'][$manufacturer.'_'.$manufacturer][0] = $manufacturer;
                        }

                        $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
                        $setup->addAttributeOption($option);
                        try {
                              $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
                              $passwrd = substr( str_shuffle( $chars ), 0, 6 );
                              $randcode = substr( str_shuffle( $chars ), 0, 5 );
                              $user = Mage::getModel('admin/user')
                                            ->setData(array(
                                                'username'  => $arrData['firstname'].'-'.$randcode,
                                                'firstname' => $arrData['firstname'],
                                                'lastname'    => $arrData['lastname'],
                                                'email'     => $arrData['email'],
                                                'password'  => $passwrd,
                                                'is_active' => 1
                                            ))->save();

                        } catch (Exception $e) {
                                echo $e->getMessage();
                                exit;
                        }
                        $UserId=$user->getUserId();
                        try {
                                $user->setRoleIds(array(3))
                                    ->setRoleUserId($user->getUserId())
                                    ->saveRelations();

                        } catch (Exception $e) {
                                echo $e->getMessage();
                                exit;
                        }
                     }
                     if ($customer->isConfirmationRequired()) {
                         $customer->sendNewAccountEmail('confirmation', $this->_getSession()->getBeforeAuthUrl());
                         $this->_getSession()->addSuccess($this->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.',
                             Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())
                         ));
                         $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                         return;
                     }else {
                         $this->_getSession()->setCustomerAsLoggedIn($customer);
                         $url = $this->_welcomeCustomer($customer);
                         $this->_redirectSuccess($url);
                         return;
                     }
                 } else {

                     $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());

                     if (is_array($errors)) {
                         foreach ($errors as $errorMessage) {
                             $this->_getSession()->addError($errorMessage);
                         }
                     }
                     else {
                         $this->_getSession()->addError($this->__('Invalid customer data'));
                     }
                 }
             }
             catch (Mage_Core_Exception $e) {
                 $this->_getSession()->addError($e->getMessage())
                     ->setCustomerFormData($this->getRequest()->getPost());
             }
             catch (Exception $e) {
                 $this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
                     ->addException($e, $this->__('Can\'t save customer'));
            }
       }
       
       if($group_id=='4')
                 {
                 $this->_getSession()->setinvalidemail('User with this emailid already exists');
                         
                 $this->_redirectError(Mage::getUrl('register/index/member', array('_secure' => true)));
                 }
                else
                 {
                    $this->_getSession()->setinvalidemail('User with this emailid already exists');
                 $this->_redirectError(Mage::getUrl('register/index/member', array('_secure' => true)));
                 }
    }
    
 
           

}
                   
                 
		
		
               

?>