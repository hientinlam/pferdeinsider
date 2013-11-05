<?php
class Brst_Experts_Adminhtml_AmountController extends Mage_Adminhtml_Controller_Action
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
        $model = Mage::getModel('brst_experts/amount');
     
        if ($id) {
            // Load record
            $model->load($id);
     
            // Check if record is loaded
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This amount no longer exists.'));
                $this->_redirect('*/*/');
     
                return;
            }  
        }  
     
        $this->_title($model->getId() ? $model->getName() : $this->__('New Amount'));
     
        $data = Mage::getSingleton('adminhtml/session')->getAmountData(true);
        if (!empty($data)) {
            $model->setData($data);
        }  
     
        Mage::register('brst_experts', $model);
     
        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Amount') : $this->__('New Amount'), $id ? $this->__('Edit Amount') : $this->__('New Amount'))
            ->_addContent($this->getLayout()->createBlock('brst_experts/adminhtml_amount_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }
     
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getSingleton('brst_experts/amount');
            $model->setData($postData);
 
            try {
                $model->save();
 
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The Amount has been saved.'));
                $this->_redirect('*/*/');
 
                return;
            }  
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this amount.'));
            }
 
            Mage::getSingleton('adminhtml/session')->setAmountData($postData);
            $this->_redirectReferer();
        }
    }
     
    public function deleteAction()
    {
        $affiliateId = (int)$this->getRequest()->getParam('id');
        $affiliate =Mage::getSingleton('brst_experts/amount')->load($affiliateId);
        if ($affiliate->getData()) {
            $affiliate->delete();
            $this->_getSession()->addSuccess($this->__("Amount has been successfully deleted."));
        } else {
            $this->_getSession()->addError($this->__("Can't load Amount by given ID."));

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
        $fileName   = 'orders.csv';
        $content    = $this->getLayout()->createBlock('brst_experts_block_adminhtml_amount_grid');
        $this->_prepareDownloadResponse($fileName, $content->getCsvFile());
    }
    
    public function exportExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('brst_experts_block_adminhtml_amount_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    }
    
    public function messageAction()
    {
        $data = Mage::getModel('brst_experts/amount')->load($this->getRequest()->getParam('id'));
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
            ->_setActiveMenu('sales/brst_experts_amount')
            ->_title($this->__('Sales'))->_title($this->__('Amount'))
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Amount'), $this->__('Amount'));
         
        return $this;
    }
     
    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/brst_experts_amount');
    }
}