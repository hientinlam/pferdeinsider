<?php
class Brst_Calculate_Adminhtml_DiscountController extends Mage_Adminhtml_Controller_Action
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
        $model = Mage::getModel('brst_calculate/discount');
     
        if ($id) {
            // Load record
            $model->load($id);
     
            // Check if record is loaded
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This discount no longer exists.'));
                $this->_redirect('*/*/');
     
                return;
            }  
        }  
     
        $this->_title($model->getId() ? $model->getName() : $this->__('New Discount'));
     
        $data = Mage::getSingleton('adminhtml/session')->getDiscountData(true);
        if (!empty($data)) {
            $model->setData($data);
        }  
     
        Mage::register('brst_calculate', $model);
     
        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Discount') : $this->__('New Discount'), $id ? $this->__('Edit Discount') : $this->__('New Discount'))
            ->_addContent($this->getLayout()->createBlock('brst_calculate/adminhtml_discount_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }
     
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getSingleton('brst_calculate/discount');
            $model->setData($postData);
 
            try {
                $model->save();
 
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The Discount has been saved.'));
                $this->_redirect('*/*/');
 
                return;
            }  
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this baz.'));
            }
 
            Mage::getSingleton('adminhtml/session')->setDiscountData($postData);
            $this->_redirectReferer();
        }
    }
     
    public function deleteAction()
    {
        $affiliateId = (int)$this->getRequest()->getParam('id');
        $affiliate =Mage::getSingleton('brst_calculate/discount')->load($affiliateId);
        if ($affiliate->getData()) {
            $affiliate->delete();
            $this->_getSession()->addSuccess($this->__("Discount has been successfully deleted."));
        } else {
            $this->_getSession()->addError($this->__("Can't load Discount by given ID."));

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
        $content    = $this->getLayout()->createBlock('brst_calculate_block_adminhtml_discount_grid');
        $this->_prepareDownloadResponse($fileName, $content->getCsvFile());
    }
    
    public function exportExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('brst_calculate_block_adminhtml_discount_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    }
    
    /*protected function _prepareDownloadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }*/
    
    /*protected function _prepareDownloadResponse($fileName, $content, $contentType = 'application/octet-stream', $contentLength = null)
    {die('here');
        $session = Mage::getSingleton('admin/session');
        if ($session->isFirstPageAfterLogin()) {
            $this->_redirect($session->getUser()->getStartupPageUrl());
            return $this;
        }
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength)
            ->setHeader('Content-Disposition', 'attachment; filename=' . $fileName)
            ->setHeader('Last-Modified', date('r'));
        if (!is_null($content)) {
            $this->getResponse()->setBody($content);
        }
        return $this;
    }*/
    
    public function messageAction()
    {
        $data = Mage::getModel('brst_calculate/discount')->load($this->getRequest()->getParam('id'));
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
            ->_setActiveMenu('sales/brst_calculate_discount')
            ->_title($this->__('Sales'))->_title($this->__('Discount'))
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Discount'), $this->__('Discount'));
         
        return $this;
    }
     
    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/brst_calculate_discount');
    }
}