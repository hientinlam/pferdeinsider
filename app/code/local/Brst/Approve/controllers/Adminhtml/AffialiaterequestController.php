<?php
class Brst_Approve_Adminhtml_AffialiaterequestController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {  
        // Let's call our initAction method which will set some basic params for each action
        $this->_initAction()
            ->renderLayout();
    }  
     
    public function newAction()
    {  
        // We just forward the new action to a blank edit form
        $this->_forward('edit');
    }  
     
    public function editAction()
    {  
       
        $this->_initAction();
     
        // Get id if available
        $id  = $this->getRequest()->getParam('id');
        $model = Mage::getModel('brst_approve/affialiaterequest');
       
        if ($id) {
            // Load record
          
         $model->load($id);
   //  echo "<pre>";print_r($model->load($id)); die('hjdf');
            // Check if record is loaded
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This Affialiate no longer exists.'));
                $this->_redirect('*/*/');
     
                return;
            }  
        }  
     
        $this->_title($model->getId() ? $model->getName() : $this->__('New Affialiate'));
     
        $data = Mage::getSingleton('adminhtml/session')->getAffialiaterequestData(true);
        if (!empty($data)) {
            $model->setData($data);
        }  
    
        Mage::register('brst_approve', $model);
     
        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Affialiate') : $this->__('New Affialiate'), $id ? $this->__('Edit Affialiate') : $this->__('New Affialiate'))
            ->_addContent($this->getLayout()->createBlock('brst_approve/adminhtml_affialiaterequest_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }
     
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getSingleton('brst_approve/affialiaterequest');
            $model->setData($postData);
 
            try {
                 if($_POST['status']=='complete')
                {
                      $customer = Mage::getModel('customer/customer')->load($_POST['customer_id']);
                      $customerId=$customer->getId();
                      $affiliate =  Mage::getModel('awaffiliate/affiliate')->getCollection()->getData();
                         foreach($affiliate as $inactive)
                         {
                             if($customerId==$inactive['customer_id'])
                             {
                                 $affid=$inactive['id'];
                                 $status=$inactive['status'];
                                 break;
                             }
                         }
                        // die('hhdhd');
                         if($affid != '')
                         {
                          $affdata['id']=$affid;
                          $affdata['customer_id']=$customer->getId();
                          $affdata['rate']=0;
                          $affdata['status']='active';
                          $affdata['current_balance']=0;
                          $affdata['active_balance']=0;
                          $madeaffiliate = Mage::getModel('awaffiliate/affiliate')->load($affid);
                          $madeaffiliate->setData($affdata);
                          $madeaffiliate->save(); 
                         }
                         else{
                          $affdata['customer_id']=$customer->getId();
                          $affdata['rate']=0;
                          $affdata['status']='active';
                          $affdata['current_balance']=0;
                          $affdata['active_balance']=0;
                          $madeaffiliate = Mage::getModel('awaffiliate/affiliate');
                          $madeaffiliate->setData($affdata);
                          $madeaffiliate->save();  
                         }
                      $customer = Mage::getModel('customer/customer')->load($_POST['customer_id']);
                      $sender_name=Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name Admin
                      $sender_email=Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email Admin
                      $emailid=$customer->getEmail();
                 
                      $mailTemplate  = Mage::getModel('core/email_template');
                      $translate     = Mage::getSingleton('core/translate');
                      $templateId    = 3; 
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
                }
                $model->save();
 
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The Affialiate has been saved.'));
                $this->_redirect('*/*/');
 
                return;
            }  
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this baz.'));
            }
 
            Mage::getSingleton('adminhtml/session')->setAffialiaterequestData($postData);
            $this->_redirectReferer();
        }
    }
     
    public function deleteAction()
    {
        $affiliateId = (int)$this->getRequest()->getParam('id');
        $affiliate =Mage::getSingleton('brst_approve/affialiaterequest')->load($affiliateId);
        if ($affiliate->getData()) {
            $affiliate->delete();
            $this->_getSession()->addSuccess($this->__("Affiliate has been successfully deleted."));
        } else {
            $this->_getSession()->addError($this->__("Can't load Affiliate by given ID."));

        }
        $this->_redirect('*/*');
    }
    public function messageAction()
    {
        $data = Mage::getModel('brst_approve/affialiaterequest')->load($this->getRequest()->getParam('id'));
        echo $data->getContent();
    }
     
    /**
     * Initialize action
     *
     * Here, we set the breadcrumbs and the active menu
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            // Make the active menu match the menu config nodes (without 'children' inbetween)
            ->_setActiveMenu('sales/brst_approve_affialiaterequest')
            ->_title($this->__('Sales'))->_title($this->__('Affialiaterequest'))
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Affialiaterequest'), $this->__('Affialiaterequest'));
         
        return $this;
    }
     
    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/foo_bar_baz');
    }
}