<?php
class Brst_Member_Adminhtml_PaymentController extends Mage_Adminhtml_Controller_Action
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
        $model = Mage::getModel('brst_member/payment');
     
        if ($id) {
            // Load record
            $model->load($id);
     
            // Check if record is loaded
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This payment no longer exists.'));
                $this->_redirect('*/*/');
     
                return;
            }  
        }  
     
        $this->_title($model->getId() ? $model->getName() : $this->__('New Payment'));
     
        $data = Mage::getSingleton('adminhtml/session')->getPaymentData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
     
        Mage::register('brst_member', $model);
     
        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Payment') : $this->__('New Payment'), $id ? $this->__('Edit Payment') : $this->__('New Payment'))
            ->_addContent($this->getLayout()->createBlock('brst_member/adminhtml_payment_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }
     
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getSingleton('brst_member/payment');
            $model->setData($postData);
 
            try {
                $model->save();
 
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The Payment has been saved.'));
                $this->_redirect('*/*/');
 
                return;
            }  
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this payment.'));
            }
 
            Mage::getSingleton('adminhtml/session')->setPaymentData($postData);
            $this->_redirectReferer();
        }
    }
     
    public function deleteAction()
    {
        $affiliateId = (int)$this->getRequest()->getParam('id');
        $affiliate =Mage::getSingleton('brst_member/payment')->load($affiliateId);
        if ($affiliate->getData()) {
            $affiliate->delete();
            $this->_getSession()->addSuccess($this->__("Payment has been successfully deleted."));
        } else {
            $this->_getSession()->addError($this->__("Can't load Payment by given ID."));
            
        }
        $this->_redirect('*/*');
    }
    
    /** 
    * @function         : exportCsvAction 
    * @created by       : parvez Alam 
    * @description      : Export data grid to CSV format 
    * @params           : null 
    * @returns          : array 
    */
    public function exportCsvAction()
    {
        $fileName   = 'member.csv';
        $content    = $this->getLayout()->createBlock('brst_member_block_adminhtml_payment_grid');
        $this->_prepareDownloadResponse($fileName, $content->getCsvFile());
    }
    
    public function exportExcelAction()
    {
        $fileName   = 'member.xml';
        $grid       = $this->getLayout()->createBlock('brst_member_block_adminhtml_payment_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    }
    
    public function messageAction()
    {
        $data = Mage::getModel('brst_member/payment')->load($this->getRequest()->getParam('id'));
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
            ->_setActiveMenu('sales/brst_member_payment')
            ->_title($this->__('Sales'))->_title($this->__('Payment'))
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Payment'), $this->__('Payment'));
         
        return $this;
    }
    
    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/brst_member_payment');
    }
}